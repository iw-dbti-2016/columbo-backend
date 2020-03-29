<?php

namespace Columbo\Events;

use Columbo\Interfaces\TrackedByActions;
use Columbo\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResourceCreated
{
	use SerializesModels;

	public $user;
	public $model;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, TrackedByActions $model)
	{
		$this->user = $user;
		$this->model = $model;
	}
}
