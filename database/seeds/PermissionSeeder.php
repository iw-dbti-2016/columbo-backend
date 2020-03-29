<?php

use Columbo\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->getPermissionMap() as $permission) {
			Permission::create($permission);
		}
    }

    public function getPermissionMap()
    {
    	return [
			[
				"name" => "edit all locations",
				"label" => "location:all:edit",
			],
			[
				"name" => "remove all locations",
				"label" => "location:all:remove",
			],
			[
				"name" => "search all locations",
				"label" => "location:all:search",
			],
			[
				"name" => "view all locations",
				"label" => "location:all:view",
			],
			[
				"name" => "create locations",
				"label" => "location:create",
			],
			[
				"name" => "edit own locations",
				"label" => "location:own:edit",
			],
			[
				"name" => "remove own locations",
				"label" => "location:own:remove",
			],
			[
				"name" => "add members",
				"label" => "members:add",
			],
			[
				"name" => "edit admin roles",
				"label" => "members:edit-admin",
			],
			[
				"name" => "edit basic roles",
				"label" => "members:edit-roles",
			],
			[
				"name" => "remove basic members",
				"label" => "members:remove",
			],
			[
				"name" => "remove admin members",
				"label" => "members:remove-admin",
			],
			[
				"name" => "update members",
				"label" => "members:update",
			],
			[
				"name" => "change all payment divisions",
				"label" => "payment:all:change-division",
			],
			[
				"name" => "edit all payments",
				"label" => "payment:all:edit",
			],
			[
				"name" => "remove all payments",
				"label" => "payment:all:remove",
			],
			[
				"name" => "create payments",
				"label" => "payment:create",
			],
			[
				"name" => "change own payment divisions",
				"label" => "payment:own:change-division",
			],
			[
				"name" => "edit own payments",
				"label" => "payment:own:edit",
			],
			[
				"name" => "remove own payments",
				"label" => "payment:own:remove",
			],
			[
				"name" => "view all payments",
				"label" => "payment:all:view",
			],
			[
				"name" => "edit all plans",
				"label" => "plan:all:edit",
			],
			[
				"name" => "link location to all plans",
				"label" => "plan:all:link-location",
			],
			[
				"name" => "link poi to all plans",
				"label" => "plan:all:link-poi",
			],
			[
				"name" => "remove all plans",
				"label" => "plan:all:remove",
			],
			[
				"name" => "unlink location from all plans",
				"label" => "plan:all:unlink-location",
			],
			[
				"name" => "unlink poi from all plans",
				"label" => "plan:all:unlink-poi",
			],
			[
				"name" => "create plans",
				"label" => "plan:create",
			],
			[
				"name" => "edit own plans",
				"label" => "plan:own:edit",
			],
			[
				"name" => "link location to own plans",
				"label" => "plan:own:link-location",
			],
			[
				"name" => "link poi to own plans",
				"label" => "plan:own:link-poi",
			],
			[
				"name" => "remove own plans",
				"label" => "plan:own:remove",
			],
			[
				"name" => "unlink location from own plans",
				"label" => "plan:own:unlink-location",
			],
			[
				"name" => "unlink poi from own plans",
				"label" => "plan:own:unlink-poi",
			],
			[
				"name" => "search all pois",
				"label" => "poi:all:search",
			],
			[
				"name" => "view all pois",
				"label" => "poi:all:view",
			],
			[
				"name" => "edit all reports",
				"label" => "report:all:edit",
			],
			[
				"name" => "link plan to all reports",
				"label" => "report:all:link-plan",
			],
			[
				"name" => "remove all reports",
				"label" => "report:all:remove",
			],
			[
				"name" => "unlink plan from all reports",
				"label" => "report:all:unlink-plan",
			],
			[
				"name" => "view all reports",
				"label" => "report:all:view",
			],
			[
				"name" => "create reports",
				"label" => "report:create",
			],
			[
				"name" => "edit own reports",
				"label" => "report:own:edit",
			],
			[
				"name" => "link plan to own reports",
				"label" => "report:own:link-plan",
			],
			[
				"name" => "remove own reports",
				"label" => "report:own:remove",
			],
			[
				"name" => "unlink plan from own reports",
				"label" => "report:own:unlink-plan",
			],
			[
				"name" => "edit all sections",
				"label" => "section:all:edit",
			],
			[
				"name" => "link location to all sections",
				"label" => "section:all:link-location",
			],
			[
				"name" => "link payment to all sections",
				"label" => "section:all:link-payment",
			],
			[
				"name" => "link poi to all sections",
				"label" => "section:all:link-poi",
			],
			[
				"name" => "remove all sections",
				"label" => "section:all:remove",
			],
			[
				"name" => "unlink location from all sections",
				"label" => "section:all:unlink-location",
			],
			[
				"name" => "unlink payment form all sections",
				"label" => "section:all:unlink-payment",
			],
			[
				"name" => "unlink poi from all sections",
				"label" => "section:all:unlink-poi",
			],
			[
				"name" => "view all sections",
				"label" => "section:all:view",
			],
			[
				"name" => "create sections",
				"label" => "section:create",
			],
			[
				"name" => "edit own sections",
				"label" => "section:own:edit",
			],
			[
				"name" => "link location to own sections",
				"label" => "section:own:link-location",
			],
			[
				"name" => "link payment to own sections",
				"label" => "section:own:link-payment",
			],
			[
				"name" => "link poi to own sections",
				"label" => "section:own:link-poi",
			],
			[
				"name" => "remove own sections",
				"label" => "section:own:remove",
			],
			[
				"name" => "unlink location from own sections",
				"label" => "section:own:unlink-location",
			],
			[
				"name" => "unlink payment from own sections",
				"label" => "section:own:unlink-payment",
			],
			[
				"name" => "unlink poi from own sections",
				"label" => "section:own:unlink-poi",
			],
			[
				"name" => "edit trip",
				"label" => "trip:edit",
			],
			[
				"name" => "link payment to trip",
				"label" => "trip:link-payment",
			],
			[
				"name" => "remove trip",
				"label" => "trip:remove",
			],
			[
				"name" => "unlink payment from trip",
				"label" => "trip:unlink-payment",
			],
			[
				"name" => "view trip",
				"label" => "trip:view",
			],
			[
				"name" => "add visitors",
				"label" => "visitors:add",
			],
			[
				"name" => "remove visitors",
				"label" => "visitors:remove",
			],
			[
				"name" => "update visitors",
				"label" => "visitors:update",
			],
		];
    }
}
