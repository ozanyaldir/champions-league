<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'fixture_id',
        'home_goals',
        'away_goals',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'fixture_id' => 'integer',
        'home_goals' => 'integer',
        'away_goals' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }
}
