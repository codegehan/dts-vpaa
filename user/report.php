<?php 
ob_start();
include("header.php");
$sender = $_SESSION['userno'];
$departmentNo = $_SESSION['departmentno'];
$month = isset($_GET['mt']) ? htmlspecialchars($_GET['mt'], ENT_QUOTES, 'UTF-8') : date('n');
$currentYear = isset($_GET['yr']) ? htmlspecialchars($_GET['yr'], ENT_QUOTES, 'UTF-8') : date('Y');
$yearList = [];
for ($year = $currentYear - 10; $year <= $currentYear + 10; $year++) { $yearList[] = $year; }

$monthlyReportSql = "SELECT UPPER(d.Department_Description) AS Department, UPPER(c.Campus_Description) AS Campus,
        (
            SELECT COUNT(f_sub.File_No) 
            FROM files f_sub
            LEFT JOIN account_user a_sub ON f_sub.Sender = a_sub.User_No
            WHERE a_sub.Department = d.Department_No
            AND MONTH(f_sub.Date_Created) = ?
            AND YEAR(f_sub.Date_Created) = ?
        ) AS Total_Incoming,
        (
            SELECT COUNT(f_sub.File_No) 
            FROM files f_sub
            LEFT JOIN account_user a_sub ON f_sub.Sender = a_sub.User_No
            WHERE f_sub.Status = 'APPROVED'
            AND a_sub.Department = d.Department_No
            AND MONTH(f_sub.Date_Created) = ?
            AND YEAR(f_sub.Date_Created) = ?
        ) AS Total_Completed,
        (
            SELECT COUNT(f_sub.File_No) 
            FROM files f_sub
            LEFT JOIN account_user a_sub ON f_sub.Sender = a_sub.User_No
            WHERE f_sub.Status = 'DISSAPROVED'
            AND a_sub.Department = d.Department_No
            AND MONTH(f_sub.Date_Created) = ?
            AND YEAR(f_sub.Date_Created) = ?
        ) AS Total_Declined
        FROM department d
        LEFT JOIN account_user a ON d.Department_No = a.Department
        LEFT JOIN campus c ON d.Campus = c.Campus_No
        LEFT JOIN files f ON f.Sender = a.User_No
        GROUP BY d.Department_Description,c.Campus_Description, Total_Incoming, Total_Completed, Total_Declined
        ORDER BY d.Department_Description;";
$monthlyReportResult = $db->fetchAll($monthlyReportSql, [$month,$currentYear,$month,$currentYear,$month,$currentYear]);

$reportTotal = "SELECT 
                (
                    SELECT COUNT(File_No) 
                    FROM files
                    WHERE MONTH(Date_Created) = ?
                    AND YEAR(Date_Created) = ?
                ) AS Total_Incoming,
                (
                    SELECT COUNT(File_No) 
                    FROM files
                    WHERE `Status` = 'APPROVED' 
                    AND MONTH(Date_Created) = ?
                    AND YEAR(Date_Created) = ?
                ) AS Total_Completed,
                (
                    SELECT COUNT(File_No) 
                    FROM files
                    WHERE `Status` = 'DISSAPROVED' 
                    AND MONTH(Date_Created) = ?
                    AND YEAR(Date_Created) = ?
                ) AS Total_Declined";
$reportTotalResult = $db->fetchAll($reportTotal, [$month, $currentYear, $month, $currentYear, $month, $currentYear]);
ob_end_flush();
?>

