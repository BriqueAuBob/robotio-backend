<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Helpers\Discord;
use Illuminate\Console\Command;

class Unban_Rank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unban:rank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Discord::getDiscordBot()->guild->addGuildMemberRole([
            'guild.id' => 223070469148901376,
            'user.id' => 307531336388968458,
            'role.id' => 674191557175541771
        ]);
        
        
        // Discord::getDiscordBot()->channel->createMessage([
        //     'channel.id' => 643448496606937109,
        //     'content' => '<@452475691410128906> ton tutoriel a été refusé bg...',
        // ]);

        // $type = $this->argument('type');

        // if($type == 1) {
        //     Discord::getDiscordBot()->guild->removeGuildBan([
        //         'guild.id' => (int)config("services.discord.guild_id"),
        //         'user.id' => 307531336388968458,
        //     ]);

        //     return;
        // }
        // elseif ($type == 2) {
        //     Discord::getDiscordBot()->guild->addGuildMemberRole([
        //         'guild.id' => (int)config("services.discord.guild_id"),
        //         'user.id' => 307531336388968458,
        //         'role.id' => 645343766588293120
        //     ]);
        // } elseif ($type == 3) {
        //     User::create( [
        //         "discord_id" => 307531336388968458,
        //         "username" => "brique",
        //         "tag" => "0001",
        //     ] );
        // }
    }
}
