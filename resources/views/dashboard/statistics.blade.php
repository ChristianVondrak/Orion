<x-app-layout>
    {{-- exclusive slot for <title> --}}
    <x-slot name="title">
        Statistics
    </x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Human Resources Dashboard') }}
            </h2>
            <div class="text-sm text-gray-500">
                Last update: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    {{-- CSRF Meta for JavaScript --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Styles and Scripts --}}
    @vite(['resources/css/statistics-dashboard.css', 'resources/js/statistics-dashboard.js'])

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- General Summary --}}
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total Contractors --}}
                <div class="stat-card bg-white overflow-visible shadow-xl sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Total Contractors</h3>
                        <div class="tooltip-container">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-help" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="tooltip-content">
                                Total number of active contractors across all projects
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="loading-overlay" id="totalContractorsLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <p class="text-3xl font-bold text-indigo-600" id="totalContractors">...</p>
                    </div>
                </div>

                {{-- Active Projects --}}
                <div class="stat-card bg-white overflow-visible shadow-xl sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Active Projects</h3>
                        <div class="tooltip-container">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-help" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="tooltip-content">
                                Number of projects with assigned contractors currently logging hours
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="loading-overlay" id="activeProjectsLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <p class="text-3xl font-bold text-green-600" id="activeProjects">...</p>
                    </div>
                </div>

                {{-- Hours this month --}}
                <div class="stat-card bg-white overflow-visible shadow-xl sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Hours this month</h3>
                        <div class="tooltip-container">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-help" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="tooltip-content">
                                Total hours logged in the current month by all contractors
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="loading-overlay" id="monthlyHoursLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <p class="text-3xl font-bold text-blue-600" id="monthlyHours">...</p>
                    </div>
                </div>

                {{-- Occupancy Rate --}}
                <div class="stat-card bg-white overflow-visible shadow-xl sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Occupancy Rate</h3>
                        <div class="tooltip-container">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-help" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="tooltip-content">
                                <p>Indicates the percentage of worked hours completion in relation to planned hours up to the current date of the month. Calculated considering:</p>
                                <ul class="mt-2 mb-3 list-disc list-inside text-sm">
                                    <li>Working days elapsed</li>
                                    <li>Planned weekly hours</li>
                                    <li>Total hours logged</li>
                                </ul>
                                <div class="border-t border-gray-600 pt-2">
                                    <div class="status-indicator">
                                        <span class="status-optimal">≥90%</span> Optimal
                                    </div>
                                    <div class="status-indicator">
                                        <span class="status-moderate">70-89%</span> Moderate
                                    </div>
                                    <div class="status-indicator">
                                        <span class="status-low">&lt;70%</span> Low
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="loading-overlay" id="occupancyRateLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <p class="text-3xl font-bold" id="occupancyRate">...</p>
                    </div>
                </div>
            </div>

            {{-- First Row of Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Compensation --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Compensation Structure</h3>
                        <div class="text-sm text-gray-500" id="compensationTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="compensationChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="compensationChart"></canvas>
                    </div>
                </div>

                {{-- Hours by Project (movido aquí) --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Hours Completion by Project</h3>
                        <div class="text-sm text-gray-500" id="hoursTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="hoursChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="hoursChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Second Row of Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Companies --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Distribution by Company</h3>
                        <div class="text-sm text-gray-500" id="companiesTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="companiesChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="companiesChart"></canvas>
                    </div>
                </div>

                {{-- Seniority --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Seniority Ranges</h3>
                        <div class="text-sm text-gray-500" id="seniorityTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="seniorityChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="seniorityChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Third Row of Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Marital Status by Gender --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Marital Status by Gender</h3>
                        <div class="text-sm text-gray-500" id="maritalTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="maritalChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="maritalChart"></canvas>
                    </div>
                </div>

                {{-- Positions --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Distribution by Position</h3>
                        <div class="text-sm text-gray-500" id="positionsTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="positionsChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="positionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-app-layout>

