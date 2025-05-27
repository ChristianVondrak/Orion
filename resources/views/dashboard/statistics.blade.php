<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard de Recursos Humanos') }}
            </h2>
            <div class="text-sm text-gray-500">
                Última actualización: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    {{-- Meta CSRF para JavaScript --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Estilos y Scripts --}}
    @vite(['resources/css/statistics-dashboard.css', 'resources/js/statistics-dashboard.js'])

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Resumen General --}}
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total Contratistas --}}
                <div class="stat-card bg-white overflow-visible shadow-xl sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Total Contratistas</h3>
                        <div class="tooltip-container">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-help" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="tooltip-content">
                                Número total de contratistas activos en todos los proyectos
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

                {{-- Proyectos Activos --}}
                <div class="stat-card bg-white overflow-visible shadow-xl sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Proyectos Activos</h3>
                        <div class="tooltip-container">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-help" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="tooltip-content">
                                Cantidad de proyectos que tienen contratistas asignados y están registrando horas actualmente
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

                {{-- Horas este mes --}}
                <div class="stat-card bg-white overflow-visible shadow-xl sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Horas este mes</h3>
                        <div class="tooltip-container">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-help" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="tooltip-content">
                                Total de horas registradas en el mes actual por todos los contratistas
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

                {{-- Tasa de Ocupación --}}
                <div class="stat-card bg-white overflow-visible shadow-xl sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tasa de Ocupación</h3>
                        <div class="tooltip-container">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-help" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="tooltip-content">
                                <p>Indica el porcentaje de cumplimiento de horas trabajadas en relación a las horas planificadas hasta la fecha actual del mes. Se calcula considerando:</p>
                                <ul class="mt-2 mb-3 list-disc list-inside text-sm">
                                    <li>Días laborables transcurridos</li>
                                    <li>Horas semanales planificadas</li>
                                    <li>Total de horas registradas</li>
                                </ul>
                                <div class="border-t border-gray-600 pt-2">
                                    <div class="status-indicator">
                                        <span class="status-optimal">≥90%</span> Óptimo
                                    </div>
                                    <div class="status-indicator">
                                        <span class="status-moderate">70-89%</span> Moderado
                                    </div>
                                    <div class="status-indicator">
                                        <span class="status-low">&lt;70%</span> Bajo
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

            {{-- Primera Fila de Gráficos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Compensación --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Estructura de Compensación</h3>
                        <div class="text-sm text-gray-500" id="compensationTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="compensationChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="compensationChart"></canvas>
                    </div>
                </div>

                {{-- Compañías --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Distribución por Compañía</h3>
                        <div class="text-sm text-gray-500" id="companiesTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="companiesChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="companiesChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Segunda Fila de Gráficos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Antigüedad --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Rangos de Antigüedad</h3>
                        <div class="text-sm text-gray-500" id="seniorityTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="seniorityChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="seniorityChart"></canvas>
                    </div>
                </div>

                {{-- Estado Civil por Género --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Estado Civil por Género</h3>
                        <div class="text-sm text-gray-500" id="maritalTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="maritalChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="maritalChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Tercera Fila de Gráficos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Posiciones --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Distribución por Posición</h3>
                        <div class="text-sm text-gray-500" id="positionsTotal"></div>
                    </div>
                    <div class="chart-container">
                        <div class="loading-overlay" id="positionsChartLoader">
                            <div class="loading-spinner"></div>
                        </div>
                        <canvas id="positionsChart"></canvas>
                    </div>
                </div>

                {{-- Horas por Proyecto --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Completitud de Horas por Proyecto</h3>
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
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-app-layout>

