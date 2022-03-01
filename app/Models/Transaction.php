<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'outlet_id',
        'user_id',
        'member_id',
        'invoice',
        'date',
        'deadline',
        'payment_date',
        'additional_cost',
        'discount',
        'discount_type',
        'tax',
        'status',
        'payment_status'
    ];

    /**
     * Generate & return new invoice number
     *
     * @return string $invoice
     */
    public static function createInvoice()
    {
        $lastNumber = self::selectRaw("IFNULL(MAX(SUBSTRING(`invoice`,9,5)),0) + 1 AS last_number")->whereRaw("SUBSTRING(`invoice`,1,4) = '" . date('Y') . "'")->whereRaw("SUBSTRING(`invoice`,5,2) = '" . date('m') . "'")->orderBy('last_number')->first()->last_number;
        $invoice = date("Ymd") . sprintf("%'.05d", $lastNumber);
        return $invoice;
    }

    /**
     * Return transaction details
     *
     * @return \App\Models\TransactionDetail
     */
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }


    /**
     * Return transaction's member
     *
     * @return \App\Models\TransactionDetail
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Return transaction's user
     *
     * @return \App\Models\TransactionDetail
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return transaction's outlet
     *
     * @return \App\Models\Outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function getTotalPrice()
    {
        return $this->details->reduce(function ($total, $detail) {
            return $total + ($detail->price_history * $detail->qty);
        });
    }

    public function getTotalDiscount()
    {
        return $this->discount_type == 'percent' ? $this->getTotalPrice() * ($this->discount / 100) : $this->discount;
    }

    public function getTotalTax()
    {
        return $this->getTotalPrice() * ($this->tax / 100);
    }

    public function getTotalPayment()
    {
        return $this->getTotalPrice() - $this->getTotalDiscount() + $this->getTotalTax() + $this->additional_cost;
    }
}
