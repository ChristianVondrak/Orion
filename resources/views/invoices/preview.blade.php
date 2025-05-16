<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Corte mensual:').' '.$project->name }}
            </h2>
            <a href="{{ route('project.invoices.preview', $project->id) }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                {{ __('Recargar Vista') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-6">

        {{-- BARRA DE BÚSQUEDA --}}
        <form class="mb-4 flex" method="GET"
              action="{{ route('project.invoices.preview', $project->id) }}">
            <input type="text"
                   name="search"
                   value="{{ old('search', $search) }}"
                   placeholder="Buscar profesional..."
                   class="flex-grow border-gray-300 rounded-l-md focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700">
                {{ __('Buscar') }}
            </button>
        </form>

        <form method="POST" action="{{ route('project.invoices.send', $project->id) }}">
            @csrf

            {{-- GRID DE TARJETAS PAGINADAS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                            </div>
                            <div class="text-right space-y-1">
                                <div class="text-sm text-gray-600">{{ __('Subtotal') }}:
                                    <span class="font-medium">${{ number_format($inv['subtotal'],2) }}</span>
                                </div>
                                <div class="text-sm {{ $inv['auto_adjustment'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ __('Auto-ajuste') }}:
                                    <span class="font-medium">
                                        ${{ number_format($inv['auto_adjustment'],2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- CUERPO--}}
                        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Manual --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Ajuste manual') }}
                                </label>
                                <input type="number" step="0.01"
                                       name="manual_adjustments[{{ $inv['user']->id }}]"
                                       x-model.number="manual"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            </div>
                            {{-- Total --}}
                            <div class="text-right flex flex-col justify-end">
                                <p class="text-sm text-gray-700">{{ __('Total estimado') }}</p>
                                <p class="text-2xl font-bold text-indigo-600">
                                    $<span x-text="({{ $inv['subtotal'] }} +
                                                   {{ $inv['auto_adjustment'] }} +
                                                   manual).toFixed(2)"></span>
                                </p>
                            </div>
                        </div>

                        {{-- DETALLE DIARIO --}}
                        <details class="px-6 pb-4">
                            <summary class="cursor-pointer text-indigo-600 hover:underline">
                                {{ __('Ver detalle diario') }}
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
                    </div>
                @endforeach
            </div>

            {{-- LINKS DE PAGINACIÓN --}}
            <div class="mt-6">
                {{ $invoices->links() }}
            </div>

            {{-- BOTÓN DE ENVÍO --}}
            <div class="mt-8 flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    {{ __('Confirmar y enviar facturas') }}
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
