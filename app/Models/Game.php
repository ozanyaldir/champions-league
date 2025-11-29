<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixture_id',
        'home_goals',
        'away_goals',
    ];

    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }
}
