<?php 
include("header.php");
require_once("../classes/encryption.php");
$encryption = new Encryption('09124765961'); // change here for key encryption

// At the top of your PHP section, store the POST values if they exist
$formData = [];
$hasError = false;
$activeUserChartData = [];


$sqlCountActiveUser = "SELECT 
    SUM(CASE WHEN Last_Login >= DATE_SUB(NOW(), INTERVAL 10 DAY) THEN 1 ELSE 0 END) as active_count,
    SUM(CASE WHEN Last_Login < DATE_SUB(NOW(), INTERVAL 10 DAY) OR Last_Login IS NULL THEN 1 ELSE 0 END) as inactive_count
FROM account_user";
$resultCountUser = $db->fetch($sqlCountActiveUser);
$activeCount = $resultCountUser['active_count'];
$inactiveCount = $resultCountUser['inactive_count'];
$totalAccountUser = $activeCount + $inactiveCount;
if(isset($_POST['addUser'])) {
    $formData = [
        'firstname' => trim($_POST['firstname']),
        'middlename' => trim($_POST['middlename']),
        'lastname' => trim($_POST['lastname']),
        'suffix' => trim($_POST['suffix']),
        'campus' => trim($_POST['campus']),
        'department' => trim($_POST['department']),
        'accountType' => trim($_POST['accountType']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password'])
    ];
    try {
        $checkEmail = "SELECT * FROM account_user WHERE Email = ?";
        $checkEmailData = $db->fetch($checkEmail, [$formData['email']]);
        if($checkEmailData) {
            echo '<script>toastr.error("Email already exists");</script>';
            // Don't exit or redirect
            $hasError = true;
        }

        if (!$hasError) {
            $checkName = "SELECT * FROM account_user WHERE LOWER(Firstname) = ? AND LOWER(Lastname) = ?";
            $checkNameData = $db->fetch($checkName, [strtolower($formData['firstname']), strtolower($formData['lastname'])]);
            if($checkNameData) {
                echo '<script>toastr.error("Name already exists");</script>';
                // Don't exit or redirect
                $hasError = true;
            }
        }
        if (!$hasError) {
            $addUser = "INSERT INTO account_user (Firstname, Middlename, Lastname, Suffix, Campus, Department, Account_Type, Email, Password) VALUES (UPPER(?),UPPER(?),UPPER(?),UPPER(?),?,?,?,?, SHA2(?, 256))";
            $db->query($addUser, array_values($formData));
            echo '<script>toastr.success("User added successfully");</script>';
        }
    } catch(Exception $e) {
        echo '<script>toastr.error("'.$e->getMessage().'");</script>';
        $hasError = true;
    }
}
if(isset($_POST['updateUser'])) {
    $id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');

    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $suffix = trim($_POST['suffix']);
    $campus = trim($_POST['campus']);
    $department = trim($_POST['department']);
    $accountType = trim($_POST['accountType']);
    $email = trim($_POST['email']);
    $user_no = $encryption->decrypt($id);
    try {
        $updateUser = "UPDATE account_user 
                        SET Firstname = UPPER(?), 
                            Middlename = UPPER(?), 
                            Lastname = UPPER(?), 
                            Suffix = UPPER(?), 
                            Campus  = ?, 
                            Department = ?, 
                            Account_Type = ?, 
                            Email = ?, 
                            Date_Updated = CURRENT_TIMESTAMP() 
                        WHERE User_No = ? ";
        $result = $db->query($updateUser, [$firstname, $middlename, $lastname, $suffix, $campus, $department, $accountType, $email, $user_no]);
        if(!$result) {
            echo '<script>toastr.error("Failed to update user");</script>';
        } else {
            echo '<script>toastr.success("User updated successfully");</script>';
            echo '<script>setTimeout(function() { window.location.href="user.php"; }, 1000);</script>';
        }
    } catch(Exception $e) {
        echo '<script>toastr.error("'.$e->getMessage().'");</script>';
    }
}

if(isset($_GET['id'])) {
    $id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
    $decryptedId = $encryption->decrypt($id);
    $sql = "SELECT UPPER(a.Firstname) AS Firstname,
                UPPER(a.Middlename) AS Middlename,
                UPPER(a.Lastname) AS Lastname,
                UPPER(a.Suffix) AS Suffix,
                a.Account_Type,
                a.Email,
                c.Campus_Description,d.Department_Description 
            FROM account_user a 
            LEFT JOIN campus c ON c.Campus_No = a.Campus 
            LEFT JOIN department d ON d.Department_No = a.Department
            WHERE a.User_No = ?";
    
    $result = $db->fetchAll($sql, [$decryptedId]);
    $u = $result[0];
}
?>

<div class="main-content p-3">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="h3 header-title" style="text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);">
                    Users
                </h2>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">New User</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="firstname" class="form-label">Firstname <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" maxLength="15" required value="<?=isset($_GET['id'])?htmlspecialchars($u['Firstname']):''?>">
                                </div>
                                <div class="col">
                                    <label for="middlename" class="form-label">Middlename</label>
                                    <input type="text" class="form-control" id="middlename" name="middlename" maxLength="15" value="<?=isset($_GET['id'])?htmlspecialchars($u['Middlename']):''?>">
                                </div>
                                <div class="col">
                                    <label for="lastname" class="form-label">Lastname <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" maxLength="15" required value="<?=isset($_GET['id'])?htmlspecialchars($u['Lastname']):''?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="suffix" class="form-label">Suffix</label>
                                    <select class="form-control" id="suffix" name="suffix">      
                                        <option value=""></option>
                                        <option value="jr" <?= (isset($u['Suffix']) && $u['Suffix'] == 'jr') ? 'selected' : '' ?>>Jr</option>
                                        <option value="sr" <?= (isset($u['Suffix']) && $u['Suffix'] == 'sr') ? 'selected' : '' ?>>Sr</option>
                                        <option value="iii" <?= (isset($u['Suffix']) && $u['Suffix'] == 'iii') ? 'selected' : '' ?>>III</option>
                                        <option value="iv" <?= (isset($u['Suffix']) && $u['Suffix'] == 'iv') ? 'selected' : '' ?>>IV</option>
                                        <option value="v" <?= (isset($u['Suffix']) && $u['Suffix'] == 'v') ? 'selected' : '' ?>>V</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="campus" class="form-label">Campus <span class="text-danger">*</span></label>
                                    <select class="form-control" id="campus" name="campus" required>
                                        <option value="" selected></option>
                                        <?php 
                                            $fetchAllCampus = "SELECT * FROM campus";
                                            $allCampusData = $db->fetchAll($fetchAllCampus);
                                            foreach ($allCampusData as $campus) {
                                                $selected = (isset($u['Campus_Description']) && $u['Campus_Description'] == $campus['Campus_Description']) ? 'selected' : '';
                                                echo '<option value="' . $campus['Campus_No'] . '" ' . $selected . '>' . ucwords($campus['Campus_Description']) . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-control" id="department" name="department" required>
                                        <option value=""></option>
                                        <?php 
                                            $fetchAllDepartment = "SELECT department.*, campus.Campus_Description FROM department LEFT JOIN campus ON department.Campus = campus.Campus_No";
                                            $allDepartmentData = $db->fetchAll($fetchAllDepartment);
                                            foreach ($allDepartmentData as $department) {
                                                $selected = (isset($u['Department_Description']) && $u['Department_Description'] == $department['Department_Description']) ? 'selected' : '';
                                                echo '<option value="' . $department['Department_No'] . '" ' . $selected . '>' . ucwords($department['Department_Description']) . ' - '.ucwords($department['Campus_Description']).'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="accountType" class="form-label">Account Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="accountType" name="accountType" required>
                                        <option value=""></option>
                                        <option value="user" <?= (isset($u['Account_Type']) && $u['Account_Type'] == 'user') ? 'selected' : '' ?>>User</option>
                                        <option value="staff" <?= (isset($u['Account_Type']) && $u['Account_Type'] == 'staff') ? 'selected' : '' ?>>Staff</option>
                                        <option value="admin" <?= (isset($u['Account_Type']) && $u['Account_Type'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" maxLength="30" required value="<?=isset($_GET['id'])?htmlspecialchars($u['Email']):''?>">
                                </div>
                                <div class="col">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">   
                                        <input type="password" class="form-control" id="userPassword" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1" name="password" maxLength="10">
                                        <button class="btn btn-outline-secondary" type="button" onclick="showPassword()" id="button-addon1"><i id="eyeDisplay" class="bi bi-eye-slash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>    
                        <div>
                            <?php if(isset($_GET['id'])) { ?>
                                <button type="submit" name="updateUser" class="btn btn-danger" style="width:200px !important;">Update</button>
                                <a href="user.php" class="btn btn-primary" style="width:200px !important;">Cancel</a>
                            <?php } else { ?>
                                <button type="submit" name="addUser" class="btn btn-primary" style="width:200px !important;">Save</button>
                            <?php } ?>
                        </div> 
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header" style="padding-bottom: 15px;">
                    <h6 class="m-0 font-weight-bold text-primary">Active Users</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px; width:100%">
                        <canvas id="activeUsersChart"></canvas>
                        <div class="chart-center-text">
                            <h3 class="mb-0"><?=round($activeCount / $totalAccountUser * 100, 2)?>%</h3>
                            <p class="text-muted">Active</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-1">
        <!-- Users Table -->
        <div class="col-xl-12 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">User List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="userTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Campus</th>
                                    <th>Department</th>
                                    <th>Account Type</th>
                                    <th class="text-start">Last Login</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $fetchAllUsers = "SELECT *,department.Department_Description,campus.Campus_Description FROM account_user LEFT JOIN department ON department.Department_No = account_user.Department LEFT JOIN campus ON campus.Campus_No = account_user.Campus";
                                $allUsersData = $db->fetchAll($fetchAllUsers);
                                foreach($allUsersData as $user) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(ucwords($user['Firstname'] ." ". substr($user['Middlename'], 0, 1). ". " . $user['Lastname'])); ?></td>
                                        <td><?php echo htmlspecialchars(ucwords($user['Campus_Description'])); ?></td>
                                        <td><?php echo htmlspecialchars(ucwords($user['Department_Description'])); ?></td>
                                        <td><?php echo htmlspecialchars(ucwords($user['Account_Type'])); ?></td>
                                        <td class="text-start"><?php echo htmlspecialchars(ucwords($user['Last_Login'])); ?></td>
                                        <td>
                                            <?php 
                                                $lastLogin = $user['Last_Login'];
                                                $tenDaysAgo = strtotime('-10 days');
                                                $lastLoginTimestamp = strtotime($lastLogin);
                                                if ($lastLoginTimestamp >= $tenDaysAgo) { ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="user.php?id=<?=$encryption->encrypt($user['User_No'])?>" class="btn btn-sm btn-info">Update</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
.chart-container {
    position: relative;
}

.chart-center-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}

.table th {
    font-weight: 600;
    color: #344767;
}

.pagination .page-link {
    color: #0d47a1;
}

.pagination .page-item.active .page-link {
    background-color: #0d47a1;
    border-color: #0d47a1;
    color: #fff;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
}

.btn-info {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('activeUsersChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [<?=round($activeCount / $totalAccountUser * 100, 2)?>, <?=round($inactiveCount / $totalAccountUser * 100, 2)?>],
                backgroundColor: [
                    '#0d47a1',
                    '#e9ecef'
                ],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '75%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    $('#userTable').DataTable();
});

function showPassword(){
    const userPassword = document.getElementById('userPassword');
    const eyeDisplay = document.getElementById('eyeDisplay');
    if (userPassword.type === "password") {
        userPassword.type = 'text';
        eyeDisplay.classList.remove('bi-eye-slash');
        eyeDisplay.classList.add('bi-eye');
    } else {
        userPassword.type = 'password';
        eyeDisplay.classList.remove('bi-eye');
        eyeDisplay.classList.add('bi-eye-slash');
    }
    
}
</script>
<?php include("footer.php");?>
