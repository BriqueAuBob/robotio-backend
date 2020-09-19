<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\Discord;
use \DB;
use Config;

class UpdateColors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:colors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update colors in role table';

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
        $role_table = Config( "gca.discord.roles" );
        $roles = Discord::getGuildRoles()->toArray();
        $colors = [];

        foreach( $roles as $key => $value )
        {
            $color = $value->color;
            if(strlen($color) > 6) {
                $color = dechex( $color );
            }

            $colors[$value->id] = $color;
        }

        foreach( $role_table as $key => $value )
        {
            DB::table( "roles" )->where("name", $value)->update(["color" => $colors[$key] !== 0 ? "#".$colors[$key] : "#B9BBBE"]);
        }
    }
}
