<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesChurchOwned;

class AnnouncementPolicy
{
    use AuthorizesChurchOwned;
}
