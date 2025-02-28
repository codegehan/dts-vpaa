<?php 

include("header.php");
$fetchAllDepartmentLists = "SELECT *,campus.Campus_Description FROM department LEFT JOIN campus ON department.Campus = campus.Campus_No";
$departmentListsData = $db->fetchAll($fetchAllDepartmentLists);

if(isset($_POST['Add_Department'])) {
    // $parentNo = 0;
    // $level = 0;
    $departmentNo = $_POST['Department_No'];

    if(strlen($departmentNo) == 3) {
        $parentNo = 0;
    } 
    elseif (strlen($departmentNo) == 5) {
        $parentNo = substr($departmentNo, 0, 3);
    } 
    elseif (strlen($departmentNo) == 7) {
        $parentNo = substr($departmentNo, 0, 5);
    }

    $departmentDescription = strtolower(trim($_POST['Department_Description'] ?? ''));
    $campus = trim($_POST['Campus'] ?? '');
    $category = trim($_POST['Category'] ?? '');
    // Check if the description is empty
    if (!empty($departmentDescription) && !empty($campus)) {
        $newDepartmentSql = "INSERT INTO department (Department_No, Department_Description,Parent_No, Campus, Category) VALUES (?,?,?,?,?)";
        $newDepartmentParam = [$departmentNo, strtoupper($departmentDescription), $parentNo, $campus, $category];
        if ($db->execute($newDepartmentSql, $newDepartmentParam)) {
            echo "<script>alert('New department added!')</script>";
            echo "<script>window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Error adding new department!')</script>";
        }
    } else {
        echo "Please provide a department description and campus.";
    }
}

if(isset($_POST['Delete_Department'])) {
    $departmentNo = $_POST['Department_No'];
    $deleteDepartmentSql = "DELETE FROM Department WHERE Department_No = ?";
    if ($db->execute($deleteDepartmentSql, [$departmentNo])) {
        echo "<script>alert('Department deleted successfully!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Error deleting campus!');</script>";
    }
}
?>

<div class="main-content p-3">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="h3 header-title" style="text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);">
                    Department
                </h2>
            </div>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">New Department</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="departmentno" class="form-label">Department No</label>
                            <input type="text" class="form-control" id="departmentno" name="Department_No" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="Department_Description" required>
                        </div>
                        <div class="mb-3">
                            <label for="campus" class="form-label">Campus</label>
                            <select class="form-control" id="campus" name="Campus">
                                <option value=""></option>
                                <?php 
                                    $fetchAllCampus = "SELECT * FROM campus";
                                    $allCampusData = $db->fetchAll($fetchAllCampus);
                                    foreach($allCampusData as $campus) { ?>
                                        <option value="<?=$campus['Campus_No']?>"><?=ucwords($campus['Campus_Description'])?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-control" id="category" name="Category">
                                <option value=""></option>
                                <option value="UNIT">Unit</option>
                                <option value="COLLEGE">College</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" name="Add_Department" class="btn btn-primary w-100">Save</button>
                        </div> 
                    </form>
                </div>
            </div>
        </div>

        

        <div class="col-xl-8 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Department Lists</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="departmentTable" style="font-size:14px;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Description</th>
                                    <th>Campus</th>
                                    <th>Category</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($departmentListsData as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['Department_No']); ?></td>
                                        <td><?php echo ucwords(htmlspecialchars($row['Department_Description'])); ?></td>
                                        <td><?php echo ucwords(htmlspecialchars($row['Campus_Description'])); ?></td>
                                        <td><?php echo ucwords(htmlspecialchars($row['Category'])); ?></td>
                                        <td class="text-center">
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="Department_No" value="<?= htmlspecialchars($row['Department_No']); ?>">
                                                <button type="submit" name="Delete_Department" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete department <?= htmlspecialchars($row['Department_No']); ?>?');">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
    $('#departmentTable').DataTable();
});
</script>
<?php include("footer.php");?>
