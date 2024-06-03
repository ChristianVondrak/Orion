<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/datepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project Detail') }}
        </h2>
    </x-slot>
    <div class="flex flex-col justify-center items-start lg:mx-20 mx-4 md:mx-10 pt-12 pb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $project->name  }}
        </h2>
        <p class="my-4 text-lg text-gray-500"> {{$project->description}}</p>
    </div>
    <form action="{{route('project.show',['id'=>$project->id])}}" method="GET">
        <div class="flex  items-center justify-end  lg:mx-20 mx-4 md:mx-10">
            <div class="relative col-md-6">

                <input class="form-control form-control-solid" placeholder="Pick date rage" id="kt_daterangepicker_4" name="start"/>
                <input class="form-control form-control-solid" type="hidden" name="end"/>

                <script>
                    var start = moment().startOf("month");
                    var end = moment().endOf("month");

                    function cb(start, end) {
                        $("#kt_daterangepicker_4").html(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
                    }

                    $("#kt_daterangepicker_4").daterangepicker({
                        startDate: start,
                        endDate: end,
                        ranges: {
                            "Today": [moment(), moment()],
                            "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                            "Last 7 Days": [moment().subtract(6, "days"), moment()],
                            "Last 30 Days": [moment().subtract(29, "days"), moment()],
                            "This Month": [moment().startOf("month"), moment().endOf("month")],
                            "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
                        }
                    }, cb);

                    $('input[name="start"]').on('apply.daterangepicker', function(ev, picker) {
                        $(this).val(picker.startDate.format('YYYY/MM/DD'));
                        $('input[name="end"]').val(picker.endDate.format('YYYY/MM/DD'));
                    });

                    cb(start, end);
                </script>


                {{--        <div date-rangepicker class="flex  items-center justify-end  lg:mx-20 mx-4 md:mx-10">--}}
                {{--            <div class="relative col-md-6">--}}
                {{--                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">--}}
                {{--                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"--}}
                {{--                         xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">--}}
                {{--                        <path--}}
                {{--                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>--}}
                {{--                    </svg>--}}
                {{--                </div>--}}
                {{--                <input datepicker-format="yyyy/mm/dd" name="start" type="text"--}}
                {{--                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 "--}}
                {{--                       placeholder="Select date start">--}}
                {{--            </div>--}}
                {{--            <span class="mx-4 text-gray-500">to</span>--}}
                {{--            <div class="relative">--}}
                {{--                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">--}}
                {{--                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"--}}
                {{--                         xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">--}}
                {{--                        <path--}}
                {{--                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>--}}
                {{--                    </svg>--}}
                {{--                </div>--}}
                {{--                <input datepicker-format="yyyy/mm/dd" name="end" type="text"--}}
                {{--                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  "--}}
                {{--                       placeholder="Select date end">--}}
                {{--            </div>--}}
                <button type="submit" id="btnEnviar"
                        class="m-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Default
                </button>
            </div>
        </div>
    </form>
    {{-- Margin in Y 48px --}}
    <div class="py-12 flex flex-col justify-center items-center lg:mx-20 mx-4 md:mx-10">

        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">

            <table class="table-fixed w-full">
                <thead>
                <tr>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Users
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Total Hours
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Rating
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Total profits
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($project->projectUsers as $projectUser)
                        @php
                            $user = $projectUser->worknapUser;
                            if ($user->timings_count_in_hours->totalSeconds == 0){
                                continue;
                            }
                        @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <a href="#"
                               class="text-indigo-600 hover:text-indigo-900">{{$user->first_name }} {{$user->last_name}}</a>
                        </td>
                        {{--              timmings            --}}
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            {{$user->timings_count_in_hours}}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            {{$projectUser->hourly_rate}} $
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            {{$user->total_profits($user->timings_count_in_hours,$projectUser->hourly_rate)}}$
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{--    Scripts for Flowbite Datepicker  --}}
    <script src="{{ asset('build/assets/tailwind_datepicker.js') }}"></script>
    <script src="{{ asset('build/assets/datepicker.min.js') }}"></script>
</x-app-layout>
