<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user = getUser();
$title = 'Visual Calendar';
$is_dash = true;

// Fetch bookings for the calendar
// Only show non-cancelled and non-rejected bookings.
$stmt = $pdo->query("SELECT b.*, f.name as facility_name, u.name as user_name, t.start_time, t.end_time 
                     FROM bookings b 
                     JOIN facilities f ON b.facility_id = f.id 
                     JOIN users u ON b.user_id = u.id
                     LEFT JOIN availability t ON b.timeslot_id = t.id
                     WHERE b.status IN ('approved', 'pending')");
$bookings = $stmt->fetchAll();

$events = [];
foreach ($bookings as $b) {
    $color = ($b['status'] === 'approved') ? '#10b981' : '#f59e0b'; // Green or Yellow
    
    // For timeslot based bookings
    if ($b['start_time'] && $b['end_time']) {
        $start = $b['booking_date'] . 'T' . $b['start_time'];
        $end = $b['booking_date'] . 'T' . $b['end_time'];
    } else {
        // Fallback for full-day bookings if timeslot wasn't selected
        $start = $b['booking_date'];
        $end = $b['booking_date'];
    }

    $titleStr = $b['facility_name'];
    if ($user['role'] === 'admin' || $user['id'] === $b['user_id']) {
        $titleStr .= " (" . $b['user_name'] . ")";
    } else {
        $titleStr .= " (Booked)";
    }

    $events[] = [
        'id' => $b['id'],
        'title' => $titleStr,
        'start' => $start,
        'end' => $end,
        'color' => $color,
        'extendedProps' => [
            'status' => $b['status']
        ]
    ];
}

$maint_stmt = $pdo->query("SELECT m.*, f.name as facility_name FROM maintenance m JOIN facilities f ON m.facility_id = f.id WHERE m.status IN ('scheduled', 'in_progress')");
$maintenances = $maint_stmt->fetchAll();

foreach ($maintenances as $m) {
    $events[] = [
        'id' => 'm_' . $m['id'],
        'title' => 'Maintenance: ' . $m['facility_name'],
        'start' => $m['start_date'],
        'end' => $m['end_date'] ? date('Y-m-d', strtotime($m['end_date'] . ' +1 day')) : $m['start_date'],
        'color' => '#dc2626',
        'extendedProps' => [
            'status' => 'Under Maintenance'
        ]
    ];
}

require_once __DIR__ . '/includes/header_dash.php';
?>

<div class="sb-page-header">
    <h2><?= render_icon('calendar', '', 20) ?> Booking Calendar</h2>
</div>

<div class="sb-card sb-fade">
    <div class="sb-card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- FullCalendar Library -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?= json_encode($events) ?>,
        eventClick: function(info) {
            alert('Booking: ' + info.event.title + '\nStatus: ' + info.event.extendedProps.status);
        }
    });
    calendar.render();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
