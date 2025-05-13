<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@php use App\Enums\Country; @endphp

<x-app-layout>
    <x-slot name="header">
        {{-- Breadcrumbs --}}
        <nav class="text-sm text-gray-500 mb-1" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home.index') }}" class="hover:underline">Home</a>
                    <span class="mx-2">/</span>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('user.index') }}" class="hover:underline">Users</a>
                    <span class="mx-2">/</span>
                </li>
                <li class="flex items-center text-gray-700">
                    User Detail
                </li>
            </ol>
        </nav>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Detail') }}
        </h2>
    </x-slot>

    <div class="mt-6 lg:mx-20 mx-4 md:mx-10">

        {{-- Alerts --}}
        @if(session('success'))
            <x-alert type="success" :message="session('success')" duration="4000" />
        @endif
        @if(session('error'))
            <x-alert type="error" :message="session('error')" duration="4000" />
        @endif

        {{-- User Info Card --}}
        <div class="bg-white p-6 rounded shadow flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $user->first_name }} {{ $user->last_name }}
                </h2>
                <p class="my-4 text-lg text-gray-500">
                    {{ $user->email }}
                </p>
            </div>
            <div class="flex items-center gap-4 mb-4">
                {{-- Botón para mostrar/ocultar el form de detalles --}}
                <button id="toggle-detail-form"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    {{ $user->detail ? 'Edit Details' : 'Add Details' }}
                </button>

                {{-- Nuevo botón para actualizar tarifas --}}
                <button id="toggle-rate-form"
                        class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    Update Hourly Rates
                </button>

                <button id="toggle-terminate-form"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Terminate Contract
                </button>
            </div>
        </div>

        {{-- UserDetails Form --}}
        <div id="detail-form" class="hidden bg-white p-6 rounded shadow mb-6">
            <form action="{{ $user->detail
                          ? route('user.details.update', $user->id)
                          : route('user.details.store',  $user->id) }}"
                  method="POST">
                @csrf
                @if($user->detail) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Country</label>
                        <select name="country"
                                class="mt-1 block w-full border rounded p-2">
                            <option value="" disabled {{ old('country', $user->detail->country ?? '') === null ? 'selected' : '' }}>
                                — select country —
                            </option>
                            @foreach(Country::cases() as $c)
                                <option value="{{ $c->value }}"
                                    {{ old('country', $user->detail->country ?? '') === $c->value ? 'selected' : '' }}>
                                    {{ $c->value }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Phone</label>
                        <input name="phone" type="text"
                               value="{{ old('phone', $user->detail->phone ?? '') }}"
                               class="mt-1 block w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Position</label>
                        <input name="position" type="text"
                               value="{{ old('position', $user->detail->position ?? '') }}"
                               class="mt-1 block w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Gender</label>
                        <select name="gender"
                                class="mt-1 block w-full border rounded p-2">
                            <option value="" disabled {{ isset($user->detail) ? '' : 'selected' }}>
                                — select —
                            </option>
                            @foreach(['male'=>'Male','female'=>'Female','other'=>'Other'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('gender', $user->detail->gender ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Marital Status</label>
                        <select name="marital_status"
                                class="mt-1 block w-full border rounded p-2">
                            <option value="" disabled {{ isset($user->detail) ? '' : 'selected' }}>
                                — select —
                            </option>
                            @foreach([
                                'single'=>'Single',
                                'married'=>'Married',
                                'divorced'=>'Divorced',
                                'widowed'=>'Widowed'
                            ] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('marital_status', $user->detail->marital_status ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Date of Birth</label>
                        <input name="date_of_birth" type="date"
                               value="{{ old('date_of_birth', optional($user->detail)->date_of_birth) }}"
                               class="mt-1 block w-full border rounded p-2" />
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        {{ $user->detail ? 'Update Details' : 'Save Details' }}
                    </button>
                </div>
            </form>
        </div>

        <div id="terminate-form" class="hidden bg-gray-50 p-6 rounded mt-4 border border-gray-200">
            <form action="{{ route('user.termination.store', $user->id) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Termination Date</label>
                        <input name="termination_date" type="date"
                               value="{{ old('termination_date') }}"
                               class="mt-1 block w-full border rounded p-2" />
                        @error('termination_date')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Reason</label>
                        <input name="reason" type="text"
                               value="{{ old('reason') }}"
                               class="mt-1 block w-full border rounded p-2" />
                        @error('reason')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Confirm
                    </button>
                </div>
            </form>
        </div>


        {{-- Formulario de actualización de tarifas --}}
        <div id="rate-form" class="hidden bg-gray-50 p-6 rounded mt-4 border border-gray-200">
            <form action="{{ route('user.rate.bulkUpdate', $user->id) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($projectUsers as $pu)
                        <div class="flex items-center gap-2">
                            <span class="w-1/2">{{ $pu->project->name }}</span>
                            <input type="text"
                                   name="rates[{{ $pu->project_id }}]"
                                   value="{{ old('rates.'.$pu->project_id, $pu->hourly_rate) }}"
                                   class="w-1/2 border rounded p-1 text-right" />
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Save All Rates
                    </button>
                </div>
            </form>
        </div>

        {{-- Mostrar detalles si existen --}}
        @if($user->detail)
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold mb-4">Additional Information</h3>
                <div class="flex flex-wrap -mx-3">
                    <div class="px-3 w-full sm:w-1/2 lg:w-1/3 mb-4">
                        <p><strong>Country:</strong> {{ $user->detail->country }}</p>
                    </div>
                    <div class="px-3 w-full sm:w-1/2 lg:w-1/3 mb-4">
                        <p><strong>Phone:</strong> {{ $user->detail->phone }}</p>
                    </div>
                    <div class="px-3 w-full sm:w-1/2 lg:w-1/3 mb-4">
                        <p><strong>Position:</strong> {{ $user->detail->position }}</p>
                    </div>
                    <div class="px-3 w-full sm:w-1/2 lg:w-1/3 mb-4">
                        <p><strong>Gender:</strong> {{ ucfirst($user->detail->gender) }}</p>
                    </div>
                    <div class="px-3 w-full sm:w-1/2 lg:w-1/3 mb-4">
                        <p><strong>Marital Status:</strong> {{ ucfirst($user->detail->marital_status) }}</p>
                    </div>
                    <div class="px-3 w-full sm:w-1/2 lg:w-1/3 mb-4">
                        <p><strong>Date of Birth:</strong> {{ $user->detail->date_of_birth }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="flex justify-between items-center">
        {{--        Droprown project filter        --}}
        <div class="relative inline-block text-left lg:mx-20 mx-4 md:mx-10">
            <div>
                <button type="button" class="h-full inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button" aria-expanded="true" aria-haspopup="true">
                    Projects
                    <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="absolute z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" id="dropdown-menu">
                <div class="py-1" role="none">
                    @foreach ($projects as $project)
                        <a href="#" class="dropdown-project-item block px-4 py-2 text-sm text-gray-700" role="menuitem" data-project-id="{{ $project->id }}">
                            {{ $project->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <form id="filter-form" method="GET" action="{{ route('user.show', $user->id) }}" class="hidden">
            @if(request()->has('start') && request()->has('end'))
                <input type="hidden" name="start" value="{{ request('start') }}">
                <input type="hidden" name="end" value="{{ request('end') }}">
            @endif
            <input type="hidden" name="project_id" id="project-id">
        </form>

        <!-- Right content -- Datepicker -->
        <div class="flex-initial">
            <form id="datepicker-form" {{route('user.show',['id'=>$user->id])}}" method="GET">
                <div class="flex  items-center justify-end  lg:mx-20 mx-4 md:mx-10">
                    <input type="hidden" name="end" id="end" />
                    <input type="hidden" name="start" id="start" />
                    @if(request()->has('project_id'))
                        <input type="hidden" name="project_id" id="datepicker-project-id" value="{{ request('project_id') }}" />
                    @endif
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
                </div>
            </form>
        </div>
    </div>

    <div class="pb-12 flex flex-col justify-center items-center lg:mx-20 mx-4 md:mx-10">
        <div class="align-middle inline-block min-w-full overflow-hidden sm:rounded-lg border-gray-200 shadow">
            <table class="table-auto w-full">
                <thead>
                <tr>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Project
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Task
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Activity Level
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Total Hours
                    </th>
                </tr>
                </thead>

                <tbody class="bg-white">
                @foreach ($timmingsByDay as $date => $data)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                {{$date }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            @foreach ($data['projects'] as $project)
                                {{ $project->name }}@if (!$loop->last) - @endif
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            @foreach ($data['task_names'] as $task_name)
                                {{ $task_name }}@if (!$loop->last) - @endif
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-left">
                            {{ round($data['average_activity_level'], 2)*10 }}%
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            {{ gmdate('H:i', $data['total_seconds']) }} Hours
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><strong>Total</strong></td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200" colspan="2"></td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><strong>{{ round($overallAverageActivityLevel, 2)*10 }}%</strong></td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"> <strong>{{$totalTime}} Hours</strong></td>
                </tr>
            </table>
        </div>
    </div>
</x-app-layout>

<script type="text/javascript" src="{{ asset('js/datepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/dropdown.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/user-details.js') }}"></script>
