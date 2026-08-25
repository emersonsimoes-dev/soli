<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesChurchOwned;

class MemberPolicy
{
    use AuthorizesChurchOwned;
}
