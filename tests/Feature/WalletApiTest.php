<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_can_be_created_with_default_currency(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson("/api/users/{$user->id}/wallets", [
            'name' => 'Main Wallet',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name', 'Main Wallet')
            ->assertJsonPath('currency', 'USD');
    }

    public function test_wallet_show_returns_transactions_with_pagination(): void
    {
        $wallet = Wallet::factory()->create();

        Transaction::factory()->count(3)->create([
            'wallet_id' => $wallet->id,
        ]);

        $response = $this->getJson("/api/wallets/{$wallet->id}?per_page=2");

        $response->assertOk();

        $this->assertArrayHasKey('wallet', $response->json());
        $this->assertArrayHasKey('transactions', $response->json());
        $this->assertArrayHasKey('data', $response->json('transactions'));
        $this->assertCount(2, $response->json('transactions.data'));
    }
}
