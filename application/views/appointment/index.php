<?php
$active = 'appointments';
$title = 'Appointments';
include __DIR__ . '/../layouts/header.php';

// Helper function to get status badge class
function getStatusBadgeClass($status) {
    switch($status) {
        case 'confirmed': return 'success';
        case 'completed': return 'primary';
        case 'cancelled': return 'danger';
        case 'pending':
        default: return 'warning';
    }
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Appointments</h1>
        <div>
            <a href="/appointments/schema" class="btn btn-info shadow-sm me-2">
                <i class="bi bi-table"></i> View Schema
            </a>
            <a href="/calendar/create" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus"></i> New Appointment
            </a>
        </div>
    </div>
    
    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                <i class="bi bi-funnel"></i> Show/Hide Filters
            </button>
        </div>
        <div class="card-body collapse show" id="filtersCollapse">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="client_id" class="form-label">Client</label>
                    <select class="form-select" id="client_id" name="client_id">
                        <option value="">All Clients</option>
                        <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>" <?= (isset($_GET['client_id']) && $_GET['client_id'] == $client['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="service_id" class="form-label">Service</label>
                    <select class="form-select" id="service_id" name="service_id">
                        <option value="">All Services</option>
                        <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>" <?= (isset($_GET['service_id']) && $_GET['service_id'] == $service['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                        <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'))) ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? date('Y-m-d', strtotime('+30 days'))) ?>">
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="/appointments" class="btn btn-secondary ms-2">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Appointments 
                <?php if (!empty($_GET['client_id']) || !empty($_GET['service_id']) || !empty($_GET['status'])): ?>
                <span class="badge bg-info">Filtered</span>
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($appointments)): ?>
                <div class="alert alert-info">
                    No appointments found for the selected criteria.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="appointmentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?= date('M j, Y', strtotime($appointment['start_time'])) ?></td>
                                    <td>
                                        <?= date('g:i A', strtotime($appointment['start_time'])) ?> - 
                                        <?= date('g:i A', strtotime($appointment['end_time'])) ?>
                                    </td>
                                    <td><?= htmlspecialchars($appointment['client_name'] ?? 'Unknown Client') ?></td>
                                    <td>
                                        <?php if (!empty($appointment['color'])): ?>
                                        <span class="color-dot" style="background-color: <?= htmlspecialchars($appointment['color']) ?>"></span>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($appointment['service_name'] ?? 'Unknown Service') ?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-<?= getStatusBadgeClass($appointment['status']) ?> dropdown-toggle" type="button" id="statusDropdown<?= $appointment['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                <?= ucfirst($appointment['status']) ?>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="statusDropdown<?= $appointment['id'] ?>">
                                                <li><a class="dropdown-item status-update <?= $appointment['status'] == 'pending' ? 'active' : '' ?>" href="#" data-id="<?= $appointment['id'] ?>" data-status="pending">Pending</a></li>
                                                <li><a class="dropdown-item status-update <?= $appointment['status'] == 'confirmed' ? 'active' : '' ?>" href="#" data-id="<?= $appointment['id'] ?>" data-status="confirmed">Confirmed</a></li>
                                                <li><a class="dropdown-item status-update <?= $appointment['status'] == 'completed' ? 'active' : '' ?>" href="#" data-id="<?= $appointment['id'] ?>" data-status="completed">Completed</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item status-update text-danger <?= $appointment['status'] == 'cancelled' ? 'active' : '' ?>" href="#" data-id="<?= $appointment['id'] ?>" data-status="cancelled">Cancelled</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/calendar/edit/<?= $appointment['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $appointment['id'] ?>" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal<?= $appointment['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $appointment['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel<?= $appointment['id'] ?>">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete the appointment with <?= htmlspecialchars($appointment['client_name'] ?? 'this client') ?> on <?= date('M j, Y', strtotime($appointment['start_time'])) ?> at <?= date('g:i A', strtotime($appointment['start_time'])) ?>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="/calendar/delete/<?= $appointment['id'] ?>" method="post">
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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

<style>
.color-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 5px;
}
</style>

<!-- Add DataTables -->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables
    const appointmentsTable = document.getElementById('appointmentsTable');
    if (appointmentsTable) {
        new simpleDatatables.DataTable(appointmentsTable, {
            perPage: 15
        });
    }
});
</script>

<script src="/js/calendar.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>