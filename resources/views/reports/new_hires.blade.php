<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Hires Report') }}
        </h2>
    </x-slot>

    {{-- Datepicker deps --}}
    <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <div class="py-12 lg:mx-20 mx-4 md:mx-10">
        <div class="flex items-center justify-between mb-6">
            <form method="GET" action="{{ route('reports.newHires') }}" class="flex items-center gap-4">
                <input type="hidden" name="start" id="start" value="{{ $start }}">
                <input type="hidden" name="end"   id="end"   value="{{ $end }}">
                <div class="relative max-w-sm w-full">
                    <input id="new_hires_range" type="text"
                           class="bg-white border rounded p-2 w-full cursor-pointer"
                           value="{{ $start }} - {{ $end }}" readonly />
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Filter
                </button>
            </form>
            <div class="flex gap-2">
                <a href="{{ route('reports.newHires', array_merge(request()->only(['start','end']), ['export'=>'excel'])) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Excel</a>
                <a href="{{ route('reports.newHires', array_merge(request()->only(['start','end']), ['export'=>'pdf'])) }}"
                   class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">PDF</a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Country</th>
                    <th class="px-6 py-3">Position</th>
                    <th class="px-6 py-3">Start Date</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rows as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $r->name }}</td>
                        <td class="px-6 py-4">{{ $r->country }}</td>
                        <td class="px-6 py-4">{{ $r->position }}</td>
                        <td class="px-6 py-4">{{ $r->start_date }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 italic">
                            No records found for the selected date range.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    </div>

    {{-- init daterangepicker --}}
    <script>
        $(function(){
            // parse URL or default
            let start = moment("{{ $start }}","YYYY/MM/DD"),
                end   = moment("{{ $end }}","YYYY/MM/DD");
            $('#new_hires_range').daterangepicker({
                startDate: start,
                endDate:   end,
                locale: { format: 'YYYY/MM/DD' }
            }, function(s,e){
                $('#start').val(s.format('YYYY/MM/DD'));
                $('#end').val(  e.format('YYYY/MM/DD'));
            });
        });
    </script>
</x-app-layout>
