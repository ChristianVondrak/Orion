import { chartColors, mainColors } from './config/colors';

// Configuración de Chart.js
const chartConfig = {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 2,
    plugins: {
        legend: {
            labels: {
                color: chartColors.text.primary,
                font: {
                    size: 12,
                    weight: '500'
                }
            }
        }
    }
};

// Función para actualizar totales
const updateTotals = (data, type) => {
    const total = Array.isArray(data) ? data.reduce((sum, item) => sum + (item.total || 0), 0) : 0;
    document.getElementById(`${type}Total`).textContent = `Total: ${total}`;
    return total;
};

// Función para manejar errores
const handleError = (chartId, error) => {
    console.error(`Error loading ${chartId} data:`, error);
    document.getElementById(chartId).parentElement.innerHTML += 
        `<p class="text-red-500 mt-2">Error al cargar datos</p>`;
};

// Función para ocultar el loader
const hideLoader = (loaderId) => {
    const loader = document.getElementById(loaderId);
    if (loader) {
        loader.style.display = 'none';
    }
};

// Función para actualizar la tasa de ocupación
const updateOccupancyRate = (totalHours, workingDaysInMonth, currentWorkDay, monthlyPlannedHours) => {
    const expectedHoursToDate = (monthlyPlannedHours / workingDaysInMonth) * currentWorkDay;
    const occupancyRate = expectedHoursToDate > 0 
        ? Math.round((totalHours / expectedHoursToDate) * 100)
        : 0;
    
    const occupancyElement = document.getElementById('occupancyRate');
    occupancyElement.textContent = `${occupancyRate}%`;
    
    occupancyElement.title = `
        Horas registradas: ${Math.round(totalHours)}h
        Horas esperadas a la fecha: ${Math.round(expectedHoursToDate)}h
        Días transcurridos: ${currentWorkDay} de ${workingDaysInMonth}
    `;
    
    occupancyElement.className = 'text-3xl font-bold';
    if (occupancyRate >= 90) {
        occupancyElement.classList.add('stat-value', 'success');
    } else if (occupancyRate >= 70) {
        occupancyElement.classList.add('stat-value', 'warning');
    } else {
        occupancyElement.classList.add('stat-value', 'danger');
    }
};

