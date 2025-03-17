<?php
// Helper function to get the appropriate badge class for status
function getStatusBadgeClass($status) {
    switch($status) {
        case 'confirmed': return 'success';
        case 'completed': return 'primary';
        case 'cancelled': return 'danger';
        case 'pending':
        default: return 'warning';
    }
}

$active = 'calendar';
$title = 'Calendar - Daily View';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daily Schedule: <?= htmlspecialchars($dayData['dateString']) ?></h1>
        <div>
            <a href="/calendar" class="btn btn-sm btn-outline-secondary shadow-sm mr-2">
                <i class="bi bi-calendar"></i> Month View
            </a>
            <a href="/calendar/create?date=<?= $dayData['dateFormatted'] ?>" class="btn btn-sm btn-primary shadow-sm">
                <i class="bi bi-plus"></i> New Appointment
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

    <!-- Day Navigation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <a href="/calendar/day?day=<?= $dayData['prevDay'] ?>&month=<?= $dayData['prevMonth'] ?>&year=<?= $dayData['prevYear'] ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> Previous Day
            </a>
            <h6 class="m-0 font-weight-bold text-primary"><?= htmlspecialchars($dayData['dateString']) ?></h6>
            <a href="/calendar/day?day=<?= $dayData['nextDay'] ?>&month=<?= $dayData['nextMonth'] ?>&year=<?= $dayData['nextYear'] ?>" class="btn btn-sm btn-outline-secondary">
                Next Day <i class="bi bi-chevron-right"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="day-schedule-container">
                <?php if (empty($dayData['appointments'])): ?>
                    <div class="text-center p-5">
                        <p class="mb-3">No appointments scheduled for this day</p>
                        <a href="/calendar/create?date=<?= $dayData['dateFormatted'] ?>" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Schedule an Appointment
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 15%">Time</th>
                                    <th style="width: 20%">Client</th>
                                    <th style="width: 20%">Service</th>
                                    <th style="width: 30%">Details</th>
                                    <th style="width: 15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dayData['appointments'] as $appointment): 
                                    $startTime = date('g:i A', strtotime($appointment['start_time'])); 
                                    $endTime = date('g:i A', strtotime($appointment['end_time']));
                                    $color = $appointment['color'] ?? '#3498db';
                                    $statusClass = '';
                                    
                                    // Map appointment status to CSS class
                                    switch($appointment['status']) {
                                        case 'completed': $statusClass = 'success'; break;
                                        case 'cancelled': $statusClass = 'danger'; break;
                                        case 'pending': $statusClass = 'warning'; break;
                                        case 'confirmed': $statusClass = 'primary'; break;
                                        default: $statusClass = 'secondary';
                                    }
                                    
                                    // Generate appointment title if not available
                                    $appointmentTitle = isset($appointment['title']) ? 
                                        htmlspecialchars($appointment['title']) : 
                                        htmlspecialchars($appointment['service_name'] . ' - ' . $appointment['client_name']);
                                ?>
                                <tr>
                                    <td>
                                        <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: <?= $color ?>; margin-right: 5px;"></span>
                                        <?= $startTime ?> - <?= $endTime ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($appointment['client_name']) ?></strong>
                                        <?php if (!empty($appointment['client_phone'])): ?>
                                            <div class="small text-muted"><?= htmlspecialchars($appointment['client_phone'] ?? '') ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($appointment['service_name']) ?>
                                        <div class="small text-muted"><?= $appointment['duration'] ?? 'N/A' ?> min</div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong><?= $appointmentTitle ?></strong>
                                                <?php if (!empty($appointment['notes'])): ?>
                                                    <div class="small"><?= htmlspecialchars($appointment['notes']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <span class="badge bg-<?= $statusClass ?>">
                                                    <?= ucfirst($appointment['status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/calendar/edit/<?= $appointment['id'] ?>" class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $appointment['id'] ?>" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <!-- Status Dropdown -->
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
                                        </div>
                                        
                                        <!-- Delete Modal for this appointment -->
                                        <div class="modal fade" id="deleteModal<?= $appointment['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $appointment['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel<?= $appointment['id'] ?>">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Are you sure you want to delete this appointment:
                                                        <p class="fw-bold mt-2 mb-0"><?= $appointmentTitle ?></p>
                                                        <p class="mb-0">Client: <?= htmlspecialchars($appointment['client_name']) ?></p>
                                                        <p class="mb-0">Time: <?= $startTime ?> - <?= $endTime ?></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="/calendar/delete/<?= $appointment['id'] ?>" method="post">
                                                            <input type="hidden" name="referrer" value="/calendar/day?day=<?= $dayData['day'] ?>&month=<?= $dayData['month'] ?>&year=<?= $dayData['year'] ?>">
                                                            <button type="submit" class="btn btn-danger">Delete Appointment</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Delete Modal -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status update clicks
    document.querySelectorAll('.status-update').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const appointmentId = this.getAttribute('data-id');
            const newStatus = this.getAttribute('data-status');

            fetch(`/calendar/update-status/${appointmentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ status: newStatus }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload(); // Reload the page to reflect the updated status
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
</script>

<!-- Include the calendar.js script at the bottom of the file -->
<script src="/js/calendar.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>