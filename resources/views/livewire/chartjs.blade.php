<div>
    <canvas id="salesChart"></canvas>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('myChart').getContext('2d');
            const chartData = @json($chartData);

            const sortedChartData = chartData.sort((a, b) => new Date(a.time) - new Date(b.time));
            const labels = sortedChartData.map(item => {
        const date = new Date(item.time);
        const day = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        const time = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        return `${day}\n${time}`; // Split the date and time into two lines
    });
            const values = chartData.map(item => item.value); // Adjust according to your data structure

            const myChart = new Chart(ctx, {
                type: 'line', // or 'line', 'pie', etc.
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Dataset',
                        data: values,
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
                            time: {
                                unit: 'minute',
                                displayFormats: {
                                    minute: 'MMM d, HH:mm'
                                }
                            },
                            grid: { display: false },
                            title: { display: true, text: 'Time', color: '#666', font: { size: 15 } },
                            ticks: { autoSkip: true, maxRotation: 0, minRotation: 0, align: 'center', padding: 0 }
                        },

                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(200, 200, 200, 0.2)', borderDash: [5, 5] },
                            title: { display: true, text: 'Grams Feed', color: '#666', font: { size: 15 } }
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
                                label: function (context) {
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
                                font: { size: 14 }
                            }
                        }
                    }
                }
            });
        });
    </script>
</div>
