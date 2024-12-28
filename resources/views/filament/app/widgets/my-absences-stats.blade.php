<x-filament::widget>
    <x-filament::card>
        <div class="space-y-6">
            @forelse($this->reports as $report)
                <div>
                    <h2 class="text-lg font-bold mb-2">
                        {{ $report['title'] }}
                    </h2>

                    @if (count($report['data']) === 0)
                        <p class="text-gray-600">No absences in this period.</p>
                    @else
                        <table class="table-auto w-full text-sm">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="py-1 px-2 text-left">Type</th>
                                <th class="py-1 px-2 text-left">Occurrences</th>
                                <th class="py-1 px-2 text-left">Total Days</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($report['data'] as $row)
                                <tr class="border-b">
                                    <td class="py-1 px-2">{{ $row['type'] }}</td>
                                    <td class="py-1 px-2">{{ $row['count'] }}</td>
                                    <td class="py-1 px-2">{{ $row['days'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @empty
                <div>
                    <h2 class="text-lg font-bold mb-2">
                        My Absences Stats
                    </h2>
                    <p class="text-gray-600">No data found or not linked to a person.</p>
                </div>
            @endforelse
        </div>
    </x-filament::card>
</x-filament::widget>
