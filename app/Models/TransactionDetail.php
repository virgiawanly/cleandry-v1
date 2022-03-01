<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'service_name_history',
        'price_history',
        'qty',
        'description',
    ];

    /**
     * Return transaction
     *
     * @return \App\Models\TransactionDetail
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Return transaction service
     *
     * @return \App\Models\Service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
