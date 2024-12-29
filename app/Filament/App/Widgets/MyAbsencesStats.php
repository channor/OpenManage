<?php

namespace App\Filament\App\Widgets;

use App\Models\Absence;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class MyAbsencesStats extends Widget
{
    protected static ?int $sort = 1;

    protected static string $view = 'filament.app.widgets.my-absences-stats';

    public array $reports = [];

    public function mount(): void
    {
        $user = Auth::user();
        $person = $user?->person;

        if (! $person) {
            $this->reports = [];
            return;
        }

        $now = Carbon::now();

        // Last 12 months
        $this->reports[] = [
            'title' => 'Last 12 Months',
            'data'  => $this->buildStats($person->id, now()->subYear(), now()),
        ];

        // Current Year
        $currentYearStart = $now->copy()->startOfYear();
        $currentYearEnd   = $now->copy()->endOfYear();
        $this->reports[] = [
            'title' => 'This Year (' . $now->year . ')',
            'data'  => $this->buildStats($person->id, $currentYearStart, $currentYearEnd),
        ];

        // Last Year
        $lastYear = $now->year - 1;
        $lastYearStart = Carbon::create($lastYear)->startOfYear();
        $lastYearEnd   = Carbon::create($lastYear)->endOfYear();
        $this->reports[] = [
            'title' => "Last Year ({$lastYear})",
            'data'  => $this->buildStats($person->id, $lastYearStart, $lastYearEnd),
        ];
    }

    /**
     * 'Egen sykdom' is "Own illness", and stats is hardcoded for Norwegian laws
     * @TODO Make stats more customizable.
     * @TODO Make filter/tabs for different periods.
     */
    private function buildStats(int $personId, Carbon $start, Carbon $end): array
    {
        $absences = Absence::where('person_id', $personId)
            ->whereBetween('start_date', [$start, $end])
            ->with('absenceType')
            ->get();

        $grouped = $absences->groupBy('absence_type_id');

        $results = [];
        foreach ($grouped as $typeId => $items) {
            $typeName = $items->first()->absenceType->name ?? 'Unknown';

            // If this is own illness, we want to split them further by is_medically_certified
            $own_illness = config('open_manage.absence.default_own_illness_name');
            if ($typeName === $own_illness) {
                // Group again by is_medically_certified
                $subGroups = $items->groupBy('is_medically_certified');

                foreach ($subGroups as $certified => $subItems) {
                    $count = $subItems->count();
                    $days  = $subItems->sum(function ($absence) {
                        $endDate = $absence->end_date ?? now();
                        return $endDate->diffInDays($absence->start_date) + 1;
                    });

                    $label = $certified
                        ? "$own_illness (doctor-certified)"
                        : "$own_illness (self-certified)";

                    $results[] = [
                        'type'  => $label,
                        'count' => $count,
                        'days'  => $days,
                    ];
                }
            } else {
                $count = $items->count();
                $days  = $items->sum(function ($absence) {
                    $endDate = $absence->end_date ?? now();
                    return $endDate->diffInDays($absence->start_date) + 1;
                });

                $results[] = [
                    'type'  => $typeName,
                    'count' => $count,
                    'days'  => $days,
                ];
            }
        }

        return $results;
    }
}
