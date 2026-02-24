<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WalletResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalBalanceCents = (int) ($this->total_balance_cents ?? 0);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'wallets' => WalletResource::collection($this->whenLoaded('wallets')),
            'total_balance' => $this->formatCents($totalBalanceCents),
            'total_balance_cents' => $totalBalanceCents,
        ];
    }

    private function formatCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
