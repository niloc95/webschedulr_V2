<?php
$active = 'dashboard';
$title = 'Dashboard';
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
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="/calendar/create" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
            <i class="bi bi-plus"></i> New Appointment
        </a>
    </div>

    <!-- Content Row - Stats Cards -->
    <div class="row">
        <!-- Upcoming Appointments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Upcoming Appointments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['upcoming_appointments'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Appointments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Appointments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['today_appointments'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-day fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tomorrow's Appointments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tomorrow's Appointments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['tomorrow_appointments'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-plus fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Today's Date</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= date('M j, Y') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar3 fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Main Content -->
    <div class="row">
        <!-- Today's Schedule -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Today's Schedule</h6>
                    <a href="/calendar/day/<?= date('Y-m-d') ?>" class="btn btn-sm btn-primary">
                        View Full Day
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($todaysAppointments)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x fs-1 text-gray-300"></i>
                            <p class="mt-2 mb-0">No appointments scheduled for today.</p>
                            <a href="/calendar/create" class="btn btn-sm btn-primary mt-3">
                                <i class="bi bi-plus"></i> Schedule Appointment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Client</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todaysAppointments as $appointment): ?>
                                        <tr>
                                            <td>
                                                <?= date('g:i A', strtotime($appointment['start_time'])) ?> - 
                                                <?= date('g:i A', strtotime($appointment['end_time'])) ?>
                                            </td>
                                            <td><?= htmlspecialchars($appointment['client_name']) ?></td>
                                            <td>
                                                <?php if (!empty($appointment['color'])): ?>
                                                <span class="color-dot" style="background-color: <?= htmlspecialchars($appointment['color']) ?>"></span>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($appointment['service_name']) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= getStatusBadgeClass($appointment['status']) ?>">
                                                    <?= ucfirst($appointment['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="/calendar/edit/<?= $appointment['id'] ?>"><i class="bi bi-pencil"></i> Edit</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item status-update <?= $appointment['status'] == 'pending' ? 'active' : '' ?>" href="#" data-id="<?= $appointment['id'] ?>" data-status="pending">Mark as Pending</a></li>
                                                        <li><a class="dropdown-item status-update <?= $appointment['status'] == 'confirmed' ? 'active' : '' ?>" href="#" data-id="<?= $appointment['id'] ?>" data-status="confirmed">Mark as Confirmed</a></li>
                                                        <li><a class="dropdown-item status-update <?= $appointment['status'] == 'completed' ? 'active' : '' ?>" href="#" data-id="<?= $appointment['id'] ?>" data-status="completed">Mark as Completed</a></li>
                                                        <li><a class="dropdown-item status-update text-danger <?= $appointment['status'] == 'cancelled' ? 'active' : '' ?>" href="#" data-id="<?= $appointment['id'] ?>" data-status="cancelled">Cancel Appointment</a></li>
                                                    </ul>
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

        <!-- Status Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Appointments by Status</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($statusStats['labels'])): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-bar-chart fs-1 text-gray-300"></i>
                            <p class="mt-2 mb-0">No appointment data available.</p>
                        </div>
                    <?php else: ?>
                        <div class="chart-pie pt-4">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <?php foreach ($statusStats['labels'] as $i => $label): ?>
                                <span class="me-2">
                                    <i class="bi bi-circle-fill" style="color: <?= $statusStats['backgrounds'][$i] ?>"></i> <?= $label ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Additional Info -->
    <div class="row">
        <!-- Weekly Distribution -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Appointment Distribution by Day of Week</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="appointmentsByDayChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Clients -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Clients</h6>
                    <a href="/clients" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentClients)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-gray-300"></i>
                            <p class="mt-2 mb-0">No clients added yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($recentClients as $client): ?>
                                <a href="/clients/view/<?= $client['id'] ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($client['name']) ?></h6>
                                        <small><?= date('M j', strtotime($client['created_at'])) ?></small>
                                    </div>
                                    <?php if (!empty($client['email'])): ?>
                                        <small class="text-muted"><i class="bi bi-envelope"></i> <?= htmlspecialchars($client['email']) ?></small>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="/calendar/create" class="btn btn-primary d-block py-3">
                                <i class="bi bi-calendar-plus me-2"></i> New Appointment
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="/clients/create" class="btn btn-success d-block py-3">
                                <i class="bi bi-person-plus me-2"></i> New Client
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="/services/create" class="btn btn-info d-block py-3">
                                <i class="bi bi-tools me-2"></i> New Service
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="/appointments" class="btn btn-secondary d-block py-3">
                                <i class="bi bi-list-check me-2"></i> View All Appointments
                            </a>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <h6 class="font-weight-bold">Jump to Date</h6>
                            <form action="/calendar/day" method="get" class="input-group">
                                <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>">
                                <button class="btn btn-outline-primary" type="submit">View Day</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Set new default font family for Chart.js
Chart.defaults.font.family = 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
Chart.defaults.color = '#858796';

document.addEventListener('DOMContentLoaded', function() {
    // Pie Chart for Status Distribution
    const statusChartEl = document.getElementById('statusPieChart');
    if (statusChartEl) {
        new Chart(statusChartEl, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($statusStats['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($statusStats['data'] ?? []) ?>,
                    backgroundColor: <?= json_encode($statusStats['backgrounds'] ?? []) ?>,
                    hoverBackgroundColor: <?= json_encode($statusStats['backgrounds'] ?? []) ?>,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        caretPadding: 10,
                        displayColors: false,
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                    }
                },
                cutout: '60%',
            },
        });
    }
    
    // Bar Chart for Weekly Distribution
    const dayChartEl = document.getElementById('appointmentsByDayChart');
    if (dayChartEl) {
        new Chart(dayChartEl, {
            type: 'bar',
            data: {
                labels: <?= json_encode($appointmentsByDay['labels'] ?? []) ?>,
                datasets: [{
                    label: "Appointments",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: <?= json_encode($appointmentsByDay['data'] ?? []) ?>,
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            min: 0,
                            maxTicksLimit: 5,
                            padding: 10,
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    }
                }
            }
        });
    }
});
</script>

<!-- Status update script (if needed) -->
<script src="/js/calendar.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>