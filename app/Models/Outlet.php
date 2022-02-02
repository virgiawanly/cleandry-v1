<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    /**
     * Return services belongs to this outlet
     *
     * @return \App\Models\Outlet
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
