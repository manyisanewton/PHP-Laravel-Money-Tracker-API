<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user): JsonResponse
    {
        $wallets = $user->wallets()
            ->withSum(['transactions as income_total' => function ($query) {
                $query->where('type', 'income');
            }], 'amount_cents')
            ->withSum(['transactions as expense_total' => function ($query) {
                $query->where('type', 'expense');
            }], 'amount_cents')
            ->get();

        $wallets->each(function ($wallet): void {
            $income = (int) ($wallet->income_total ?? 0);
            $expense = (int) ($wallet->expense_total ?? 0);
            $wallet->balance_cents = $income - $expense;
        });

        $totalBalanceCents = $wallets->sum('balance_cents');

        $user->setRelation('wallets', $wallets);
        $user->total_balance_cents = $totalBalanceCents;

        return (new UserResource($user))->response();
    }
}
