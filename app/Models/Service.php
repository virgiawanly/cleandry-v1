<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type_id',
        'unit',
        'price',
        'outlet_id'
    ];

    /**
     * Return service type collection
     *
     * @return \App\Models\Outlet
     */
    public function type()
    {
        return $this->belongsTo(ServiceType::class, 'type_id');
    }

    /**
     * Return the service outlet
     *
     * @return \App\Models\Outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
