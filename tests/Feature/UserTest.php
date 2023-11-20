<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class UserTest extends TestCase
{
    private $userSuccess1 = ['name' => 'user1', 'email' => 'user1@example.com', 'password' => '!qdqw21WE', 'password_confirmation' => '!qdqw21WE'];
    private $userSuccess2 = ['name' => 'user2', 'email' => 'user2@example.com', 'password' => '!qdqw21WE', 'password_confirmation' => '!qdqw21WE'];

    private $userPasswordMismatch = ['name' => 'user1', 'email' => 'user1@example.com', 'password' => '!qdqw21WE!', 'password_confirmation' => '!qdqw21WE'];
    private $userPasswordSmall = ['name' => 'user1', 'email' => 'user1@example.com', 'password' => '123', 'password_confirmation' => '123'];

    // From Seeder:
    private $user0 = ['email' => 'worker0@example.com', 'password' => 'worker0@example.com'];
    private $user1 = ['email' => 'worker1@example.com', 'password' => 'worker1@example.com'];
    private $user2 = ['email' => 'worker2@example.com', 'password' => 'worker2@example.com'];

    private $admin = ['email' => 'admin@example.com', 'password' => 'admin@example.com'];
    private $userX = ['email' => 'workerX0@example.com', 'password' => 'workerX0@example.com'];

    protected function setUp(): void
    {
        parent::setUp();

    }

    /**
     * A basic test example.
     *
     * @return void
     *
     * @test
     */
    public function home_page()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Register
     * Test Register functionality: Success
     *
     * @return void
     *
     * @test
     */
    public function register_successful()
    {

        $response = $this->postJson('/api/register', $this->userSuccess1);
        $response->assertSuccessful();
        $lastAddedUser = User::orderBy('id', 'DESC')->first();
        assertEquals($this->userSuccess1['email'], $lastAddedUser->email);
        assertEquals($this->userSuccess1['name'], $lastAddedUser->name);

    }

    /**
     * Store
     * Test Store functionality: Failure: password mismatch
     *
     * @return void
     *
     * @test
     */
    public function store_failure_password_mismatch()
    {
        $response = $this->postJson('/api/register', $this->userPasswordMismatch);
        $response
            ->assertStatus(422)
            ->assertJsonPath('errors.password.0', 'The password confirmation does not match.');
    }

    /**
     * Store
     * Test Store functionality: Failure: password small
     *
     * @return void
     *
     * @test
     */
    public function store_failure_password_small()
    {
        $response = $this->postJson('/api/register', $this->userPasswordSmall);
        $response
            ->assertStatus(422)
            ->assertJsonPath('errors.password.0', 'The password must be at least 8 characters.');
    }

    /**
     * login
     * Test Login functionality: Success
     *
     * @return void
     *
     * @test
     */
    public function login_success()
    {
        $response = $this->postJson('/api/login', $this->user0);
        $response->assertSuccessful();
    }

    /**
     * login
     * Test Login functionality: Success
     *
     * @return void
     *
     * @test
     */
    public function login_failed()
    {
        $response = $this->postJson('/api/login', $this->userX);
        $response->assertStatus(401);
    }

    /**
     * admin_login
     *
     * @return $token
     */
    public function admin_login()
    {
        $response = $this->postJson('/api/login', $this->admin);
        $response->assertSuccessful();
        $token = $response->getData()->token;

        return $token;
    }

    /**
     * admin_login
     *
     * @return $token
     */
    public function get_user($token, $user)
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/users/search?email=' . $user['email']);
        $response->assertSuccessful();
        $user = $response->getData()[0];

        return $user;
    }

    /**
     * Update
     * Test Update functionality: Success
     *
     * @return void
     *
     * @test
     */
    public function complete_scenario_update_success()
    {
        $token = $this->admin_login();

        $user = $this->get_user($token, $this->user1);
        $id = $user->id;
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->putJson("/api/users/{$id}", ['name' => 'Update Name']);
        $response->assertSuccessful();

        $updatedUser = $this->get_user($token, $this->user1);
        assertEquals($updatedUser->name, 'Update Name');

    }

    /**
     * Make admin
     * Test Make Admin functionality: Success
     *
     * @return void
     *
     * @test
     */
    public function make_admin_success()
    {
        $token = $this->admin_login();

        // Get user1 from the database.
        $user = $this->get_user($token, $this->user1);

        // Delete user1 from the database.

        $id = $user->id;
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson('/api/users/make-admin', ['user_id' => $id]);
        $response->assertSuccessful();

        // Get user1 from the database (SHOULDN't FIND IT).
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/users/search?email=' . $this->user1['email']);
        $response->assertSuccessful();
        $user = $response->getData()[0];
        assertEquals($user->is_admin, 1);

    }

    /**
     * delete
     * Test Delete functionality: Success
     *
     * @return void
     *
     * @test
     */
    public function delete_success()
    {
        $token = $this->admin_login();

        // Get user1 from the database.
        $user = $this->get_user($token, $this->user2);

        // Delete user1 from the database.

        $id = $user->id;
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->deleteJson("/api/users/{$id}");
        $response->assertSuccessful();

        // Get user1 from the database (SHOULDN't FIND IT).
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/users/search?email=' . $this->user2['email']);
        $response->assertSuccessful();
        $users = $response->getData();
        assertEquals(0, count($users));

    }

    /**
     * Store
     * Test Store functionality: Success
     *
     * @return void
     *
     * @test
     */
    public function store_successful()
    {
        $token = $this->admin_login();
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson('/api/users', $this->userSuccess2);

        $response->assertSuccessful();
        $lastAddedUser = User::orderBy('id', 'DESC')->first();
        assertEquals($this->userSuccess2['email'], $lastAddedUser->email);
        assertEquals($this->userSuccess2['name'], $lastAddedUser->name);

    }
}
