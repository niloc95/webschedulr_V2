<?php
// Set active menu
$active = 'calendar';
$title = 'Calendar';

// Include header
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Calendar</h1>
        <a href="/calendar/create" class="btn btn-primary">
            <i class="bi bi-plus"></i> New Appointment
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <?php
            // Calculate previous and next month links
            $prevMonth = $month - 1;
            $prevYear = $year;
            if ($prevMonth < 1) {
                $prevMonth = 12;
                $prevYear--;
            }
            
            $nextMonth = $month + 1;
            $nextYear = $year;
            if ($nextMonth > 12) {
                $nextMonth = 1;
                $nextYear++;
            }
            ?>
            <div class="d-flex align-items-center">
                <a href="/calendar?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <h2 class="h5 mb-0"><?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?></h2>
                <a href="/calendar?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
            
            <div>
                <a href="/calendar?month=<?= date('n') ?>&year=<?= date('Y') ?>" class="btn btn-sm btn-outline-primary">
                    Today
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="calendar">
                <div class="calendar-header">
                    <div class="weekday">Sunday</div>
                    <div class="weekday">Monday</div>
                    <div class="weekday">Tuesday</div>
                    <div class="weekday">Wednesday</div>
                    <div class="weekday">Thursday</div>
                    <div class="weekday">Friday</div>
                    <div class="weekday">Saturday</div>
                </div>
                
                <div class="calendar-body">
                    <?php
                    // First, output days from previous month to fill the first week
                    $daysInPrevMonth = date('t', mktime(0, 0, 0, $month - 1, 1, $year));
                    $startingDay = $firstDayOfWeek;
                    $prevMonthDays = $startingDay;
                    
                    // Previous month days
                    for ($i = 0; $i < $startingDay; $i++) {
                        $dayNumber = $daysInPrevMonth - $startingDay + $i + 1;
                        echo '<div class="calendar-day other-month">';
                        echo '<div class="date">' . $dayNumber . '</div>';
                        echo '</div>';
                    }
                    
                    // Current month days
                    $today = date('Y-m-d');
                    
                    for ($day = 1; $day <= $numDays; $day++) {
                        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $isToday = ($currentDate == $today);
                        
                        echo '<div class="calendar-day' . ($isToday ? ' today' : '') . '">';
                        echo '<a href="/calendar/day?date=' . $currentDate . '" class="date-link">' . $day . '</a>';
                        
                        // Show appointments for this day
                        if (isset($appointmentsByDate[$currentDate])) {
                            echo '<div class="appointments">';
                            foreach ($appointmentsByDate[$currentDate] as $appointment) {
                                $time = date('g:i A', strtotime($appointment['start_time']));
                                $status = $appointment['status'];
                                $statusClass = '';
                                
                                switch ($status) {
                                    case 'confirmed':
                                        $statusClass = 'success';
                                        break;
                                    case 'pending':
                                        $statusClass = 'warning';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'danger';
                                        break;
                                    default:
                                        $statusClass = 'secondary';
                                }
                                
                                // Add a link to each appointment
                                echo '<a href="/calendar/edit/' . $appointment['id'] . '?src=calendar_view">';
                                echo '<div class="appointment bg-' . $statusClass . '-soft">';
                                echo '<div class="time">' . $time . '</div>';
                                echo '<div class="client">' . htmlspecialchars($appointment['client_name']) . '</div>';
                                echo '</div>';
                                echo '</a>';
                            }
                            echo '</div>';
                        }
                        
                        echo '</div>';
                    }
                    
                    // Next month days to fill out the grid
                    $totalDaysShown = $startingDay + $numDays;
                    $remainingDays = 42 - $totalDaysShown; // 42 = 6 rows of 7 days
                    
                    for ($i = 1; $i <= $remainingDays; $i++) {
                        echo '<div class="calendar-day other-month">';
                        echo '<div class="date">' . $i . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include calendar-specific styles -->
<style>
.calendar {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.weekday {
    padding: 10px;
    text-align: center;
    font-weight: 600;
    color: #4e73df;
}

.calendar-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-auto-rows: minmax(120px, 1fr);
}

.calendar-day {
    border-right: 1px solid #e3e6f0;
    border-bottom: 1px solid #e3e6f0;
    padding: 8px;
    position: relative;
}

.calendar-day:nth-child(7n) {
    border-right: none;
}

.date {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 8px;
}

.date-link {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 8px;
    display: inline-block;
    text-decoration: none;
    color: inherit;
}

.other-month {
    background-color: #f8f9fc;
    color: #b7b9cc;
}

.today {
    background-color: #eef;
}

.today .date-link {
    color: #4e73df;
}

.appointments {
    display: flex;
    flex-direction: column;
    gap: 4px;
    max-height: calc(100% - 30px);
    overflow-y: auto;
}

.appointment-link {
    text-decoration: none;
    color: inherit;
    display: block;
    z-index: 15;
    position: relative;
}

.appointment {
    padding: 4px 6px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.appointment:hover {
    opacity: 0.8;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.time {
    font-weight: 600;
}

.client {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Status colors */
.bg-success-soft {
    background-color: rgba(40, 167, 69, 0.15);
}

.bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.15);
}

.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.15);
}

.bg-secondary-soft {
    background-color: rgba(108, 117, 125, 0.15);
}
</style>

<?php
// Include footer
include __DIR__ . '/../layouts/footer.php';
?>