// Función para crear el gráfico de compensación
const createCompensationChart = async () => {
    try {
        const response = await axios.get('/api/statistics/compensation');
        const data = response.data;
        const totalCompensation = data.fixed + data.hourly;
        
        new Chart(document.getElementById('compensationChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pago Fijo', 'Por Hora'],
                datasets: [{
                    data: [data.fixed, data.hourly],
                    backgroundColor: [
                        chartColors.compensation.fixed,
                        chartColors.compensation.hourly
                    ]
                }]
            },
            options: {
                ...chartConfig,
                plugins: {
                    ...chartConfig.plugins,
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        document.getElementById('compensationTotal').textContent = `Total: ${totalCompensation}`;
        document.getElementById('totalContractors').textContent = totalCompensation;
        hideLoader('compensationChartLoader');
        hideLoader('totalContractorsLoader');
    } catch (error) {
        handleError('compensationChart', error);
    }
};

// Función para crear el gráfico de compañías
const createCompaniesChart = async () => {
    try {
        const response = await axios.get('/api/statistics/companies');
        const data = response.data;
        
        new Chart(document.getElementById('companiesChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.name),
                datasets: [{
                    label: 'Contratistas',
                    data: data.map(d => d.total),
                    backgroundColor: data.map((_, index) => 
                        chartColors.sequential[index % chartColors.sequential.length]
                    )
                }]
            },
            options: {
                ...chartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            color: chartColors.text.secondary
                        },
                        grid: {
                            color: chartColors.background.light
                        }
                    },
                    x: {
                        ticks: {
                            color: chartColors.text.secondary
                        },
                        grid: {
                            color: chartColors.background.light
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
        document.getElementById('activeProjects').textContent = data.length;
        hideLoader('companiesChartLoader');
        hideLoader('activeProjectsLoader');
    } catch (error) {
        handleError('companiesChart', error);
    }
};

// Función para crear el gráfico de antigüedad
const createSeniorityChart = async () => {
    try {
        const response = await axios.get('/api/statistics/seniority');
        const data = response.data;

        // Definir el orden específico de los rangos
        const rangeOrder = ['0-2', '3-5', '6-8', '9-11', '12-20', '21+'];
        const rangeLabels = {
            '0-2': '0 a 2 años',
            '3-5': '3 a 5 años',
            '6-8': '6 a 8 años',
            '9-11': '9 a 11 años',
            '12-20': '12 a 20 años',
            '21+': 'Más de 21 años'
        };

        // Preparar los datos asegurando que todos los rangos existan
        const chartData = rangeOrder.map(range => data[range] || 0);
        
        // Calcular el total
        const total = chartData.reduce((sum, value) => sum + value, 0);
        document.getElementById('seniorityTotal').textContent = `Total: ${total} personas`;
        
        new Chart(document.getElementById('seniorityChart'), {
            type: 'bar',
            data: {
                labels: rangeOrder.map(range => rangeLabels[range]),
                datasets: [{
                    label: 'Contratistas',
                    data: chartData,
                    backgroundColor: mainColors.secondary,
                    borderColor: mainColors.secondary,
                    borderWidth: 1
                }]
            },
            options: {
                ...chartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            color: chartColors.text.secondary
                        },
                        grid: {
                            color: chartColors.background.light
                        },
                        title: {
                            display: true,
                            text: 'Número de Contratistas',
                            color: chartColors.text.primary
                        }
                    },
                    x: {
                        ticks: {
                            color: chartColors.text.secondary
                        },
                        grid: {
                            color: chartColors.background.light
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.parsed.y} personas`;
                            }
                        }
                    }
                }
            }
        });
        
        hideLoader('seniorityChartLoader');
    } catch (error) {
        handleError('seniorityChart', error);
        console.error('Datos recibidos:', error.response?.data);
    }
};

// Función para crear el gráfico de estado civil
const createMaritalChart = async () => {
    try {
        const response = await axios.get('/api/statistics/marital-status');
        const data = response.data;
        
        // Definir el orden específico de los estados civiles
        const statusOrder = ['single', 'married', 'divorced', 'widowed'];
        const statusLabels = {
            'single': 'Soltero',
            'married': 'Casado',
            'divorced': 'Divorciado',
            'widowed': 'Viudo'
        };

        // Preparar los datos para cada género
        const maleData = statusOrder.map(status => {
            const record = data.find(d => d.gender === 'male' && d.marital_status === status);
            return record ? record.total : 0;
        });

        const femaleData = statusOrder.map(status => {
            const record = data.find(d => d.gender === 'female' && d.marital_status === status);
            return record ? record.total : 0;
        });

        new Chart(document.getElementById('maritalChart'), {
            type: 'bar',
            data: {
                labels: statusOrder.map(status => statusLabels[status]),
                datasets: [
                    {
                        label: 'Hombres',
                        data: maleData,
                        backgroundColor: chartColors.gender.male,
                        borderColor: chartColors.gender.male,
                        borderWidth: 1
                    },
                    {
                        label: 'Mujeres',
                        data: femaleData,
                        backgroundColor: chartColors.gender.female,
                        borderColor: chartColors.gender.female,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                ...chartConfig,
                scales: {
                    x: {
                        grid: {
                            color: chartColors.background.light
                        },
                        ticks: {
                            color: chartColors.text.secondary
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: chartColors.background.light
                        },
                        ticks: {
                            stepSize: 1,
                            color: chartColors.text.secondary
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: chartColors.text.primary,
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y} personas`;
                            }
                        }
                    }
                }
            }
        });

        // Actualizar el total
        const total = data.reduce((sum, item) => sum + item.total, 0);
        document.getElementById('maritalTotal').textContent = `Total: ${total} personas`;
        hideLoader('maritalChartLoader');
    } catch (error) {
        handleError('maritalChart', error);
    }
};

