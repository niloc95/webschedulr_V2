<?php
$active = 'clients';
$title = 'Client Details';
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

// Format date helper
function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function formatDateTime($date) {
    return date('M j, Y g:i A', strtotime($date));
}

// Determine appointment status class
function getStatusClass($status) {
    switch ($status) {
        case 'confirmed': return 'success';
        case 'pending': return 'warning';
        case 'cancelled': return 'danger';
        case 'completed': return 'primary';
        default: return 'secondary';
    }
}
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($client['name']) ?></h1>
        <div>
            <a href="/calendar/create?client_id=<?= $client['id'] ?>" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm me-2">
                <i class="bi bi-plus"></i> New Appointment
            </a>
            <a href="/clients/edit/<?= $client['id'] ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm me-2">
                <i class="bi bi-pencil"></i> Edit Client
            </a>
            <a href="/clients" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to Clients
            </a>
        </div>
    </div>

    <?php if ($successMessage = getFlashMessage('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($successMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Client Information Column -->
        <div class="col-xl-4 col-lg-5">
            <!-- Client Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Client Information</h6>
                </div>
                <div class="card-body">
                    <div class="client-profile mb-4">
                        <div class="client-avatar mb-3">
                            <div class="avatar-circle">
                                <?php 
                                $initials = substr($client['name'], 0, 1); 
                                if (strpos($client['name'], ' ') !== false) {
                                    $nameParts = explode(' ', $client['name'], 2);
                                    $initials = substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1);
                                }
                                ?>
                                <span class="initials"><?= strtoupper($initials) ?></span>
                            </div>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush">
                        <?php if (!empty($client['email'])): ?>
                        <li class="list-group-item d-flex">
                            <div class="me-3"><i class="bi bi-envelope text-primary"></i></div>
                            <div>
                                <div class="text-muted small">Email</div>
                                <a href="mailto:<?= htmlspecialchars($client['email']) ?>"><?= htmlspecialchars($client['email']) ?></a>
                            </div>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($client['phone'])): ?>
                        <li class="list-group-item d-flex">
                            <div class="me-3"><i class="bi bi-telephone text-primary"></i></div>
                            <div>
                                <div class="text-muted small">Phone</div>
                                <a href="tel:<?= htmlspecialchars($client['phone']) ?>"><?= htmlspecialchars($client['phone']) ?></a>
                            </div>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($client['address'])): ?>
                        <li class="list-group-item d-flex">
                            <div class="me-3"><i class="bi bi-geo-alt text-primary"></i></div>
                            <div>
                                <div class="text-muted small">Address</div>
                                <div><?= nl2br(htmlspecialchars($client['address'])) ?></div>
                            </div>
                        </li>
                        <?php endif; ?>
                        
                        <li class="list-group-item d-flex">
                            <div class="me-3"><i class="bi bi-calendar3 text-primary"></i></div>
                            <div>
                                <div class="text-muted small">Client Since</div>
                                <div><?= formatDate($client['created_at']) ?></div>
                            </div>
                        </li>
                    </ul>
                    
                    <?php if (!empty($client['notes'])): ?>
                    <div class="mt-3">
                        <h6 class="font-weight-bold">Notes</h6>
                        <div class="p-3 bg-light rounded">
                            <?= nl2br(htmlspecialchars($client['notes'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="/clients/edit/<?= $client['id'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit Client
                        </a>
                        <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete(<?= $client['id'] ?>, '<?= addslashes(htmlspecialchars($client['name'])) ?>')">
                            <i class="bi bi-trash"></i> Delete Client
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Appointments Column -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary" id="appointments">Appointment History</h6>
                    <a href="/calendar/create?client_id=<?= $client['id'] ?>" class="btn btn-sm btn-success">
                        <i class="bi bi-plus"></i> New Appointment
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($appointments)): ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #d1d3e2;"></i>
                            </div>
                            <h4>No appointment history</h4>
                            <p class="text-muted">This client doesn't have any appointments yet</p>
                            <a href="/calendar/create?client_id=<?= $client['id'] ?>" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Schedule Appointment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): ?>
                                        <?php 
                                        $statusClass = getStatusClass($appointment['status']);
                                        $statusText = ucfirst($appointment['status']);
                                        $isPast = strtotime($appointment['start_time']) < time();
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?= formatDate($appointment['start_time']) ?></div>
                                                <div class="text-muted small">
                                                    <?= date('g:i A', strtotime($appointment['start_time'])) ?> - 
                                                    <?= date('g:i A', strtotime($appointment['end_time'])) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (!empty($appointment['color'])): ?>
                                                <div class="d-flex align-items-center">
                                                    <div class="service-color me-2" style="background-color: <?= htmlspecialchars($appointment['color']) ?>"></div>
                                                    <?= htmlspecialchars($appointment['service_name']) ?>
                                                </div>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($appointment['service_name']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/calendar/edit/<?= $appointment['id'] ?>" class="btn btn-outline-secondary" title="Edit Appointment">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if (!$isPast && $appointment['status'] !== 'cancelled'): ?>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="confirmCancelAppointment(<?= $appointment['id'] ?>)"
                                                            title="Cancel Appointment">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                    <?php endif; ?>
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
    </div>
</div>

<!-- Delete Client Modal -->
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
                <form id="deleteForm" action="" method="post">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Delete Client</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Appointment Modal -->
<div class="modal fade" id="cancelAppointmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Appointment</h5>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this appointment?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">No, Keep It</button>
                <form id="cancelAppointmentForm" action="" method="post">
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-danger">Yes, Cancel Appointment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 80px;
    height: 80px;
    background-color: #4e73df;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    margin: 0 auto;
}

.service-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.client-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
</style>

<script>
// Handle client deletion
function confirmDelete(clientId, clientName) {
    document.getElementById('deleteClientName').textContent = clientName;
    document.getElementById('deleteForm').action = '/clients/delete/' + clientId;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Handle appointment cancellation
function confirmCancelAppointment(appointmentId) {
    document.getElementById('cancelAppointmentForm').action = '/calendar/update/' + appointmentId;
    
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelAppointmentModal'));
    cancelModal.show();
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>