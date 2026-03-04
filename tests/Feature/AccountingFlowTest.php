<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_auto_creates_default_first_generation_member(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();

        $this->assertDatabaseHas('members', [
            'name' => '第一代',
            'sponsor_id' => null,
        ]);
    }

    public function test_first_generation_recruitment_has_no_bonus_and_second_generation_starts_bonus(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard');
        $first = Member::where('name', '第一代')->firstOrFail();

        $this->actingAs($user)->post('/members/recruit', [
            'recruiter_id' => $first->id,
            'new_member_name' => '第二代',
        ])->assertRedirect();

        $second = Member::where('name', '第二代')->firstOrFail();

        $this->actingAs($user)->post('/members/recruit', [
            'recruiter_id' => $second->id,
            'new_member_name' => '第三代',
        ])->assertRedirect();

        $dashboard = $this->actingAs($user)->get('/dashboard');
        $dashboard->assertOk();
        $dashboard->assertSee('72,000', false);
        $dashboard->assertSee('15,000', false);
        $dashboard->assertSee('57,000', false);
        $dashboard->assertSee('無', false);
        $dashboard->assertSee('第一代：5,000', false);
        $dashboard->assertSee('第二代：10,000', false);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/');
    }
}