// Función para crear el gráfico de posiciones
const createPositionsChart = async () => {
    try {
        const response = await axios.get('/api/statistics/positions');
        const data = response.data;
        
        new Chart(document.getElementById('positionsChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.position),
                datasets: [{
                    label: 'Contratistas',
                    data: data.map(d => d.total),
                    backgroundColor: mainColors.primary
                }]
            },
            options: {
                ...chartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            color: chartColors.text.secondary
                        },
                        grid: {
                            color: chartColors.background.light
                        }
                    },
                    x: {
                        ticks: {
                            color: chartColors.text.secondary
                        },
                        grid: {
                            color: chartColors.background.light
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
        updateTotals(data, 'positions');
        hideLoader('positionsChartLoader');
    } catch (error) {
        handleError('positionsChart', error);
    }
};

// Función para crear el gráfico de horas
const createHoursChart = async () => {
    try {
        const response = await axios.get('/api/statistics/project-hours');
        const data = response.data;
        
        // Calcular totales
        const totalHours = data.reduce((sum, project) => sum + parseFloat(project.total_hours), 0);
        const totalPlannedHours = data.reduce((sum, project) => sum + parseFloat(project.planned_hours), 0);
        
        // Actualizar el total de horas mensuales
        document.getElementById('monthlyHours').textContent = `${Math.round(totalHours)}h`;
        document.getElementById('hoursTotal').textContent = 
            `Total: ${Math.round(totalHours)}h / ${Math.round(totalPlannedHours)}h planificadas`;
        
        new Chart(document.getElementById('hoursChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.project_name || 'Sin nombre'),
                datasets: [{
                    label: '% del Objetivo Mensual',
                    data: data.map(d => parseFloat(d.percentage).toFixed(1)),
                    backgroundColor: data.map(d => {
                        switch(d.status) {
                            case 'on-track': return chartColors.status.optimal;
                            case 'warning': return chartColors.status.moderate;
                            case 'over': return chartColors.status.over;
                            default: return chartColors.status.low;
                        }
                    })
                }]
            },
            options: {
                ...chartConfig,
                indexAxis: 'y',
                scales: {
                    x: {
                        max: 100,
                        grid: { color: chartColors.background.light },
                        ticks: { 
                            color: chartColors.text.secondary,
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Porcentaje Completado',
                            color: chartColors.text.primary
                        }
                    },
                    y: {
                        ticks: { color: chartColors.text.secondary }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const project = data[context.dataIndex];
                                const status = {
                                    'on-track': 'En tiempo',
                                    'warning': 'Precaución',
                                    'over': 'Excedido',
                                    'behind': 'Atrasado'
                                };
                                return [
                                    `Estado: ${status[project.status]}`,
                                    `Porcentaje: ${parseFloat(project.percentage).toFixed(1)}%`,
                                    `Horas: ${Math.round(project.total_hours)}h / ${Math.round(project.planned_hours)}h`
                                ];
                            }
                        }
                    },
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Calcular tasa de ocupación
        const now = new Date();
        const currentDay = now.getDate();
        const totalDaysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
        const workingDaysInMonth = Math.min(20, totalDaysInMonth);
        const currentWorkDay = Math.min(currentDay, workingDaysInMonth);

        updateOccupancyRate(totalHours, workingDaysInMonth, currentWorkDay, totalPlannedHours);
        hideLoader('hoursChartLoader');
        hideLoader('monthlyHoursLoader');
        hideLoader('occupancyRateLoader');
    } catch (error) {
        handleError('hoursChart', error);
    }
};

// Inicialización
document.addEventListener('DOMContentLoaded', async () => {
    // Configurar Axios
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Inicializar cookie de CSRF
    await axios.get('/sanctum/csrf-cookie');

    try {
        // Crear todos los gráficos
        await Promise.all([
            createCompensationChart(),
            createCompaniesChart(),
            createSeniorityChart(),
            createMaritalChart(),
            createPositionsChart(),
            createHoursChart()
        ]);
    } catch (error) {
        console.error('Error general al cargar el dashboard:', error);
    }
}); 