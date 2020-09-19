<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\Discord;
use \DB;
use Carbon\Carbon;

class CheckBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check birthday all day';

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
        $birthday = DB::table('users')->where("birthday", Carbon::today())->get();
        $oldbirth = DB::table('users')->where("birthday", Carbon::yesterday())->get();
        $fields = [];

        foreach( $oldbirth as $key => $user )
        {
            Discord::getDiscordBot()->guild->removeGuildMemberRole([
                'guild.id' => (int)config("services.discord.guild_id"),
                'user.id' => $user->discord_id,
                'role.id' => 711988691073105970
            ]);
        }
        
        foreach( $birthday as $key => $user )
        {
            Discord::getDiscordBot()->guild->addGuildMemberRole([
                'guild.id' => (int)config("services.discord.guild_id"),
                'user.id' => $user->discord_id,
                'role.id' => 711988691073105970
            ]);

            $fields[] = [
                'name' => $user->username.$user->tag,
                'value' => "http://g-ca.fr/profile/{$user->discord_id}"
            ];
        }

        Discord::getDiscordBot()->channel->createMessage([
            'channel.id' => 652151351287087104,
            'embed' => [
                "title" => "Anniversaires",
                "timestamp" => Carbon::now(),
                "author" => [
                    "name" => "[Birthday] » 🎂 Anniversaires",
                    "icon_url" => "https://images-ext-1.discordapp.net/external/Dz5gfqQHC1SZKACxCWYcF1iPekolK-kwuE2uKIbepRE/%3Fsize%3D2048/https/cdn.discordapp.com/avatars/614510476256084165/4e5e0cdd9df766bc558c05c71484692c.png"
                ],
                "fields" => $fields
            ]
        ]);
    }
}
