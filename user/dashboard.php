<?php 
include("header.php");

// Example data - Replace with your actual database queries
$incomingCount = $count_incoming;  // Get from your DB
$pendingCount = $count_outgoing;   // Get from your DB
$completedCount = $count_completed; // Get from your DB

$currentMonth = date('m');
$previousMonth = date('m', strtotime('-1 month'));

$monthlyData = $db->fetchAll("SELECT 
    DATE_FORMAT(Action_Date,'%b') as month,
    DATE_FORMAT(Action_Date,'%m') as month_num,
    SUM(CASE WHEN Status = 'PENDING' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN Status IN ('APPROVED', 'DISSAPROVED') THEN 1 ELSE 0 END) as completed_count
FROM `file_logs` 
GROUP BY DATE_FORMAT(Action_Date,'%m'), month
ORDER BY DATE_FORMAT(Action_Date,'%m') ASC");

// Create array for all months
$allMonths = [];
for($i = 1; $i <= 12; $i++) {
    $monthNum = str_pad($i, 2, '0', STR_PAD_LEFT);
    $allMonths[] = [
        'month' => date('M', strtotime("2024-$monthNum-01")),
        'pending' => 0,
        'completed' => 0
    ];
}

// Merge actual data with all months
foreach($monthlyData as $row) {
    $monthIndex = intval($row['month_num']) - 1; // Convert month number to 0-based index
    $allMonths[$monthIndex]['pending'] = $row['pending_count'];
    $allMonths[$monthIndex]['completed'] = $row['completed_count'];
}

$monthlyData = $allMonths;
?>

<div class="main-content p-3">
    <div class="container">
        <!-- Dashboard Header -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="h3 header-title" style="text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);">Dashboard</h2>
            </div>
            <hr>
        </div>
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <!-- Incoming Documents Card -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-left-primary shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Incoming Documents</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $incomingCount; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-arrow-bar-down fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Documents Card -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-left-info shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Pending Documents</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingCount; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-hourglass-top fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Documents Card -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-left-success shadow">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Completed Documents</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $completedCount; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-check2-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Document Processing Chart -->
            <div class="col-xl-12 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Document Processing Overview</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="documentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>


            <!-- DIRI NALANG KULANG KULANG KULANG KULANG KULANG KULANG -->
            <!-- Recent Activities -->
            <!-- <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline-small">
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #123</strong> - Processed</p>
                                <p class="text-muted small">5 minutes ago</p>
                            </div>
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #122</strong> - Received</p>
                                <p class="text-muted small">15 minutes ago</p>
                            </div>
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #121</strong> - Pending Review</p>
                                <p class="text-muted small">1 hour ago</p>
                            </div>
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #121</strong> - Pending Review</p>
                                <p class="text-muted small">1 hour ago</p>
                            </div>
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #121</strong> - Pending Review</p>
                                <p class="text-muted small">1 hour ago</p>
                            </div>
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #121</strong> - Pending Review</p>
                                <p class="text-muted small">1 hour ago</p>
                            </div>
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #121</strong> - Pending Review</p>
                                <p class="text-muted small">1 hour ago</p>
                            </div>
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #121</strong> - Pending Review</p>
                                <p class="text-muted small">1 hour ago</p>
                            </div>
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #121</strong> - Pending Review</p>
                                <p class="text-muted small">1 hour ago</p>
                            </div>
                            <div class="timeline-item">
                                <p class="mb-2"><strong>Document #121</strong> - Pending Review</p>
                                <p class="text-muted small">1 hour ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- DIRI TAMAN -->
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

<!-- Initialize Chart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('documentChart').getContext('2d');
    const monthlyData = <?php echo json_encode($monthlyData); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(data => data.month),
            datasets: [
                {
                    label: 'Incoming Documents',
                    data: monthlyData.map(data => data.incoming),
                    borderColor: 'rgb(78, 115, 223)',
                    tension: 0.3,
                    fill: false
                },
                {
                    label: 'Pending Documents',
                    data: monthlyData.map(data => data.pending),
                    borderColor: 'rgb(135, 206, 235)',
                    tension: 0.3,
                    fill: false
                },
                {
                    label: 'Completed Documents',
                    data: monthlyData.map(data => data.completed),
                    borderColor: 'rgb(28, 200, 138)',
                    tension: 0.3,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<!-- Custom CSS for timeline -->
<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }

.timeline-small {
    padding-right: 10px; /* Add padding for scrollbar */
}
.timeline-small .timeline-item {
    padding-bottom: 1rem;
    border-left: 2px solid #e3e6f0;
    padding-left: 20px;
    position: relative;
}

.timeline-small .timeline-item:before {
    content: '';
    position: absolute;
    left: 4px;
    top: 5px;
    width: 10px;
    height: 10px;
    background: #4e73df;
    border-radius: 50%;
}

.timeline-small .timeline-item:last-child {
    padding-bottom: 0;
}
.timeline-small::-webkit-scrollbar {
    width: 6px;
}
.timeline-small::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.timeline-small::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.timeline-small::-webkit-scrollbar-thumb:hover {
    background: #555;
}
.timeline-small {
    max-height: 260px; /* Adjusted to show approximately 4 items */
    overflow-y: auto;
    scrollbar-width: thin; /* For Firefox */
    scrollbar-color: #888 #f1f1f1; /* For Firefox */
}
</style>

<?php include("footer.php"); ?>