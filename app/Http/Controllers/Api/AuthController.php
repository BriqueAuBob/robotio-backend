<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Discord;
use App\Http\Requests\AuthorizeRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\TokenRefreshRequest;
use App\Http\Resources\Tokens\TokenResource;
use App\Http\Resources\Users\UserResource;
use App\Models\Token;
use App\Models\User;
use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Psr\Log\NullLogger;
use RestCord\DiscordClient;
use Storage;
use Str;
use finfo;

use Illuminate\Http\Request;

/**
 * @group Authentication
 * Endpoint used to authenticate a user or refresh a token.
 */
class AuthController extends Controller
{
    public function auth()
    {
        $client_id = config( "services.discord.client_id" );
        $redirect_uri = urlencode(config("services.discord.redirect"));
        $redirectto = "https://discordapp.com/api/oauth2/authorize?client_id=${client_id}&redirect_uri=${redirect_uri}&response_type=token&scope=identify%20email%20guilds";

        return [
            "redirect" => $redirectto
        ];
    }

    /**
     * Authenticate a user
     *
     * Use this endpoint to authenticate a user and register it to our database or recover it.
     *
     * @bodyParam discord_access_token string required Access token returned by discord.
     * @responseFile responses/authentication/authorize.get.json
     * @responseFile 401 responses/authentication/bad.discord.access_token.json
     *
     * @param AuthorizeRequest $request
     * @return UserResource|JsonResponse
     */
    public function authorizeUser(AuthorizeRequest $request)
    {
        $access_token = $request->get('discord_access_token');

        // Init Guzzle client with the user access_token.
        $client = new DiscordClient([
            'token' => $access_token,
            'tokenType' => 'OAuth',
            'logger' => new NullLogger(),
        ]);

        // Getting base user information.
        try {
            $member = $client->user->getCurrentUser([]);
        } catch (CommandClientException $e) {
            return response()->json([
                'message' => __('error.401.bad_access_token')
            ], 401);
        }

        // Update or create user model with discord data.
        $user = User::firstOrNew(["discord_id" => (string)$member->id]);
        $user->discord_id = (string)$member->id;
        $user->email = $member->email;
        $user->username = e($member->username);
        $user->tag = '#' . $member->discriminator;

        if(!isset($user->money)) {
            $user->money = 0;
        }

        // Create or update DM and recover channel_id to save it
        $user->discord_dm_id = Discord::getDmChannel($user)->id;

        // Handle avatar for each user.
        $file_info = new finfo(FILEINFO_MIME_TYPE);
        $contents = file_get_contents($member->avatar !== null ? "https://cdn.discordapp.com/avatars/$member->id/$member->avatar?size=256" : "https://discordapp.com/assets/322c936a8c8be1b803cd94861bdfa868.png");
        $mime_type = str_replace("image/", "", $file_info->buffer($contents));
        if($mime_type !== "gif") {
            $mime_type = "png";
        }

        $path = 'avatars/' . $user->discord_id.".".$mime_type;
        Storage::disk("public")->put($path, $contents);
        $user->avatar = $mime_type;

        $guilds = collect($client->user->getCurrentUserGuilds([]))->map(function($guild) {
            return [
                "id"        => (string)$guild->id,
                "icon"      => isset($guild->icon) ? "https://cdn.discordapp.com/icons/{$guild->id}/{$guild->icon}.webp?size=128" : null,
                "is_owner"  => $guild->owner,
                "name"      => $guild->name
            ];
        })->toArray();
        $user->guilds = $guilds;

        // Refresh token
        if ($user->personalToken() !== null) {
            $user->personalToken()->extendTime();
        }

        Token::create([
            'discord_id' => (string)$user->discord_id,
            'is_revokable' => false,
            'is_personal' => true,
            'access_token' => Str::random(110),
        ]);

        // Save actual state of the user.
        $user->push();
        $user->refresh();

        // Finally return the user model as resource.
        return new TokenResource($user->personalToken());
    }

    /**
     * Refresh a token
     *
     * Endpoint used to refresh the validity of an access token.
     *
     * @bodyParam access_token string required The current access token
     * @bodyParam refresh_token string required The refresh token associated to the access token
     * @bodyParam discord_id int required The discord id of the token owner
     *
     * @responseFile responses/authentication/token.refresh.json
     *
     * @param TokenRefreshRequest $request
     * @return TokenResource|JsonResponse
     */
    public function tokenRefresh(TokenRefreshRequest $request)
    {
        $token = Token::haveRefreshToken()
            ->isPersonal()
            ->with(['refreshToken' => static function ($query) use ($request) {
                $query->where('access_token', $request->get('refresh_token'));
            }])
            ->where('discord_id', (int) $request->get('discord_id'))
            ->where('access_token', $request->get('access_token'))
            ->first();

        if (!$token || !$token instanceof Token) {
            return response()->json([
                'message' => __('error.404.could_not_match_tokens')
            ], 404);
        }

        // Extend token time validity
        $token->extendTime();

        return new TokenResource($token);
    }

    public function logout(Request $request)
    {
        $headers = getallheaders();
        if(!isset($headers["Authorization"])) {
            return;
        }

        $token = str_replace("Bearer ", "", $headers["Authorization"] );
    
        $token = Token::where( "access_token", $token )
            ->where( "is_personal", 1 )
            ->first();

        $user_id = $token->discord_id;
        $token->delete();
    }
}
