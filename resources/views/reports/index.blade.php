<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12 lg:mx-20 mx-4 md:mx-10">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Select a report</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($reports as $report)
                    <a href="{{ $report['route'] }}"
                       class="block border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
                        <h4 class="text-md font-semibold text-gray-800">{{ $report['title'] }}</h4>
                        <p class="mt-1 text-gray-600 text-sm">{{ $report['description'] }}</p>
                    </a>
                @endforeach
                <a href="{{ route('reports.annualhours') }}"
                   class="block border border-blue-300 rounded-lg p-4 hover:shadow-lg transition">
                    <h4 class="text-md font-semibold text-blue-800">Annual hours per contractor</h4>
                    <p class="mt-1 text-gray-600 text-sm">Annual report of hours worked per contractor, grouped by month and filterable by project.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
