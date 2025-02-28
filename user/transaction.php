<?php 
ob_start();
include("header.php");
$sender = $_SESSION['userno'];
if(isset($_POST['sendDocument'])) {
    $transactionCode = 'DOC-'. date('His');
    $description = strtoupper($_POST['description']);
    $purpose = strtoupper($_POST['purpose']);

    $sentTo_Test = $_POST['sendTo_Test'];
    $decodedSentTo = json_decode($sentTo_Test, true);

    $sentToSingle = $_POST['sendTo'];

    // if (empty($decodedSentTo)) {
    //     // $_SESSION['message'] = "Please select office to sent.";
    //     // $_SESSION['messagestatus'] = 'Error';
    //     // header("Location: " . $_SERVER['PHP_SELF']);
    //     // exit();
        
    // }
   
    // Get file extension
    $fileExt = pathinfo($_FILES['fileInput']['name'], PATHINFO_EXTENSION);
    // Create new filename with transaction code
    $filename = pathinfo($_FILES['fileInput']['name'], PATHINFO_FILENAME) . '_' . $transactionCode . '.' . $fileExt;
    try {
        $db->pdo->beginTransaction();
        // Upload file first to check if successful
        $targetDir = "../files/";
        $targetFile = $targetDir . $filename;
        
        if (!move_uploaded_file($_FILES["fileInput"]["tmp_name"], $targetFile)) {
            throw new Exception("Error uploading file");
        }

        if (!empty($decodedSentTo)) {
            foreach($decodedSentTo as $dpNo) {
                $transactionCode = 'DOC-' . date('His') . substr(microtime(), 2, 3) . rand(1, 99);
                $insertTransaction = "INSERT INTO files (Transaction_Code, Sender, Receiving_Office, Description, Purpose, Filename) VALUES (?, ?, ?, ?, ?, ?)";
                $db->query($insertTransaction, [$transactionCode, $sender, $dpNo['sent_to_department'], $description, $purpose, $filename]);
    
                $insertTransactionDetail = "INSERT INTO file_logs (Transaction_Code, Receiving_Office) VALUES (?, ?)";
                $db->query($insertTransactionDetail, [$transactionCode, $dpNo['sent_to_department']]);
            }
        } else {
            $transactionCode = 'DOC-' . date('His') . substr(microtime(), 2, 3) . rand(1, 99);
            $insertTransaction = "INSERT INTO files (Transaction_Code, Sender, Receiving_Office, Description, Purpose, Filename) VALUES (?, ?, ?, ?, ?, ?)";
            $db->query($insertTransaction, [$transactionCode, $sender, $sentToSingle, $description, $purpose, $filename]);

            $insertTransactionDetail = "INSERT INTO file_logs (Transaction_Code, Receiving_Office) VALUES (?, ?)";
            $db->query($insertTransactionDetail, [$transactionCode, $sentToSingle]);
        }
        
        
        $db->pdo->commit();
        $_SESSION['message'] = "Transaction Sent Successfully";
        $_SESSION['messagestatus'] = 'Success';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        $db->pdo->rollBack();

        // Remove uploaded file if exists after rollback
        if (isset($targetFile) && file_exists($targetFile)) {
            unlink($targetFile);
        }
        $_SESSION['message'] = "Failed to make transaction";
        $_SESSION['messagestatus'] = 'Error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } 
    ob_end_flush();
}

$vpaaSql = "SELECT *, Campus_Description FROM department LEFT JOIN campus ON department.Campus = campus.Campus_No WHERE Department_No = 101";
$vpaaData = $db->fetchAll($vpaaSql);

$allDepartment = "SELECT *, COALESCE(Campus_Description, '') AS Campus_Description 
                FROM department LEFT JOIN campus ON department.Campus = campus.Campus_No 
                WHERE Department_No != 101
                ORDER BY Department_No";
$allDepartmentData = $db->fetchAll($allDepartment);

?>
<div class="main-content p-3">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="h3 header-title" style="text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);">
                    Transactions 
                </h2>
            </div>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">New File</h6>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        <div class="mb-3">
                            <label for="purpose" class="form-label">Purpose</label>
                            <textarea class="form-control" id="purpose" rows=4 name="purpose" required></textarea>
                        </div>
                        <input type="hidden" id="sendTo_Test" name="sendTo_Test">
                        <div class="mb-3">
                            <label for="sendTo" class="form-label">Send To</label>
                            <?php 
                            if(!empty($vpaaData)) { 
                                if (strtoupper($_SESSION['accounttype']) == 'ADMIN' || strtoupper($_SESSION['accounttype']) == 'STAFF') { ?>
                                   <button type="button" class="btn btn-success form-control" data-toggle="modal" data-target="#selectOFfice">Choose</button>
                                <?php } else { ?>
                                   <input type="text" class="form-control" value="<?=$vpaaData[0]['Department_No']?>" name="sendTo" hidden>
                                   <input type="text" class="form-control" value="<?=strtoupper($vpaaData[0]['Department_Description']) . " - " . strtoupper($vpaaData[0]['Campus_Description'])?>" name="description" disabled>
                                <?php }?>
                            <?php } ?>
                            <!-- <button type="button" class="btn btn-success form-control" data-toggle="modal" data-target="#selectOFfice">Choose</button> -->
                        </div>

                        <!-- File Upload Section -->
                        <div class="mb-3">
                            <label class="form-label">Upload Documents</label>
                            <div class="file-upload-wrapper">
                                <div class="file-upload-message">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Drag and drop files here or click to browse</p>
                                    <p class="small text-muted">Supported formats: PDF (Max 10MB per file)</p>
                                </div>
                                <input type="file" id="fileInput" name="fileInput" class="file-upload-input" multiple accept=".pdf" required onchange="validateFiles(this)">
                            </div>
                            
                            <!-- File Preview Container -->
                            <div id="filePreviewContainer" class="file-preview-container mt-3"></div>
                        </div>
                        <script>
                            function validateFiles(input) {
                                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                                const files = input.files;
                                
                                for(let i = 0; i < files.length; i++) {
                                    if(files[i].size > maxSize) {
                                        toastr.error(`File "${files[i].name}" exceeds 10MB size limit`);
                                        input.value = ''; // Clear the input
                                        return false;
                                    }
                                }
                                return true;
                            }
                        </script>

                        <div>
                            <button type="submit" name="sendDocument" class="btn btn-primary w-100">Send</button>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
        
        <!-- MODAL FOR LIST OF UNTIS/COLLEGES -->
        <div class="modal fade" id="selectOFfice" tabindex="-1" aria-labelledby="selectOFficeLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selectOFficeLabel">UNIT/COLLEGE</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 row align-items-end">
                            <div class="col">
                                <label for="sendTo" class="form-label">Send To</label>
                                <select class="form-control mb-2" name="sendTo" id="sendToSearch">
                                    <option value=""></option>
                                    <option value="ALL">ALL</option>
                                    <option value="UNIT">UNIT</option>
                                    <option value="COLLEGE">COLLEGE</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="searchBar" class="form-label">Search</label>
                                <input type="text" id="searchBar" class="form-control mb-2" placeholder="Search...">
                            </div>    
                        </div>

                        <ul class="list-group" id="departmentList">
                        <?php 
                            $selectAllOfficeSQL = "SELECT d.*, c.Campus_Description
                                                FROM department d
                                                LEFT JOIN campus c ON d.Campus = c.Campus_No";
                            $fetchOfficeResult = $db->fetchAll($selectAllOfficeSQL);
                            foreach ($fetchOfficeResult as $fo) { ?>
                                <li class="list-group-item" data-category="<?=$fo['Category']?>">
                                    <input type="checkbox" class="me-1" data-department-no="<?=$fo['Department_No']?>">
                                    <?=$fo['Department_Description']?> - <?=$fo['Campus_Description']?>
                                </li>
                            <?php } ?>
                        </ul>
                        <!-- Pagination Controls -->
                        <nav aria-label="Page navigation" class="mt-3">
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination buttons will be dynamically added here -->
                            </ul>
                        </nav>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="doneSelectionButton" data-dismiss="modal">Done</button>
                    </div>
                </div>
            </div>
        </div>

        

        <div class="col-xl-8 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">File Lists</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 d-flex align-items-center">
                                <label for="statusSearch" class="form-label me-2 mb-0">Filter Status</label>
                                <select class="form-control" id="statusSearch">
                                    <option value="">All</option>
                                    <option value="PENDING">Pending</option>
                                    <option value="APPROVED">Approved</option>
                                    <option value="DISSAPPROVED">Dissapproved</option>
                                    <option value="ACKNOWLEDGE">Acknowledge</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 d-flex align-items-center">
                                <label for="detailSearch" class="form-label me-2 mb-0">Search Details</label>
                                <input type="text" class="form-control" id="detailSearch" placeholder="Search in filtered results...">
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" style="font-size: 12px;" id="documentTable">
                            <thead>
                                <tr>
                                    <th class="align-top">T-Code</th>
                                    <th class="align-top">Office</th>
                                    <th class="align-top">Description</th>
                                    <th class="align-top">Purpose</th>
                                    <th class="align-top">Date Submitted</th>
                                    <th class="align-top">Viewed On</th>
                                    <th class="align-top">Action</th>
                                    <th class="text-center align-top">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                $limit = 10;
                                $offset = ($page - 1) * $limit;
                                // Base query - Get files and latest file_log entry for receiving office
                                $baseQuery = "SELECT f.*, 
                                            d.Department_Description,
                                            fl.Receiving_Office,
                                            fl.Note,
                                            f.Date_Created,
                                            f.Viewed_On
                                            FROM files f
                                            LEFT JOIN (
                                                SELECT Transaction_Code, Receiving_Office, Note
                                                FROM file_logs fl1
                                                WHERE (Transaction_Code, Action_Date) IN (
                                                    SELECT Transaction_Code, MAX(Action_Date)
                                                    FROM file_logs
                                                    GROUP BY Transaction_Code
                                                )
                                            ) fl ON f.Transaction_Code = fl.Transaction_Code
                                            LEFT JOIN department d ON fl.Receiving_Office = d.Department_No
                                            WHERE f.Sender = ? ";

                                // Add search conditions if provided
                                $searchStatus = isset($_GET['status']) ? $_GET['status'] : '';
                                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                                $params = [$sender];

                                if (!empty($searchStatus)) {
                                    $baseQuery .= " AND f.Status = ?";
                                    $params[] = $searchStatus;
                                }

                                if (!empty($searchTerm)) {
                                    $baseQuery .= " AND (f.Transaction_Code LIKE ? OR d.Department_Description LIKE ? OR f.Description LIKE ? OR f.Purpose LIKE ?)";
                                    $searchPattern = "%$searchTerm%";
                                    $params = array_merge($params, [$searchPattern, $searchPattern, $searchPattern, $searchPattern]);
                                }

                                // Add limit and offset
                                $baseQuery .= " LIMIT ? OFFSET ?";
                                $params = array_merge($params, [$limit, $offset]);

                                $allMyDocument = $db->fetchAll($baseQuery, $params);

                                // Get total records for pagination (without limit/offset)
                                $totalQuery = str_replace(" LIMIT ? OFFSET ?", "", $baseQuery);
                                $totalResult = $db->fetchAll($totalQuery, array_slice($params, 0, -2));
                                $totalRecords = count($totalResult);
                                $totalPages = ceil($totalRecords / $limit);

                                if (count($allMyDocument) > 0) :
                                    foreach($allMyDocument as $doc) : ?>
                                        <tr>
                                            <td><?=htmlspecialchars($doc['Transaction_Code']);?></td>
                                            <td><?=htmlspecialchars(strtoupper($doc['Department_Description'])); ?></td>
                                            <td><?=htmlspecialchars(ucwords($doc['Description']));?></td>
                                            <td><?=htmlspecialchars(ucwords($doc['Purpose']));?></td>
                                            <td><?=htmlspecialchars(ucwords($doc['Date_Created']));?></td>
                                            <td><?=htmlspecialchars(ucwords($doc['Viewed_On']));?></td>
                                            <td class="text-center">
                                                <?php 
                                                    $fileUrl = "../files/" . htmlspecialchars($doc['Filename']);
                                                    $fileName = htmlspecialchars(pathinfo($doc['Filename'], PATHINFO_FILENAME));
                                                    $fileExtension = pathinfo($doc['Filename'], PATHINFO_EXTENSION);
                                                ?>
                                                <a href="<?=$fileUrl?>" onclick="sendLastView('<?=$doc['Transaction_Code']?>')" target="_blank" class="">
                                                    View
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <?php 
                                                    if(htmlspecialchars(strtoupper($doc['Status'])) == "PENDING") { ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php } else if(htmlspecialchars(strtoupper($doc['Status'])) == "APPROVED") { ?>
                                                    <span class="badge bg-success" data-toggle="modal" data-target="#noteModal" data-note="<?=$doc['Note']?>" data-status="APPROVED" >Approved</span>
                                                <?php } else if(htmlspecialchars(strtoupper($doc['Status'])) == "ACKNOWLEDGE") { ?>
                                                    <span class="badge bg-primary" data-toggle="modal" data-target="#noteModal" data-note="<?=$doc['Note']?>" data-status="ACKNOWLEDGE" >Acknowledge</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-danger mb-1" data-toggle="modal" data-target="#noteModal" data-note="<?=$doc['Note']?>" data-status="PENDING" >Dissapproved</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php endforeach;  ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No data available</td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo !empty($searchStatus) ? '&status='.$searchStatus : ''; ?><?php echo !empty($searchTerm) ? '&search='.$searchTerm : ''; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($searchStatus) ? '&status='.$searchStatus : ''; ?><?php echo !empty($searchTerm) ? '&search='.$searchTerm : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo !empty($searchStatus) ? '&status='.$searchStatus : ''; ?><?php echo !empty($searchTerm) ? '&search='.$searchTerm : ''; ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>

                        <div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="noteModalLabel">Note/Comments</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <textarea class="form-control" id="noteDisplayModal" disabled rows=5></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<style>
/* Adjust text size */
.jstree-anchor {
    font-size: 26px; /* Adjust text size */
    margin-bottom: 20px;
}

/* Adjust checkbox size */
.jstree-checkbox {
    transform: scale(1.5); /* Increase checkbox size */
    margin-right: 5px; /* Add spacing */
}

th {
    background-color: lightgray !important;
}
.file-upload-wrapper {
    border: 2px dashed #ccc;
    border-radius: 5px;
    padding: 20px;
    text-align: center;
    background-color: #f8f9fa;
    cursor: pointer;
    position: relative;
}

.file-upload-wrapper:hover {
    border-color: #4e73df;
    background-color: #f1f3ff;
}

.file-upload-message {
    color: #6c757d;
}

.file-upload-message i {
    font-size: 2em;
    margin-bottom: 10px;
    color: #4e73df;
}

.file-upload-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.file-preview-item {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    max-width: 250px;
}

.file-preview-item i {
    margin-right: 10px;
    font-size: 1.5em;
}

.file-preview-item .file-info {
    flex-grow: 1;
    overflow: hidden;
}

.file-preview-item .file-name {
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-preview-item .file-size {
    margin: 0;
    font-size: 0.75em;
    color: #6c757d;
}

.file-preview-item .delete-file {
    color: #dc3545;
    cursor: pointer;
    padding: 5px;
    margin-left: 10px;
}

.file-preview-item .delete-file:hover {
    color: #bd2130;
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
    const fileInput = document.getElementById('fileInput');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    
    // File type icons mapping
    const fileIcons = {
        'pdf': 'bi-file-pdf',
        // 'doc': 'fa-file-word',
        // 'docx': 'fa-file-word',
        // 'xls': 'fa-file-excel',
        // 'xlsx': 'fa-file-excel'
    };

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Create file preview element
    function createFilePreview(file) {
        const extension = file.name.split('.').pop().toLowerCase();
        const iconClass = fileIcons[extension] || 'bi-file';
        console.log(file.type);

        if (file.type !== 'application/pdf') {
            toastr.error("Only PDF files is allowed to be uploaded");
            return;
        }

        const filePreview = document.createElement('div');
        filePreview.className = 'file-preview-item';
        filePreview.innerHTML = `
            <i class="fas ${iconClass}" style="color: ${extension === 'pdf' ? '#dc3545' : '#4e73df'}"></i>
            <div class="file-info">
                <p class="file-name">${file.name}</p>
                <p class="file-size">${formatFileSize(file.size)}</p>
            </div>
            <div class="delete-file">
                <i class="bi bi-x"></i>
            </div>
        `;

        // Add delete functionality
        const deleteButton = filePreview.querySelector('.delete-file');
        deleteButton.addEventListener('click', () => {
            filePreview.remove();
            // Create a new FileList without the deleted file
            const dt = new DataTransfer();
            const files = fileInput.files;
            for (let i = 0; i < files.length; i++) {
                if (files[i] !== file) {
                    dt.items.add(files[i]);
                }
            }
            fileInput.files = dt.files;
        });

        return filePreview;
    }

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        filePreviewContainer.innerHTML = ''; // Clear previous previews
        const files = e.target.files;
        for (let file of files) {
            const preview = createFilePreview(file);
            filePreviewContainer.appendChild(preview);
        }
    });

    // Handle drag and drop
    const uploadWrapper = document.querySelector('.file-upload-wrapper');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadWrapper.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadWrapper.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadWrapper.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        uploadWrapper.classList.add('border-primary');
    }

    function unhighlight(e) {
        uploadWrapper.classList.remove('border-primary');
    }

    uploadWrapper.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        
        // Trigger change event
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }

    // Add search functionality
    const statusSearch = document.getElementById('statusSearch');
    const detailSearch = document.getElementById('detailSearch');
    
    function updateSearchParams() {
        const status = statusSearch.value;
        const search = detailSearch.value;
        let url = new URL(window.location.href);
        
        // Update or remove status parameter
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        // Update or remove search parameter
        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }
        
        // Reset to page 1 when search params change
        url.searchParams.set('page', '1');
        
        // Navigate to new URL
        window.location.href = url.toString();
    }    
    // Add debounce for text search
    let searchTimeout;
    detailSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(updateSearchParams, 1500); // Wait 500ms after user stops typing
    });

    // Set initial values from URL params
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status')) {
        statusSearch.value = urlParams.get('status');
    }
    if (urlParams.has('search')) {
        detailSearch.value = urlParams.get('search');
    }
});


