<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesChurchOwned;

class RosterEntryPolicy
{
    use AuthorizesChurchOwned;
}
