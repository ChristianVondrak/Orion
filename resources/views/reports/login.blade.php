<x-app-layout>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Login por Profesional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-6">
                        <form method="GET" action="{{ route('reports.login') }}" class="flex items-center space-x-4">
                            <div class="flex-1">
                                @php
                                    $startDate = $start instanceof \Carbon\Carbon ? $start : \Carbon\Carbon::parse($start);
                                    $endDate = $end instanceof \Carbon\Carbon ? $end : \Carbon\Carbon::parse($end);
                                @endphp
                                <input type="text" name="daterange" class="form-input rounded-md shadow-sm mt-1 block w-full"
                                       value="{{ $startDate->format('m/d/Y') }} - {{ $endDate->format('m/d/Y') }}" />
                                <input type="hidden" name="start" value="{{ $startDate->format('Y-m-d') }}" />
                                <input type="hidden" name="end" value="{{ $endDate->format('Y-m-d') }}" />
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    {{ __('Filtrar') }}
                                </button>
                                <a href="{{ route('reports.login', ['export' => 'excel'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                    {{ __('Excel') }}
                                </a>
                                <a href="{{ route('reports.login', ['export' => 'pdf'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                    {{ __('PDF') }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Nombre') }}
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Email') }}
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Primer Login') }}
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Último Login') }}
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Hora Promedio Inicio') }}
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Retrasos') }}
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Minutos Retrasados') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($rows as $row)
                                <tr class="{{ $row->is_delayed ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        {{ $row->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        {{ $row->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        <div>{{ $row->first_login }}</div>
                                        <div class="{{ strtotime($row->first_login_time) > strtotime('9:00 AM') ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                            {{ $row->first_login_time }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        <div>{{ $row->last_login }}</div>
                                        <div>{{ $row->last_login_time }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap {{ strtotime($row->average_start_time) > strtotime('9:00 AM') ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                        {{ $row->average_start_time }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap {{ $row->delays_count > 0 ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                        {{ $row->delays_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap {{ $row->is_severely_delayed ? 'text-red-600 font-bold' : ($row->total_delay_minutes > 0 ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ $row->total_delay_minutes_formatted }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 italic">
                                        No records found for the selected date range.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $rows->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'MM/DD/YYYY'
                }
            }, function(start, end, label) {
                $('input[name="start"]').val(start.format('YYYY-MM-DD'));
                $('input[name="end"]').val(end.format('YYYY-MM-DD'));
            });
        });
    </script>
</x-app-layout>