<div class="main-content p-3">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="h3 header-title" style="text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);">
                    Report
                </h2>
            </div>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center mb-4">
                    <h6 class="m-0 font-weight-bold text-primary">Report for the Month of</h6>
                    <select name="monthForReport" id="monthForReport" onchange="selectedMonthForReport()" class="form-control w-25 ms-3 border-success">
                        <option value="1" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 1) ? 'selected' : ''; ?>>January</option>
                        <option value="2" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 2) ? 'selected' : ''; ?>>February</option>
                        <option value="3" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 3) ? 'selected' : ''; ?>>March</option>
                        <option value="4" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 4) ? 'selected' : ''; ?>>April</option>
                        <option value="5" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 5) ? 'selected' : ''; ?>>May</option>
                        <option value="6" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 6) ? 'selected' : ''; ?>>June</option>
                        <option value="7" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 7) ? 'selected' : ''; ?>>July</option>
                        <option value="8" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 8) ? 'selected' : ''; ?>>August</option>
                        <option value="9" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 9) ? 'selected' : ''; ?>>September</option>
                        <option value="10" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 10) ? 'selected' : ''; ?>>October</option>
                        <option value="11" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 11) ? 'selected' : ''; ?>>November</option>
                        <option value="12" <?php echo (isset($_GET['mt']) && $_GET['mt'] == 12) ? 'selected' : ''; ?>>December</option>
                    </select>
                    <select name="yearList" id="yearList" onchange="selectYearForReport()" class="form-control w-25 ms-3 border-success">
                    <?php foreach ($yearList as $year): ?>
                        <option value="<?=$year?>" <?=$year == $currentYear ? 'selected' : '' ?>><?=$year?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
            <div class="row mb-2">
                <!-- Incoming Documents Card -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-left-primary shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Incoming Documents</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$reportTotalResult[0]['Total_Incoming']?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-box-arrow-down fa-2x text-gray-300"></i>
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
                                        Total Completed Documents</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$reportTotalResult[0]['Total_Completed']?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Declined Documents Card -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-left-success shadow">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Declined Documents</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$reportTotalResult[0]['Total_Declined']?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-ban fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="documentTable">
                        <thead>
                            <tr>
                                <th class="align-top">Deparment</th>
                                <th class="align-top">Campus</th>
                                <th class="text-center align-top fixed-width">Total File Submitted</th>
                                <th class="text-center align-top fixed-width">No. Completed</th>
                                <th class="text-center align-top fixed-width">No. DISSAPROVED</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($monthlyReportResult as $r) : ?>
                                <tr>
                                    <td><?=$r['Department']?></td>
                                    <td><?=$r['Campus']?></td>
                                    <td class="text-center fixed-width"><?=$r['Total_Incoming']?></td>
                                    <td class="text-center fixed-width"><?=$r['Total_Completed']?></td>
                                    <td class="text-center fixed-width"><?=$r['Total_Declined']?></td>
                                </tr>
                            <?php endforeach; ?>                               
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <a href="print-report.php?mt=<?=$month?>&yr=<?=$currentYear?>" target="_blank" class="btn btn-success">Print Report</a>
        </div>
    </div>
</div>

<script>
$('#exampleModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var transactionCode = button.data('id');
    $('#transactionCode').val(transactionCode);
});
</script>
</div>
<style>
th {
    background-color: lightgray !important;
}
.fixed-width {
    width: 180px !important;
}
.btn-primary {
    background-color: #0d47a1;
    border: none;
    padding: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #1565c0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 71, 161, 0.3);
}
.table th {
    background-color: #f8f9fc;
    cursor: pointer;
}

.table th:hover {
    background-color: #eaecf4;
}

.table th i {
    font-size: 0.8em;
    color: #858796;
}

.badge {
    padding: 0.5em 0.75em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Hover effect for table rows */
.table tbody tr:hover {
    background-color: #f8f9fc;
}

/* Custom styles for action buttons */
.btn-sm i {
    font-size: 0.875rem;
}

/* Search input styling */
.input-group {
    width: 250px;
}

/* Status badge colors */
.badge.bg-warning {
    background-color: #f6c23e !important;
}

.badge.bg-success {
    background-color: #1cc88a !important;
}

.badge.bg-danger {
    background-color: #e74a3b !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .input-group {
        width: 100%;
        margin-top: 1rem;
    }
    
    .card-header {
        flex-direction: column;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: stretch !important;
    }
    
    .pagination {
        margin-top: 1rem;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#documentTable').DataTable();
});
function sendLastView(tcode) {
    console.log("Sending update for Transaction codce: " + tcode);
    const lastViewedFormData = new FormData();
    lastViewedFormData.append('action', 'UPDATEVIEWSTATUS');
    lastViewedFormData.append('transaction_code', tcode);
    fetch(window.location.href, {
        method: 'POST',
        body: lastViewedFormData,
    })
    .then()
    .catch(error => {
        console.error('Error during request.');
    })
}

const today = new Date();
const month = today.getMonth();
const year = today.getFullYear();
let selectedMonth = document.getElementById('monthForReport');
let selectedMonthValue = selectedMonth.value;
let selectedYear = document.getElementById('yearList');
let selectedYearValue = selectedYear.value;
function selectedMonthForReport(){
    selectedMonth = document.getElementById('monthForReport');
    selectedMonthValue = selectedMonth.value;
    window.location.href = `report.php?mt=${selectedMonthValue}&yr=${selectedYearValue}`;
}
function selectYearForReport(){
    selectedMonth = document.getElementById('monthForReport');
    selectedMonthValue = selectedMonth.value;
    selectedYear = document.getElementById('yearList');
    selectedYearValue = selectedYear.value;
    window.location.href = `report.php?mt=${selectedMonthValue}&yr=${selectedYearValue}`;
}
</script>
<?php include("footer.php");?>
