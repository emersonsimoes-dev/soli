<?php

namespace App\Services;

use App\Models\Church;

class ChurchResolver
{
    public function default(): Church
    {
        $church = Church::query()->orderBy('id')->first();

        abort_unless($church, 404);

        return $church;
    }

    public function authorizeAccess(Church $church): Church
    {
        abort_unless(auth()->user()?->canAccessChurch($church), 403);

        return $church;
    }
}
