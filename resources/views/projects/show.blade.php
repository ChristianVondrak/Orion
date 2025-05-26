<x-app-layout>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Project Detail') }}
            </h2>
            <a href="{{ route('project.invoices.preview', $project->id) }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                {{ __('Corte mensual') }}
            </a>
        </div>
    </x-slot>

    <div class="flex justify-between items-center">
        <div class="flex-initial">
            <!-- Left content div -->
            <div class="flex flex-col justify-center items-start lg:mx-20 mx-4 md:mx-10 pt-12 pb-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $project->name  }}
                </h2>
                <p class="my-4 text-lg text-gray-500">
                    {{$project->description}}
                </p>
            </div>
        </div>

        <!-- Right content div -->
        <div class="flex-initial">
            <form action="{{route('project.show',['id'=>$project->id])}}" method="GET">
                <div class="flex  items-center justify-end  lg:mx-20 mx-4 md:mx-10">
                    <input type="hidden" name="end" id="end" />
                    <input type="hidden" name="start" id="start" />
                    <div class="relative max-w-sm min-w-52">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                            </svg>
                        </div>
                        <input id="kt_daterangepicker_4" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date">
                    </div>
                    <button type="submit" id="btnEnviar" class="m-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Search
                    </button>
                    <button type="button" id="btnPlannedHours" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                        {{ __('Planned Hours') }}
                    </button>
                </div>
            </form>

            @php
                $weekStart = today()->startOfWeek();
                $default   = \App\Models\PlannedProjectHour::getForWeek($project->id, $weekStart);
            @endphp
            
            <!-- Modal para horas planificadas -->
            <div id="plannedHoursModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-semibold mb-4">Horas planificadas (semana {{ $weekStart->format('Y-m-d') }})</h3>
                        <form action="{{ route('projects.planned-hours.store', $project) }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Horas planificadas</label>
                                <input type="number" step="0.01" min="0" name="planned_hours"
                                       value="{{ old('planned_hours', $default) }}"
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div class="flex justify-end space-x-3 mt-4">
                                <button type="button" id="closeModal"
                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                                    Cancelar
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pb-12 flex flex-col justify-center items-center lg:mx-20 mx-4 md:mx-10">
        <div class="align-middle inline-block min-w-full overflow-hidden sm:rounded-lg border-gray-200 shadow">
            <table class="table-fixed w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            Users
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            Total Hours
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            Remaining Hours
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            Total profits
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @foreach($project->projectUsers as $projectUser)
                        @php
                            $user = $projectUser->worknapUser;
                        @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <a href="{{route('user.show',['id'=>$user->id])}}" class="text-indigo-600 hover:text-indigo-900">
                                {{$user->first_name }} {{$user->last_name}}
                            </a>
                        </td>
                        {{-- timmings --}}
                        @if ($user->timings_count_in_hours < $MonthHoursGoal - $DayHours)
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-red-600">
                                {{$user->timings_count_in_hours}} H
                            </td>
                        @elseif ($user->timings_count_in_hours < $MonthHoursGoal)
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-yellow-600">
                                {{$user->timings_count_in_hours}} H
                            </td>
                        @else
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-green-600">
                                {{$user->timings_count_in_hours}} H
                            </td>
                        @endif
                        {{--Remaining Hours--}}
                        @if($MonthHoursGoal - $user->timings_count_in_hours > 0)
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                {{round($MonthHoursGoal - $user->timings_count_in_hours, 2)}} H
                            </td>
                        @else
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                0 H
                            </td>
                        @endif

                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            {{$user->totalProfits($user->timings_count_in_hours,$projectUser->hourly_rate)}}$
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<script type="text/javascript" src="{{ asset('js/datepicker.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('plannedHoursModal');
        const btn = document.getElementById('btnPlannedHours');
        const closeBtn = document.getElementById('closeModal');

        btn.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });

        closeBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });

        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
