<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Columbo\Permission;

class PermissionTest extends TestCase
{
  	use RefreshDatabase;

  	/** @test */
    public function a_permission_can_be_created()
    {
        $permission = factory(Permission::class)->create();

        $this->assertDatabaseHas('permissions', ['label' => $permission->label]);
    }
}
