<?php

namespace Tests\Unit;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_resource_formats_amount(): void
    {
        $transaction = Transaction::factory()->create([
            'amount' => 12.34,
            'amount_cents' => 1234,
        ]);

        $resource = (new TransactionResource($transaction))->toArray(Request::create('/'));

        $this->assertSame('12.34', $resource['amount']);
        $this->assertSame(1234, $resource['amount_cents']);
    }
}
