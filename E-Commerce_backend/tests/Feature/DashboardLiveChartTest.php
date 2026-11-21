<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardLiveChartTest extends TestCase
{
    use RefreshDatabase;

    private function orderAt(User $user, string $when, float $amount): void
    {
        $order = Order::create(['user_id' => $user->id, 'total_amount' => $amount, 'status' => 'pending', 'phone' => '012345678', 'shipping_address' => 'Phnom Penh']);
        $order->forceFill(['created_at' => Carbon::parse($when)])->save();
    }

    public function test_live_chart_buckets_orders_and_compares_with_the_previous_window(): void
    {
        Carbon::setTestNow('2026-11-21 15:30:00');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->orderAt($admin, '2026-11-21 09:00:00', 20);   // today
        $this->orderAt($admin, '2026-11-21 14:10:00', 30);   // today
        $this->orderAt($admin, '2026-11-18 10:00:00', 15.5); // 3 days ago
        $this->orderAt($admin, '2026-11-10 10:00:00', 40);   // previous 7-day window

        $data = $this->actingAs($admin)
            ->getJson(route('admin.dashboard.live', ['range' => '7d']))
            ->assertOk()
            ->json();

        $this->assertCount(7, $data['labels']);
        $this->assertSame([0, 0, 0, 1, 0, 0, 2], $data['orders']);
        $this->assertEquals(50, end($data['revenue']));
        $this->assertSame(3, $data['totals']['orders']);
        $this->assertEquals(65.5, $data['totals']['revenue']);
        $this->assertSame(1, $data['previous']['orders']);
        $this->assertEquals(40, $data['previous']['revenue']);
        $this->assertNotNull($data['latestOrderAt']);

        $hourly = $this->actingAs($admin)->getJson(route('admin.dashboard.live', ['range' => '24h']))->json();
        $this->assertCount(24, $hourly['orders']);
        $this->assertSame(2, $hourly['totals']['orders']);
    }

    public function test_unknown_range_falls_back_to_thirty_days(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.live', ['range' => 'forever']))
            ->assertOk()
            ->assertJsonPath('range', '30d')
            ->assertJsonCount(30, 'labels');
    }
}
