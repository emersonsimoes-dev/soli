<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ContributionResource;
use App\Models\Church;
use App\Models\Contribution;
use App\Services\ChurchResolver;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContributionController extends Controller
{
    public function index(Church $church, ChurchResolver $churches): AnonymousResourceCollection
    {
        $churches->authorizeAccess($church);

        return ContributionResource::collection(
            Contribution::query()
                ->where('church_id', $church->id)
                ->orderByDesc('occurred_on')
                ->get(),
        );
    }
}
