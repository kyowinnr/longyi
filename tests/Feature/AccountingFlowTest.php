<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\RecruitmentEvent;
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

    public function test_recruitment_stores_profile_with_manual_expiry_date_and_note(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard');
        $company = Member::where('name', '公司')->firstOrFail();

        $this->actingAs($user)->post('/members/recruit', [
            'recruiter_id' => $company->id,
            'new_member_name' => '第一代',
            'id_number' => 'A123456789',
            'birthday' => '1990-01-01',
            'phone' => '0911222333',
            'joined_at' => '2026-01-15',
            'expires_at' => '2026-12-31',
            'note' => '首次招募備註',
        ])->assertRedirect();

        $this->assertDatabaseHas('members', [
            'name' => '第一代',
            'id_number' => 'A123456789',
            'expires_at' => '2026-12-31 00:00:00',
        ]);

        $this->assertDatabaseHas('recruitment_events', [
            'note' => '首次招募備註',
        ]);
    }

    public function test_can_update_member_profile(): void
    {
        $user = User::factory()->create();

        $member = Member::create([
            'name' => '第一代',
            'id_number' => 'B123456789',
            'birthday' => '1991-01-01',
            'phone' => '0900000001',
            'joined_at' => '2026-01-01',
            'expires_at' => '2026-12-31',
            'sponsor_id' => null,
        ]);

        $this->actingAs($user)->put(route('members.update', $member), [
            'name' => '第一代-更新',
            'id_number' => 'B223456789',
            'birthday' => '1991-02-02',
            'phone' => '0900009999',
            'joined_at' => '2026-02-01',
            'expires_at' => '2027-01-31',
        ])->assertRedirect();

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'name' => '第一代-更新',
            'id_number' => 'B223456789',
            'phone' => '0900009999',
        ]);
    }

    public function test_can_update_event_note(): void
    {
        $user = User::factory()->create();

        $company = Member::create([
            'name' => '公司',
            'id_number' => null,
            'birthday' => null,
            'phone' => null,
            'joined_at' => null,
            'expires_at' => null,
            'sponsor_id' => null,
        ]);

        $member = Member::create([
            'name' => '第一代',
            'id_number' => 'C123456789',
            'birthday' => '1992-01-01',
            'phone' => '0900000002',
            'joined_at' => '2026-01-01',
            'expires_at' => '2026-12-31',
            'sponsor_id' => $company->id,
        ]);

        $event = RecruitmentEvent::create([
            'recruiter_id' => $company->id,
            'new_member_id' => $member->id,
            'company_income' => 36000,
            'bonus_payout_total' => 0,
            'note' => null,
        ]);

        $this->actingAs($user)->put(route('events.note.update', $event), [
            'note' => '更新後備註',
        ])->assertRedirect();

        $this->assertDatabaseHas('recruitment_events', [
            'id' => $event->id,
            'note' => '更新後備註',
        ]);
    }

    public function test_network_page_shows_upline_downline_and_second_downline(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard');
        $company = Member::where('name', '公司')->firstOrFail();

        $first = Member::create([
            'name' => '第一代',
            'id_number' => 'D123456789',
            'birthday' => '1991-01-01',
            'phone' => '0900000001',
            'joined_at' => now(),
            'expires_at' => now()->addYear(),
            'sponsor_id' => $company->id,
        ]);

        $second = Member::create([
            'name' => '第二代',
            'id_number' => 'E123456789',
            'birthday' => '1992-01-01',
            'phone' => '0900000002',
            'joined_at' => now(),
            'expires_at' => now()->addYear(),
            'sponsor_id' => $first->id,
        ]);

        Member::create([
            'name' => '第三代',
            'id_number' => 'F123456789',
            'birthday' => '1993-01-01',
            'phone' => '0900000003',
            'joined_at' => now(),
            'expires_at' => now()->addYear(),
            'sponsor_id' => $second->id,
        ]);

        $res = $this->actingAs($user)->get('/members/network');
        $res->assertOk();
        $res->assertSee('第一代');
        $res->assertSee('第二代');
        $res->assertSee('第三代');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/');
    }
}
