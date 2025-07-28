<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Annual hours worked per contractor') }}
        </h2>
    </x-slot>

    <div class="py-12 lg:mx-20 mx-4 md:mx-10">
        <div class="flex items-center justify-between mb-6">
            <form method="GET" action="{{ route('reports.annualHours') }}" class="flex items-center gap-4">
                <label for="year">Year:</label>
                <select name="year" id="year" class="border rounded pl-2 pr-8 py-2">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @if($year == $y) selected @endif>{{ $y }}</option>
                    @endfor
                </select>
                <label for="project_id">Project:</label>
                <select name="project_id" id="project_id" class="border rounded pl-2 pr-8 py-2 truncate">
                    <option value="">All</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @if($selectedProject == $project->id) selected @endif>{{ $project->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Filter</button>
            </form>
            <div class="flex gap-2">
                <a href="{{ route('reports.annualHours', array_merge(request()->only(['year','project_id']), ['export'=>'excel'])) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Excel</a>
                <a href="{{ route('reports.annualHours', array_merge(request()->only(['year','project_id']), ['export'=>'pdf'])) }}"
                   class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">PDF</a>
            </div>
        </div>

        <div class="bg-white p-6 rounded shadow overflow-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50 sticky top-0">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    @for($m = 1; $m <= 12; $m++)
                        <th class="px-4 py-2">{{ \Carbon\Carbon::create()->month($m)->locale('en')->isoFormat('MMM') }}</th>
                    @endfor
                    <th class="px-4 py-2">Total</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($data as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap">{{ $row['name'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $row['email'] }}</td>
                        @php $total = 0; @endphp
                        @for($m = 1; $m <= 12; $m++)
                            @php $h = $row['months'][$m] ?? 0; $total += $h; @endphp
                            <td class="px-4 py-2 text-right @if($h < 160 && $h > 0) bg-red-100 text-red-700 @elseif($h >= 160) bg-green-100 text-green-700 @endif">
                                {{ $h > 0 ? number_format($h,2) : '-' }}
                            </td>
                        @endfor
                        <td class="px-4 py-2 font-bold text-right @if($total < 160*12 && $total > 0) bg-red-100 text-red-700 @elseif($total >= 160*12) bg-green-100 text-green-700 @endif">
                            {{ number_format($total,2) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
