<?php
$active = 'clients';
$title = 'Edit Client';
include __DIR__ . '/../layouts/header.php';

// Helper for form validation errors
function hasError($field) {
    return isset($_SESSION['errors'][$field]) ? 'is-invalid' : '';
}

function getError($field) {
    if (isset($_SESSION['errors'][$field])) {
        $error = $_SESSION['errors'][$field];
        return '<div class="invalid-feedback">' . htmlspecialchars($error) . '</div>';
    }
    return '';
}

function getOldValue($field, $default = '') {
    return $_SESSION['old'][$field] ?? $client[$field] ?? $default;
}

// Clear session data after using it
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Client</h1>
        <div>
            <a href="/clients/show/<?= $client['id'] ?>" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm me-2">
                <i class="bi bi-eye"></i> View Client
            </a>
            <a href="/clients" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to Clients
            </a>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Client Information</h6>
        </div>
        <div class="card-body">
            <form action="/clients/update/<?= $client['id'] ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" class="form-control <?= hasError('name') ?>" id="name" name="name" 
                               value="<?= getOldValue('name') ?>" required>
                        <?= getError('name') ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?= hasError('email') ?>" id="email" name="email" 
                               value="<?= getOldValue('email') ?>">
                        <?= getError('email') ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= getOldValue('phone') ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= getOldValue('address') ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= getOldValue('notes') ?></textarea>
                </div>
                
                <div class="mb-3">
                    <p class="text-muted small">* Required fields</p>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/clients" class="btn btn-secondary me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Client</button>
                </div>
                
                <?php if (isset($errors['db_error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <?= htmlspecialchars($errors['db_error']) ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>