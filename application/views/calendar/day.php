<?php
$active = 'calendar';
$title = 'Calendar: ' . $dayData['dateString'];
include __DIR__ . '/../layouts/header.php';

// Update this line to use the correct path


// Helper function to get status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'confirmed':
            return 'success';
        case 'completed':
            return 'primary';
        case 'cancelled':
            return 'danger';
        case 'pending':
        default:
            return 'warning';
    }
}

// Helper function to convert hex color to rgba
function hexToRgb($hex) {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return "$r,$g,$b"; // return the RGB values as a string
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Calendar: <?= $dayData['dateString'] ?></h1>
        <div>
            <a href="/calendar/create?date=<?= $dayData['dateFormatted'] ?>" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
                <i class="bi bi-plus"></i> New Appointment
            </a>
        </div>
    </div>

    <!-- Calendar Navigation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <a href="/calendar/day/<?= $dayData['prevDateFormatted'] ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Previous Day
                </a>
                <a href="/calendar/day/<?= date('Y-m-d') ?>" class="btn btn-outline-primary mx-2">
                    Today
                </a>
                <a href="/calendar/day/<?= $dayData['nextDateFormatted'] ?>" class="btn btn-outline-secondary">
                    Next Day <i class="bi bi-chevron-right"></i>
                </a>
            </div>

            <div>
                <form action="/calendar/day" method="get" class="d-flex">
                    <input type="date" name="date" class="form-control" value="<?= $dayData['dateFormatted'] ?>">
                    <button type="submit" class="btn btn-primary ms-2">Go</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Calendar Day View -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Appointments for <?= date('F j, Y', strtotime($dayData['dateFormatted'])) ?></h6>
        </div>
        <div class="card-body">
            <?php if (empty($dayData['appointments'])): ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x fs-1 text-gray-300"></i>
                    <p class="mt-2 mb-0">No appointments scheduled for this day.</p>
                    <a href="/calendar/create?date=<?= $dayData['dateFormatted'] ?>" class="btn btn-sm btn-primary mt-3">
                        <i class="bi bi-plus"></i> Schedule Appointment
                    </a>
                </div>
            <?php else: ?>
                <div class="day-schedule">
                    <!-- Time labels -->
                    <div class="time-labels">
                        <?php
                        $startHour = 8; // 8 AM
                        $endHour = 20; // 8 PM
                        for ($hour = $startHour; $hour <= $endHour; $hour++):
                            $displayHour = $hour > 12 ? $hour - 12 : $hour;
                            $ampm = $hour >= 12 ? 'PM' : 'AM';
                        ?>
                            <div class="time-label">
                                <?= $displayHour ?>:00 <?= $ampm ?>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <!-- Appointment slots -->
                    <div class="appointment-slots">
                        <?php for ($hour = $startHour; $hour <= $endHour; $hour++): ?>
                            <div class="time-slot" id="hour-<?= $hour ?>">
                                <?php
                                // Display appointments for this hour
                                foreach ($dayData['appointments'] as $appointment) {
                                    $appointmentHour = (int)date('G', strtotime($appointment['start_time']));
                                    if ($appointmentHour === $hour) {
                                        // Prepare tooltip content
                                        $tooltip = "<div class='tooltip-content'>";
                                        $tooltip .= "<strong>" . htmlspecialchars($appointment['client_name']) . "</strong><br>";

                                        if (!empty($appointment['client_phone'])) {
                                            $tooltip .= "<i class='bi bi-telephone'></i> " . htmlspecialchars($appointment['client_phone']) . "<br>";
                                        }

                                        if (!empty($appointment['client_email'])) {
                                            $tooltip .= "<i class='bi bi-envelope'></i> " . htmlspecialchars($appointment['client_email']) . "<br>";
                                        }

                                        $tooltip .= "<hr class='my-1'>";
                                        $tooltip .= "<strong>Service:</strong> " . htmlspecialchars($appointment['service_name']) . "<br>";
                                        $tooltip .= "<strong>Duration:</strong> " . $appointment['duration'] . " min<br>";

                                        if (!empty($appointment['notes'])) {
                                            $tooltip .= "<strong>Notes:</strong> " . htmlspecialchars($appointment['notes']);
                                        }

                                        $tooltip .= "</div>";

                                        // Get background color based on status
                                        $bgColor = '';
                                        $borderColor = $appointment['color'] ?? '#4e73df';

                                        switch ($appointment['status']) {
                                            case 'cancelled':
                                                $bgColor = '#f8d7da'; // Light red
                                                break;
                                            case 'completed':
                                                $bgColor = '#d1e7dd'; // Light green
                                                break;
                                            default:
                                                // Use service color with transparency
                                                $rgb = hexToRgb($borderColor);
                                                $bgColor = "rgba($rgb, 0.2)";
                                                break;
                                        }

                                        echo '<div class="appointment" 
                                                 style="background-color: ' . $bgColor . '; 
                                                        border-left: 4px solid ' . $borderColor . ';"
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-html="true"
                                                 title="' . htmlspecialchars($tooltip) . '">
                                                <div class="appointment-time">
                                                    ' . date('g:i A', strtotime($appointment['start_time'])) . ' - 
                                                    ' . date('g:i A', strtotime($appointment['end_time'])) . '
                                                </div>
                                                <div class="appointment-details">
                                                    <strong>' . htmlspecialchars($appointment['client_name']) . '</strong><br>
                                                    ' . htmlspecialchars($appointment['service_name']) . '
                                                    <span class="badge bg-' . getStatusBadgeClass($appointment['status']) . '">
                                                        ' . ucfirst($appointment['status']) . '
                                                    </span>
                                                </div>
                                                <div class="appointment-actions">
                                                    <div class="appointment-actions d-flex justify-content-end gap-1">
                                                        <a href="/calendar/edit/' . $appointment['id'] . '" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-success status-update" 
                                                                data-id="' . $appointment['id'] . '" 
                                                                data-status="' . ($appointment['status'] == 'completed' ? 'pending' : 'completed') . '"
                                                                data-bs-toggle="tooltip" 
                                                                title="' . ($appointment['status'] == 'completed' ? 'Mark as Pending' : 'Mark as Completed') . '">
                                                            <i class="bi bi-check2' . ($appointment['status'] == 'completed' ? '-all' : '') . '"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deleteModal' . $appointment['id'] . '"
                                                                title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>

                                                    <!-- Delete Modal -->
                                                    <div class="modal fade" id="deleteModal' . $appointment['id'] . '" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to delete this appointment with ' . htmlspecialchars($appointment['client_name']) . '?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <form action="/calendar/delete/' . $appointment['id'] . '" method="post">
                                                                        <input type="hidden" name="referrer" value="/calendar/day/' . $dayData['dateFormatted'] . '">
                                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                                    }
                                }
                                ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips (only on desktop)
    if (window.innerWidth > 768 && typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Handle status update links
    document.querySelectorAll('.status-update').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const id = this.dataset.id;
            const status = this.dataset.status;
            
            // Add loading indicator to button
            const originalContent = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            this.disabled = true;
            
            // Send AJAX request to update status
            fetch('/calendar/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Visual feedback before reload
                    const appointment = this.closest('.appointment');
                    const statusBadge = appointment.querySelector('.badge');
                    if (statusBadge) {
                        statusBadge.classList.remove('bg-warning', 'bg-success', 'bg-primary', 'bg-danger');
                        statusBadge.classList.add('bg-' + getStatusClass(status));
                        statusBadge.textContent = capitalizeFirstLetter(status);
                    }
                    
                    // Reload after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    alert('Error: ' + data.message);
                    this.innerHTML = originalContent;
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                this.innerHTML = originalContent;
                this.disabled = false;
            });
        });
    });
    
    // Helper functions
    function getStatusClass(status) {
        switch(status) {
            case 'confirmed': return 'success';
            case 'completed': return 'primary';
            case 'cancelled': return 'danger';
            case 'pending':
            default: return 'warning';
        }
    }
    
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>