<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ChurchResource;
use App\Models\Church;
use App\Services\ChurchResolver;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChurchController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ChurchResource::collection(
            Church::query()->orderBy('name')->get(),
        );
    }

    public function show(Church $church): ChurchResource
    {
        return new ChurchResource($church);
    }

    public function current(ChurchResolver $churches): ChurchResource
    {
        return new ChurchResource($churches->default());
    }
}
