<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function store(StoreTransactionRequest $request, Wallet $wallet): JsonResponse
    {
        $validated = $request->validated();
        $amount = (float) $validated['amount'];
        $amountCents = (int) round($amount * 100);

        $validated['amount'] = $amount;
        $validated['amount_cents'] = $amountCents;

        $transaction = $wallet->transactions()->create($validated);

        return (new TransactionResource($transaction))->response()->setStatusCode(201);
    }
}
