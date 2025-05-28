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
            <a href="{{ route('projects.invoices.preview', $project->id) }}"
               class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                </svg>
                {{ __('Monthly Report') }}
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
            <form action="{{route('projects.show',['id'=>$project->id])}}" method="GET">
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
                    <button type="submit" id="btnEnviar" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        Search
                    </button>
                    <button type="button" id="btnPlannedHours" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Planned Hours') }}
                    </button>
                </div>
            </form>

            @php
                $weekStart = today()->startOfWeek();
                $default   = \App\Models\PlannedProjectHour::getForWeek($project->id, $weekStart);
            @endphp

            <!-- Modal para horas planificadas -->
            <div id="plannedHoursModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[1100]">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-semibold mb-4">Planned Hours (Week {{ $weekStart->format('Y-m-d') }})</h3>
                        <form action="{{ route('projects.planned-hours.store', $project) }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Planned Hours</label>
                                <input type="number" step="0.01" min="0" name="planned_hours"
                                       value="{{ old('planned_hours', $default) }}"
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-[#2563EB] focus:border-[#2563EB]" />
                            </div>
                            <div class="flex justify-end space-x-3 mt-4">
                                <button type="button" id="closeModal" class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Save
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
                            Total Profits
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @foreach($project->projectUsers as $projectUser)
                        @php
                            $user = $projectUser->worksnapUser;
                        @endphp
                        @if($user)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <a href="{{route('user.show',['id'=>$user->id])}}" class="text-indigo-600 hover:text-indigo-900">
                                        {{$user->first_name }} {{$user->last_name}}
                                        <span class="text-sm text-gray-500">
                                            ({{ $projectUser->payment_type === 'flat' ? 'Flat Rate' : 'Hourly Rate' }})
                                        </span>
                                    </a>
                                </td>
                                @if($projectUser->payment_type === 'hourly')
                                    {{-- timmings para pago por hora --}}
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
                                @else
                                    {{-- Para pago fijo --}}
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-gray-500">
                                        N/A
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-gray-500">
                                        N/A
                                    </td>
                                @endif

                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    {{$user->totalProfits($user->timings_count_in_hours, $projectUser)}}$
                                </td>
                            </tr>
                        @endif
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
        const backdrop = document.querySelector('.flex.justify-between.items-center');

        function toggleModal(show) {
            if (show) {
                modal.classList.remove('hidden');
                backdrop.style.zIndex = '1000';
            } else {
                modal.classList.add('hidden');
                backdrop.style.zIndex = '';
            }
        }

        btn.addEventListener('click', () => toggleModal(true));
        closeBtn.addEventListener('click', () => toggleModal(false));

        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                toggleModal(false);
            }
        });

        // Cerrar con Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                toggleModal(false);
            }
        });
    });
</script>
