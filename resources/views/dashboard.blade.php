<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    {{-- Margin in Y 48px --}}
    <div class="py-12 flex flex-col justify-center items-center lg:mx-20 mx-4 md:mx-10">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
            <table class="table-fixed w-full">
                <thead>
                <tr>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Project
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Users
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Tracked hours
                    </th>
                    <th
                        class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white">
                @foreach($projects as $project)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">{{$project->name}}</a>
                        </td>
                        {{--              Count of users per project            --}}
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{$project->worksnapUsers->count()}}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">423 Hours</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                @if($project->status == 1)
                                    Active
                                @else
                                    Inactive
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Avanty way</a>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">15</td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">423 Hours</td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Profolio</a>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">10</td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">423 Hours</td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Savvy way</a>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">30</td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">423 Hours</td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
