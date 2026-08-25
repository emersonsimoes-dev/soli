<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesChurchOwned;

class ContributionPolicy
{
    use AuthorizesChurchOwned;
}
