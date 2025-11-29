<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    protected $attributes = [
        'played' => 0,
        'won' => 0,
        'draw' => 0,
        'lost' => 0,
        'points' => 0,
    ];

    protected $casts = [
        'played' => 'integer',
        'won' => 'integer',
        'draw' => 'integer',
        'lost' => 'integer',
        'points' => 'integer',
    ];
}
