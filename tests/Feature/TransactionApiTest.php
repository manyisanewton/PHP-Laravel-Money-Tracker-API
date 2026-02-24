<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_can_be_created(): void
    {
        $wallet = Wallet::factory()->create();

        $response = $this->postJson("/api/wallets/{$wallet->id}/transactions", [
            'type' => 'income',
            'amount' => 12.34,
            'description' => 'Invoice',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('type', 'income')
            ->assertJsonPath('amount', '12.34');

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount_cents' => 1234,
        ]);
    }

    public function test_transaction_requires_positive_amount(): void
    {
        $wallet = Wallet::factory()->create();

        $response = $this->postJson("/api/wallets/{$wallet->id}/transactions", [
            'type' => 'expense',
            'amount' => -10,
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
    }
}
