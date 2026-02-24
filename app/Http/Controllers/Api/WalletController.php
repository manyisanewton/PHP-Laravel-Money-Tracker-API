<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function store(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $wallet = $user->wallets()->create($validated);

        return response()->json($wallet, 201);
    }

    public function show(Wallet $wallet): JsonResponse
    {
        $wallet->loadSum(['transactions as income_total' => function ($query) {
            $query->where('type', 'income');
        }], 'amount');
        $wallet->loadSum(['transactions as expense_total' => function ($query) {
            $query->where('type', 'expense');
        }], 'amount');

        $income = (float) ($wallet->income_total ?? 0);
        $expense = (float) ($wallet->expense_total ?? 0);
        $balance = $income - $expense;

        $transactions = $wallet->transactions()
            ->orderByDesc('created_at')
            ->get(['id', 'type', 'amount', 'description', 'created_at']);

        return response()->json([
            'id' => $wallet->id,
            'user_id' => $wallet->user_id,
            'name' => $wallet->name,
            'description' => $wallet->description,
            'balance' => $balance,
            'transactions' => $transactions,
        ]);
    }
}
