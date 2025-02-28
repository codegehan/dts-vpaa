<?php 
date_default_timezone_set('Asia/Manila');
session_start(); 
include('../connection/conn.php');
$db = new DatabaseConnector();
if (!isset($_SESSION["fullname"])) {
    header("Location: ../");
    exit();
} else {
    $user = $_SESSION['userno'];
    $departmentNo = $_SESSION['departmentno'];
}

$sql_incoming = "SELECT COUNT(*) as incoming FROM files WHERE UPPER(status) = 'PENDING' AND Receiving_Office = ?";
$result_incoming = $db->fetchAll($sql_incoming, [$departmentNo]);
$count_incoming = $result_incoming[0]['incoming'];

$sql_outgoing = "SELECT COUNT(*) as outgoing FROM files WHERE UPPER(status) = 'PENDING' AND Sender = ?";
$result_outgoing = $db->fetchAll($sql_outgoing, [$user]);
$count_outgoing = $result_outgoing[0]['outgoing'];

$sql_completed = "SELECT COUNT(*) as completed FROM files WHERE UPPER(status) = 'APPROVED' OR UPPER(status) = 'DISSAPPROVED' AND Sender = ?";
$result_completed = $db->fetchAll($sql_completed, [$user]);
$count_completed = $result_completed[0]['completed'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VP Unit - Document Tracking</title>
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../node_modules/bootstrap-icons/font/bootstrap-icons.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
     <link rel="stylesheet" href="../node_modules/font-awesome/css/font-awesome.min.css">
     <link rel="stylesheet" href="../assets/dataTables.css">
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" /> -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
     <script src="../node_modules/jquery/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="../node_modules/toastr/build/toastr.min.css">
    <script src="../node_modules/toastr/build/toastr.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>

</head>
<style>
    :root {
        --sidebar-width: 280px;
        --header-height: 200px;
        --footer-height: 80px;
        --collapsed-sidebar-width: 0px;
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        width: var(--sidebar-width);
        background: rgba(255, 193, 7, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        border-radius: 0px 30px 20px 0px;
    }

    .sidebar-header {
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        height: var(--header-height);
        flex-shrink: 0;
    }

    .sidebar-header img {
        width: 80px;
        height: 80px;
        margin-bottom: 1rem;
    }

    .sidebar-header h4 {
        color: #2c3e50;
        font-size: 1.2rem;
        margin: 0;
        font-weight: 600;
    }

    .sidebar-header p {
        color: #2c3e50;
        font-size: 0.9rem;
        margin: 0;
        opacity: 0.8;
    }

    /* Navigation Container */
    .navigation-container {
        flex: 1;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        padding-bottom: var(--footer-height);
    }

    /* Scrollbar Styling */
    .navigation-container::-webkit-scrollbar {
        width: 6px;
    }

    .navigation-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .navigation-container::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 3px;
    }

    .nav-pills .nav-link {
        color: #2c3e50;
        padding: 0.5rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        border-radius: 0;
        margin: 0.2rem 0;
        font-size: 0.8rem;
    }

    .nav-pills .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.3);
        color: #0d47a1;
    }

    .nav-pills .nav-link.active {
        background-color: #0d47a1;
        color: white;
    }

    .nav-pills .nav-link i {
        font-size: 0.8rem;
        width: 24px;
        text-align: center;
    }

    .nav-section {
        padding: 1rem 0;
    }

    .nav-section-title {
        padding: 0.5rem 1.5rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #2c3e50;
        font-weight: 600;
        opacity: 0.6;
    }

    .main-content {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-blue) 100%);
        min-height: 100vh;
        padding: 2rem !important;
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        transition: all 0.3s ease;
    }

    .user-profile {
        position: fixed;
        bottom: 0;
        width: var(--sidebar-width);
        padding: 1rem 1.5rem;
        /* background: rgba(255, 255, 255, 0.1); */
        background: rgba(255, 193, 7, 0.95);
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
        height: var(--footer-height);
        flex-shrink: 0;
    }

    .user-profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-info {
        flex-grow: 1;
    }

    .user-info h6 {
        margin: 0;
        font-size: 0.9rem;
        color: #2c3e50;
    }

    .user-info p {
        margin: 0;
        font-size: 0.8rem;
        color: #2c3e50;
        opacity: 0.8;
    }

    .logout-btn {
        color: #dc3545;
        background: none;
        border: none;
        padding: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        color: #c82333;
        transform: scale(1.1);
    }

    /* Responsive Design */
    @media (max-height: 600px) {
        :root {
            --header-height: 160px;
            --footer-height: 60px;
        }
        
        .sidebar-header {
            padding: 1rem;
        }
        
        .sidebar-header img {
            width: 60px;
            height: 60px;
            margin-bottom: 0.5rem;
        }
    }
    .btn-danger {
        background-color: #dc3545;
        border: none;
        padding: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .btn{
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 71, 161, 0.3);
    }

    /* Add these new responsive styles */
    .sidebar-toggle {
        display: none;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: rgba(255, 193, 7, 0.95);
        border: none;
        padding: 0.5rem;
        border-radius: 5px;
        cursor: pointer;
    }
    table{
        font-size: 14px;
    }
    @media (max-width: 768px) {
        .sidebar-toggle {
            display: block;
        }

        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            padding-top: 4rem;
        }
        .header-title{
            margin-top: 60px;
        }
    }

    /* Optional: Add overlay for mobile */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    @media (max-width: 768px) {
        .sidebar-overlay.active {
            display: block;
        }
    }

    /* Add these new notification badge styles */
    .nav-link .notification-badge {
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
        position: absolute;
        right: 1rem;
        min-width: 1.5rem;
        text-align: center;
    }

    .nav-link {
        position: relative;  /* Add this to make absolute positioning work */
    }

    /* Update wiggle animation keyframes with larger angles */
    @keyframes wiggle {
        0% { transform: rotate(0deg) scale(1); }
        25% { transform: rotate(-50deg) scale(1.3); }
        50% { transform: rotate(0deg) scale(1); }
        75% { transform: rotate(50deg) scale(1.3); }
        100% { transform: rotate(0deg) scale(1); }
    }

    /* Add class for wiggle animation with longer duration */
    .notification-badge.wiggle {
        animation: wiggle 0.5s ease-in-out;
        transform-origin: center center;
    }

    /* Header Title Enhancement */
    .header-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .header-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        height: 3px;
        width: 50px;
        background: linear-gradient(to right, #4e73df, #224abe);
    }

    /* Main Content Layout */
    .main-content {
        background: linear-gradient(135deg, var(--white) 0%, var(--light-blue) 100%);
        min-height: 100vh;
        padding: 2rem !important;
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        transition: all 0.3s ease;
    }

    /* Container Override */
    .container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Card Container */
    .card {
        width: 100%;
        margin-bottom: 2rem;
    }

    /* Table Container */
    .table-responsive {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    /* Header Container */
    .header-container {
        width: 100%;
        margin-bottom: 2rem;
        padding: 0 1rem;
        border-bottom: 2px solid rgba(13, 71, 161, 0.1);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            width: 100%;
        }
    }

    /* DataTables Wrapper */
    .dataTables_wrapper {
        width: 100% !important;
        padding: 0 !important;
    }

    /* Row Modifications */
    .row {
        margin: 0 !important;
        width: 100% !important;
    }
    .badge {
        cursor: pointer;
    }
    .badge:hover {
        transform: scale(1.1);
    }
