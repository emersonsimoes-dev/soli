<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RosterEntryResource;
use App\Models\Church;
use App\Models\RosterEntry;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RosterEntryController extends Controller
{
    public function index(Church $church): AnonymousResourceCollection
    {
        return RosterEntryResource::collection(
            RosterEntry::query()
                ->where('church_id', $church->id)
                ->whereDate('service_date', '>=', now('America/Fortaleza')->toDateString())
                ->with('member')
                ->orderBy('service_date')
                ->orderBy('ministry')
                ->get(),
        );
    }
}
