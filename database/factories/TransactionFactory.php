<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Transaction> */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 1, 1000);
        $amountCents = (int) round($amount * 100);

        return [
            'wallet_id' => Wallet::factory(),
            'type' => $this->faker->randomElement(['income', 'expense']),
            'amount' => $amount,
            'amount_cents' => $amountCents,
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