</style>
<body>
    <!-- Add toggle button -->
    <button class="sidebar-toggle">
        <i class="bi bi-list"></i>
    </button>

    <!-- Add overlay -->
    <div class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/img/logo.png" alt="JRMSU Logo" class="img-fluid">
            <h4>Document Management</h4>
            <p>VP Academic Affairs Unit</p>
        </div>

        <!-- Navigation Container -->
        <div class="navigation-container">
            <!-- Navigation Menu -->
            <div class="nav-section">
                <div class="nav-section-title">Main Menu</div>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php" id="dashboard-link">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="incoming.php" id="incoming-link">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                            Incoming
                            <?php 
                            if($count_incoming > 0){
                                echo '<span class="notification-badge">'.$count_incoming.'</span>';
                            }
                            ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="actioned-document.php" id="actioned-link">
                            <i class="bi bi-check2-circle -arrow-down"></i>
                            Actioned
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="transaction.php" id="transaction-link">
                            <i class="bi bi-file-earmark-text"></i>
                            File Transaction
                            <?php 
                            if($count_outgoing > 0){
                                echo '<span class="notification-badge">'.$count_outgoing.'</span>';
                            }
                            ?>
                        </a>
                    </li>
                    <?php 
                    if(strtoupper($_SESSION['accounttype']) == 'ADMIN'){
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="archiving.php" id="archiving-link">
                            <i class="bi bi-file-earmark-text"></i>
                            File Archiving
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <?php 
                if(strtoupper($_SESSION['accounttype']) == 'ADMIN'){
                    ?>
                    <div class="nav-section">
                        <div class="nav-section-title">Management</div>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="user.php" id="user-link">
                            <i class="bi bi-people"></i>
                            Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="department.php" id="department-link">
                            <i class="bi bi-building"></i>
                            College/Unit
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="campus.php" id="campus-link">
                            <i class="bi bi-building"></i>
                            Campus
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="report.php" id="report-link">
                            <i class="bi bi-file-earmark-text"></i>
                            Report
                        </a>
                    </li>
                    </ul>
                </div>
            <?php } ?>
        </div>

        <!-- User Profile Section -->
        <div class="user-profile">
            <img src="../assets/img/user.png" alt="User Avatar">
            <div class="user-info">
                <h6><?=$_SESSION['email']?></h6>
                <p><?=strtoupper($_SESSION['accounttype'])?> | <?=strtoupper($_SESSION['department'])?> | <?=strtoupper($_SESSION['campus'])?></p>
            </div>
            <!-- <button class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
            </button> -->
            <a class="nav-link" href="logout.php">
                <i class="bi bi-box-arrow-right text-danger"></i>
            </a>
        </div>
    </div>

<?php
    if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messagestatus = $_SESSION['messagestatus'];
    if ($messagestatus == "Error") { echo '<script>toastr.error("' . htmlspecialchars($message) . '")</script>'; } 
    else { echo '<script>toastr.success("' . htmlspecialchars($message) . '")</script>'; }
    unset($_SESSION['message']);
    unset($_SESSION['messagestatus']);
    }
