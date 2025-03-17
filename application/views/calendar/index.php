<?php
$active = 'calendar';
$title = 'Calendar';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Calendar</h1>
        <a href="/calendar/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
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

<style>
.calendar-table {
    table-layout: fixed;
    height: 800px;
}

.calendar-day {
    height: 120px;
    position: relative;
    vertical-align: top;
    transition: background 0.2s;
    padding: 0;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.calendar-day.empty {
    background-color: #f6f6f6;
}

.calendar-day.today {
    background-color: #e8f4ff;
}

.calendar-day-number {
    padding: 5px 8px;
    font-weight: bold;
    display: inline-block;
}

.calendar-day-number a {
    color: inherit;
    text-decoration: none;
}

.calendar-day.today .calendar-day-number {
    background-color: #007bff;
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 5px;
}

.appointment-list {
    max-height: 80%;
    overflow-y: auto;
    padding: 0 4px;
}

.appointment-item {
    font-size: 0.8rem;
    padding: 2px 4px;
    margin-bottom: 2px;
    background-color: white;
    border-radius: 3px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.appointment-item a {
    color: #333;
    text-decoration: none;
    display: block;
}

.appointment-time {
    font-weight: bold;
    font-size: 0.75rem;
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>