<?php

namespace App\Filament\App\Widgets;

use App\Models\Absence;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class MyAbsencesStats extends Widget
{
    // In Filament 3, you can define a static $sort if you want ordering among multiple widgets.
    protected static ?int $sort = 1;

    /**
     * Possibly, we can define the 'view' to a custom Blade if we want.
     * For a StatsOverview, we might do it differently, but let's show an example:
     */
    protected static string $view = 'filament.app.widgets.my-absences-stats';

    public array $reports = [];

    public function mount(): void
    {
        $user = Auth::user();
        $person = $user?->person;

        if (! $person) {
            // Not linked or not logged in, no data
            $this->reports = [];
            return;
        }

        // Build 3 different periods:
        $now = Carbon::now();

        // 1) Last 12 months
        $this->reports[] = [
            'title' => 'Last 12 Months',
            'data'  => $this->buildStats($person->id, now()->subYear(), now()),
        ];

        // 2) This Year (e.g. 2024)
        $thisYearStart = $now->copy()->startOfYear();
        $thisYearEnd   = $now->copy()->endOfYear();
        $this->reports[] = [
            'title' => 'This Year (' . $now->year . ')',
            'data'  => $this->buildStats($person->id, $thisYearStart, $thisYearEnd),
        ];

        // 3) Last Year (e.g. 2023)
        $lastYear = $now->year - 1;
        $lastYearStart = Carbon::create($lastYear)->startOfYear();
        $lastYearEnd   = Carbon::create($lastYear)->endOfYear();
        $this->reports[] = [
            'title' => "Last Year ({$lastYear})",
            'data'  => $this->buildStats($person->id, $lastYearStart, $lastYearEnd),
        ];
    }

    /**
     * Builds stats for one period, grouped by absence type.
     * Returns an array of [ ['type' => 'Sick Leave', 'count' => X, 'days' => Y ], ... ]
     */
    private function buildStats(int $personId, Carbon $start, Carbon $end): array
    {
        $absences = Absence::where('person_id', $personId)
            ->whereBetween('start_date', [$start, $end])
            ->with('absenceType')
            ->get();

        // Group by absence_type_id
        $grouped = $absences->groupBy('absence_type_id');

        $results = [];
        foreach ($grouped as $typeId => $items) {
            $typeName = $items->first()->absenceType->name ?? 'Unknown';
            $count    = $items->count();
            // Sum total days
            $days = $items->sum(function ($absence) {
                $startDate = $absence->start_date;
                $endDate   = $absence->end_date ?? now();
                return $endDate->diffInDays($startDate) + 1;
            });

            $results[] = [
                'type'  => $typeName,
                'count' => $count,
                'days'  => $days,
            ];
        }

        return $results;
    }
}
