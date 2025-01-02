@extends('layout.main')
@section('styles')
<style>
/* Page-specific Left Navigation Bar */
.page-sidebar {
    position: sticky;
    top: 0;
    height: 100vh;
    z-index: 900;
    padding-top: 20px;
    border-right: 2px solid #ddd;
    background-color: #f8f9fa;
    overflow-y: auto; /* Allow scrolling if content overflows */
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Add subtle shadow for depth */
}

.page-sidebar .list-group {
    padding: 0; /* Remove default padding */
}

.page-sidebar .list-group-item {
    cursor: pointer;
    font-size: 1rem;
    padding: 12px 20px; /* Add padding for better touch targets */
    border: none; /* Remove borders */
    border-radius: 0; /* Remove border radius for a flat look */
    transition: background-color 0.3s, color 0.3s; /* Smooth background and color transition */
}

.page-sidebar .list-group-item:hover {
    background-color: #e9ecef; /* Highlight on hover */
    color: #007bff; /* Change text color on hover */
}

.page-sidebar .list-group-item.active {
    background-color: #007bff; /* Active item background */
    color: white; /* Active item text color */
    font-weight: bold; /* Make active item text bold */
}

.col-md-9 {
    padding-left: 25px; /* Add padding to the main content area */
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}
</style>

