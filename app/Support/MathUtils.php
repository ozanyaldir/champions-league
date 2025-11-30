<?php

namespace App\Support;

class MathUtils
{
    public static function sampleGoals(float $lambda): int
    {
        $v = max(0, round($lambda + (mt_rand(-100, 100) / 100.0) * 0.6));

        return (int) $v;
    }
}
