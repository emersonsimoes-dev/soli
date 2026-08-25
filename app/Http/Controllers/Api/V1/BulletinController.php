<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BulletinResource;
use App\Models\Bulletin;
use App\Models\Church;
use App\Services\BulletinReader;
use App\Services\ChurchResolver;

class BulletinController extends Controller
{
    public function current(BulletinReader $reader, ChurchResolver $churches): BulletinResource
    {
        return $this->respond($reader->publishedFor($churches->default()));
    }

    public function show(int $year, int $month, BulletinReader $reader, ChurchResolver $churches): BulletinResource
    {
        return $this->respond($reader->publishedForPeriod($churches->default(), $year, $month));
    }

    public function currentForChurch(Church $church, BulletinReader $reader): BulletinResource
    {
        return $this->respond($reader->publishedFor($church));
    }

    public function showForChurch(Church $church, int $year, int $month, BulletinReader $reader): BulletinResource
    {
        return $this->respond($reader->publishedForPeriod($church, $year, $month));
    }

    private function respond(?Bulletin $bulletin): BulletinResource
    {
        abort_unless($bulletin, 404);

        return new BulletinResource($bulletin);
    }
}
