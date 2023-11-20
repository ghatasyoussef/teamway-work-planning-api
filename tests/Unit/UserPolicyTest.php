<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\UserPolicy;
use PHPUnit\Framework\TestCase;

class UserPolicyTest extends TestCase
{
    private $policy;
    private $user1;
    private $admin1;
    private $user2;

    protected function setUp(): void
    {
        $this->policy = new UserPolicy;
        $this->user1 = new User;
        $this->user1->id = 10;
        $this->user1->is_admin = false;
        $this->admin1 = new User;
        $this->admin1->id = 150;
        $this->admin1->is_admin = true;
        $this->user2 = new User;
        $this->user2->id = 250;
        $this->user2->is_admin = false;

    }

    // ** View** //
    /**
     * @test
     */
    public function user_view_policy_same_user()
    {
        $this->assertTrue($this->policy->view($this->user1, $this->user1));
    }

    /**
     * @test
     */
    public function user_view_policy_admin()
    {
        $this->assertTrue($this->policy->view($this->admin1, $this->user1));
    }

    /**
     * @test
     */
    public function user_view_policy_other_user()
    {
        $this->assertFalse($this->policy->view($this->user1, $this->user2));
    }

    /**
     * @test
     */
    public function user_view_policy_other_user_admin()
    {
        $this->assertFalse($this->policy->view($this->user1, $this->admin1));
    }

    // ** Update** //
    /**
     * @test
     */
    public function user_update_policy_same_user()
    {
        $this->assertTrue($this->policy->update($this->user1, $this->user1));
    }

    /**
     * @test
     */
    public function user_update_policy_admin()
    {
        $this->assertTrue($this->policy->update($this->admin1, $this->user1));
    }

    /**
     * @test
     */
    public function user_update_policy_other_user()
    {
        $this->assertFalse($this->policy->update($this->user1, $this->user2));
    }

    /**
     * @test
     */
    public function user_update_policy_other_user_admin()
    {
        $this->assertFalse($this->policy->update($this->user1, $this->admin1));
    }

    // ** Make Admin**

    /**
     * @test
     */
    public function user_makeAdmin_policy_same_user()
    {
        $this->assertFalse($this->policy->makeAdmin($this->user1, $this->user1));
    }

    /**
     * @test
     */
    public function user_makeAdmin_policy_admin()
    {
        $this->assertTrue($this->policy->makeAdmin($this->admin1, $this->user1));
    }

    /**
     * @test
     */
    public function user_makeAdmin_policy_other_user()
    {
        $this->assertFalse($this->policy->makeAdmin($this->user1, $this->user2));
    }

    // ** Delete**

    /**
     * @test
     */
    public function user_delete_policy_same_user()
    {
        $this->assertFalse($this->policy->delete($this->user1, $this->user1));
    }

    /**
     * @test
     */
    public function user_delete_policy_admin()
    {
        $this->assertTrue($this->policy->delete($this->admin1, $this->user1));
    }

    /**
     * @test
     */
    public function user_delete_policy_other_user()
    {
        $this->assertFalse($this->policy->delete($this->user1, $this->user2));
    }
}
