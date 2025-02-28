<?php 
include("header.php");

$fetchAllCampusLists = "SELECT * FROM campus";
$campusListsData = $db->fetchAll($fetchAllCampusLists);

if(isset($_POST['Add_Campus'])) {
    $campusDescription = trim($_POST['Campus_Description'] ?? '');
    // Check if the description is empty
    if (!empty($campusDescription)) {
        $newCampusSql = "INSERT INTO campus (Campus_Description) VALUES (?)";
        $newCampusParam = [$campusDescription];
        if ($db->execute($newCampusSql, $newCampusParam)) {
            echo "<script>alert('New campus added!')</script>";
            echo "<script>window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Error adding new campus!')</script>";
        }
    } else {
        echo "Please provide a campus description.";
    }
}

if(isset($_POST['Delete_Campus'])) {
    $campusNo = $_POST['Campus_No'];
    $deleteCampusSql = "DELETE FROM campus WHERE Campus_No = ?";
    if ($db->execute($deleteCampusSql, [$campusNo])) {
        echo "<script>alert('Campus deleted successfully!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Error deleting campus!');</script>";
    }
}
if(isset($_POST['Edit_Campus'])) {
    $campusNo = trim($_POST['Campus_No'] ?? '');
    $campusDescription = trim($_POST['Campus_Description'] ?? '');
    $editCampusSql = "UPDATE campus SET Campus_Description = ? WHERE Campus_No = ?";
    $editCampusParam = [$campusDescription, $campusNo];
    if ($db->execute($editCampusSql, $editCampusParam)) {
        echo "<script>alert('Campus updated successfully!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Error updating campus!');</script>";
    }

    
}
?>

<div class="main-content p-3">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="h3 header-title" style="text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);">
                    Campus
                </h2>
            </div>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">New Campus</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3" id="campusForm">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" name="Campus_Description" id="campusDescription" required>
                        </div>
                        <div>
                            <button type="submit" name="Add_Campus" id="addCampusBtn" class="btn btn-primary w-100">Save</button>
                            <button type="submit" name="Edit_Campus" id="editCampusBtn" class="btn btn-secondary w-100" hidden>Edit</button>
                            <button type="button" name="Cancel_Edit" id="cancelCampusBtn" onclick="CancelEdit()" class="btn btn-primary w-100 mt-2" hidden>Cancel</button>
                        </div> 
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Campus Lists</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="campusTable" style="font-size:14px;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Description</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($campusListsData as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['Campus_No']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Campus_Description']); ?></td>
                                        <td class="text-center">
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="Campus_No" value="<?= htmlspecialchars($row['Campus_No']); ?>">
                                                <button type="button" class="btn btn-sm btn-primary me-1" title="Edit" onclick="EditCampus('<?=htmlspecialchars($row['Campus_No']);?>', '<?=htmlspecialchars($row['Campus_Description']);?>')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="submit" name="Delete_Campus" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete campus <?= htmlspecialchars($row['Campus_No']); ?>?');">
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
.btn-secondary {
    background-color: #f6c23e;
    border: none;
    padding: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background-color: #e2c680;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(226, 198, 128, 0.4);
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
    $('#campusTable').DataTable();
});
var addCampusBtn = document.getElementById('addCampusBtn');
var editCampusBtn = document.getElementById('editCampusBtn');
var cancelEditBtn = document.getElementById('cancelCampusBtn');
 // Edit campus
 function EditCampus(campusNo, campusDesc){
        var campusDescription = document.getElementById('campusDescription');
        addCampusBtn.hidden = true;
        editCampusBtn.hidden = false;
        cancelEditBtn.hidden = false;
        campusDescription.value = campusDesc;
        let hiddenInput = document.getElementById('campusNo');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'Campus_No';
            hiddenInput.id = 'campusNo';
            campusForm.appendChild(hiddenInput);
        }
        hiddenInput.value = campusNo;
    }
function CancelEdit(){
    addCampusBtn.hidden = false;
    editCampusBtn.hidden = true;
    cancelEditBtn.hidden = true;
    campusDescription.value = "";
    let hiddenInput = document.getElementById('campusNo');
    if (hiddenInput) {
        campusForm.removeChild(hiddenInput);
    }
}
</script>
<?php include("footer.php");?>
