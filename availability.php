<?php
// availability.php
require_once __DIR__ . '/config.php';

$title = 'Scheduler';

// Fetch all facilities to check their status
$facilities = $pdo->query("SELECT id, name, image_path, status, maintenance_time, maintenance_end_time FROM facilities ORDER BY name")->fetchAll();

// Fetch maintenance records
$maint_records = $pdo->query("SELECT facility_id, start_date, end_date FROM maintenance WHERE status IN ('scheduled', 'in_progress')")->fetchAll(PDO::FETCH_ASSOC);

$dates = [];
for ($i = 0; $i < 7; $i++) {
    $d = date('Y-m-d', strtotime("+$i days"));
    $dates[$d] = date('l', strtotime($d));
}

$activeDate = isset($_GET['date']) && isset($dates[$_GET['date']]) ? $_GET['date'] : date('Y-m-d');
$activeDayName = $dates[$activeDate];

// Fetch all availability slots for the active day of the week
$slots = $pdo->prepare("SELECT a.*, f.name as facility_name, f.image_path 
                      FROM availability a 
                      JOIN facilities f ON a.facility_id = f.id 
                      WHERE a.status = 'available' AND a.day_of_week = ?
                      ORDER BY f.name, a.start_time");
$slots->execute([$activeDayName]);
$slots = $slots->fetchAll();

// Fetch booked slots for the active date
$bookedStmt = $pdo->prepare("SELECT timeslot_id FROM bookings WHERE booking_date = ? AND status IN ('pending', 'approved')");
$bookedStmt->execute([$activeDate]);
$bookedSlotIds = $bookedStmt->fetchAll(PDO::FETCH_COLUMN);

$scheduleData = [];
foreach ($facilities as $f) {
    $scheduleData[$f['id']] = [
        'facility_name' => $f['name'],
        'image_path' => $f['image_path'],
        'status' => $f['status'],
        'maintenance_time' => $f['maintenance_time'],
        'maintenance_end_time' => $f['maintenance_end_time'],
        'is_fully_unavailable' => false,
        'slots' => []
    ];
    
    if (in_array($f['status'], ['maintenance', 'unavailable']) && empty($f['maintenance_time'])) {
        $scheduleData[$f['id']]['is_fully_unavailable'] = true;
    }
}

foreach ($slots as $s) {
    $fid = $s['facility_id'];
    
    if ($scheduleData[$fid]['is_fully_unavailable']) {
        continue;
    }
    
    // Check if slot is booked
    if (in_array($s['id'], $bookedSlotIds)) {
        $s['is_booked'] = true;
    }
    
    // Check if slot falls in maintenance time
    if (in_array($scheduleData[$fid]['status'], ['maintenance', 'unavailable']) && !empty($scheduleData[$fid]['maintenance_time'])) {
        $mStart = strtotime($scheduleData[$fid]['maintenance_time']);
        $sStartTimestamp = strtotime($activeDate . ' ' . $s['start_time']);
        $sEndTimestamp = strtotime($activeDate . ' ' . $s['end_time']);
        
        if (empty($scheduleData[$fid]['maintenance_end_time'])) {
            // Indefinite maintenance starting at mStart
            if ($sEndTimestamp > $mStart) {
                $s['is_maintenance'] = true;
            }
        } else {
            $mEnd = strtotime($scheduleData[$fid]['maintenance_end_time']);
            if ($sStartTimestamp < $mEnd && $mStart < $sEndTimestamp) {
                $s['is_maintenance'] = true;
            }
        }
    }
    
    // Check new maintenance table
    foreach ($maint_records as $m) {
        if ($m['facility_id'] == $fid) {
            $mStart = strtotime($m['start_date'] . ' 00:00:00');
            $mEnd = $m['end_date'] ? strtotime($m['end_date'] . ' 23:59:59') : strtotime($m['start_date'] . ' 23:59:59');
            $sStartTimestamp = strtotime($activeDate . ' ' . $s['start_time']);
            $sEndTimestamp = strtotime($activeDate . ' ' . $s['end_time']);
            
            if ($sStartTimestamp <= $mEnd && $mStart <= $sEndTimestamp) {
                $s['is_maintenance'] = true;
                break;
            }
        }
    }
    
    $scheduleData[$fid]['slots'][] = $s;
}

// Remove facilities that have no slots and aren't fully unavailable
foreach ($scheduleData as $fid => $data) {
    if (empty($data['slots']) && !$data['is_fully_unavailable']) {
        unset($scheduleData[$fid]);
    }
}

require_once __DIR__ . '/includes/header_public.php';
?>

<style>
.scheduler-tabs {
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    margin-bottom: 2.5rem;
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
    scroll-behavior: smooth;
}
.scheduler-tabs::-webkit-scrollbar { display: none; } /* Hide scrollbar */

.tab-btn {
    padding: 0.85rem 1.75rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.03);
    color: var(--muted-foreground);
    border: 1px solid rgba(255,255,255,0.05);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
}
.tab-btn:hover {
    color: #fff;
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.15);
}
.tab-btn.active {
    background: var(--primary);
    color: var(--primary-foreground);
    border-color: var(--primary);
    box-shadow: 0 4px 15px rgba(92, 225, 230, 0.3);
}

.timeline-row {
    display: flex;
    background: rgba(25, 30, 42, 0.6);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}
.timeline-row:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.3);
    border-color: rgba(92, 225, 230, 0.3);
}

