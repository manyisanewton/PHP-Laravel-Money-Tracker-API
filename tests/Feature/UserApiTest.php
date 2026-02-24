<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name', 'Jane Doe')
            ->assertJsonPath('email', 'jane@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
        ]);
    }

    public function test_user_profile_returns_wallets_and_balances(): void
    {
        $user = User::factory()->create();

        $walletA = Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'USD',
        ]);
        $walletB = Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'USD',
        ]);

        Transaction::factory()->create([
            'wallet_id' => $walletA->id,
            'type' => 'income',
            'amount' => 100.00,
            'amount_cents' => 10000,
        ]);
        Transaction::factory()->create([
            'wallet_id' => $walletA->id,
            'type' => 'expense',
            'amount' => 40.00,
            'amount_cents' => 4000,
        ]);

        Transaction::factory()->create([
            'wallet_id' => $walletB->id,
            'type' => 'income',
            'amount' => 25.00,
            'amount_cents' => 2500,
        ]);

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertOk();

        $wallets = collect($response->json('wallets'));
        $balances = $wallets->pluck('balance')->all();

        $this->assertContains('60.00', $balances);
        $this->assertContains('25.00', $balances);
        $this->assertSame('85.00', $response->json('total_balance'));
    }
}
