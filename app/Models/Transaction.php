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
}