?>

    <!-- Add this JavaScript before closing body tag -->
    <script>
        $(document).ready(function() {
            $('.sidebar-toggle').on('click', function() {
                $('.sidebar').toggleClass('active');
                $('.sidebar-overlay').toggleClass('active');
            });

            $('.sidebar-overlay').on('click', function() {
                $('.sidebar').removeClass('active');
                $('.sidebar-overlay').removeClass('active');
            });

            // Close sidebar when clicking a link (mobile)
            $('.nav-link').on('click', function() {
                if (window.innerWidth <= 768) {
                    $('.sidebar').removeClass('active');
                    $('.sidebar-overlay').removeClass('active');
                }
            });

            // Function to add wiggle animation
            function wiggleNotifications() {
                $('.notification-badge').addClass('wiggle');
                
                // Increased timeout to match longer animation duration
                setTimeout(function() {
                    $('.notification-badge').removeClass('wiggle');
                }, 500);
            }

            // Wiggle every 5 seconds
            setInterval(wiggleNotifications, 5000);

            // Optional: Add wiggle on hover
            $('.nav-link').hover(function() {
                $(this).find('.notification-badge').addClass('wiggle');
            }, function() {
                $(this).find('.notification-badge').removeClass('wiggle');
            });
        });
    </script>
</body>
</html>