// Pagination and Search Implementation
$(document).ready(function() {
    const sendToSearch = $('#sendToSearch');
    const searchBar = $('#searchBar');
    const departmentList = $('#departmentList');
    const paginationContainer = $('#pagination');
    const itemsPerPage = 10;
    let currentPage = 1;
    let filteredItems = [];
    
    // Initialize pagination
    function initializePagination() {
        // Store all list items for filtering
        const allItems = departmentList.children().toArray();
        filteredItems = allItems;
        
        // Apply initial filters
        applyFilters();
    }
    
    // Apply both category and search filters
    function applyFilters() {
        const categoryFilter = sendToSearch.val().toUpperCase();
        const searchQuery = searchBar.val().toLowerCase().trim();
        
        // Get all list items
        const allItems = departmentList.children().toArray();
        
        // Filter items based on category and search query
        filteredItems = allItems.filter(function(item) {
            const category = $(item).data('category').toUpperCase();
            const itemText = $(item).text().toLowerCase().trim();
            
            // Category filter
            const categoryMatch = categoryFilter === '' || 
                                 categoryFilter === 'ALL' || 
                                 category === categoryFilter;
            
            // Search filter
            const searchMatch = searchQuery === '' || 
                               itemText.includes(searchQuery);
            
            return categoryMatch && searchMatch;
        });
        
        // Update display with filtered items
        displayItems(currentPage);
        
        // Rebuild pagination based on filtered results
        buildPagination();
    }
    
    // Display items for current page
    function displayItems(page) {
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        
        // Hide all items first
        departmentList.children().hide();
        
        // Show only the items for current page
        filteredItems.slice(startIndex, endIndex).forEach(function(item) {
            $(item).show();
        });
    }
    
    // Build pagination controls with limited page numbers
    function buildPagination() {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        paginationContainer.empty();
        
        // Don't show pagination if only one page or no results
        if (totalPages <= 1) {
            return;
        }
        
        // Previous button
        const prevButton = $('<li class="page-item"><a class="page-link" href="#">Previous</a></li>');
        if (currentPage === 1) {
            prevButton.addClass('disabled');
        }
        prevButton.on('click', function(e) {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                displayItems(currentPage);
                buildPagination(); // Rebuild pagination to update visible page numbers
            }
        });
        paginationContainer.append(prevButton);
        
        // Determine which page numbers to show (max 3)
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, startPage + 2);
        
        // Adjust if we're at page 2 (special case as per requirement)
        if (currentPage === 2 && totalPages > 3) {
            startPage = 1;
            endPage = 4; // Show pages 1, 2, 3, 4 when on page 2
        } 
        // General case - ensure we show 3 pages when possible
        else if (endPage - startPage < 2 && totalPages > 3) {
            if (currentPage <= 2) {
                startPage = 1;
                endPage = 3;
            } else if (currentPage >= totalPages - 1) {
                startPage = totalPages - 2;
                endPage = totalPages;
            }
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            const pageItem = $(`<li class="page-item"><a class="page-link" href="#">${i}</a></li>`);
            
            if (i === currentPage) {
                pageItem.addClass('active');
            }
            
            pageItem.on('click', function(e) {
                e.preventDefault();
                currentPage = i;
                displayItems(currentPage);
                buildPagination(); // Rebuild pagination to update visible page numbers
            });
            
            paginationContainer.append(pageItem);
        }
        
        // Next button
        const nextButton = $('<li class="page-item"><a class="page-link" href="#">Next</a></li>');
        if (currentPage === totalPages) {
            nextButton.addClass('disabled');
        }
        nextButton.on('click', function(e) {
            e.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                displayItems(currentPage);
                buildPagination(); // Rebuild pagination to update visible page numbers
            }
        });
        paginationContainer.append(nextButton);
    }
    
    // Event handlers
    searchBar.on('input', function() {
        currentPage = 1;
        applyFilters();
    });
    
    sendToSearch.on('change', function() {
        currentPage = 1;
        applyFilters();
    });
    
    // Initialize the pagination on document ready
    initializePagination();
});

