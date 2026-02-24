<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\WalletResource;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    public function store(StoreWalletRequest $request, User $user): JsonResponse
    {
        $payload = $request->validated();
        $payload['currency'] = $payload['currency'] ?? 'USD';
        $wallet = $user->wallets()->create($payload);

        return (new WalletResource($wallet))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Wallet $wallet): JsonResponse
    {
        $wallet->loadSum(['transactions as income_total' => function ($query) {
            $query->where('type', 'income');
        }], 'amount_cents');
        $wallet->loadSum(['transactions as expense_total' => function ($query) {
            $query->where('type', 'expense');
        }], 'amount_cents');

        $income = (int) ($wallet->income_total ?? 0);
        $expense = (int) ($wallet->expense_total ?? 0);
        $wallet->balance_cents = $income - $expense;

        $perPage = min(max((int) request()->query('per_page', 15), 1), 100);
        $transactions = $wallet->transactions()
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'wallet' => new WalletResource($wallet),
            'transactions' => TransactionResource::collection($transactions)->response()->getData(true),
        ]);
    }
}
