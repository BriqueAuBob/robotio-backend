<?php

namespace App\Http\Controllers\Api\V1\Modules;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Modules\Warn;
use App\Models\GuildMember;

use RestCord\DiscordClient;

use App\Http\Requests\Modules\WarnRequest;

class WarnController extends Controller
{
    public function index(string $id, Request $request)
    {
        $user = $request->input("user_id");
        $warns = Warn::where("app_id", $id)
            ->where(function($query) use ($user)  {
                if(isset($user)) {
                    $query->where("user_id", $user);
                }
            })
            ->with("author")
            ->with("user")
            ->get();
        
        return [
            "warns" => $warns
        ];
    }

    public function get(string $id, Warn $warn)
    {
        if($warn->app_id !== $id) {
            return response()->json([
                "status" => 404,
                "message" => __("error.404.message")
            ], 400);
        }
        return [
            "warn" => $warn
        ];
    }

    public function store(string $id, WarnRequest $request)
    {
        $application = resolve("application");
        $author_id = $request->input("author_id");
        $user_id = $request->input("user_id");

        if($author_id && $user_id) {
            $author = GuildMember::where("discord_id", $author_id)->first();
            $user = GuildMember::where("discord_id", $user_id)->first();

            $bot = new DiscordClient([
                "token" => $application->bot_token,
                "tokenType" => "Bot",
            ]);

            if(!$author) {
                $member = $bot->guild->getGuildMember([
                    "guild.id" => (integer)$application->guild_id,
                    "user.id" => (integer)$author_id
                ])->user;

                GuildMember::create([
                    "discord_id" => $author_id,
                    "avatar" => "https://cdn.discordapp.com/avatars/{$author_id}/{$member->avatar}.png?size=128",
                    "username" => $member->username,
                    "tag" => $member->discriminator
                ]);
            }
            if(!$user) {
                $member = $bot->guild->getGuildMember([
                    "guild.id" => (integer)$application->guild_id,
                    "user.id" => (integer)$user_id
                ])->user;

                GuildMember::create([
                    "discord_id" => $user_id,
                    "avatar" => "https://cdn.discordapp.com/avatars/{$user_id}/{$member->avatar}.png?size=128",
                    "username" => $member->username,
                    "tag" => $member->discriminator
                ]);
            }
        }
        
        Warn::create($request->validated());

        $warns = Warn::where("app_id", $application->id)
            ->where("user_id", $user_id)
            ->get();
        return [
            "warns" => $warns
        ];
    }

    public function destroy(string $id, Request $request)
    {
        $user = $request->input("user_id");
        $warns = Warn::where("app_id", $id)
            ->where(function($query) use ($user)  {
                if(isset($user)) {
                    $query->where("user_id", $user);
                }
            });
        
        $count = $warns->count();
        $warns->delete();
        
        return [
            "count" => $count
        ];
    }
}