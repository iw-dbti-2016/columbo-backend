<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\ResourceFactory;
use Columbo\Currency;
use Columbo\Report;
use Columbo\Section;
use Columbo\Trip;
use Columbo\User;

class ReportTest extends TestCase
{
	use RefreshDatabase, ResourceFactory;

    /** @test */
    public function a_report_can_be_created()
    {
    	$report = $this->createReport();

        $this->assertDatabaseHas('reports', ['id' => $report->id]);
    }

    /** @test */
    public function a_report_can_have_sections()
    {
        $user    = $this->createUser();
        $report  = $this->createReport($user);
        $section = $this->createSection($user, $report);

        $this->assertDatabaseHas('sections', ['user_id' => $user->id, 'report_id' => $report->id]);
        $this->assertCount(1, $report->sections);
        $this->assertCount(1, $user->sections);
    }
}
