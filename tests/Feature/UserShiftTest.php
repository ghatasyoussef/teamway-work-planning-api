<?php

namespace Tests\Feature;

use App\Models\UserShift;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class UserShiftTest extends TestCase
{
    // From Seeder:
    private $user0 = ['email' => 'worker0@example.com', 'password' => 'worker0@example.com'];
    private $user1 = ['email' => 'worker1@example.com', 'password' => 'worker1@example.com'];
    private $admin = ['email' => 'admin@example.com', 'password' => 'admin@example.com'];

    protected function setUp(): void
    {
        parent::setUp();
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
     * get_user1
     *
     * @return $token
     */
    public function get_user1($token)
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/users/search?email=' . $this->user1['email']);
        $response->assertSuccessful();
        $user = $response->getData()[0];

        return $user;
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
        $worker = $this->get_user1($token);
        $shift = ['shift_number' => '1', 'worker_id' => "{$worker->id}", 'day' => '2025-10-10'];
        $response = $this->postJson('/api/shifts', $shift);
        $response->assertSuccessful();
        $lastAddedUser = UserShift::orderBy('id', 'DESC')->first();
        assertEquals($shift['worker_id'], $lastAddedUser->worker_id);
        assertEquals($shift['shift_number'], $lastAddedUser->shift_number);
        assertEquals($shift['day'], $lastAddedUser->day);
    }

    /**
     * Complete Scenario: Update
     * Test Update functionality: Success
     *
     * @return void
     *
     * @test
     */
    public function complete_scenario_update_success()
    {
        $token = $this->admin_login();
        $day = '2024-12-12';
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->putJson('/api/shifts/1', ['day' => $day]);

        $response->assertSuccessful();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson("/api/shifts/search?day={$day}");
        $response
            ->assertSuccessful()
            ->assertJsonPath('data.0.day', $day);

    }

    /**
     * Search
     * Test Search functionality: Success
     *
     * @return void
     *
     * @test
     */
    public function search_success()
    {
        $token = $this->admin_login();
        $day = '2024-12-12';

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson("/api/shifts/search?day={$day}");
        $response
            ->assertSuccessful()
            ->assertJsonPath('data.0.day', $day);

        $shift = $response->getData()->data[0];
        // print_r($shift);
        // die();
        $email = $shift->user->email;
        $name = $shift->user->name;

        $url = "/api/shifts/search?worker_id={$shift->worker_id}&email={$email}&name={$name}&day={$day}&shift_number={$shift->shift_number}";
        // print_r($url);
        // die();
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson("/api/shifts/search?worker_id={$shift->worker_id}&email={$email}&name={$name}&day={$day}&shift_number={$shift->shift_number}");
        $response->assertSuccessful();

    }
}
