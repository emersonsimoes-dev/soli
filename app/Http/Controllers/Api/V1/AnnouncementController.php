<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AnnouncementStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AnnouncementResource;
use App\Models\Announcement;
use App\Models\Church;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnnouncementController extends Controller
{
    public function index(Church $church): AnonymousResourceCollection
    {
        return AnnouncementResource::collection(
            Announcement::query()
                ->where('church_id', $church->id)
                ->where('status', AnnouncementStatus::Published)
                ->orderByDesc('published_at')
                ->get(),
        );
    }
}
