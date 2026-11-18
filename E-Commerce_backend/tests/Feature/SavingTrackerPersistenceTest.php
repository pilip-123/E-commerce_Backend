<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingTrackerPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_spending_and_notes_are_saved_for_the_signed_in_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $state = [
            'entries' => [
                ['id' => 'save-1', 'kind' => 'saved', 'amount' => 125.50, 'category' => 'Emergency fund', 'date' => '2026-11-18', 'note' => 'Monthly deposit', 'source' => 'manual'],
                ['id' => 'spend-1', 'kind' => 'spent', 'amount' => 12.25, 'category' => 'Food', 'date' => '2026-11-18', 'note' => 'Lunch', 'source' => 'manual'],
            ],
            'plans' => [],
            'monthlyTargets' => ['2026-11' => 500],
            'yearlyTargets' => ['2026' => 6000],
            'notes' => [
                ['id' => 'note-1', 'title' => 'Savings idea', 'content' => '<p><span style="color:#15803d">Save consistently</span></p>', 'savedAt' => '2026-11-18T08:00:00.000Z', 'revisions' => []],
            ],
            'currency' => 'USD',
        ];

        $this->actingAs($user)->assertAuthenticatedAs($user);
        $response = $this->putJson(route('admin.saving.state'), ['data' => $state]);
        $response->assertOk()->assertJson(['saved' => true]);

        $storedJson = \DB::table('saving_tracker_states')->where('user_id', $user->id)->value('data');
        $this->assertNotNull($storedJson);
        $stored = json_decode($storedJson, true, flags: JSON_THROW_ON_ERROR);

        $this->assertDatabaseHas('saving_entries', [
            'user_id' => $user->id,
            'client_id' => 'save-1',
            'kind' => 'saved',
            'amount' => 125.50,
            'category' => 'Emergency fund',
            'date' => '2026-11-18',
        ]);
        $this->assertDatabaseHas('saving_entries', [
            'user_id' => $user->id,
            'client_id' => 'spend-1',
            'kind' => 'spent',
            'amount' => 12.25,
            'category' => 'Food',
            'date' => '2026-11-18',
        ]);
        $this->assertArrayNotHasKey('entries', $stored);
        $this->assertSame('<p><span style="color:#15803d">Save consistently</span></p>', $stored['notes'][0]['content']);
        $this->assertSame('500', (string) $stored['monthlyTargets']['2026-11']);
        $this->assertSame('USD', $stored['currency']);

        $this->actingAs($user)
            ->get(route('admin.saving.index'))
            ->assertOk()
            ->assertSee('Save consistently');
    }

    public function test_empty_optional_tracker_collections_can_also_be_saved(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $emptyState = [
            'entries' => [],
            'plans' => [],
            'monthlyTargets' => [],
            'yearlyTargets' => [],
            'notes' => [],
            'currency' => 'USD',
        ];

        $this->actingAs($user)
            ->putJson(route('admin.saving.state'), ['data' => $emptyState])
            ->assertOk()
            ->assertJson(['saved' => true]);

        $this->assertDatabaseHas('saving_tracker_states', [
            'user_id' => $user->id,
            'data' => json_encode([
                'plans' => [], 'monthlyTargets' => [], 'yearlyTargets' => [], 'notes' => [], 'currency' => 'USD',
            ]),
        ]);
        $this->assertDatabaseMissing('saving_entries', ['user_id' => $user->id]);
    }

    public function test_save_route_returns_json_validation_errors_instead_of_redirecting(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($user)
            ->putJson(route('admin.saving.state'), ['data' => [
                'entries' => [],
                'plans' => [],
                'monthlyTargets' => [],
                'yearlyTargets' => [],
                'notes' => [],
                'currency' => 'INVALID',
            ]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['data.currency']);
    }
}
