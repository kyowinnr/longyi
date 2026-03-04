<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_auto_creates_default_company_member(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();

        $this->assertDatabaseHas('members', [
            'name' => '公司',
            'sponsor_id' => null,
        ]);
    }

    public function test_company_recruit_first_generation_has_no_bonus_then_first_generation_recruit_starts_bonus(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard');
        $company = Member::where('name', '公司')->firstOrFail();

        // 公司 -> 第一代（不分獎金）
        $this->actingAs($user)->post('/members/recruit', [
            'recruiter_id' => $company->id,
            'new_member_name' => '第一代',
        ])->assertRedirect();

        $firstGeneration = Member::where('name', '第一代')->firstOrFail();

        // 第一代 -> 第二代（開始分獎金）
        $this->actingAs($user)->post('/members/recruit', [
            'recruiter_id' => $firstGeneration->id,
            'new_member_name' => '第二代',
        ])->assertRedirect();

        $dashboard = $this->actingAs($user)->get('/dashboard');
        $dashboard->assertOk();
        $dashboard->assertSee('72,000', false);
        $dashboard->assertSee('15,000', false);
        $dashboard->assertSee('57,000', false);
        $dashboard->assertSee('無', false);
        $dashboard->assertSee('第一代：15,000', false);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/');
    }
}
