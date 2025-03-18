<?php
$title = 'Error';
include __DIR__ . '/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Error</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <?= $error ?? 'An error occurred. Please try again later.' ?>
                    </div>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Go Back
                    </a>
                    <a href="/" class="btn btn-primary">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>