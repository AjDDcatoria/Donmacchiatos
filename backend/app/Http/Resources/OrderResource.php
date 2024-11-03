<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $payment
 * @property mixed $grand_total
 * @property mixed $status
 * @property mixed $message
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'order_id' => $this->id,
            'payment' => $this->payment,
            'grand_total' => $this->grand_total,
            'status' => $this->status,
            'message' => $this->message,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'order_items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
