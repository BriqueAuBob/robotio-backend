<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Str;
use Carbon\Carbon;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apikey:generate {userid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate api key';

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
        $userId = $this->argument('userid');

        $token = \App\Models\Token::create([
            'discord_id' => $userId,
            'is_revokable' => false,
            'is_personal' => false,
            'access_token' => Str::random(110),
        ]);

        $token->expires_at = Carbon::now()->addDays( 999999999999999 );
        $token->save();
        \Log::info( "Token: Bearer ".$token->access_token );
    }
}
