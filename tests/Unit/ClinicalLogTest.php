<?php

namespace Tests\Unit;

use App\Models\ClinicalLog;
use PHPUnit\Framework\TestCase;

class ClinicalLogTest extends TestCase
{
    /**
     * Test activity types constant.
     */
    public function test_activity_types_exist(): void
    {
        $types = ClinicalLog::getActivityTypes();

        $this->assertArrayHasKey('anamnesis', $types);
        $this->assertArrayHasKey('physical_exam', $types);
        $this->assertArrayHasKey('procedure', $types);
        $this->assertArrayHasKey('education', $types);
        $this->assertArrayHasKey('consultation', $types);
        $this->assertArrayHasKey('other', $types);
    }

    /**
     * Test patient conditions constant.
     */
    public function test_patient_conditions_exist(): void
    {
        $conditions = ClinicalLog::getPatientConditions();

        $this->assertArrayHasKey('stable', $conditions);
        $this->assertArrayHasKey('improving', $conditions);
        $this->assertArrayHasKey('worsening', $conditions);
        $this->assertArrayHasKey('critical', $conditions);
    }

    /**
     * Test status constants.
     */
    public function test_status_constants(): void
    {
        $this->assertEquals('draft', ClinicalLog::STATUS_DRAFT);
        $this->assertEquals('submitted', ClinicalLog::STATUS_SUBMITTED);
        $this->assertEquals('reviewed', ClinicalLog::STATUS_REVIEWED);
        $this->assertEquals('revision_requested', ClinicalLog::STATUS_REVISION_REQUESTED);
        $this->assertEquals('rejected', ClinicalLog::STATUS_REJECTED);
    }
}
