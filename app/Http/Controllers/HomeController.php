<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Services\BulletinReader;
use App\Support\CurrentMonth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(BulletinReader $reader): View
    {
        $church = Church::query()->first();
        $current = CurrentMonth::in($church?->timezone);
        $bulletin = $church ? $reader->publishedFor($church) : null;

        return view('bulletin.show', [
            'church' => $church,
            'bulletin' => $bulletin,
            'current' => $current,
        ]);
    }
}
