<?php

namespace Tests\Feature;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Testing authorize endpoint authentication
     * @return void
     */
    public function testGettingTokenFromDiscordAndPassedItToAuthRoute(): void
    {
        $this->withoutExceptionHandling();
        $client = new Client([
            'base_uri' => 'https://discordapp.com/api/v6/'
        ]);
        $discordResponse = $client->request('POST', 'oauth2/token', [
            'auth' =>  [env('DISCORD_KEY'), env('DISCORD_SECRET')],
            'headers' => [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'query' => [
                'grant_type' => 'client_credentials',
                'scope' => ['identify', 'connections', 'email', 'guilds']
            ]
        ]);

        $discord_access_token = json_decode($discordResponse->getBody(), true)['access_token'];
        $this->assertIsString($discord_access_token);
        $this->assertNotEmpty($discord_access_token);

        $response = $this->postJson(route('auth.authorize'), [
            'discord_access_token' => $discord_access_token
        ]);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'discord_id' => true,
                'token' => [
                    'access_token' => true,
                    'refresh_token' => true,
                    'expires_at' => true
                ]
            ]
        ]);

        $discord_id = $response->json('data.discord_id');
        $access_token = $response->json('data.token.access_token');
        $refresh_token = $response->json('data.token.refresh_token');
        $expires_at = Carbon::parse($response->json('data.token.expires_at'));
        $this->assertNotEmpty($refresh_token);
        $this->assertNotEmpty($access_token);
        $this->assertNotEmpty($discord_id);

        $refreshTokenResponse = $this->postJson(route('auth.token.refresh'), [
            'discord_id' => $discord_id,
            'access_token' => $access_token,
            'refresh_token' => $refresh_token
        ]);

        $refreshTokenResponse->assertStatus(200)->assertJson([
            'data' => [
                'access_token' => true,
                'refresh_token' => true,
            ]
        ]);
        $new_access_token = $refreshTokenResponse->json('data.access_token');
        $new_refresh_token = $refreshTokenResponse->json('data.refresh_token');
        $new_expires_at = Carbon::parse($refreshTokenResponse->json('data.expires_at'));
        $this->assertNotEmpty($new_access_token);
        $this->assertNotEmpty($new_refresh_token);
        $this->assertNotEquals($new_access_token, $access_token);
        $this->assertNotEquals($new_refresh_token, $refresh_token);
        $this->assertTrue($new_expires_at->greaterThan($expires_at));
    }
}
