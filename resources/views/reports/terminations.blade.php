{{-- resources/views/reports/terminations.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Destitutions Report') }}
        </h2>
    </x-slot>

    <div class="py-12 lg:mx-20 mx-4 md:mx-10">
        {{-- Datepicker & Export Buttons --}}
        <div class="flex items-center justify-between mb-6">
            <form method="GET"
                  action="{{ route('reports.terminations') }}"
                  class="flex items-center gap-4">
                <input type="hidden" name="start" id="start" value="{{ $start }}">
                <input type="hidden" name="end"   id="end"   value="{{ $end }}">
                <div class="relative max-w-sm w-full">
                    <input id="terminations_date_range"
                           type="text"
                           class="bg-white border border-gray-300 rounded-lg p-2 w-full cursor-pointer"
                           value="{{ $start }} - {{ $end }}"
                           readonly />
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Filter
                </button>
            </form>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.terminations', array_merge(request()->only(['start','end']), ['export'=>'excel'])) }}"
                   class="flex items-center gap-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V7.414A2 2 0 0016.586 6L12 1.414A2 2 0 0010.586 1H4zM11 2.414L15.586 7H12a1 1 0 01-1-1V2.414z"/>
                        <path d="M8.5 11a.5.5 0 01.5.5V13h1.5a.5.5 0 010 1H9v1.5a.5.5 0 01-1 0V14H6.5a.5.5 0 010-1H8v-1.5a.5.5 0 01.5-.5z"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('reports.terminations', array_merge(request()->only(['start','end']), ['export'=>'pdf'])) }}"
                   class="flex items-center gap-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6 2a1 1 0 00-1 1v14l7-4 7 4V3a1 1 0 00-1-1H6z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="bg-white shadow rounded-lg overflow-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Country</th>
                    <th class="px-6 py-3">Department</th>
                    <th class="px-6 py-3">Position</th>
                    <th class="px-6 py-3">Start Date</th>
                    <th class="px-6 py-3">Termination Date</th>
                    <th class="px-6 py-3">Reason</th>
                    <th class="px-6 py-3">Tenure</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($rows as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $r->name }}</td>
                        <td class="px-6 py-4">{{ $r->country }}</td>
                        <td class="px-6 py-4">{{ $r->department }}</td>
                        <td class="px-6 py-4">{{ $r->position }}</td>
                        <td class="px-6 py-4">{{ $r->start_date }}</td>
                        <td class="px-6 py-4">{{ $r->termination_date }}</td>
                        <td class="px-6 py-4">{{ $r->reason }}</td>
                        <td class="px-6 py-4">{{ $r->tenure }}</td>
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

    {{-- Datepicker & Moment.js --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script>
        $(function() {
            let start = moment("{{ $start }}","YYYY/MM/DD"),
                end   = moment("{{ $end }}","YYYY/MM/DD");

            $('#terminations_date_range').daterangepicker({
                startDate: start,
                endDate:   end,
                ranges: {
                    'Today': [moment(), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
                },
                locale: { format: 'YYYY/MM/DD' }
            }, function(s, e) {
                $('#start').val(s.format('YYYY/MM/DD'));
                $('#end').val(  e.format('YYYY/MM/DD'));
            });
        });
    </script>
</x-app-layout>

