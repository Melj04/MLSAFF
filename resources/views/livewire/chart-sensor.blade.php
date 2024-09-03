<div class="flex flex-col items-center justify-center">
    <div class=" bg  w-full  overflow-hidden">
    <canvas id="myChart" ></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('myChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($this->sensorData->pluck('label')),
                datasets: [{
                    label: 'Sensor Value',
                    data: @json($this->sensorData->pluck('value')),
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
