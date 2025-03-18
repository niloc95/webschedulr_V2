<?php
$active = 'clients';
$title = 'Client Management';
include __DIR__ . '/../layouts/header.php';

// Helper for flash messages
function getFlashMessage($type) {
    if (isset($_SESSION[$type])) {
        $message = $_SESSION[$type];
        unset($_SESSION[$type]);
        return $message;
    }
    return null;
}
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Clients</h1>
        <a href="/clients/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus"></i> Add New Client
        </a>
    </div>

    <?php if ($errorMessage = getFlashMessage('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($successMessage = getFlashMessage('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($successMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Client Stats Cards -->
    <div class="row">
        <!-- Total Clients Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Clients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Clients Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                New Clients (30 days)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['recent'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clients with Appointments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Clients with Appointments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['with_appointments'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Clients Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Active Clients (30 days)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['active'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table with Search - KEEP THIS SECTION -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Manage Clients</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="clientOptionsDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in"
                    aria-labelledby="clientOptionsDropdown">
                    <div class="dropdown-header">Client Options:</div>
                    <a class="dropdown-item" href="/clients/create">
                        <i class="bi bi-plus-circle fa-sm fa-fw mr-2 text-gray-400"></i>
                        Add New Client
                    </a>
                    <a class="dropdown-item" href="#" id="exportClientsBtn">
                        <i class="bi bi-download fa-sm fa-fw mr-2 text-gray-400"></i>
                        Export Clients (CSV)
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importClientsModal">
                        <i class="bi bi-upload fa-sm fa-fw mr-2 text-gray-400"></i>
                        Import Clients
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <form action="/clients" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for clients by name, email or phone..."
                           name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="/clients" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (empty($clients)): ?>
                <div class="text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-people" style="font-size: 3rem; color: #d1d3e2;"></i>
                    </div>
                    <?php if (!empty($search)): ?>
                        <h4>No clients found matching "<?= htmlspecialchars($search) ?>"</h4>
                        <p class="text-muted">Try a different search term or clear the filter</p>
                    <?php else: ?>
                        <h4>No clients yet</h4>
                        <p class="text-muted">Add your first client to get started</p>
                        <a href="/clients/create" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Add New Client
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Client Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th class="text-center">Appointments</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td>
                                        <a href="/clients/show/<?= $client['id'] ?>" class="font-weight-bold text-primary">
                                            <?= htmlspecialchars($client['name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($client['email'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($client['phone'] ?: '-') ?></td>
                                    <td class="text-center">
                                        <a href="/clients/show/<?= $client['id'] ?>#appointments" class="btn btn-sm btn-light">
                                            <i class="bi bi-calendar3"></i> View
                                        </a>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($client['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/clients/show/<?= $client['id'] ?>" class="btn btn-outline-primary" title="View Client">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/clients/edit/<?= $client['id'] ?>" class="btn btn-outline-secondary" title="Edit Client">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDelete(<?= $client['id'] ?>, '<?= addslashes(htmlspecialchars($client['name'])) ?>')"
                                                    title="Delete Client">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total'] > 1): ?>
                <nav aria-label="Client pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $pagination['hasPrev'] ? '' : 'disabled' ?>">
                            <a class="page-link" href="?page=<?= $pagination['prevPage'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>
                        
                        <?php
                        $startPage = max(1, $pagination['current'] - 2);
                        $endPage = min($pagination['total'], $startPage + 4);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i == $pagination['current'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $pagination['total']): ?>
                            <?php if ($endPage < $pagination['total'] - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $pagination['total'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <?= $pagination['total'] ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="page-item <?= $pagination['hasNext'] ? '' : 'disabled' ?>">
                            <a class="page-link" href="?page=<?= $pagination['nextPage'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                
                <div class="text-muted text-center mt-2">
                    Showing <?= count($clients) ?> of <?= $pagination['total_records'] ?> clients
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Client</h5>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this client?</p>
                <p class="font-weight-bold text-danger" id="deleteClientName"></p>
                <p class="text-danger"><strong>Warning:</strong> This will also delete all appointments associated with this client. This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="post">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Delete Client</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Import Clients Modal -->
<div class="modal fade" id="importClientsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Clients</h5>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Upload a CSV file with client data. The CSV should have the following columns:</p>
                <ul>
                    <li>name (required)</li>
                    <li>email</li>
                    <li>phone</li>
                    <li>address</li>
                    <li>notes</li>
                </ul>
                <form id="importForm" action="/clients/import" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="csvFile" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="skipHeader" name="skipHeader" checked>
                        <label class="form-check-label" for="skipHeader">
                            Skip first row (header row)
                        </label>
                    </div>
                </form>
                <p class="small text-muted">
                    <i class="bi bi-info-circle"></i> Need a template? <a href="#" id="downloadTemplateBtn">Download a sample CSV template</a>.
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="importForm" class="btn btn-primary">Import Clients</button>
            </div>
        </div>
    </div>
</div>

<script>
// Handle client deletion
function confirmDelete(clientId, clientName) {
    document.getElementById('deleteClientName').textContent = clientName;
    document.getElementById('deleteForm').action = '/clients/delete/' + clientId;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Handle client export
document.getElementById('exportClientsBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Create a form to submit the request
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '/clients/export';
    
    // Add search parameter if exists
    const searchParam = new URLSearchParams(window.location.search).get('search');
    if (searchParam) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'search';
        input.value = searchParam;
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
});

// Handle template download
document.getElementById('downloadTemplateBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Create template content
    const csvContent = "name,email,phone,address,notes\n" +
                    "John Doe,john@example.com,555-123-4567,\"123 Main St, Anytown\",First client\n" +
                    "Jane Smith,jane@example.com,555-987-6543,\"456 Oak Ave, Sometown\",Regular customer";
    
    // Create download link
    const link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent);
    link.target = '_blank';
    link.download = 'client_template.csv';
    link.click();
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>