.timeline-facility {
    width: 280px;
    min-width: 280px;
    padding: 1.5rem;
    border-right: 1px solid rgba(255,255,255,0.05);
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: rgba(0,0,0,0.15);
    position: relative;
}
.timeline-facility img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: transform 0.4s ease;
}
.timeline-row:hover .timeline-facility img {
    transform: scale(1.03);
}
.timeline-facility h3 {
    font-size: 1.25rem;
    font-weight: 800;
    margin: 0;
    color: #fff;
    letter-spacing: -0.02em;
}

.timeline-slots {
    display: flex;
    padding: 1.5rem;
    gap: 1rem;
    overflow-x: auto;
    align-items: center;
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
    scroll-behavior: smooth;
}
.timeline-slots::-webkit-scrollbar { display: none; }

.time-block {
    background: rgba(16, 185, 129, 0.05);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: #10b981;
    padding: 0.85rem 1.5rem;
    border-radius: 12px;
    white-space: nowrap;
    text-align: center;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    min-width: 120px;
}
.time-block:hover:not(.disabled) {
    background: #10b981;
    color: #fff;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    transform: translateY(-3px);
    border-color: #10b981;
}
.time-block.disabled {
    cursor: not-allowed;
    opacity: 0.6;
}
.time-block span.status {
    font-size: 0.7rem;
    text-transform: uppercase;
    font-weight: 800;
    letter-spacing: 0.08em;
    opacity: 0.9;
}
.time-block span.time {
    font-size: 1.1rem;
    font-weight: 700;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .timeline-row {
        flex-direction: column;
    }
    .timeline-facility {
        width: 100%;
        min-width: 100%;
        border-right: none;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        padding: 1.25rem;
        flex-direction: row;
        align-items: center;
        gap: 1rem;
    }
    .timeline-facility img {
        width: 80px;
        height: 80px;
        margin-bottom: 0;
    }
    .timeline-facility h3 {
        font-size: 1.1rem;
    }
    .timeline-slots {
        padding: 1.25rem;
    }
}
</style>

<div class="sb-hero" style="min-height: auto; padding: 4rem 2.5rem 2rem; background: linear-gradient(135deg, rgba(15,17,26,0.95), rgba(15,17,26,0.8));">
    <div class="sb-container" style="text-align: center;">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Court Scheduler</h1>
        <p style="color: var(--muted-foreground); font-size: 1.2rem; max-width: 600px; margin: 0 auto 2rem;">Select a day to view the live timeline of available courts and instantly secure your next game.</p>
        <a href="<?= BASE_URL ?>/equipment.php" class="sb-btn sb-btn-outline" style="border-radius: 999px; padding: 0.75rem 2rem;">
            <?= render_icon('box', '', 18) ?> Need Gear? View Equipment
        </a>
    </div>
</div>

<section class="sb-section" style="padding-top: 2rem;">
    <div class="sb-container">
        
        <!-- Day Selector Tabs -->
        <div class="scheduler-tabs">
            <?php foreach ($dates as $date => $dayName): ?>
                <a href="?date=<?= $date ?>" class="tab-btn <?= $date === $activeDate ? 'active' : '' ?>">
                    <?= date('D, d M', strtotime($date)) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Timeline Grid for Active Day -->
        <?php if (empty($scheduleData)): ?>
            <div style="text-align: center; padding: 4rem; background: var(--surface); border: 1px dashed var(--border); border-radius: var(--radius);">
                <?= render_icon('calendar', 'rgba(255,255,255,0.2)', 48) ?>
                <h3 style="margin-top: 1rem; font-size: 1.2rem;">No courts scheduled for <?= date('l, d M Y', strtotime($activeDate)) ?></h3>
                <p style="color: var(--muted-foreground);">Please select another date from the timeline above.</p>
            </div>
        <?php else: ?>
            
            <div class="scheduler-grid">
                <?php foreach ($scheduleData as $fid => $data): ?>
                    <div class="timeline-row">
                        <div class="timeline-facility">
                            <?php if (!empty($data['image_path'])): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($data['image_path']) ?>" alt="<?= htmlspecialchars($data['facility_name']) ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 120px; background: rgba(255,255,255,0.05); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items:center; justify-content:center;">
                                    <?= render_icon('map-pin', 'rgba(255,255,255,0.2)', 24) ?>
                                </div>
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($data['facility_name']) ?></h3>

                        </div>
                        
                        <div class="timeline-slots">
                            <?php if (!empty($data['is_fully_unavailable'])): ?>
                                <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 1rem 2rem; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; color: #ef4444; width: 100%; min-width: 300px;">
                                    <div style="font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Unavailable</div>
                                </div>
                            <?php elseif (empty($data['slots'])): ?>
                                <div style="padding: 1.5rem; color: var(--muted-foreground); font-style: italic;">No slots available</div>
                            <?php else: ?>
                                <?php foreach ($data['slots'] as $slot): ?>
                                    <?php if (!empty($slot['is_maintenance'])): ?>
                                        <div class="time-block disabled" style="background: rgba(239, 68, 68, 0.05); border-color: rgba(239,68,68,0.2); color: #ef4444;">
                                            <span class="status">Maintenance</span>
                                            <span class="time"><?= date('h:i A', strtotime($slot['start_time'])) ?></span>
                                        </div>
                                    <?php elseif (!empty($slot['is_booked'])): ?>
                                        <div class="time-block disabled" style="background: rgba(245, 158, 11, 0.05); border-color: rgba(245,158,11,0.2); color: #f59e0b;">
                                            <span class="status">Booked</span>
                                            <span class="time"><?= date('h:i A', strtotime($slot['start_time'])) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/student/book.php?facility_id=<?= $fid ?>&timeslot_id=<?= $slot['id'] ?>&date=<?= $activeDate ?>" class="time-block">
                                            <span class="status">Available</span>
                                            <span class="time"><?= date('h:i A', strtotime($slot['start_time'])) ?></span>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