@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Page-specific Left Navigation Bar (Avoiding 'sidebar' ID conflict) -->
        <div class="col-md-3 col-lg-2 d-none d-md-block page-sidebar sticky-top">
                <h4 class="mt-4"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-speedometer2" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.39.39 0 0 0-.029-.518z"/>
                    <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A8 8 0 0 1 0 10m8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3"/>
                  </svg> Dashboard</h4>
                <div class="list-group">
                    <a href="#monthlyEvaluations" class="list-group-item">EDOM Per 4 Minggu (Per Bulan)</a>
                    <a href="#weeklyEvaluations" class="list-group-item">EDOM Per Minggu</a>
                    <a href="#evaluationCalendar" class="list-group-item">Kalender Pengisian EDOM</a>
                    <a href="#layananResponse" class="list-group-item">Layanan Responses</a>
                </div>
        </div>

        <!-- Edom Content Area -->
        <div class="col-md-9 col-lg-10">
            <h1 class="mt-4"><svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-speedometer2" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.39.39 0 0 0-.029-.518z"/>
                <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A8 8 0 0 1 0 10m8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3"/>
              </svg> Dashboard Evaluasi</h1>

            <!-- Chart Section -->
            <div id="monthlyEvaluations" style="width: 70%; margin: 0 auto;">
                <h2 class="mt-5">Evaluasi Per 4 Minggu (Per Bulan)</h2>
                <canvas id="evaluationChart" width="400" height="200"></canvas>
            </div>
            <hr>
            <!-- Weekly Chart Section -->
            <div id="weeklyEvaluations" style="width: 70%; margin: 0 auto;">
                <h2 class="mt-5">Evaluasi Per Minggu</h2>
                <canvas id="weeklyEvaluationChart"></canvas>
            </div>
            <hr>
            <!-- Table Section -->
            <h2 id="evaluationCalendar" class="mt-5">Kalender Pengisian Evaluasi</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center mt-4">
                    <thead class="table-light">
                        <tr>
                            <th class="sticky-header">Group</th>
                            @foreach ($weeks->sort() as $week)
                                <th class="sticky-header">W {{ $week }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tableData as $groupName => $evaluations)
                            <tr>
                                <td>{{ $groupName }}</td>
                                @foreach ($weeks->sort() as $week)
                                    @if (isset($evaluations[$week]))
                                        @php
                                            $isCompleted = $evaluations[$week]['all_completed'];
                                        @endphp
                                        <td class="evaluation-cell" 
                                            style="background-color: {{ $isCompleted ? '#e2fade' : '#ffd1d1' }};">
                                            <a href="{{ url('/admin/home') . '?week=' . $week . '&group=' . $evaluations[$week]['group_name'] }}" 
                                            class="d-block w-100 h-100 text-decoration-none text-dark">
                                                {{ $isCompleted ? '✔' : '✘' }}
                                            </a>
                                        </td>
                                    @else
                                        <td class="table-light evaluation-cell"></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <hr>
            {{-- Layanan Response --}}
            <div class="container mt-4" id="layananResponse">
                <h1 class="text-center">Layanan Responses</h1>
            
                <!-- Chart Section -->
                <div class="d-flex flex-wrap justify-content-center">
                    @foreach($layananChartData as $index => $responses)
                        <div class="card my-2 mx-2" style="width: 400px;">
                            <div class="card-header">
                                <h6>{{ $loop->iteration }}. {{ $index }}</h6> {{-- Adds numbering --}}
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="height: 200px; width: 100%;">
                                    <canvas id="chart-{{ Str::slug($index, '-') }}"></canvas>
                                </div>                            
                            </div>
                        </div>
                    @endforeach
                </div>
            
                <!-- Feedback Section -->
                <div class="card my-4">
                    <div class="card-header">
                        <h4>{{ count($layananChartData) + 1 }}. User Feedback</h4> {{-- Adds numbering as the last section --}}
                    </div>
                    <div class="card-body">
                        @if(!empty($feedbackData))
                            @foreach($feedbackData as $question => $feedbacks)
                                <h5>{{ $question }}</h5>
                                @foreach($feedbacks as $feedback)
                                    <div class="alert alert-secondary">
                                        {{ $feedback }}
                                    </div>
                                @endforeach
                            @endforeach
                        @else
                            <p class="text-muted">No feedback responses available.</p>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Monthly Chart
        const ctx = document.getElementById('evaluationChart').getContext('2d');
        const chartData = @json($formattedChartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(item => item.month),
                datasets: [
                    {
                        label: 'Sudah terisi semua',
                        data: chartData.map(item => item.completed_groups),
                        backgroundColor: '#90EE90', // Light green
                        borderColor: '#2E8B57',     // Dark green
                        borderWidth: 1
                    },
                    {
                        label: 'Belum terisi semua',
                        data: chartData.map(item => item.not_completed_groups),
                        backgroundColor: '#FFB6C6', // Light red
                        borderColor: '#DC143C',     // Dark red
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: false,
                        title: {
                            display: true,
                            text: 'Bulan (Tiap 4 minggu dihitung 1 bulan)'
                        }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Grup'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Status pengisian evaluasi tiap grup per bulan'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    });
    const weeklyCtx = document.getElementById('weeklyEvaluationChart').getContext('2d');
    const weeklyData = @json($weeklyData); // Make sure this matches the format in the controller

    new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: weeklyData.map(item => item.week),
            datasets: [
                {
                    label: 'Sudah terisi semua',
                    data: weeklyData.map(item => item.completed_groups),
                    backgroundColor: '#90EE90', // Light green
                    borderColor: '#2E8B57',     // Dark green
                    borderWidth: 1
                },
                {
                    label: 'Belum terisi semua',
                    data: weeklyData.map(item => item.not_completed_groups),
                    backgroundColor: '#FFB6C6', // Light red
                    borderColor: '#DC143C',     // Dark red
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked:true,
                    title: {
                        display: true,
                        text: 'Minggu ke'
                    }
                },
                y: {
                    stacked: true,  // Stack the bars to combine the completed and not completed data
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Grup'
                    },
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Status pengisian evaluasi tiap grup per minggu'
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

</script>    
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to generate random colors
    function generateColors(count) {
        var colors = [];
        for (var i = 0; i < count; i++) {
            var hue = (i * 137.508) % 360;
            colors.push('hsl(' + hue + ', 70%, 60%)');
        }
        return colors;
    }

    // Initialize charts for each question
    @foreach($layananChartData as $question => $responses)
        var ctx = document.getElementById('chart-{!! Str::slug($question, '-') !!}').getContext('2d');
        
        // Create fixed labels 1-4
        var labels = ['1', '2', '3', '4'];
        
        // Map data to fixed labels, use 0 for missing values
        var chartData = [];
        var responseData = {!! json_encode($responses) !!};
        
        for (var i = 0; i < labels.length; i++) {
            chartData.push(responseData[labels[i]] || 0);
        }
        
        // Generate colors
        var colors = generateColors(4);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Respons',
                    data: chartData,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: '{!! addslashes($question) !!}'
                    }
                },
                scales: {
                    x: {
                        min: 0,
                        max: 3,
                        ticks: {
                            callback: function(value) {
                                return value + 1;
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    @endforeach
});
</script>
@endsection
