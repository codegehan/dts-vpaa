<?php 
ob_start();
include("header.php");
$sender = $_SESSION['userno'];
if(isset($_POST['sendDocument'])) {
    $transactionCode = 'DOC-'. date('His');
    $description = strtoupper($_POST['description']);
    $purpose = strtoupper($_POST['purpose']);
    $sendTo = $_POST['sendTo'];
    // echo '<script>alert("'. $sendTo .'")</script>';
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
        
        if($sendTo == "ALL") {
            $fetchAllOffices = "SELECT Department_No as departmentcode FROM department WHERE Department_No > 1 AND `Level` = 0";
            $fetchAllOfficeResult = $db->fetchAll($fetchAllOffices);
            foreach ($fetchAllOfficeResult as $dpt) {
                $transactionCode = 'DOC-' . date('His') . substr(microtime(), 2, 3);
                $insertTransaction = "INSERT INTO files (Transaction_Code, Sender, Receiving_Office, Description, Purpose, Filename) VALUES (?, ?, ?, ?, ?, ?)";
                $db->query($insertTransaction, [$transactionCode, $sender, $dpt["departmentcode"], $description, $purpose, $filename]);

                $insertAllQuery = "INSERT INTO file_logs (Transaction_Code, Receiving_office) VALUES (?, ?)";
                $db->query($insertAllQuery, [$transactionCode, $dpt["departmentcode"]]);
                // echo '<script>alert("'. $dpt["departmentcode"] .'")</script>';
            }
        } else {
            $checkDepartmentLevel = "SELECT `Level` FROM department WHERE Department_No = ?";
            $checkDepartmentLevelResult = $db->fetch($checkDepartmentLevel, [$sendTo]);
            if($checkDepartmentLevelResult["Level"] == 1) {
                $childDepartmentQuery = "SELECT Department_No FROM department WHERE Parent_No = ?";
                $childDepartmentQueryResult = $db->fetchAll($childDepartmentQuery, [$sendTo]);
                foreach ($childDepartmentQueryResult as $cdqr) {
                    $transactionCode = 'DOC-' . date('His') . substr(microtime(), 2, 3);
                    $insertTransaction = "INSERT INTO files (Transaction_Code, Sender, Receiving_Office, Description, Purpose, Filename) VALUES (?, ?, ?, ?, ?, ?)";
                    $db->query($insertTransaction, [$transactionCode, $sender, $cdqr["Department_No"], $description, $purpose, $filename]);

                    $insertTransactionDetail = "INSERT INTO file_logs (Transaction_Code, Receiving_Office) VALUES (?, ?)";
                    $db->query($insertTransactionDetail, [$transactionCode, $cdqr["Department_No"]]);
                }
            } else {
                $transactionCode = 'DOC-' . date('His') . substr(microtime(), 2, 3);
                $insertTransaction = "INSERT INTO files (Transaction_Code, Sender, Receiving_Office, Description, Purpose, Filename) VALUES (?, ?, ?, ?, ?, ?)";
                $db->query($insertTransaction, [$transactionCode, $sender, $sendTo, $description, $purpose, $filename]);

                $insertTransactionDetail = "INSERT INTO file_logs (Transaction_Code, Receiving_Office) VALUES (?, ?)";
                $db->query($insertTransactionDetail, [$transactionCode, $sendTo]);
            }
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
                        <div class="mb-3">
                            <label for="sendTo" class="form-label">Send To</label>
                            <?php 
                                if(!empty($vpaaData)) { 
                                 if (strtoupper($_SESSION['accounttype']) == 'ADMIN' || strtoupper($_SESSION['departmentno']) == 101) { ?>
                                    <select class="form-control" name="sendTo">
                                        <option value=""></option>
                                        <option value="ALL">ALL</option>
                                        <?php 
                                            foreach($allDepartmentData as $dpt) { ?>
                                                <option value="<?=$dpt['Department_No']?>"><?=strtoupper($dpt['Department_Description']). " - " . strtoupper($dpt['Campus_Description'])?></option>
                                        <?php } ?>
                                    </select>
                                 <?php } else { ?>
                                    <input type="text" class="form-control" value="<?=$vpaaData[0]['Department_No']?>" name="sendTo" hidden>
                                    <input type="text" class="form-control" value="<?=strtoupper($vpaaData[0]['Department_Description']) . " - " . strtoupper($vpaaData[0]['Campus_Description'])?>" name="description" disabled>
                                 <?php }?>
                                
                            <?php } else { ?>
                                <input type="text" class="form-control" value="No department registered" name="description" disabled>
                            <?php } ?>
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
                                    <option value="REJECTED">Rejected</option>
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
                                                    if(htmlspecialchars(strtoupper($doc['Status'])) == "PENDING") { ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php } else if(htmlspecialchars(strtoupper($doc['Status'])) == "APPROVED") { ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-danger">Rejected</span>
                                                    <button class="btn btn-sm btn-secondary py-0 view-details" data-toggle="modal" data-target="#noteModal" data-note="<?php echo $doc['Note']; ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
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
                                        <h5 class="modal-title" id="noteModalLabel">Reason of declined document</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <textarea class="form-control text-danger" id="noteDisplayModal" disabled rows=5></textarea>
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

    // Add event listeners for search inputs
    statusSearch.addEventListener('change', updateSearchParams);
    
    // Add debounce for text search
    let searchTimeout;
    detailSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(updateSearchParams, 500); // Wait 500ms after user stops typing
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

$('#noteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var note = button.data('note');
    $('#noteDisplayModal').val(note);
});
</script>
<?php include("footer.php");?>
