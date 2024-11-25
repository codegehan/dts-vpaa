<?php 

include("header.php");

?>

<div class="main-content p-3">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="h3 header-title" style="text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);">
                    Archiving
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
                    <form>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description">
                        </div>
                        <div class="mb-3">
                            <label for="purpose" class="form-label">Purpose</label>
                            <textarea class="form-control" id="purpose" rows=4></textarea>
                        </div>
                        <!-- File Upload Section -->
                        <div class="mb-3">
                            <label class="form-label">Upload Documents</label>
                            <div class="file-upload-wrapper">
                                <div class="file-upload-message">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Drag and drop files here or click to browse</p>
                                    <p class="small text-muted">Supported formats: PDF, DOC, DOCX, XLS, XLSX</p>
                                </div>
                                <input type="file" id="fileInput" class="file-upload-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                            </div>
                            
                            <!-- File Preview Container -->
                            <div id="filePreviewContainer" class="file-preview-container mt-3"></div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
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
                    
                     <form action="">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="campus">Campus</label>
                                    <select class="form-control" name="campus" id="campus">
                                        <option value=""></option>
                                        <option value="1">Dapitan - Main</option>
                                        <option value="2">Dipolog</option>
                                        <option value="3">Katipunan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">File Category</label>
                                    <select class="form-control" name="campus" id="campus">
                                        <option value=""></option>
                                        <option value="1">Prospectus</option>
                                        <option value="2">Memorandums</option>
                                        <option value="3">Communcations</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Date</label>
                                    <input type="date" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-primary" style="width:150px;">Search</button>
                        </div>
                    </form>
                    <hr>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="fileTable" style="font-size:14px;">
                            <thead>
                                <tr>
                                    <th>
                                        Transaction Code
                                        <i class="fas fa-sort ms-1"></i>
                                    </th>
                                    <th>
                                        Purpose
                                        <i class="fas fa-sort ms-1"></i>
                                    </th>
                                    <th>
                                        Description
                                        <i class="fas fa-sort ms-1"></i>
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TRX-2024-001</td>
                                    <td>Documentation Request</td>
                                    <td>Student Records Request</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-2024-001</td>
                                    <td>Documentation Request</td>
                                    <td>Student Records Request</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-2024-001</td>
                                    <td>Documentation Request</td>
                                    <td>Student Records Request</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-2024-001</td>
                                    <td>Documentation Request</td>
                                    <td>Student Records Request</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-2024-001</td>
                                    <td>Documentation Request</td>
                                    <td>Student Records Request</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-2024-001</td>
                                    <td>Documentation Request</td>
                                    <td>Student Records Request</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-2024-001</td>
                                    <td>Documentation Request</td>
                                    <td>Student Records Request</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-2024-001</td>
                                    <td>Documentation Request</td>
                                    <td>Student Records Request</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-2024-002</td>
                                    <td>Clearance</td>
                                    <td>Graduation Clearance</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-2024-003</td>
                                    <td>Leave Request</td>
                                    <td>Medical Leave Application</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="View">
                                            <i class="bi bi-cloud-arrow-down-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="dataTables_info">
                            Showing 1 to 3 of 25 entries
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
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
        'pdf': 'fa-file-pdf',
        'doc': 'fa-file-word',
        'docx': 'fa-file-word',
        'xls': 'fa-file-excel',
        'xlsx': 'fa-file-excel'
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
        const iconClass = fileIcons[extension] || 'fa-file';
        
        const filePreview = document.createElement('div');
        filePreview.className = 'file-preview-item';
        filePreview.innerHTML = `
            <i class="fas ${iconClass}" style="color: ${extension === 'pdf' ? '#dc3545' : '#4e73df'}"></i>
            <div class="file-info">
                <p class="file-name">${file.name}</p>
                <p class="file-size">${formatFileSize(file.size)}</p>
            </div>
            <div class="delete-file">
                <i class="fas fa-times"></i>
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
});
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const table = document.getElementById('fileTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length - 1; j++) {
                const cell = cells[j];
                if (cell.textContent.toLowerCase().indexOf(searchText) > -1) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }
    });

    // Sorting functionality
    document.querySelectorAll('th').forEach(header => {
        header.addEventListener('click', function() {
            const table = document.getElementById('fileTable');
            const rows = Array.from(table.getElementsByTagName('tr'));
            const headers = Array.from(table.getElementsByTagName('th'));
            const index = headers.indexOf(this);
            
            if (index > -1 && index < 4) { // Don't sort the actions column
                const isAscending = this.classList.contains('sort-asc');
                
                // Reset all headers
                headers.forEach(h => {
                    h.classList.remove('sort-asc', 'sort-desc');
                    h.querySelector('i').className = 'fas fa-sort ms-1';
                });

                // Set new sort direction
                if (isAscending) {
                    this.classList.add('sort-desc');
                    this.querySelector('i').className = 'fas fa-sort-down ms-1';
                } else {
                    this.classList.add('sort-asc');
                    this.querySelector('i').className = 'fas fa-sort-up ms-1';
                }

                // Sort rows
                const sortedRows = rows.slice(1).sort((a, b) => {
                    const aValue = a.cells[index].textContent;
                    const bValue = b.cells[index].textContent;
                    return isAscending ? 
                        bValue.localeCompare(aValue) : 
                        aValue.localeCompare(bValue);
                });

                // Reattach rows
                const tbody = table.querySelector('tbody');
                tbody.innerHTML = '';
                sortedRows.forEach(row => tbody.appendChild(row));
            }
        });
    });
});
</script>
<?php include("footer.php");?>
