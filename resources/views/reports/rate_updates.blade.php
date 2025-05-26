<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hourly Rate Updates') }}
        </h2>
    </x-slot>

    <div class="py-12 lg:mx-20 mx-4 md:mx-10">
        {{-- Filters & Export --}}
        <div class="flex items-center justify-between mb-6">
            <form method="GET" action="{{ route('reports.rateupdates') }}" class="flex items-center gap-3">
                <input type="number" name="year" min="2000" max="{{ now()->year }}"
                       value="{{ $year }}" class="w-24 border rounded p-2" />
                <input type="hidden" name="export" value="">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Filter
                </button>
            </form>
            <div class="flex gap-2">
                <a href="{{ route('reports.rateupdates', array_merge(request()->only(['year','start','end']), ['export'=>'excel'])) }}"
                   class="flex items-center gap-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    <!-- Excel icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V7.414A2 2 0 0016.586 6L12 1.414A2 2 0 0010.586 1H4zM11 2.414L15.586 7H12a1 1 0 01-1-1V2.414z"/>
                        <path d="M8.5 11a.5.5 0 01.5.5V13h1.5a.5.5 0 010 1H9v1.5a.5.5 0 01-1 0V14H6.5a.5.5 0 010-1H8v-1.5a.5.5 0 01.5-.5z"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('reports.rateupdates', array_merge(request()->only(['year','start','end']), ['export'=>'pdf'])) }}"
                   class="flex items-center gap-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    <!-- PDF icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6 2a1 1 0 00-1 1v14l7-4 7 4V3a1 1 0 00-1-1H6z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white shadow rounded-lg overflow-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Updated At</th>
                    <th class="px-6 py-3">Previous Rate</th>
                    <th class="px-6 py-3">New Rate</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($rows as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $r->name }}</td>
                        <td class="px-6 py-4">{{ $r->updated_at }}</td>
                        <td class="px-6 py-4">${{ number_format($r->previous_rate,2) }}</td>
                        <td class="px-6 py-4">${{ number_format($r->new_rate,2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    </div>
</x-app-layout>
