<?php
$active = 'services';
$title = 'Service Management';
include __DIR__ . '/../layouts/header.php';

if (isset($_GET['debug_path'])) {
    echo "Current path: " . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    exit;
}

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
        <h1 class="h3 mb-0 text-gray-800">Services</h1>
        <a href="/services/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus"></i> Add New Service
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

    <!-- Service Stats Cards -->
    <div class="row">
        <!-- Total Services Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-grid fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Service Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['categories'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Popular Service Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Most Popular Service
                            </div>
                            <?php if ($stats['popular']): ?>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($stats['popular']['name']) ?></div>
                                <div class="small text-muted"><?= $stats['popular']['appointment_count'] ?> appointments</div>
                            <?php else: ?>
                                <div class="small text-muted">No appointment data</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Longest Service Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Longest Service</div>
                            <?php if ($stats['longest']): ?>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($stats['longest']['name']) ?></div>
                                <div class="small text-muted"><?= $stats['longest']['duration'] ?> minutes</div>
                            <?php else: ?>
                                <div class="small text-muted">No services defined</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Table with Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Manage Services</h6>
            <a href="/services/create" class="btn btn-sm btn-primary">
                <i class="bi bi-plus"></i> Add New Service
            </a>
        </div>
        <div class="card-body">
            <!-- Search and Filter Form -->
            <form action="/services" method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" 
                                   placeholder="Search for services..."
                                   name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="category" class="form-select bg-light border-0 small">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['category']) ?>" 
                                        <?= (isset($category) && $category === $cat['category']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <?php if (!empty($search) || !empty($category)): ?>
                            <a href="/services" class="btn btn-secondary w-100">
                                <i class="bi bi-x-circle"></i> Clear
                            </a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select bg-light border-0 small">
                            <option value="name-asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name-asc') ? 'selected' : '' ?>>Name (A-Z)</option>
                            <option value="name-desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name-desc') ? 'selected' : '' ?>>Name (Z-A)</option>
                            <option value="price-asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price-asc') ? 'selected' : '' ?>>Price (Low to High)</option>
                            <option value="price-desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price-desc') ? 'selected' : '' ?>>Price (High to Low)</option>
                            <option value="duration-asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'duration-asc') ? 'selected' : '' ?>>Duration (Shortest)</option>
                            <option value="duration-desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'duration-desc') ? 'selected' : '' ?>>Duration (Longest)</option>
                        </select>
                    </div>
                </div>
            </form>

            <?php if (empty($services)): ?>
                <div class="text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-grid-3x3-gap" style="font-size: 3rem; color: #d1d3e2;"></i>
                    </div>
                    <?php if (!empty($search) || !empty($category)): ?>
                        <h4>No services found matching your criteria</h4>
                        <p class="text-muted">Try a different search term or category filter</p>
                    <?php else: ?>
                        <h4>No services yet</h4>
                        <p class="text-muted">Add your first service to get started</p>
                        <a href="/services/create" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Add New Service
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Service Name</th>
                                <th>Category</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Appointments</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td>
                                        <div class="service-color-indicator" style="background-color: <?= htmlspecialchars($service['color']) ?>"></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($service['name']) ?></div>
                                        <?php if (!empty($service['description'])): ?>
                                            <div class="small text-muted"><?= htmlspecialchars(substr($service['description'], 0, 60)) ?><?= strlen($service['description']) > 60 ? '...' : '' ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($service['category'] ?: '—') ?></td>
                                    <td><?= $service['duration'] ?> min</td>
                                    <td>
                                        <?php if ($service['price'] > 0): ?>
                                            $<?= number_format($service['price'], 2) ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $service['appointment_count'] ?? 0 ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                        <a href="/services/edit/<?= $service['id'] ?>" class="btn btn-outline-secondary" title="Edit Service">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDelete(<?= $service['id'] ?>, '<?= addslashes(htmlspecialchars($service['name'])) ?>')"
                                                    title="Delete Service">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
                <h5 class="modal-title">Delete Service</h5>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this service?</p>
                <p class="font-weight-bold text-danger" id="deleteServiceName"></p>
                <p><strong>Note:</strong> Services with associated appointments cannot be deleted.</p>
                <?php if (isset($service['appointment_count']) && $service['appointment_count'] > 0): ?>
                    <p class="text-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        This service has <?= $service['appointment_count'] ?> appointment(s). Deleting it will affect those appointments.
                    </p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="post">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Delete Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.service-color-indicator {
    width: 20px;
    height: 20px;
    border-radius: 4px;
}
</style>

<script>
// Handle service deletion
function confirmDelete(serviceId, serviceName) {
    document.getElementById('deleteServiceName').textContent = serviceName;
    document.getElementById('deleteForm').action = '/services/delete/' + serviceId;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>