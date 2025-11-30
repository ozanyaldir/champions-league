<?php

namespace Tests\Unit;

use App\Support\MathUtils;
use PHPUnit\Framework\TestCase;

class MathUtilsTest extends TestCase
{
    /** @test */
    public function sample_goals_returns_integer()
    {
        $lambda = 2.5;
        $result = MathUtils::sampleGoals($lambda);

        $this->assertIsInt($result);
    }

    /** @test */
    public function sample_goals_never_returns_negative()
    {
        $lambda = 0.0;

        for ($i = 0; $i < 100; $i++) {
            $result = MathUtils::sampleGoals($lambda);
            $this->assertGreaterThanOrEqual(0, $result);
        }
    }

    /** @test */
    public function sample_goals_changes_based_on_lambda()
    {
        $lambdaLow = 0.5;
        $lambdaHigh = 5.0;

        $lowResult = MathUtils::sampleGoals($lambdaLow);
        $highResult = MathUtils::sampleGoals($lambdaHigh);

        $this->assertGreaterThanOrEqual($lowResult, $highResult - 3);
    }
}
