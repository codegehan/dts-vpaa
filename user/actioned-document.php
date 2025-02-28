<?php 
ob_start();
include("header.php");
$sender = $_SESSION['userno'];
$departmentNo = $_SESSION['departmentno'];

if(isset($_POST['Accept_Document'])) {
    try{
        $db->pdo->beginTransaction();
        $transactionCode = $_POST['Transaction_Code'];
        $updateStatus = "INSERT INTO file_logs (Transaction_Code,Receiving_Office, Action_By, Status) VALUES (?, ?, ?, 'APPROVED')";
        $db->query($updateStatus, [$transactionCode, $departmentNo, $sender]);

        $updateFileStatus = "UPDATE files SET Status = 'APPROVED' WHERE Transaction_Code = ?";
        $db->query($updateFileStatus, [$transactionCode]);

        $db->pdo->commit();
        $_SESSION['message'] = "File Processed Successfully";
        $_SESSION['messagestatus'] = 'Success';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        $db->pdo->rollBack();
        $_SESSION['message'] = "Error processing file";
        $_SESSION['messagestatus'] = 'Error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
if(isset($_POST['Decline_Document'])) {
    try{
        $db->pdo->beginTransaction();
        $transactionCode = $_POST['Transaction_Code'];
        $note = $_POST['note'];
        $updateStatus = "INSERT INTO file_logs (Transaction_Code, Action_By,Receiving_Office, Status, Note) VALUES (?, ?, ?, 'DISSAPPROVED', ?)";
        $db->query($updateStatus, [$transactionCode, $sender, $departmentNo, $note]);

        $updateFileStatus = "UPDATE files SET Status = 'DISSAPPROVED' WHERE Transaction_Code = ?";
        $db->query($updateFileStatus, [$transactionCode]);

        $db->pdo->commit();
        $_SESSION['message'] = "File Processed Successfully";
        $_SESSION['messagestatus'] = 'Success';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        $db->pdo->rollBack();
        $_SESSION['message'] = "Error processing file";
        $_SESSION['messagestatus'] = 'Error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
if(isset($_POST['action']) && strtoupper($_POST['action']) == 'UPDATEVIEWSTATUS') {
    $transCode = $_POST['transaction_code'];
    $sql = "UPDATE files SET Viewed_On = CURRENT_TIMESTAMP() WHERE Transaction_Code = ?";
    $db->query($sql, [$transCode]);
}
ob_end_flush();
?>

<div class="main-content p-3">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="h3 header-title" style="text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);">
                    Actioned
                </h2>
            </div>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Actioned Files</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="documentTable">
                            <thead>
                                <tr>
                                    <th class="align-top">Code</th>
                                    <th class="align-top">Sender</th>
                                    <th class="align-top">Description</th>
                                    <th class="align-top">Purpose</th>
                                    <th class="align-top">File</th>
                                    <th class="align-top text-center">Status</th>
                                    <!-- <th class="text-center align-top">Actions</th> -->
                                </tr>
                            </thead>
                            <tbody>                               
                                <?php 
                                $fetchIncomingDocument = "SELECT 
                                    f.*,
                                    fl.*,
                                    d.Department_Description,
                                    au.Campus as user_campus,
                                    c.Campus_Description as camp_desc,
                                    d.Department_Description as dep_desc,
                                    CONCAT(
                                        (SELECT c2.Campus_Description 
                                         FROM account_user au2
                                         LEFT JOIN campus c2 ON au2.Campus = c2.Campus_No
                                         WHERE au2.User_No = f.Sender),
                                        ' - ',
                                        (SELECT d2.Department_Description
                                         FROM account_user au2 
                                         LEFT JOIN department d2 ON au2.Department = d2.Department_No
                                         WHERE au2.User_No = f.Sender)
                                    ) as sender_info
                                    FROM files f
                                    LEFT JOIN file_logs fl ON f.Transaction_Code = fl.Transaction_Code
                                    LEFT JOIN department d ON fl.Receiving_Office = d.Department_No 
                                    LEFT JOIN account_user au ON f.Sender = au.User_No
                                    LEFT JOIN campus c ON au.Campus = c.Campus_No
                                    WHERE fl.Receiving_Office = ?
                                    AND fl.Status = 'DISSAPPROVED'
                                    OR fl.Status = 'APPROVED'
                                    OR fl.Status = 'ACKNOWLEDGE'";
                                $allIncomingDocument = $db->fetchAll($fetchIncomingDocument, [$departmentNo]);
                                foreach($allIncomingDocument as $incoming) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($incoming['Transaction_Code']); ?></td>
                                        <td><?php echo htmlspecialchars($incoming['sender_info']); ?></td>
                                        <td style="white-space: pre-wrap; word-wrap: break-word; max-width: 400px;"><?php echo htmlspecialchars(strtoupper($incoming['Description'])); ?></td>
                                        <td style="white-space: pre-wrap; word-wrap: break-word; max-width: 400px;"><?php echo htmlspecialchars(ucwords($incoming['Purpose'])); ?></td>
                                        <td style="width: 200px;">
                                            <?php 
                                                $fileUrl = "../files/" . htmlspecialchars($incoming['Filename']);
                                                $fileName = htmlspecialchars(pathinfo($incoming['Filename'], PATHINFO_FILENAME));
                                                $fileExtension = pathinfo($incoming['Filename'], PATHINFO_EXTENSION);
                                            ?>
                                            <span><u><i><?= $fileName . '.' . $fileExtension ?></i></u> = </span>
                                            <a href="<?=$fileUrl?>" onclick="sendLastView('<?=$incoming['Transaction_Code']?>')" target="_blank" class="">View</a>
                                            <!-- <a href="<?= $fileUrl ?>" download class="">Download</a> -->
                                        </td>
                                        <td class="text-center"><?php echo $incoming['Status'];?></td>
                                        <!-- <td class="text-center" style="width: 140px;">
                                            <form method="POST">
                                                <input type="hidden" name="Transaction_Code" value="<?php echo htmlspecialchars($incoming['Transaction_Code']); ?>">
                                                <button type="submit" class="btn btn-sm btn-primary me-1" name="Accept_Document" title="Accept" onclick="return confirm('Are you sure you want to accept document <?= htmlspecialchars($incoming['Transaction_Code']); ?>?');">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" title="Decline" data-toggle="modal" data-target="#exampleModal" data-id="<?php echo $incoming['Transaction_Code']; ?>">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        </td> -->
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Decline Document</h5>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" id="transactionCode" name="Transaction_Code">
                        <div class="mb-3">
                            <label for="note" class="form-label">Reason</label>
                            <textarea class="form-control" id="note" name="note" rows=5></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="Decline_Document" class="btn btn-danger">Decline</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->

    <script>
    // $('#exampleModal').on('show.bs.modal', function (event) {
    //     var button = $(event.relatedTarget);
    //     var transactionCode = button.data('id');
    //     $('#transactionCode').val(transactionCode);
    // });
    </script>

</div>

<style>
th {
    background-color: lightgray !important;
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
</script>
<?php include("footer.php");?>
