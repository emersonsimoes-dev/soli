<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BulletinResource;
use App\Models\Bulletin;
use App\Models\Church;
use App\Services\BulletinReader;

class BulletinController extends Controller
{
    public function current(BulletinReader $reader): BulletinResource
    {
        return $this->respond($reader->publishedFor($this->church()));
    }

    public function show(int $year, int $month, BulletinReader $reader): BulletinResource
    {
        return $this->respond($reader->publishedForPeriod($this->church(), $year, $month));
    }

    private function church(): Church
    {
        $church = Church::query()->first();

        abort_unless($church, 404);

        return $church;
    }

    private function respond(?Bulletin $bulletin): BulletinResource
    {
        abort_unless($bulletin, 404);

        return new BulletinResource($bulletin);
    }
}
