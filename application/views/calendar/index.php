<?php
$active = 'calendar';
$title = 'Calendar';
// No need to explicitly include calendar.css, it's handled automatically by the header

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Calendar</h1>
        <a href="/calendar/create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus"></i> New Appointment
        </a>
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

    <!-- Calendar Navigation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><?= htmlspecialchars($calendarData['dateString']) ?></h6>
            <div class="btn-group">
                <a href="/calendar?month=<?= $calendarData['prevMonth'] ?>&year=<?= $calendarData['prevYear'] ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
                <a href="/calendar" class="btn btn-sm btn-outline-secondary">Today</a>
                <a href="/calendar?month=<?= $calendarData['nextMonth'] ?>&year=<?= $calendarData['nextYear'] ?>" class="btn btn-sm btn-outline-secondary">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </div>
            <div class="btn-group">
                <a href="/calendar/day" class="btn btn-sm btn-outline-primary">Day</a>
                <a href="/calendar" class="btn btn-sm btn-primary">Month</a>
            </div>
        </div>
        <div class="card-body">
            <div class="calendar-container">
                <table class="calendar-table table table-bordered">
                    <thead>
                        <tr>
                            <th>Sunday</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dayCount = 1;
                        $totalCells = ceil(($calendarData['startingDayOfWeek'] + $calendarData['numDaysInMonth']) / 7) * 7;
                        
                        for ($i = 0; $i < $totalCells; $i++) {
                            if ($i % 7 === 0) {
                                echo "<tr>";
                            }
                            
                            if ($i < $calendarData['startingDayOfWeek'] || $dayCount > $calendarData['numDaysInMonth']) {
                                echo "<td class='calendar-day empty'></td>";
                            } else {
                                $isToday = ($dayCount == date('j') && $calendarData['currentMonth'] == date('n') && $calendarData['currentYear'] == date('Y'));
                                $dayClass = $isToday ? 'calendar-day today' : 'calendar-day';
                                
                                echo "<td class='$dayClass'>";
                                
                                // Day number with link to day view
                                echo "<div class='calendar-day-number'>";
                                echo "<a href='/calendar/day?day=$dayCount&month={$calendarData['currentMonth']}&year={$calendarData['currentYear']}'>";
                                echo $dayCount;
                                echo "</a>";
                                echo "</div>";
                                
                                // Appointments for this day
                                if (isset($calendarData['appointmentsByDay'][$dayCount])) {
                                    echo "<div class='appointment-list'>";
                                    foreach ($calendarData['appointmentsByDay'][$dayCount] as $appointment) {
                                        $time = date('g:i A', strtotime($appointment['start_time']));
                                        $color = $appointment['color'] ?? '#3498db';
                                        
                                        echo "<div class='appointment-item' style='border-left: 4px solid $color;'>";
                                        echo "<a href='/calendar/edit/{$appointment['id']}'>";
                                        echo "<span class='appointment-time'>$time</span> ";
                                        echo "<span class='appointment-title'>" . htmlspecialchars($appointment['service_name'] . ' - ' . $appointment['client_name']) . "</span>";
                                        echo "</a>";
                                        echo "</div>";
                                    }
                                    echo "</div>";
                                }
                                
                                echo "</td>";
                                $dayCount++;
                            }
                            
                            if (($i + 1) % 7 === 0) {
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>