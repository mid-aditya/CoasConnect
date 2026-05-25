<?php

namespace Tests\Unit;

use App\Models\Evaluation;
use PHPUnit\Framework\TestCase;

class EvaluationTest extends TestCase
{
    /**
     * Test rating labels constant.
     */
    public function test_rating_labels_exist(): void
    {
        $labels = Evaluation::getRatingLabels();

        $this->assertArrayHasKey(1, $labels);
        $this->assertArrayHasKey(2, $labels);
        $this->assertArrayHasKey(3, $labels);
        $this->assertArrayHasKey(4, $labels);
        $this->assertArrayHasKey(5, $labels);
    }

    /**
     * Test rating label descriptions.
     */
    public function test_rating_labels_descriptions(): void
    {
        $labels = Evaluation::getRatingLabels();

        $this->assertEquals('Needs Improvement', $labels[1]);
        $this->assertEquals('Developing', $labels[2]);
        $this->assertEquals('Competent', $labels[3]);
        $this->assertEquals('Proficient', $labels[4]);
        $this->assertEquals('Exemplary', $labels[5]);
    }

    /**
     * Test status constants.
     */
    public function test_status_constants(): void
    {
        $this->assertEquals('approved', Evaluation::STATUS_APPROVED);
        $this->assertEquals('revision_requested', Evaluation::STATUS_REVISION_REQUESTED);
        $this->assertEquals('rejected', Evaluation::STATUS_REJECTED);
    }
}
