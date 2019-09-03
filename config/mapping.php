<?php

return [
	/**
	 * The visibility is used in a lot of tables.
	 * 	It defines who can see the content.
	 * 	Important is that these keys are oredered:
	 * 		The higher the number, the stricter the
	 * 		access control.
	 *
	 * 	The keys are spread out to allow for possible
	 * 	updates and new values inbetween the current
	 * 	ones.
	 */
	"visibility" => [
		"public" => 0,
		"authenticated" => 52,
		"friends" => 104,
		"visitors" => 156,
		"members" => 208,
		"private" => 255,
	],

	/**
	 * The action defines which action a
	 * 	user has taken. Order is not of
	 * 	the essence here.
	 */
	"action" => [
		"create" => 0,
		"update" => 1,
		"delete" => 2,
		"revert" => 3,
	],

	/**
	 * In some tables, a status is indicated.
	 * 	This is the mapping for the meaning of
	 * 	the integer. Order is not important.
	 */
	"status" => [
		"TODO" => 0,
		"IN PROCESS" => 1,
		"TO CONFIRM" => 2,
		"DONE" => 3,
	],
];
