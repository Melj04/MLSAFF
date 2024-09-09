<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="flex justify-center flex-col-reverse sm:flex-row mt-2 h-fit">
        <div class="py-1 bg-white grow rounded-lg xs:my-0 sm:my-8 max-w-xl basis-7/12 sm:px-2 h-5/6">
            <div class="flex justify-between">
                <h2 class="font-semibold text-md my-1 ml-1 text-gray-800 leading-tight ">
                {{ __('Feeding History') }}
                </h2>
                @livewire('monthly-report')
            </div>
            <hr class="w-11/12 mx-2">
            <canvas id="myChart"></canvas>
        </div>

        <div class="py-4 mx-3 sm:pt-5 basis-4/12">
            <div class="max-w-sm lg:px-8">
                <div class="overflow-hidden">
                    <div class="flex flex-col">
                        <div class="bg-white shadow-sm rounded-lg p-6 mb-3">
                            <h2 class="font-semibold text-xl mb-1 text-gray-800 leading-tight text-center">
                                {{ __('Next Feeding schedule') }}
                            </h2>
                            <hr>
                            @livewire('next-fed')
                        </div>
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h2 class="font-semibold text-xs mb-1 text-gray-800 leading-tight">
                                {{ __('Feeding Stock') }}
                            </h2>
                            <hr>
                            @livewire('circular-progress-bar')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('myChart').getContext('2d');
            let myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Feeding History',
                        data: [],
                        fill: false,
                        backgroundColor: 'rgba(60, 192, 192, 0.42)',
                        borderColor: 'rgba(60, 192, 192, 1)',
                        pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 3,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Time',
                                color: '#666',
                                font: {
                                    size: 15
                                }
                            },
                            ticks: {
                                autoSkip: true,
                                maxRotation: 0,
                                minRotation: 0,
                                align: 'center',
                                padding: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(200, 200, 200, 0.2)',
                                borderDash: [5, 5]
                            },
                            title: {
                                display: true,
                                text: 'Grams Feed',
                                color: '#666',
                                font: {
                                    size: 15
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#333',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed.y;
                                    return label;
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#333',
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });

            function updateChart() {
                $.ajax({
                    url: '/chart-data', // Ensure this matches the route in your controller
                    method: 'GET',
                    success: function(data) {
                        if (Array.isArray(data)) {
                            const sortedData = data.sort((a, b) => new Date(a.time) - new Date(b.time));

                            const labels = sortedData.map(item => {
                                const date = new Date(item.time);
                                const day = date.toLocaleDateString('en-US', {
                                    month: 'short',
                                    day: 'numeric'
                                });
                                const time = date.toLocaleTimeString('en-US', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                return `${day}\n${time}`;
                            });
                            const values = sortedData.map(item => item.value);

                            myChart.data.labels = labels;
                            myChart.data.datasets[0].data = values;
                            myChart.update();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching chart data:', status, error);
                    }
                });
            }

            // Initial load
            updateChart();

            // Update chart every 5 seconds
            setInterval(updateChart, 5000);
        });
    </script>

</x-app-layout>
