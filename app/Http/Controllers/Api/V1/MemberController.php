<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MemberResource;
use App\Models\Church;
use App\Models\Member;
use App\Services\ChurchResolver;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberController extends Controller
{
    public function index(Church $church, ChurchResolver $churches): AnonymousResourceCollection
    {
        $churches->authorizeAccess($church);

        return MemberResource::collection(
            Member::query()
                ->where('church_id', $church->id)
                ->orderBy('name')
                ->get(),
        );
    }
}
