<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\Discord;
use \DB;
use Carbon\Carbon;

class SupportersCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supporters:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add & remove from DB the supporters who have the rank on the discord.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $supporters = DB::table('supporters')->get();

        $users = [];
        $lastfetched = "0";
        $count_fetched = 1000;

        while ($count_fetched > 0) {
            $req = Discord::getDiscordBot()->guild->listGuildMembers([
                'guild.id' => (int)config("services.discord.guild_id"),
                'limit' => 1000,
                'after' => $lastfetched === "0" ? null : $lastfetched,
            ]);
            $count_fetched = count($req);

            if ($count_fetched === 0) break;

            $lastfetched = $req[$count_fetched-1]->user->id;
            $users = array_merge( $users, $req );
        }
        
        $count = [
            "new" => 0,
            "removed" => 0,
            "waiting" => 0,
        ];
        $waiting_users = "";
        $new_users = [];

        foreach( $users as $user )
        {
            if(in_array("593052082118983691", $user->roles) || in_array("591954692095868931", $user->roles) || in_array("669171847379681291", $user->roles))
            {
                $new_users[] = $user->user->id;
                $supporter = DB::table("supporters")->where("user_id", $user->user->id)->first();

                if ($supporter) {
                    continue;
                }

                $db_user = DB::table("users")->where("discord_id", $user->user->id)->first();
                if ($db_user) {
                    $count["new"] = $count["new"] + 1;
                    \Log::info( $user->user->id.' | '.(( in_array("669171847379681291", $user->roles) 
                        ? "Partner" : in_array("591954692095868931", $user->roles) ) 
                        ? "Michel" : "Basic" ) );
                    
                    $create = DB::table("supporters")->insert([
                        "user_id" => $user->user->id,
                        "type" => ( in_array("669171847379681291", $user->roles) ) ? "Partner" : 
                            ( ( in_array("591954692095868931", $user->roles) ) ? "Michel" : "Basic" )
                    ]);
                } else {
                    $waiting_users = $waiting_users !== "" ? $waiting_users.', '.$user->user->username : $user->user->username;
                    $count["waiting"] = $count["waiting"] + 1;
                }
            }
        }

        $supporters = DB::table("supporters")->get();
        foreach($supporters as $supporter)
        {
            if(!in_array($supporter->user_id, $new_users))
            {
                $count["removed"] = $count["removed"] + 1;
                DB::table('users')->where('discord_id', $supporter->user_id)->update([
                    'slug' => null,
                    'headline' => null
                ]);
                DB::table('supporters')->where('user_id', $supporter->user_id)->delete();
            }
        }

        Discord::getDiscordBot()->channel->createMessage([
            'channel.id' => 652151351287087104,
            'embed' => [
                "title" => "Les Supporters",
                "timestamp" => Carbon::now(),
                "author" => [
                    "name" => "[Supporters] » ⏰ Daily Check",
                    "icon_url" => "https://images-ext-1.discordapp.net/external/Dz5gfqQHC1SZKACxCWYcF1iPekolK-kwuE2uKIbepRE/%3Fsize%3D2048/https/cdn.discordapp.com/avatars/614510476256084165/4e5e0cdd9df766bc558c05c71484692c.png"
                ],
                "fields" => [
                    [
                        "name" => "Nombre de soutiens",
                        "value" => count( $supporters ) - $count["removed"]
                    ],
                    [
                        "name" => "Nouveaux soutiens 😏",
                        "value" => $count["new"]
                    ],
                    [
                        "name" => "Soutient plus 😕",
                        "value" => $count["removed"]
                    ],
                    [
                        "name" => "En attente de création de compte ⏰",
                        "value" => $count["waiting"].' | '.$waiting_users
                    ]
                ]
            ]
        ]);
    }
}
