<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity Index Report') }}
        </h2>
    </x-slot>

    {{-- Datepicker Dependencies --}}
    <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <div class="mt-6 lg:mx-20 mx-4 md:mx-10">
        <div class="flex items-center justify-between mb-6">
            {{-- Filtro de rango (lado izquierdo) --}}
            <form id="datepicker-form"
                  method="GET"
                  action="{{ route('reports.activity') }}"
                  class="flex items-center gap-4">
                <input type="hidden" name="start" id="start" value="{{ $start }}">
                <input type="hidden" name="end"   id="end"   value="{{ $end }}">
                <div class="relative max-w-sm w-full">
                    <input
                        id="report_date_range"
                        type="text"
                        class="bg-white border border-gray-300 rounded-lg p-2 w-full cursor-pointer"
                        value="{{ $start }} - {{ $end }}"
                        readonly
                    />
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none">
                    Filter
                </button>
            </form>

            {{-- Botones de exportación (lado derecho) --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.activity', array_merge(request()->only(['start','end']), ['export'=>'excel'])) }}"
                   class="flex items-center gap-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    <!-- Icono Excel -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V7.414A2 2 0 0016.586 6L12 1.414A2 2 0 0010.586 1H4zM11 2.414L15.586 7H12a1 1 0 01-1-1V2.414z"/>
                        <path d="M8.5 11a.5.5 0 01.5.5V13h1.5a.5.5 0 010 1H9v1.5a.5.5 0 01-1 0V14H6.5a.5.5 0 010-1H8v-1.5a.5.5 0 01.5-.5z"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('reports.activity', array_merge(request()->only(['start','end']), ['export'=>'pdf'])) }}"
                   class="flex items-center gap-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    <!-- Icono PDF -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6 2a1 1 0 00-1 1v14l7-4 7 4V3a1 1 0 00-1-1H6z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>

        {{-- Tabla simplificada --}}
        <div class="bg-white p-6 rounded shadow overflow-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50 sticky top-0">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Activity Index</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($rows as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $r->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $r->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span @class([
                                'px-2 inline-flex text-xs font-semibold rounded-full',
                                'bg-green-100 text-green-800' => $r->activity_index >= 75,
                                'bg-red-100   text-red-800'   => $r->activity_index < 75,
                            ])>
                                {{ $r->activity_index }}%
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{-- Paginación --}}
        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    </div>

    {{-- Inicializar Datepicker --}}
    <script src="{{ asset('js/reports.js') }}"></script>
</x-app-layout>
