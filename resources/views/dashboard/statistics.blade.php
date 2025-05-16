<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">HR Dashboard Statistics</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- 1. Compensation Structure -->
                <div>
                    <h4 class="text-md font-semibold mb-2">Fixed vs Hourly</h4>
                    <canvas id="compensationChart"></canvas>
                </div>

                <!-- 2. Contractors per Company -->
                <div>
                    <h4 class="text-md font-semibold mb-2">Contractors per Company</h4>
                    <canvas id="companiesChart"></canvas>
                </div>

                <!-- 3. Seniority Ranges -->
                <div>
                    <h4 class="text-md font-semibold mb-2">Seniority Ranges</h4>
                    <canvas id="seniorityChart"></canvas>
                </div>

                <!-- 4. Marital Status by Gender -->
                <div>
                    <h4 class="text-md font-semibold mb-2">Marital Status by Gender</h4>
                    <canvas id="maritalChart"></canvas>
                </div>

                <!-- 5. Contractors per Department -->
                <div>
                    <h4 class="text-md font-semibold mb-2">Contractors per Department</h4>
                    <canvas id="departmentsChart"></canvas>
                </div>

                <!-- 6. Project Hour Completion -->
                <div>
                    <h4 class="text-md font-semibold mb-2">Project Hour Completion</h4>
                    <canvas id="hoursChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        // 1. Compensation Structure (Pie)
        axios.get('/api/statistics/compensation')
            .then(({ data }) => {
                new Chart(document.getElementById('compensationChart'), {
                    type: 'pie',
                    data: {
                        labels: ['Fixed', 'Hourly'],
                        datasets: [{
                            data: [data.fixed, data.hourly],
                            backgroundColor: ['#4A5568','#3182CE']
                        }]
                    }
                });
            });

        // 2. Contractors per Company (Bar)
        axios.get('/api/statistics/companies')
            .then(({ data }) => {
                const labels = data.map(d => d.name);
                const values = data.map(d => d.total);
                new Chart(document.getElementById('companiesChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{ label: 'Contractors', data: values }]
                    },
                    options: { responsive: true }
                });
            });

        // 3. Seniority Ranges (Bar)
        axios.get('/api/statistics/seniority')
            .then(({ data }) => {
                const labels = Object.keys(data);
                const values = Object.values(data);
                new Chart(document.getElementById('seniorityChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{ label: 'Count', data: values }]
                    },
                    options: { responsive: true }
                });
            });

        // 4. Marital Status by Gender (Stacked Bar)
        axios.get('/api/statistics/marital-status')
            .then(({ data }) => {
                const genders = [...new Set(data.map(d => d.gender))];
                const statuses = [...new Set(data.map(d => d.marital_status))];
                const datasets = genders.map(gender => ({
                    label: gender,
                    data: statuses.map(status => {
                        const rec = data.find(d => d.gender === gender && d.marital_status === status);
                        return rec ? rec.total : 0;
                    }),
                    backgroundColor: gender === 'Male' ? '#4299E1' : '#ED64A6'
                }));
                new Chart(document.getElementById('maritalChart'), {
                    type: 'bar',
                    data: { labels: statuses, datasets },
                    options: {
                        responsive: true,
                        scales: { x: { stacked: true }, y: { stacked: true } }
                    }
                });
            });

        // 5. Contractors per Department (Bar)
        axios.get('/api/statistics/departments')
            .then(({ data }) => {
                const labels = data.map(d => d.department);
                const values = data.map(d => d.total);
                new Chart(document.getElementById('departmentsChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{ label: 'Contractors', data: values }]
                    },
                    options: { responsive: true }
                });
            });

        // 6. Project Hour Completion (Horizontal Bar)
        axios.get('/api/statistics/project-hours')
            .then(({ data }) => {
                const labels = data.map(d => `Project ${d.project_id}`);
                const values = data.map(d => d.percentage);
                new Chart(document.getElementById('hoursChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{ label: '% of 160h goal', data: values }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        scales: { x: { max: 100 } }
                    }
                });
            });

    });
    </script>
</x-app-layout>
