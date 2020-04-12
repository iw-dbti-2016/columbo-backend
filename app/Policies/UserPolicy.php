<?php

namespace Columbo\Policies;

use Columbo\Traits\PolicyInformationPoint;
use Columbo\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization, PolicyInformationPoint;

    public function view(User $authUser, User $user)
    {
        return true;
    }

    public function update(User $authUser, User $user)
    {
        return $authUser == $user;
    }

    public function delete(User $authUser, User $user)
    {
        return $authUser == $user;
    }
}
