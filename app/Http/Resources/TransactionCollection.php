<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'outlet_id' => $this->outlet_id,
            'outlet' => $this->outlet,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'member_id' => $this->member_id,
            'member' => $this->member,
            'invoice' => $this->invoice,
            'date' => $this->date,
            'deadline' => $this->deadline,
            'payment_date' => $this->payment_date,
            'additional_cost' => $this->additional_cost,
            'total_price' => $this->getTotalPrice(),
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'total_discount' => $this->getTotalDiscount(),
            'tax' => $this->tax,
            'total_tax' => $this->getTotalTax(),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
        ];
    }
}
