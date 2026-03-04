<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_seed_example_and_view_expected_totals(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/seed-example');
        $response->assertRedirect();

        $dashboard = $this->actingAs($user)->get('/dashboard');
        $dashboard->assertOk();
        $dashboard->assertSee('108,000', false);
        $dashboard->assertSee('45,000', false);
        $dashboard->assertSee('63,000', false);
        $dashboard->assertSee('第一代：15,000', false);
        $dashboard->assertSee('第一代：5,000', false);
        $dashboard->assertSee('第二代：10,000', false);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/');
    }
}
