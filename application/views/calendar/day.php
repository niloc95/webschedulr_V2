<?php
// Set active menu
$active = 'calendar';
$title = 'Daily Schedule: ' . date('F j, Y', strtotime($date));

// Include header
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daily Schedule: <?= date('F j, Y', strtotime($date)) ?></h1>
        <div>
            <a href="/calendar" class="btn btn-outline-secondary me-2">
                <i class="bi bi-calendar"></i> Month View
            </a>
            <a href="/calendar/create?date=<?= $date ?>" class="btn btn-primary">
                <i class="bi bi-plus"></i> New Appointment
            </a>
        </div>
    </div>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Appointment was successfully created.
        </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <?php
            // Calculate previous and next day links
            $prevDay = date('Y-m-d', strtotime($date . ' -1 day'));
            $nextDay = date('Y-m-d', strtotime($date . ' +1 day'));
            ?>
            <div class="d-flex align-items-center">
                <a href="/calendar/day?date=<?= $prevDay ?>" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-chevron-left"></i> Previous Day
                </a>
                <a href="/calendar/day?date=<?= $nextDay ?>" class="btn btn-sm btn-outline-secondary">
                    Next Day <i class="bi bi-chevron-right"></i>
                </a>
            </div>
            
            <div>
                <a href="/calendar/day?date=<?= date('Y-m-d') ?>" class="btn btn-sm btn-outline-primary">
                    Today
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <?php if (empty($appointments)): ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-calendar-x" style="font-size: 3rem; color: #d1d3e2;"></i>
                    </div>
                    <h4>No appointments for this day</h4>
                    <p class="text-muted">Create a new appointment to get started.</p>
                    <a href="/calendar/create?date=<?= $date ?>" class="btn btn-primary">
                        <i class="bi bi-plus"></i> New Appointment
                    </a>
                </div>
            <?php else: ?>
                <div class="daily-schedule">
                    <?php foreach ($appointments as $appointment): ?>
                        <?php
                        $startTime = strtotime($appointment['start_time']);
                        $endTime = strtotime($appointment['end_time']);
                        $status = $appointment['status'];
                        $statusClass = '';
                        $statusText = '';
                        
                        switch ($status) {
                            case 'confirmed':
                                $statusClass = 'success';
                                $statusText = 'Confirmed';
                                break;
                            case 'pending':
                                $statusClass = 'warning';
                                $statusText = 'Pending';
                                break;
                            case 'cancelled':
                                $statusClass = 'danger';
                                $statusText = 'Cancelled';
                                break;
                            case 'completed':
                                $statusClass = 'primary';
                                $statusText = 'Completed';
                                break;
                            default:
                                $statusClass = 'secondary';
                                $statusText = ucfirst($status);
                        }
                        ?>
                        <div class="appointment-block border-left-<?= $statusClass ?> shadow-sm mb-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="appointment-time">
                                        <?= date('g:i A', $startTime) ?> - <?= date('g:i A', $endTime) ?>
                                    </div>
                                    <div class="appointment-duration text-muted">
                                        <?= round((strtotime($appointment['end_time']) - strtotime($appointment['start_time'])) / 60) ?> minutes
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="appointment-client">
                                        <strong>Client:</strong> <?= htmlspecialchars($appointment['client_name']) ?>
                                    </div>
                                    <div class="appointment-service">
                                        <strong>Service:</strong> <?= htmlspecialchars($appointment['service_name']) ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="appointment-status">
                                        <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                    </div>
                                    <?php if (!empty($appointment['notes'])): ?>
                                        <div class="appointment-notes text-muted">
                                            <?= htmlspecialchars($appointment['notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-2 text-end">
                                    <div class="btn-group">
                                        <a href="/calendar/edit/<?= $appointment['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?= $appointment['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Appointment Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this appointment? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger" id="deleteButton" href="#">Delete Appointment</a>
            </div>
        </div>
    </div>
</div>

<!-- Include day view specific styles -->
<style>
.appointment-block {
    padding: 15px;
    border-left-width: 4px !important;
    background-color: #ffffff;
}

.appointment-time {
    font-weight: 600;
    font-size: 1.1em;
}

.appointment-notes {
    margin-top: 8px;
    font-size: 0.9em;
    max-height: 60px;
    overflow-y: auto;
}

/* Bootstrap 5 badge compatibility */
.badge {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
    color: white;
}
</style>

<script>
function confirmDelete(appointmentId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('deleteButton').href = '/calendar/delete/' + appointmentId + '?date=<?= $date ?>';
    modal.show();
}
</script>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
?>