// Add event listeners to all checkboxes
$(document).ready(function() {
    // First, modify the PHP-generated checkboxes to include the Department_No value
    // Since we can't change your PHP directly, we'll add this in JavaScript
    
    // Select all checkboxes in the department list
    const checkboxes = $('#departmentList input[type="checkbox"]');
    const doneButton = $('#doneSelectionButton');
    let sendTo_data = "";
    
    // For each checkbox, find the Department_No from the parent li element
    checkboxes.each(function() {
        const $parentLi = $(this).closest('li');
        const departmentNo = $parentLi.data('department-no'); // Assuming this attribute exists or will be added
        const departmentName = $parentLi.text().trim();
        
        // Add the Department_No as a data attribute to the checkbox
        $(this).attr('data-department-no', departmentNo);
        
        // Add change event listener to each checkbox
        $(this).on('change', function() {
            const isChecked = $(this).prop('checked');
            const deptNo = $(this).data('department-no');
            
            console.log({
                sent_to_department: deptNo,
                checked: isChecked,
                department_name: departmentName
            });
            
            // Append to some data structure or form field if needed
            if (isChecked) {
                // You can append to a hidden field or array as needed
                // For example:
                if (!window.selectedDepartments) {
                    window.selectedDepartments = [];
                }
                window.selectedDepartments.push({
                    sent_to_department: deptNo
                });
                sendTo_data = JSON.stringify(window.selectedDepartments);
            } else {
                // Remove from selection if unchecked
                if (window.selectedDepartments) {
                    window.selectedDepartments = window.selectedDepartments.filter(
                        dept => dept.sent_to_department !== deptNo
                    );
                    console.log('Current selections after removal:', window.selectedDepartments);
                }
            }
        });
    });

    doneButton.on('click', function(e) {
            e.preventDefault();
            console.log(sendTo_data);
            $('#sendTo_Test').val(sendTo_data)
        });

});


$('#noteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var note = button.data('note');
    var status = button.data('status');
    var noteDisplay = document.getElementById('noteDisplayModal');

    if (status === "APPROVED") {
        noteDisplay.classList.add('text-success');
    } else if (status === "ACKNOWLEDGE") {
        noteDisplay.classList.add('text-primary');
    } else {
        noteDisplay.classList.add('text-danger');
    }

    $('#noteDisplayModal').val(note);
});
</script>
<?php include("footer.php");?>
