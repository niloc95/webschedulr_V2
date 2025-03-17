<?php
$active = 'services';
$title = 'Edit Service';
include __DIR__ . '/../layouts/header.php';

// Helper functions for form handling
function hasError($field) {
    if (isset($_SESSION['errors']) && isset($_SESSION['errors'][$field])) {
        return 'is-invalid';
    }
    return '';
}

function getError($field) {
    if (isset($_SESSION['errors']) && isset($_SESSION['errors'][$field])) {
        return '<div class="invalid-feedback">' . htmlspecialchars($_SESSION['errors'][$field]) . '</div>';
    }
    return '';
}

function getOldValue($field) {
    if (isset($_SESSION['old']) && isset($_SESSION['old'][$field])) {
        return htmlspecialchars($_SESSION['old'][$field]);
    }
    return '';
}

// Service data from controller or old input
$name = isset($service['name']) ? htmlspecialchars($service['name']) : getOldValue('name');
$duration = isset($service['duration']) ? $service['duration'] : getOldValue('duration');
$price = isset($service['price']) ? $service['price'] : getOldValue('price');
$category = isset($service['category']) ? htmlspecialchars($service['category']) : getOldValue('category');
$color = isset($service['color']) ? htmlspecialchars($service['color']) : getOldValue('color');
$description = isset($service['description']) ? htmlspecialchars($service['description']) : getOldValue('description');

// Default color if not set
if (empty($color)) $color = '#3498db';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Service: <?= $name ?></h1>
        <div>
            <a href="/services" class="btn btn-sm btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to Services
            </a>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <!-- Service Edit Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Service Information</h6>
        </div>
        <div class="card-body">
            <form action="/services/update/<?= $service['id'] ?>" method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Service Name *</label>
                        <input type="text" class="form-control <?= hasError('name') ?>" id="name" name="name" value="<?= $name ?>" required>
                        <?= getError('name') ?>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="duration" class="form-label">Duration (minutes) *</label>
                        <input type="number" class="form-control <?= hasError('duration') ?>" id="duration" name="duration" 
                               value="<?= $duration ?>" min="5" step="5" required>
                        <div class="form-text">How long this service typically takes</div>
                        <?= getError('duration') ?>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="price" class="form-label">Price ($)</label>
                        <input type="text" class="form-control <?= hasError('price') ?>" id="price" name="price" 
                               value="<?= $price ?>">
                        <div class="form-text">Leave 0 for no price display</div>
                        <?= getError('price') ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control <?= hasError('category') ?>" id="category" name="category" 
                               value="<?= $category ?>">
                        <div class="form-text">Group similar services together</div>
                        <?= getError('category') ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="color" class="form-label">Calendar Color</label>
                        <input type="color" class="form-control form-control-color w-100 <?= hasError('color') ?>" 
                               id="color" name="color" value="<?= $color ?>">
                        <div class="form-text">Used to identify this service in the calendar</div>
                        <?= getError('color') ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control <?= hasError('description') ?>" id="description" name="description" 
                              rows="3"><?= $description ?></textarea>
                    <div class="form-text">Optional details about what this service includes</div>
                    <?= getError('description') ?>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Update Service</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteServiceModal">
                        Delete Service
                    </button>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">* Required fields</small>
                </div>
            </form>
        </div>
    </div>

    <!-- Optional: Service Statistics Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Service Statistics</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Appointments</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $serviceStats['total_appointments'] ?? 0 ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-calendar-check fs-2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Completed Appointments</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $serviceStats['completed_appointments'] ?? 0 ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Upcoming Appointments</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $serviceStats['upcoming_appointments'] ?? 0 ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-calendar-plus fs-2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Service Modal -->
<div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-labelledby="deleteServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteServiceModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this service?</p>
                <p class="text-danger"><strong>Note:</strong> This will also delete all appointments associated with this service.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="/services/delete/<?= $service['id'] ?>" method="post">
                    <button type="submit" class="btn btn-danger">Delete Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>