<?php

namespace App\Http\Controllers\Api\V1\Modules;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use RestCord\DiscordClient;

class AnnouncementController extends Controller
{
    public function send(Request $request)
    {
        $application = resolve("application");
        $channel_id = $request->input("channel_id");
        $content = $request->input("content");
        $embed = $request->input("embed");
        $reactions = $request->input("reactions");

        $bot = new DiscordClient([
            "token" => $application->bot_token,
            "tokenType" => "Bot",
        ]);

        $message = $bot->channel->createMessage([
            "channel.id" => (integer)$channel_id,
            "content" => $content,
            "embed" => $embed,
        ]);
        
        if($reactions && count($reactions) > 0) {
            foreach($reactions as $key => $reaction) {
                $bot->channel->createReaction([
                    "channel.id" => (integer)$channel_id,
                    "message.id" => (integer)$message["id"],
                    "emoji" => $reaction
                ]);
                sleep(1);
            }
        }
    }
}