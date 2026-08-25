<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Services\BulletinReader;
use App\Support\CurrentMonth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function home(BulletinReader $reader): View
    {
        return $this->page($reader, Church::query()->orderBy('id')->first());
    }

    public function show(Church $church, BulletinReader $reader): View
    {
        return $this->page($reader, $church);
    }

    private function page(BulletinReader $reader, ?Church $church): View
    {
        $current = CurrentMonth::in($church?->timezone);
        $bulletin = $church ? $reader->publishedFor($church) : null;

        return view('bulletin.show', [
            'church' => $church,
            'bulletin' => $bulletin,
            'current' => $current,
        ]);
    }
}
