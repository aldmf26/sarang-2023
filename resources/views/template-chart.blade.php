<x-theme.app title="No Table" table="T">
    <x-slot name="slot">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Bar Chart</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart" width="440" height="220"
                            style="display: block; box-sizing: border-box; height: 220px; width: 440px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Line Chart</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="line" width="440" height="220"
                            style="display: block; box-sizing: border-box; height: 220px; width: 440px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @section('scripts')
            <script>
                // Misalkan Anda memiliki data yang akan ditampilkan dalam grafik
                const labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"];
                const warnaBg = {
                    
                }
                const data = {
                    labels: labels,
                    datasets: [{
                        label: 'Total Uang Keluar',
                        data: [10000, 5900, 8000, 8100, 5600, 5500, 4000],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)'
                        ],
                        borderWidth: 1
                    }]
                };
                const config = {
                    type: 'bar',
                    data: data,
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    },
                };
                const config2 = {
                    type: 'line',
                    data: data,
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    },
                };

                // Inisialisasi grafik
                window.onload = function() {
                    var ctx = document.getElementById('myChart').getContext('2d');
                    var myChart = new Chart(ctx, config);

                    var ctx = document.getElementById('line').getContext('2d');
                    var myChart = new Chart(ctx, config2);
                };
            </script>
        @endsection


    </x-slot>

</x-theme.app>
