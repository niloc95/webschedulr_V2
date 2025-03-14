<?php
$active = 'dashboard';
$title = 'Dashboard';

// Initialize default variables to prevent undefined variable errors
$stats = $stats ?? [
    'total_appointments' => 0,
    'upcoming_appointments' => 0, 
    'total_clients' => 0,
    'total_services' => 0
];

$upcomingAppointments = $upcomingAppointments ?? [];

$calendarData = $calendarData ?? [
    'month' => date('F Y'),
    'firstDay' => mktime(0, 0, 0, date('n'), 1, date('Y')),
    'numDays' => date('t'),
    'firstDayOfWeek' => date('w', mktime(0, 0, 0, date('n'), 1, date('Y')))
];

$appointmentsByDay = $appointmentsByDay ?? [
    'labels' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    'data' => [0, 0, 0, 0, 0, 0, 0]
];

$recentActivities = $recentActivities ?? [];

// Include header
include __DIR__ . '/../layouts/header.php';

// Helper function to format dates for display
function formatDateForDisplay($dateString) {
    $date = new DateTime($dateString);
    $today = new DateTime('today');
    $tomorrow = new DateTime('tomorrow');
    
    if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
        return 'Today, ' . $date->format('g:i A');
    } elseif ($date->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
        return 'Tomorrow, ' . $date->format('g:i A');
    } else {
        return $date->format('D, M j') . ' at ' . $date->format('g:i A');
    }
}
?>

<!-- REST OF YOUR DASHBOARD VIEW REMAINS THE SAME -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'User') ?></h1>
        <div>
            <span class="mr-2 d-none d-lg-inline text-gray-600">
                Today is <?= date('F j, Y') ?>
            </span>
            <a href="/calendar/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="bi bi-plus"></i> New Appointment
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Appointments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Appointments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_appointments'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Upcoming Appointments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['upcoming_appointments'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clients Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Clients
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_clients'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_services'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Upcoming Appointments -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Appointments</h6>
                    <a href="/calendar" class="btn btn-sm btn-primary">View Calendar</a>
                </div>
                <div class="card-body">
                    <?php if (empty($upcomingAppointments)): ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #d1d3e2;"></i>
                            </div>
                            <h4>No upcoming appointments</h4>
                            <p class="text-muted">Schedule your first appointment to get started</p>
                            <a href="/calendar/create" class="btn btn-primary">
                                <i class="bi bi-plus"></i> New Appointment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Client</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingAppointments as $appointment): ?>
                                        <?php
                                        $status = $appointment['status'];
                                        $statusClass = '';
                                        
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
                                        <tr>
                                            <td><?= formatDateForDisplay($appointment['start_time']) ?></td>
                                            <td><?= htmlspecialchars($appointment['client_name']) ?></td>
                                            <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                                            <td><span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/calendar/edit/<?= $appointment['id'] ?>" class="btn btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="confirmDelete(<?= $appointment['id'] ?>)">
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
            
            <!-- Weekly Distribution Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Weekly Appointment Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="weeklyDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Mini Calendar & Activity -->
        <div class="col-xl-4 col-lg-5">
            <!-- Mini Calendar -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Calendar</h6>
                </div>
                <div class="card-body">
                    <div class="mini-calendar">
                        <div class="mini-calendar-header">
                            <h5><?= $calendarData['month'] ?></h5>
                        </div>
                        <div class="mini-calendar-weekdays">
                            <div>Su</div>
                            <div>Mo</div>
                            <div>Tu</div>
                            <div>We</div>
                            <div>Th</div>
                            <div>Fr</div>
                            <div>Sa</div>
                        </div>
                        <div class="mini-calendar-days">
                            <?php
                            // Output empty cells for days before the first day of the month
                            for ($i = 0; $i < $calendarData['firstDayOfWeek']; $i++) {
                                echo '<div class="mini-day empty"></div>';
                            }
                            
                            // Output days of the month
                            for ($day = 1; $day <= $calendarData['numDays']; $day++) {
                                $isToday = $day == date('j');
                                echo '<a href="/calendar/day?date=' . date('Y-m-') . sprintf('%02d', $day) . '" class="mini-day ' . ($isToday ? 'today' : '') . '">' . $day . '</a>';
                            }
                            
                            // Calculate remaining days to fill out the grid
                            $totalCellsUsed = $calendarData['firstDayOfWeek'] + $calendarData['numDays'];
                            $remainingCells = ceil($totalCellsUsed / 7) * 7 - $totalCellsUsed;
                            
                            // Output empty cells to complete the grid
                            for ($i = 0; $i < $remainingCells; $i++) {
                                echo '<div class="mini-day empty"></div>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="/calendar" class="btn btn-primary btn-sm">
                            Go to Full Calendar <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="activity-feed">
                        <?php if (empty($recentActivities)): ?>
                            <div class="text-center py-3">
                                <p class="text-muted">No recent activity</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">
                                            New appointment with <?= htmlspecialchars($activity['client_name']) ?>
                                        </div>
                                        <div class="activity-subtitle">
                                            <?= htmlspecialchars($activity['service_name']) ?> • <?= date('M j, g:i A', strtotime($activity['start_time'])) ?>
                                        </div>
                                        <div class="activity-time text-muted">
                                            <?= date('M j, Y', strtotime($activity['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Appointment Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this appointment? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <a class="btn btn-danger" id="deleteButton" href="#">Delete Appointment</a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

<style>
/* Mini Calendar Styles */
.mini-calendar {
    width: 100%;
}

.mini-calendar-header {
    text-align: center;
    margin-bottom: 10px;
}

.mini-calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    font-weight: bold;
    font-size: 0.8rem;
    color: #4e73df;
    margin-bottom: 5px;
}

.mini-calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-auto-rows: minmax(30px, auto);
    gap: 2px;
}

.mini-day {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 0.8rem;
    padding: 5px;
    border-radius: 50%;
    text-decoration: none;
    color: #5a5c69;
}

.mini-day.empty {
    color: transparent;
    pointer-events: none;
}

.mini-day.today {
    background-color: #4e73df;
    color: white;
    font-weight: bold;
}

.mini-day:not(.empty):not(.today):hover {
    background-color: #eaecf4;
}

/* Activity Feed Styles */
.activity-feed {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e3e6f0;
}

.activity-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.activity-icon {
    width: 36px;
    height: 36px;
    background-color: #e8f0fe;
    color: #4e73df;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 2px;
}

.activity-subtitle {
    font-size: 0.85rem;
    margin-bottom: 2px;
}

.activity-time {
    font-size: 0.75rem;
}

/* Badge style fixes */
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

.bg-success {
    background-color: #28a745 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #212529;
}

.bg-danger {
    background-color: #dc3545 !important;
}

.bg-primary {
    background-color: #4e73df !important;
}

.bg-secondary {
    background-color: #858796 !important;
}
</style>

<script>
// Set up chart
document.addEventListener('DOMContentLoaded', function() {
    // Chart data from PHP
    const chartLabels = <?= json_encode($appointmentsByDay['labels']) ?>;
    const chartData = <?= json_encode($appointmentsByDay['data']) ?>;
    
    const ctx = document.getElementById('weeklyDistributionChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Appointments',
                data: chartData,
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});

// Delete confirmation
function confirmDelete(appointmentId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('deleteButton').href = '/calendar/delete/' + appointmentId;
    modal.show();
}
</script>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
?>