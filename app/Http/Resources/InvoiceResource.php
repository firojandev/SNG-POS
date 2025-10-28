<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'uuid' => $this->uuid,
            'invoice_number' => $this->invoice_number,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'phone' => $this->customer->phone,
                    'email' => $this->customer->email,
                ];
            }),
            'date' => optional($this->date)->format($phpDateFormat),
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'due_amount' => (float) $this->due_amount,
            'formatted_subtotal' => $this->formatted_subtotal,
            'formatted_discount' => $this->formatted_discount,
            'formatted_total_amount' => $this->formatted_total_amount,
            'formatted_paid_amount' => $this->formatted_paid_amount,
            'formatted_due_amount' => $this->formatted_due_amount,
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
