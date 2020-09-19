<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{

    protected $user;

    public function setUp(): void
    {
        // first include all the normal setUp operations
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * Test if users list isn't accessible if the user doesn't provide token on header.
     * @return void
     */
    public function testCannotGetUsersWithNoTokenProvided(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', route('users.index'));

        $response->assertUnauthorized()->assertJson([
            'message' => __('error.401.message'),
        ]);
    }

    /**
     * Testing users response structure and status when user provide a valid token
     * @return void
     */
    public function testGetUsersWithTokenProvided(): void
    {
        $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->user->personal_access_token
        ])->get(route('users.index'))->assertJsonStructure([
            'data' => [
                '*' => [
                    'discord_id',
                    'tag',
                    'is_banned',
                    'is_discord_verified',
                    'experience_amount',
                    'last_activity',
                    'created_at',
                ]
            ],
            'count',
            'meta' => [
                'current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total',
            ],
            'links' => [
                'first', 'last', 'prev', 'next',
            ],
        ])->assertStatus(200);
    }

    /**
     * Testing the fields return for each users when user does not have the admin permission.
     * @return void
     */
    public function testGetUsersByEmailWithoutPermissions(): void
    {
        $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->user->personal_access_token
        ])->get(route('users.index', ['email' => $this->user->email]))
            ->assertJsonCount(min(config('eloquentfilter.paginate_limit'), User::count()), 'data')
            ->assertJsonMissing([
                'data' => [
                    '*' => [
                        'deleted_at' => 'MISSING',
                        'email' => 'MISSING',
                    ]
                ]
            ])
            ->assertStatus(200);
    }

    /**
     * Testing if user with permission can access to special field and use custom filters.
     * @return void
     */
    public function testGetUsersByEmailWithPermissions(): void
    {
        $this->user->attachPermission('use-admin-users-filter');
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->user->personal_access_token
        ])->get(route('users.index', ['email' => $this->user->email]));

        $response->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'discord_id',
                        'tag',
                        'is_banned',
                        'is_discord_verified',
                        'experience_amount',
                        'last_activity',
                        'created_at',
                        'deleted_at',
                        'email'
                    ]
                ],
                'count',
                'meta' => [
                    'current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total',
                ],
                'links' => [
                    'first', 'last', 'prev', 'next',
                ],
            ])->assertStatus(200);

        // Reset permissions
        $this->user->permissions()->sync([]);
    }
}
