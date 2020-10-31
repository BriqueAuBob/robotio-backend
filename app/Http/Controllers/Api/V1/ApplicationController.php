<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

use GuzzleHttp\Command\Exception\CommandClientException;

use App\Models\Application;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

use App\Helpers\Discord;
use RestCord\DiscordClient;

use App\Http\Resources\Applications\ApplicationCollection;
use App\Http\Resources\Applications\ApplicationResource;

use App\Http\Requests\ApplicationRequest;

class ApplicationController extends Controller
{
    public function index()
    {
        $id = Auth::user()->id;
        $applications = Application::where("owner_id", $id)
            ->get();

        return new ApplicationCollection($applications);
    }

    public function get(string $id)
    {
        return new ApplicationResource(resolve("application"));
    }

    public function store()
    {
        $guild_id = $request->input("guild_id");
        $bot_token = $request->input("bot_token");

        $guild = collect(Auth::user()->guilds)->where("id", $guild_id);

        if (!isset($bot_token) or !isset($guild_id)) {
            $value = isset($bot_token) ? "guild_id" : "bot_token";
            return response()->json([
                "status" => 404,
                "message" => "missing_{$value}"
            ], 400);
        }

        if($guild->isEmpty()) {
            return response()->json([
                "status" => 404,
                "message" => "unexistant_server"
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
                "prefix" => "/",
            ]);

            return [
                "created" => $created->_id,
                "id" => $bot->id,
                "name" => $bot->username,
                "discriminator" => $bot->discriminator,
                "avatar" => "https://cdn.discordapp.com/avatars/{$bot->id}/{$bot->avatar}.png?size=128",
                "prefix" => "/",
            ];
        } catch (CommandClientException $e) {
            return response()->json([
                "status" => 404,
                "message" => __("ro-bot.404.bot_doesnt_exists")
            ], 404);
        }
    }

    public function update(string $id, ApplicationRequest $request)
    {
        $application = resolve("application");

        if($request->input("bot_token")) {
            try {
                $discord = new DiscordClient([
                    "token" => $request->input("bot_token"),
                    "tokenType" => "Bot",
                ]);
                $bot = $discord->user->getCurrentUser();
    
                $application->bot_token = $request->input("bot_token");
                $application->name = $bot->username;
                $application->discriminator = $bot->discriminator;
                $application->avatar = "https://cdn.discordapp.com/avatars/{$bot->id}/{$bot->avatar}.png?size=128";
                $application->save();
            } catch (CommandClientException $e) {
                return response()->json([
                    "status" => 404,
                    "message" => __("ro-bot.404.bot_doesnt_exists")
                ], 404);
            }
        }

        $application->update($request->validated());
        return [
            "notification" => [
                "type" => "success",
                "layout" => "notification",
                "title" => __("ro-bot.success"),
                "content" => __("ro-bot.404.success_edit_bot")
            ]
        ];
    }

    private function array_diff_recursive($array, $diff)
    {
        $new = [];
        foreach ($array as $mKey => $mValue) {
            if (array_key_exists($mKey, $diff)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->array_diff_recursive($mValue, $diff[$mKey]);
                    if (count($aRecursiveDiff)) { 
                        $new[$mKey] = $aRecursiveDiff; 
                    }
                } else {
                    if ($mValue != $diff[$mKey]) {
                        $new[$mKey] = $mValue;
                    }
                }
            } else {
                $new[$mKey] = $mValue;
            }
        }
        return $new;
    }

    public function sync(string $id) 
    {
        $user = Auth::user();
        $application = Application::find($id);
        $guild = collect($user->guilds)->firstWhere("id", $application->guild_id);

        if(isset($application->errors) && count($application->errors) > 0) {
            return response()->json([
                "status" => 401,
                "message" => __("ro-bot.401.impossible_resync")
            ], 401);
        }

        if(!$guild) {
            return response()->json([
                "status" => 404,
                "message" => __("error.404.message")
            ], 404);
        }

        // if(!$guild["is_owner"]) {
        //     return response()->json([
        //         "status" => 401,
        //         "message" => "Ce serveur ne t'appartiens pas!"
        //     ], 401);
        // }

        $botGuilds = Discord::getDiscordBot()->user->getCurrentUserGuilds([]);
        $botOnServer = collect($botGuilds)->firstWhere("id", "=", $guild["id"]);
        if ($botOnServer === null) {
            return response()->json([
                "status" => 401,
                "message" => __("ro-bot.404.bot_not_found_in_guild")
            ], 401);
        }

        $emojis = collect(Discord::getDiscordBot()->emoji->listGuildEmojis(["guild.id" => (integer)$guild["id"]]))->map(function ($emoji) {
            $mime = $emoji->animated ? "gif" : "png";
            return [
                "id"        => (string)$emoji->id,
                "url"       => "https://cdn.discordapp.com/emojis/{$emoji->id}.{$mime}",
                "name"      => $emoji->name,
            ];
        });
        $channels = collect(Discord::getDiscordBot()->guild->getGuildChannels(["guild.id" => (integer)$guild["id"]]))->filter(function ($channel, $key) {
            return $channel->type == 0;
        })->map(function ($channel) {
            return [
                "id"        => (string)$channel->id,
                "name"      => $channel->name,
            ];
        });
        $roles = collect(Discord::getDiscordBot()->guild->getGuildRoles(["guild.id" => (integer)$guild["id"]]))->map(function( $role ) {
            return [
                "id"        => (string)$role->id,
                "name"      => $role->name,
                "color"     => "#".dechex($role->color)
            ];
        });

        $old = [
            "emojis"        => $application["emojis"],
            "channels"      => $application["channels"],
            "roles"         => $application["roles"]
        ];

        $application["emojis"] = array_values($emojis->toArray());
        $application["channels"] = array_values($channels->toArray());
        $application["roles"] = array_values($roles->toArray());

        $application->push();

        $differences = [
            "channels"  => $this->array_diff_recursive($old["channels"], $application["channels"]),
            "emojis"    => $this->array_diff_recursive($old["emojis"], $application["emojis"]),
            "roles"     => $this->array_diff_recursive($old["roles"], $application["roles"]),
        ];

        $errors = [];
        foreach($application["modules"] as $key => $module)
        {
            $mod = Module::where("_id", $module["id"])->first();

            $errors[] = [
                "type" => $mod->type,
                "channels" => null,
                "roles" => null
            ];
            $id = array_key_last($errors);

            $count = 0;
            if(isset($mod->channels)) {
                foreach($differences["channels"] as $k => $diff) {
                    foreach($mod->channels as $k => $channel_id) {
                        if(isset($diff["id"]) && $channel_id == $diff["id"]) {
                            $errors[$id]["channels"][] = (object)[
                                "id" => $diff["id"],
                                "color" => $diff["color"],
                            ];
                            $count += 1;
                        }
                    }
                }
            }
            if(isset($mod->roles)) {
                foreach($differences["roles"] as $k => $diff) {
                    foreach($mod->roles as $k => $role_id) {
                        if(isset($diff["id"]) && $role_id == $diff["id"]) {
                            $errors[$id]["roles"][] = (object)[
                                "id" => $diff["id"],
                                "color" => $diff["color"],
                                "name" => $diff["name"]
                            ];
                            $count += 1;
                        }
                    }
                }
            }
            if($count == 0) {
                unset($errors[$id]);
            } else {
                $errors[$id] = (object)$errors[$id];
            }
        }

        $application->errors = $errors;
        $application->push();

        return [
            "errors" => $errors
        ];
    }
}
