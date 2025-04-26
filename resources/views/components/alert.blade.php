@props([
    'type'    => 'success',
    'message',
    'duration' => 5000
])

<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, {{ $duration }})"
    x-show="show"
    x-transition
    class="fixed top-4 right-4 z-50 flex items-start space-x-3
           p-4 rounded-lg
           bg-{{ $type === 'success' ? 'green' : 'red' }}-100
           border border-{{ $type === 'success' ? 'green' : 'red' }}-200
           shadow"
>
    {{-- Icono --}}
    <svg class="w-5 h-5 flex-shrink-0 text-{{ $type === 'success' ? 'green' : 'red' }}-600" fill="currentColor" viewBox="0 0 20 20">
        @if($type === 'success')
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414L9 13.414l4.707-4.707z" clip-rule="evenodd"/>
        @else
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 102 0V7zm0 6a1 1 0 10-2 0 1 1 0 002 0z" clip-rule="evenodd"/>
        @endif
    </svg>

    {{-- Mensaje --}}
    <div class="flex-1 text-sm text-{{ $type === 'success' ? 'green' : 'red' }}-800">
        {{ $message }}
    </div>

    {{-- Botón cerrar --}}
    <button
        @click="show = false"
        class="text-{{ $type === 'success' ? 'green' : 'red' }}-600 hover:text-{{ $type === 'success' ? 'green' : 'red' }}-800"
        aria-label="Close notification"
    >
        <span class="sr-only">Close</span>
        ×
    </button>
</div>
