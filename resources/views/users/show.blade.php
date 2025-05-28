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
                    <a href="{{ route('home') }}" class="hover:underline">Home</a>
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

    {{-- Backdrop global --}}
    <div id="global-backdrop"></div>

    {{-- Meta CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Cargamos ambos CSS y JS via Vite --}}
{{--    @vite([
      'resources/css/user-detail.css',
      'resources/js/user-detail.js'
    ])--}}

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
                        class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    {{ $user->detail ? 'Edit Details' : 'Add Details' }}
                </button>

                {{-- Nuevo botón para actualizar tarifas --}}
                <button id="toggle-rate-form"
                        class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    Update Rates
                </button>

                <button id="toggle-terminate-form"
                        class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
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
                            class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
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
                            class="btn btn-primary">
                        Confirm
                    </button>
                </div>
            </form>
        </div>


        {{-- Formulario de tarifas --}}
        <div id="rate-form" class="hidden">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-[1100]"></div>
            <div class="fixed inset-0 z-[1110] overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-6">
                        <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                            <button type="button"
                                    class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:ring-offset-2"
                                    onclick="document.getElementById('rate-form').classList.add('hidden')">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('user.rate.bulkUpdate', $user->id) }}" method="POST">
                            @csrf
                            <div class="space-y-6">
                                <div class="border-b border-gray-200 pb-4">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Rate Configuration</h3>
                                    <p class="mt-1 text-sm text-gray-500">Update payment type and rates for each project.</p>
                                </div>

                                <div class="space-y-6">
                                    @foreach($projectUsers as $pu)
                                        <div class="bg-gray-50 rounded-lg p-6 transition-all duration-300 hover:shadow-md">
                                            <h4 class="font-medium text-gray-900 mb-4 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#2563EB]" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $pu->project->name }}
                                            </h4>

                                            <div class="space-y-4">
                                                {{-- Tipo de pago --}}
                                                <div class="flex flex-wrap gap-4">
                                                    <label class="relative flex cursor-pointer items-center rounded-full p-3 hover:bg-gray-100" for="hourly-{{ $pu->project_id }}">
                                                        <input type="radio"
                                                               name="payment_types[{{ $pu->project_id }}]"
                                                               id="hourly-{{ $pu->project_id }}"
                                                               value="hourly"
                                                               {{ $pu->payment_type === 'hourly' ? 'checked' : '' }}
                                                               class="relative h-5 w-5 cursor-pointer appearance-none rounded-full border-2 border-[#2563EB] transition-all checked:border-[#2563EB] checked:bg-[#2563EB] hover:border-[#2563EB]/80"
                                                               onchange="toggleRateInputs({{ $pu->project_id }})">
                                                        <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 transition-opacity peer-checked:opacity-100">
                                                            <div class="absolute left-[7px] top-[7px] h-2.5 w-2.5 rounded-full bg-white"></div>
                                                        </div>
                                                        <span class="ml-2 text-gray-700">Hourly Payment</span>
                                                    </label>

                                                    <label class="relative flex cursor-pointer items-center rounded-full p-3 hover:bg-gray-100" for="flat-{{ $pu->project_id }}">
                                                        <input type="radio"
                                                               name="payment_types[{{ $pu->project_id }}]"
                                                               id="flat-{{ $pu->project_id }}"
                                                               value="flat"
                                                               {{ $pu->payment_type === 'flat' ? 'checked' : '' }}
                                                               class="relative h-5 w-5 cursor-pointer appearance-none rounded-full border-2 border-[#2563EB] transition-all checked:border-[#2563EB] checked:bg-[#2563EB] hover:border-[#2563EB]/80"
                                                               onchange="toggleRateInputs({{ $pu->project_id }})">
                                                        <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 transition-opacity peer-checked:opacity-100">
                                                            <div class="absolute left-[7px] top-[7px] h-2.5 w-2.5 rounded-full bg-white"></div>
                                                        </div>
                                                        <span class="ml-2 text-gray-700">Flat Payment</span>
                                                    </label>
                                                </div>

                                                {{-- Tarifa por hora --}}
                                                <div id="hourly-rate-{{ $pu->project_id }}"
                                                     class="rate-input {{ $pu->payment_type === 'flat' ? 'hidden' : '' }} transition-all duration-300">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate ($)</label>
                                                    <div class="relative rounded-md shadow-sm">
                                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                            <span class="text-gray-500 sm:text-sm">$</span>
                                                        </div>
                                                        <input type="number"
                                                               step="0.01"
                                                               name="hourly_rates[{{ $pu->project_id }}]"
                                                               value="{{ old('hourly_rates.'.$pu->project_id, $pu->hourly_rate) }}"
                                                               class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-[#2563EB] focus:ring-[#2563EB] sm:text-sm"
                                                               placeholder="0.00">
                                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                            <span class="text-gray-500 sm:text-sm">/hour</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Tarifa fija --}}
                                                <div id="flat-rate-{{ $pu->project_id }}"
                                                     class="rate-input {{ $pu->payment_type === 'hourly' ? 'hidden' : '' }} transition-all duration-300">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Flat Rate ($)</label>
                                                    <div class="relative rounded-md shadow-sm">
                                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                            <span class="text-gray-500 sm:text-sm">$</span>
                                                        </div>
                                                        <input type="number"
                                                               step="0.01"
                                                               name="flat_rates[{{ $pu->project_id }}]"
                                                               value="{{ old('flat_rates.'.$pu->project_id, $pu->flat_rate) }}"
                                                               class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-[#2563EB] focus:ring-[#2563EB] sm:text-sm"
                                                               placeholder="0.00">
                                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                            <span class="text-gray-500 sm:text-sm">/month</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button"
                                        onclick="document.getElementById('rate-form').classList.add('hidden')"
                                        class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="btn btn-primary">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleRateInputs(projectId) {
                const hourlyDiv = document.getElementById(`hourly-rate-${projectId}`);
                const flatDiv = document.getElementById(`flat-rate-${projectId}`);
                const paymentType = document.querySelector(`input[name="payment_types[${projectId}]"]:checked`).value;

                if (paymentType === 'hourly') {
                    hourlyDiv.classList.remove('hidden');
                    flatDiv.classList.add('hidden');
                } else {
                    hourlyDiv.classList.add('hidden');
                    flatDiv.classList.remove('hidden');
                }
            }

            // Cerrar modal con Escape
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    document.getElementById('rate-form').classList.add('hidden');
                }
            });
        </script>

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
