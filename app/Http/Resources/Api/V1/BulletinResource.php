<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Bulletin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Bulletin
 */
class BulletinResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'month_label' => Bulletin::monthLabel($this->month),
            'theme' => $this->theme,
            'status' => $this->status->value,
            'published_at' => $this->published_at?->timezone('America/Fortaleza')->toIso8601String(),
            'church' => [
                'name' => $this->church->name,
                'short_name' => $this->church->short_name,
                'slug' => $this->church->slug,
                'timezone' => $this->church->timezone,
                'pix_key' => $this->church->pix_key,
                'logo_url' => $this->church->logoUrl(),
                'uses_default_logo' => $this->church->usesDefaultLogo(),
            ],
            'schedule' => $this->scheduleItems->map(fn ($item) => [
                'day_label' => $item->day_label,
                'description' => $item->description,
                'is_highlight' => $item->is_highlight,
                'sort_order' => $item->sort_order,
            ])->values(),
            'events' => $this->specialEvents->map(fn ($event) => [
                'event_date' => $event->event_date?->toDateString(),
                'weekday_label' => $event->weekday_label,
                'title' => $event->title,
                'subtitle' => $event->subtitle,
                'sort_order' => $event->sort_order,
            ])->values(),
            'service_rosters' => $this->serviceRosters->map(fn ($roster) => [
                'service_date' => $roster->service_date?->toDateString(),
                'introducers' => $roster->introducers,
                'offertory' => $roster->offertory,
                'leaders' => $roster->leaders,
                'preachers' => $roster->preachers,
                'support' => $roster->support,
                'sort_order' => $roster->sort_order,
            ])->values(),
            'children_ministry' => $this->childrenMinistryRosters->map(fn ($roster) => [
                'service_date' => $roster->service_date?->toDateString(),
                'nursery' => $roster->nursery,
                'primary_class' => $roster->primary_class,
                'sort_order' => $roster->sort_order,
            ])->values(),
            'ebd_classes' => $this->ebdClasses->map(fn ($class) => [
                'class_name' => $class->class_name,
                'teachers_text' => $class->teachers_text,
                'sort_order' => $class->sort_order,
            ])->values(),
            'birthdays' => $this->birthdays->map(fn ($birthday) => [
                'day' => $birthday->day,
                'name' => $birthday->name,
                'sort_order' => $birthday->sort_order,
            ])->values(),
            'kpis' => $this->kpiCounts(),
        ];
    }
}
