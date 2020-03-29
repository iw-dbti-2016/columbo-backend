<?php

namespace Columbo\Listeners;

use Columbo\Action;
use Columbo\Events\ResourceCreated;
use Columbo\Events\ResourceDeleted;
use Columbo\Events\ResourceReverted;
use Columbo\Events\ResourceUpdated;
use Columbo\Interfaces\TrackedByActions;
use Columbo\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ActionEventSubscriber
{
	public function handleResourceCreated(ResourceCreated $event)
	{
		$this->createAction($event->user, $event->model, "create");
	}

	public function handleResourceUpdated(ResourceUpdated $event)
	{
		$this->createAction($event->user, $event->model, "update");
	}

	public function handleResourceDeleted(ResourceDeleted $event)
	{
		$this->createAction($event->user, $event->model, "delete");
	}

	public function handleResourceReverted(ResourceReverted $event)
	{
		$this->createAction($event->user, $event->model, "revert");
	}

	private function createAction(User $user, TrackedByActions $model, String $actionType)
	{
		$action = new Action;
		$action->uuid = Str::uuid();
		$action->action = $actionType;
		$action->executed_at = Carbon::now();
		$action->user()->associate($user);
		$action->subject()->associate($model);
		$action->save();
	}

	public function subscribe($events)
	{
		$events->listen(
			'Columbo\Events\ResourceCreated',
			'Columbo\Listeners\ActionEventSubscriber@handleResourceCreated'
		);

		$events->listen(
			'Columbo\Events\ResourceUpdated',
			'Columbo\Listeners\ActionEventSubscriber@handleResourceUpdated'
		);

		$events->listen(
			'Columbo\Events\ResourceDeleted',
			'Columbo\Listeners\ActionEventSubscriber@handleResourceDeleted'
		);

		$events->listen(
			'Columbo\Events\ResourceReverted',
			'Columbo\Listeners\ActionEventSubscriber@handleResourceReverted'
		);
	}
}
