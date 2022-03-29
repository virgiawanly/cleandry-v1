<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemUses extends Model
{
    use HasFactory;

    /**
     * Atribut yang mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_name',
        'user_name',
        'start_use',
        'end_use',
        'status',
    ];

    /**
     * Pendefinisian nama table di database untuk model ini.
     */
    protected $table = 'item_uses';
}
