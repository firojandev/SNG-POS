<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $phpDateFormat = get_option('date_format', 'Y-m-d');

        return [
            'id' => $this->id,
            'lender' => $this->lender,
            'date' => optional($this->date)->format($phpDateFormat),
            'amount' => (float) $this->amount,
            'note' => $this->note,
            'status' => $this->status,
        ];
    }
}
