<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use GuzzleHttp\Command\Exception\CommandClientException;

use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

use App\Helpers\Discord;
use RestCord\DiscordClient;

use App\Http\Resources\Applications\ApplicationCollection;
use App\Http\Resources\Applications\ApplicationResource;

class ApplicationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $applications = Application::where("owner_id", $user->id)
            ->with("owner")
            ->get();

        return new ApplicationCollection($applications);
    }

    public function get(string $id)
    {
        $user = Auth::user();
        $application = Application::find($id);
        
        if(!isset($application)) {
            return response()->json([
                "status" => 404,
                "message" => "Cette application n'existe pas!"
            ], 404);
        }

        return new ApplicationResource($application);
    }

    public function store(Request $request)
    {
        $guild_id = $request->input("guild_id");
        $bot_token = $request->input("bot_token");

        $user = Auth::user();
        $guild = collect($user->guilds)->where("id", $guild_id);

        if (!isset($bot_token) or !isset($guild_id)) {
            $value = isset($bot_token) ? "guild_id" : "bot_token";
            return response()->json([
                "status" => 404,
                "message" => "Il manque le {$value}!"
            ], 404);
        }

        if($guild->isEmpty()) {
            return response()->json([
                "status" => 404,
                "message" => "Ce serveur n'existe pas!"
            ], 404);
        }

        try {
            $discord = new DiscordClient([
                "token" => $bot_token,
                "tokenType" => "Bot",
            ]);
            $bot = $discord->user->getCurrentUser();

            $created = Application::create([
                "bot_token" => $bot_token, 
                "guild_id" => $guild_id,
                "owner_id" => (string)$user->id,
                "name" => $bot->username,
                "discriminator" => $bot->discriminator,
                "avatar" => "https://cdn.discordapp.com/avatars/{$bot->id}/{$bot->avatar}.png?size=128",
            ]);

            return [
                "created" => $created->_id,
                "id" => $bot->id,
                "name" => $bot->username,
                "discriminator" => $bot->discriminator,
                "avatar" => "https://cdn.discordapp.com/avatars/{$bot->id}/{$bot->avatar}.png?size=128",
            ];
        } catch (CommandClientException $e) {
            return response()->json([
                "status" => 404,
                "message" => "Ce bot n'existe pas!"
            ], 404);
        }
    }

    public function sync(string $id) {
        $user = Auth::user();
        $application = Application::find($id);
        $guild = collect($user->guilds)->firstWhere("id", $application->guild_id);

        if(!$guild) {
            return response()->json([
                "status" => 404,
                "message" => "Ce serveur n'existe pas!"
            ], 404);
        }

        if(!$guild["is_owner"]) {
            return response()->json([
                "status" => 401,
                "message" => "Ce serveur ne t'appartiens pas!"
            ], 401);
        }

        $botGuilds = Discord::getDiscordBot()->user->getCurrentUserGuilds([]);
        $botOnServer = collect($botGuilds)->firstWhere("id", "=", $guild["id"]);
        if ($botOnServer === null) {
            return response()->json([
                "status" => 401,
                "message" => "Le bot n'est pas présent sur le discord!"
            ], 401);
        }

        $emojis = collect(Discord::getDiscordBot()->emoji->listGuildEmojis(["guild.id" => (integer)$guild["id"]]))->map(function ($emoji) {
            $mime = $emoji->animated ? "gif" : "png";
            return [
                "id"        => $emoji->id,
                "url"       => "https://cdn.discordapp.com/emojis/{$emoji->id}.{$mime}",
                "name"      => $emoji->name,
            ];
        });
        $channels = collect(Discord::getDiscordBot()->guild->getGuildChannels(["guild.id" => (integer)$guild["id"]]))->filter(function ($channel, $key) {
            return $channel->type == 0;
        })->map(function ($channel) {
            return [
                "id"        => $channel->id,
                "name"      => $channel->name,
            ];
        });
        $roles = collect(Discord::getDiscordBot()->guild->getGuildRoles(["guild.id" => (integer)$guild["id"]]))->map(function( $role ) {
            return [
                "id"        => $role->id,
                "name"      => $role->name,
                "color"     => "#".dechex($role->color)
            ];
        });

        $application["emojis"] = array_values($emojis->toArray());
        $application["channels"] = array_values($channels->toArray());
        $application["roles"] = array_values($roles->toArray());

        $application->push();

        return [
            "notification" => [
                "type" => "success",
                "layout" => "modal",
                "title" => "Succès!",
                "content" => "Vous avez synchronisé les informations de votre serveur avec le site.
                    <br/>
                    Nous avons récupéré {$emojis->count()} emojis, {$channels->count()} channels et {$roles->count()} rôles."
            ]
        ];
    }
}
