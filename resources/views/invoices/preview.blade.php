<x-app-layout>
    <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Monthly Report:').' '.$project->name }}
            </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto mt-6">
        {{-- Búsqueda --}}
        <form class="mb-4 flex" method="GET"
              action="{{ route('projects.invoices.preview', $project->id) }}">
            <input type="text"
                   name="search"
                   value="{{ old('search', $search) }}"
                   placeholder="Search professional..."
                   class="flex-grow border-gray-300 rounded-l-md focus:ring-[#2563EB] focus:border-[#2563EB]">
            <button type="submit"
                    class="btn btn-primary !rounded-l-none !rounded-r-md !m-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                {{ __('Search') }}
            </button>
        </form>

        <form method="POST" action="{{ route('projects.invoices.send', $project->id) }}">
            @csrf

            {{-- GRID de 1 columna --}}
            <div class="grid grid-cols-1 gap-6">
                @foreach($invoices as $inv)
                    <div class="bg-white shadow rounded-lg overflow-hidden"
                         x-data="{ manual: 0 }">

                        {{-- HEADER --}}
                        <div class="px-6 py-4 bg-gray-50 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $inv['user']->first_name.' '.$inv['user']->last_name }}
                                </h3>
                                <p class="text-sm text-gray-500">{{ $inv['period'] }}</p>
                                <p class="text-sm">
                                    <span class="font-medium">{{ __('Payment Type') }}:</span>
                                    <span class="capitalize">{{ $inv['payment_type'] === 'flat' ? 'Flat Rate' : 'Hourly Rate' }}</span>
                                    @if($inv['payment_type'] === 'hourly')
                                        <span class="text-gray-500">${{ number_format($inv['hourly_rate'], 2) }}/h</span>
                                    @else
                                        <span class="text-gray-500">${{ number_format($inv['flat_rate'], 2) }}/month</span>
                                    @endif
                                </p>
                                @if($inv['payment_type'] === 'hourly')
                                    <p class="text-sm">
                                        <span class="font-medium">{{ __('Activity Index') }}:</span>
                                        {{ $inv['activity_index'] }}%
                                    </p>
                                @endif
                            </div>
                            <div class="text-right space-y-1">
                                @if($inv['payment_type'] === 'flat')
                                    <div class="text-sm text-gray-600">{{ __('Monthly Salary') }}:
                                        <span class="font-medium">${{ number_format($inv['expected_salary'], 2) }}</span>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-600">{{ __('Subtotal') }}:
                                        <span class="font-medium">${{ number_format($inv['subtotal'],2) }}</span>
                                    </div>
                                    <div class="text-sm {{ $inv['auto_adjustment'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ __('Auto-adjustment') }}:
                                        <span class="font-medium">
                                            ${{ number_format($inv['auto_adjustment'],2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- CUERPO DE AJUSTES --}}
                        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Manual Adjustment') }}
                                </label>
                                <input type="number" step="0.01"
                                       name="manual_adjustments[{{ $inv['user']->id }}]"
                                       x-model.number="manual"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#2563EB] focus:border-[#2563EB]"/>
                            </div>
                            {{-- Total dinámico --}}
                            <div class="text-right flex flex-col justify-end">
                                <p class="text-sm text-gray-700">{{ __('Estimated Total') }}</p>
                                <p class="text-2xl font-bold text-[#2563EB]">
                                    $<span x-text="({{ $inv['estimated_total'] }} + manual).toFixed(2)"></span>
                                </p>
                            </div>
                        </div>

                        {{-- DETALLE DIARIO (solo para pago por hora) --}}
                        @if($inv['payment_type'] === 'hourly' && count($inv['daily']) > 0)
                            <details class="px-6 pb-4">
                                <summary class="cursor-pointer text-[#2563EB] hover:underline">
                                    {{ __('View Daily Details') }}
                                </summary>
                                <ul class="mt-2 space-y-1 list-disc list-inside text-sm text-gray-700">
                                    @foreach($inv['daily'] as $day)
                                        <li>
                                            <strong>{{ $day['date'] }}:</strong>
                                            {{ $day['hours'] }}h × ${{ number_format($day['rate'],2) }}
                                            = ${{ number_format($day['amount'],2) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $invoices->links() }}
            </div>

            {{-- Botón enviar --}}
            <div class="mt-8 flex justify-end">
                <button type="submit"
                        class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Confirm and Send Invoices') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Alpine.js --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('invoiceCard', () => ({
                manual: 0
            }));
        });
    </script>
</x-app-layout>
