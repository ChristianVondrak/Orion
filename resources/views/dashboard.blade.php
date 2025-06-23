<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="pt-6 flex justify-center">
        <div class="bg-white shadow rounded-lg p-4 w-full max-w-md">
            <form method="GET" action="{{ route('projects.index') }}" class="flex items-center gap-4">
                <label for="status" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                    Filter by status:
                </label>
                <select
                    name="status"
                    id="status"
                    onchange="this.form.submit()"
                    class="block w-full px-3 py-2 text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm"
                >
                    <option value="" {{ request('status') === null ? 'selected' : '' }}>All</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </form>
        </div>
    </div>

    <div class="py-12 flex flex-col justify-center items-center lg:mx-20 mx-4 md:mx-10">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border border-gray-200">
            <table class="table-fixed w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tracked hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($projects as $project)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('projects.show', ['id' => $project->id]) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $project->ProjectUsers->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $project->timings_count_in_hours }} Hours
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($project->status == 1)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
