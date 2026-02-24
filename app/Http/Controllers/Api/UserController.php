<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function show(User $user): JsonResponse
    {
        $wallets = $user->wallets()
            ->withSum(['transactions as income_total' => function ($query) {
                $query->where('type', 'income');
            }], 'amount')
            ->withSum(['transactions as expense_total' => function ($query) {
                $query->where('type', 'expense');
            }], 'amount')
            ->get();

        $walletsPayload = $wallets->map(function ($wallet) {
            $income = (float) ($wallet->income_total ?? 0);
            $expense = (float) ($wallet->expense_total ?? 0);
            $balance = $income - $expense;

            return [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'description' => $wallet->description,
                'balance' => $balance,
                'created_at' => $wallet->created_at,
            ];
        });

        $totalBalance = $walletsPayload->sum('balance');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'wallets' => $walletsPayload,
            'total_balance' => $totalBalance,
        ]);
    }
}
