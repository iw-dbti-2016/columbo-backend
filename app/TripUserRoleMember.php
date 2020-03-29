<?php

namespace Columbo;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TripUserRoleMember extends Pivot
{
    protected $table = 'trip_user_role_members';
}
