<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('student');
$user = getUser();

$booking_id = $_GET['id'] ?? null;
if (!$booking_id) {
    setFlash('No booking ID provided.', 'error');
    header('Location: index.php'); exit;
}

// Fetch existing booking
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? AND status = 'pending'");
$stmt->execute([$booking_id, $user['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    setFlash('Booking not found or cannot be modified (it might already be approved).', 'error');
    header('Location: index.php'); exit;
}

// Fetch existing equipment for this booking
$eq_stmt = $pdo->prepare("SELECT equipment_id, quantity FROM equipment_bookings WHERE booking_id = ?");
$eq_stmt->execute([$booking_id]);
$existing_eq = [];
foreach ($eq_stmt->fetchAll() as $row) {
    $existing_eq[$row['equipment_id']] = $row['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_id = $_POST['facility_id'] ?? '';
    $timeslot_id = $_POST['timeslot_id'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $equipments_post = $_POST['equipment'] ?? [];

    if (!$facility_id || !$timeslot_id || !$booking_date) {
        setFlash('Please complete all fields.', 'error');
    } else {
        // Check if already booked (excluding current booking)
        $checkStmt = $pdo->prepare("SELECT id FROM bookings WHERE timeslot_id = ? AND booking_date = ? AND status IN ('pending', 'approved') AND id != ?");
        $checkStmt->execute([$timeslot_id, $booking_date, $booking_id]);
        if ($checkStmt->fetch()) {
            setFlash('This timeslot is already booked for the selected date.', 'error');
        } else {
            // Maintenance check
            $facStmt = $pdo->prepare("SELECT status, maintenance_time, maintenance_end_time FROM facilities WHERE id = ?");
            $facStmt->execute([$facility_id]);
            $fac = $facStmt->fetch();
            
            $isMaintenance = false;
            if ($fac && in_array($fac['status'], ['maintenance', 'unavailable'])) {
                if (empty($fac['maintenance_time']) || empty($fac['maintenance_end_time'])) {
                    $isMaintenance = true;
                } else {
                    $mStart = strtotime($fac['maintenance_time']);
                    $mEnd = strtotime($fac['maintenance_end_time']);
                    
                    $slotStmt = $pdo->prepare("SELECT start_time, end_time FROM availability WHERE id = ?");
                    $slotStmt->execute([$timeslot_id]);
                    $slot = $slotStmt->fetch();
                    if ($slot) {
                        $sStartTimestamp = strtotime($booking_date . ' ' . $slot['start_time']);
                        $sEndTimestamp = strtotime($booking_date . ' ' . $slot['end_time']);
                        if ($sStartTimestamp < $mEnd && $mStart < $sEndTimestamp) {
                            $isMaintenance = true;
                        }
                    }
                }
            }
            
            // Check new maintenance table
            $maintStmt = $pdo->prepare("SELECT start_date, end_date FROM maintenance WHERE facility_id = ? AND status IN ('scheduled', 'in_progress')");
            $maintStmt->execute([$facility_id]);
            $maintenances = $maintStmt->fetchAll();
            foreach ($maintenances as $m) {
                $mStart = strtotime($m['start_date'] . ' 00:00:00');
                $mEnd = $m['end_date'] ? strtotime($m['end_date'] . ' 23:59:59') : strtotime($m['start_date'] . ' 23:59:59');
                
                $slotStmt = $pdo->prepare("SELECT start_time, end_time FROM availability WHERE id = ?");
                $slotStmt->execute([$timeslot_id]);
                $slot = $slotStmt->fetch();
                if ($slot) {
                    $sStartTimestamp = strtotime($booking_date . ' ' . $slot['start_time']);
                    $sEndTimestamp = strtotime($booking_date . ' ' . $slot['end_time']);
                    if ($sStartTimestamp <= $mEnd && $mStart <= $sEndTimestamp) {
                        $isMaintenance = true;
                        break;
                    }
                }
            }

            if ($isMaintenance) {
                setFlash('This facility is under maintenance for the selected timeslot.', 'error');
            } else {
                try {
                    $pdo->beginTransaction();
                    
                    // 1. Restore old equipment quantities
                    $restore_eq_stmt = $pdo->prepare("UPDATE equipments SET available_quantity = available_quantity + ? WHERE id = ?");
                    foreach ($existing_eq as $eq_id => $qty) {
                        $restore_eq_stmt->execute([$qty, $eq_id]);
                    }
                    
                    // 2. Delete old equipment bookings
                    $pdo->prepare("DELETE FROM equipment_bookings WHERE booking_id = ?")->execute([$booking_id]);
                    
                    // 3. Update main booking
                    $stmt = $pdo->prepare("UPDATE bookings SET facility_id = ?, timeslot_id = ?, booking_date = ?, modified = NOW() WHERE id = ?");
                    $stmt->execute([$facility_id, $timeslot_id, $booking_date, $booking_id]);
                    
                    // 4. Insert new Equipment Bookings
                    if (!empty($equipments_post)) {
                        $eq_stmt = $pdo->prepare("INSERT INTO equipment_bookings (user_id, equipment_id, booking_id, quantity, booking_date, status, created, modified) VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())");
                        $update_eq_stmt = $pdo->prepare("UPDATE equipments SET available_quantity = available_quantity - ? WHERE id = ?");
                        foreach ($equipments_post as $eq_id => $qty) {
                            $qty = (int)$qty;
                            if ($qty > 0) {
                                $eq_stmt->execute([$user['id'], $eq_id, $booking_id, $qty, $booking_date]);
                                $update_eq_stmt->execute([$qty, $eq_id]);
                            }
                        }
                    }
                    
                    $pdo->commit();
                    setFlash('Booking updated successfully!');
                    header('Location: index.php'); exit;
                } catch (Exception $e) {
                    $pdo->rollBack();
                    setFlash('Failed to update booking. ' . $e->getMessage(), 'error');
                }
            }
        }
    }
}

$title = 'Modify Booking';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

$facilities = $pdo->query("SELECT id, name, image_path, status, maintenance_time FROM facilities ORDER BY name")->fetchAll();
$availability = $pdo->query("SELECT t.id, t.facility_id, f.name as facility_name, t.start_time, t.end_time, t.day_of_week 
                          FROM availability t JOIN facilities f ON t.facility_id = f.id 
                          WHERE t.status='available' ORDER BY f.name, FIELD(t.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), t.start_time")->fetchAll();

// Get ALL equipments. We need to artificially inflate available_quantity by what we currently hold so the user can select up to that amount again.
$equipments = $pdo->query("SELECT id, facility_id, name, available_quantity, image_path FROM equipments")->fetchAll();
foreach ($equipments as &$eq) {
    if (isset($existing_eq[$eq['id']])) {
        $eq['available_quantity'] += $existing_eq[$eq['id']];
    }
}

$future_bookings = $pdo->query("SELECT id, facility_id, timeslot_id, booking_date FROM bookings WHERE booking_date >= CURDATE() AND status IN ('pending', 'approved')")->fetchAll(PDO::FETCH_ASSOC);
$maintenance_records = $pdo->query("SELECT facility_id, start_date, end_date FROM maintenance WHERE status IN ('scheduled', 'in_progress')")->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
/* Premium Form Container */
.premium-form-container {
    background: rgba(30, 41, 59, 0.4);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2.5rem;
    max-width: 900px;
    margin: 0 auto;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}
@media (max-width: 768px) {
    .premium-form-container { padding: 1.5rem; }
}

.step-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--foreground);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: var(--primary);
    color: var(--primary-foreground);
    border-radius: 50%;
    font-size: 0.9rem;
    font-weight: 800;
}

/* Facility Grid */
.photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
.photo-card { 
    border: 2px solid transparent; 
    border-radius: 12px; 
    overflow: hidden; 
    cursor: pointer; 
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
    position: relative; 
    background: rgba(15, 23, 42, 0.6); 
}
.photo-card img { width: 100%; height: 160px; object-fit: cover; display: block; opacity: 0.8; transition: all 0.3s; }
.photo-card-body { padding: 1rem; text-align: center; font-weight: 600; font-size: 1rem; color: var(--foreground); transition: all 0.3s; }
.photo-card input[type="radio"] { position: absolute; opacity: 0; }

.photo-card:hover { transform: translateY(-4px); background: rgba(30, 41, 59, 0.8); }
.photo-card:hover img { opacity: 1; }

.photo-card:has(input:checked) { 
    border-color: #10b981; 
    background: rgba(16, 185, 129, 0.1); 
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); 
}
.photo-card:has(input:checked) img { opacity: 1; border-bottom: 2px solid #10b981; }
.photo-card:has(input:checked) .photo-card-body { color: #10b981; }

/* Date & Time Section */
.date-time-container {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2.5rem;
    margin-bottom: 3rem;
}
@media (max-width: 768px) {
    .date-time-container { grid-template-columns: 1fr; }
}

/* Time Blocks Grid */
.time-blocks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 1rem;
    min-height: 100px;
}
.time-block-btn {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: var(--muted-foreground);
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    user-select: none;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.time-block-btn:hover {
    background: rgba(255,255,255,0.1);
    color: var(--foreground);
    border-color: rgba(255,255,255,0.2);
}
.time-block-btn.active {
    background: rgba(16, 185, 129, 0.15);
    border-color: #10b981;
    color: #10b981;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
    transform: scale(1.05);
}
.time-block-btn.active .status { color: #10b981; }

.time-block-btn .status { font-size: 0.7rem; text-transform: uppercase; font-weight: 800; letter-spacing: 1px; color: var(--muted-foreground); transition: all 0.2s; }
.time-block-btn .time { font-size: 1rem; font-weight: 600; }

.empty-state-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem 1rem;
    background: rgba(0,0,0,0.2);
    border-radius: 8px;
    border: 1px dashed rgba(255,255,255,0.1);
    color: var(--muted-foreground);
}

/* Equipment Qty Control */
.eq-qty-control {
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    margin-top: 0.5rem;
}
.eq-qty-btn { 
    background: rgba(255,255,255,0.1); border: none; color: white; width: 28px; height: 28px; border-radius: 4px; font-size: 1rem; cursor: pointer; transition: all 0.2s; 
}
.eq-qty-btn:hover { background: var(--primary); color: var(--primary-foreground); }
.eq-qty-input { 
    background: transparent; border: none; color: white; width: 40px; text-align: center; font-size: 1rem; font-weight: bold; outline: none; 
}
</style>

<div class="sb-page-header">
    <h2><?= render_icon('calendar', '', 24) ?> Modify Booking</h2>
    <a href="index.php" class="sb-btn sb-btn-ghost">Cancel</a>
</div>

<div class="sb-fade">
    <form method="POST" class="premium-form-container" id="bookingForm">
        
        <div class="step-title"><span class="step-number">1</span> Choose Your Venue</div>
        <div class="photo-grid">
            <?php foreach($facilities as $f): ?>
            <label class="photo-card">
                <input type="radio" name="facility_id" value="<?= $f['id'] ?>" required <?= ($f['id'] == $booking['facility_id']) ? 'checked' : '' ?>>
                <?php if(!empty($f['image_path'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($f['image_path']) ?>" alt="<?= htmlspecialchars($f['name']) ?>">
                <?php else: ?>
                    <div style="height: 160px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.4); color: var(--muted-foreground);">No Image</div>
                <?php endif; ?>
                <div class="photo-card-body"><?= htmlspecialchars($f['name']) ?></div>
            </label>
            <?php endforeach; ?>
        </div>

        <div class="date-time-container">
            <div>
                <div class="step-title"><span class="step-number">2</span> Select Date</div>
                <div class="sb-form-group">
                    <input type="date" name="booking_date" class="sb-form-input" id="booking_date" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($booking['booking_date']) ?>" style="padding: 1rem; font-size: 1.1rem; border-radius: 8px;">
                </div>
            </div>
            
            <div>
                <div class="step-title"><span class="step-number">3</span> Pick a Timeslot</div>
                <div class="time-blocks-grid" id="timeBlocksContainer">
                    <div class="empty-state-message">
                        <?= render_icon('info', '', 32) ?>
                        <p style="margin-top: 1rem; font-size: 1.1rem;">Please select a facility and date first.</p>
                    </div>
                </div>
                <!-- Hidden input to store selected timeslot -->
                <input type="hidden" name="timeslot_id" id="selected_timeslot_id" value="<?= htmlspecialchars($booking['timeslot_id']) ?>" required>
            </div>
        </div>

        <div id="equipmentSection" style="display: none; margin-bottom: 3rem;">
            <div class="step-title"><span class="step-number">4</span> Optional: Add Equipment</div>
            <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                <label style="color: var(--foreground); font-weight: 600;">Do you need equipment for your booking?</label>
                <div class="sb-form-group" style="margin: 0; display: flex; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="need_equipment" value="yes" <?= !empty($existing_eq) ? 'checked' : '' ?> onchange="toggleEquipmentGrid(true)"> Yes
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="need_equipment" value="no" <?= empty($existing_eq) ? 'checked' : '' ?> onchange="toggleEquipmentGrid(false)"> No
                    </label>
                </div>
            </div>
            <div class="photo-grid" id="equipmentGrid" style="display: <?= !empty($existing_eq) ? 'grid' : 'none' ?>;">
                <!-- Equipment cards injected here via JS -->
            </div>
        </div>

        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="sb-btn sb-btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem; border-radius: 999px; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);">
                <?= render_icon('check', '', 20) ?> Update Booking
            </button>
        </div>
    </form>
</div>

<!-- Embed availability and equipment data as JSON for JS filtering -->
<script>
    const availabilityData = <?= json_encode($availability) ?>;
    const equipmentData = <?= json_encode($equipments) ?>;
    const facilitiesData = <?= json_encode($facilities) ?>;
    const bookedData = <?= json_encode($future_bookings) ?>;
    const maintenanceData = <?= json_encode($maintenance_records ?? []) ?>;
    const baseUrl = "<?= BASE_URL ?>";
    const existingEq = <?= json_encode($existing_eq) ?>;
    const currentBookingId = <?= $booking['id'] ?>;
    const currentTimeslotId = "<?= $booking['timeslot_id'] ?>";
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const facilityRadios = document.querySelectorAll('input[name="facility_id"]');
    const dateInput = document.getElementById('booking_date');
    const container = document.getElementById('timeBlocksContainer');
    const hiddenInput = document.getElementById('selected_timeslot_id');
    const bookingForm = document.getElementById('bookingForm');
    const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    let initialLoad = true;

    function renderTimeBlocks() {
        const selectedFacility = document.querySelector('input[name="facility_id"]:checked');
        const selectedDate = dateInput.value;
        
        container.innerHTML = '';
        
        if (!initialLoad) {
            hiddenInput.value = '';
        }

        if (!selectedFacility || !selectedDate) {
            container.innerHTML = `
                <div class="empty-state-message">
                    <p>Please select both a facility and a date.</p>
                </div>`;
            return;
        }

        const dateObj = new Date(selectedDate);
        const dayName = daysOfWeek[dateObj.getDay()];
        const fid = selectedFacility.value;

        // Filter availability
        const availableSlots = availabilityData.filter(t => t.facility_id == fid && t.day_of_week === dayName);

        if (availableSlots.length === 0) {
            container.innerHTML = `
                <div class="empty-state-message">
                    <p>No available slots for this facility on ${dayName}.</p>
                </div>`;
            return;
        }

        const bookedSlots = bookedData.filter(b => b.facility_id == fid && b.booking_date === selectedDate && b.id != currentBookingId).map(b => b.timeslot_id);
        const facility = facilitiesData.find(f => f.id == fid);

        // Render blocks
        availableSlots.forEach(slot => {
            const block = document.createElement('div');
            block.className = 'time-block-btn';
            block.dataset.id = slot.id;
            
            const start = slot.start_time.substring(0, 5);
            const end = slot.end_time.substring(0, 5);
            
            let isMaint = false;
            if (facility && (facility.status === 'maintenance' || facility.status === 'unavailable')) {
                if (!facility.maintenance_time) {
                    isMaint = true;
                } else {
                    let mStart = new Date(facility.maintenance_time.replace(' ', 'T')).getTime();
                    let sStart = new Date(`${selectedDate}T${slot.start_time}`).getTime();
                    let sEnd = new Date(`${selectedDate}T${slot.end_time}`).getTime();
                    
                    if (!facility.maintenance_end_time) {
                        if (sEnd > mStart) {
                            isMaint = true;
                        }
                    } else {
                        let mEnd = new Date(facility.maintenance_end_time.replace(' ', 'T')).getTime();
                        if (sStart < mEnd && mStart < sEnd) {
                            isMaint = true;
                        }
                    }
                }
            }
            
            // Check new maintenance table
            const facilityMaint = maintenanceData.filter(m => m.facility_id == fid);
            if (facilityMaint.length > 0) {
                let sStart = new Date(`${selectedDate}T${slot.start_time}`).getTime();
                let sEnd = new Date(`${selectedDate}T${slot.end_time}`).getTime();
                
                facilityMaint.forEach(m => {
                    let mStart = new Date(`${m.start_date}T00:00:00`).getTime();
                    let mEnd = m.end_date ? new Date(`${m.end_date}T23:59:59`).getTime() : new Date(`${m.start_date}T23:59:59`).getTime();
                    
                    if (sStart <= mEnd && mStart <= sEnd) {
                        isMaint = true;
                    }
                });
            }

            if (isMaint) {
                block.innerHTML = `
                    <span class="status" style="color: #ef4444;">Maintenance</span>
                    <span class="time">${start} - ${end}</span>
                `;
                block.style.borderColor = "rgba(239, 68, 68, 0.3)";
                block.style.background = "rgba(239, 68, 68, 0.1)";
                block.style.cursor = "not-allowed";
            } else if (bookedSlots.includes(slot.id)) {
                block.innerHTML = `
                    <span class="status" style="color: #f59e0b;">Booked</span>
                    <span class="time">${start} - ${end}</span>
                `;
                block.style.borderColor = "rgba(245, 158, 11, 0.3)";
                block.style.background = "rgba(245, 158, 11, 0.1)";
                block.style.cursor = "not-allowed";
            } else {
                block.innerHTML = `
                    <span class="status">Available</span>
                    <span class="time">${start} - ${end}</span>
                `;
                
                block.addEventListener('click', () => {
                    document.querySelectorAll('.time-block-btn').forEach(b => b.classList.remove('active'));
                    block.classList.add('active');
                    hiddenInput.value = slot.id;
                });
                
                // Preselect if it's the saved timeslot
                if (initialLoad && slot.id == currentTimeslotId) {
                    block.classList.add('active');
                    hiddenInput.value = slot.id;
                }
            }

            container.appendChild(block);
        });
    }

    function renderEquipment() {
        const selectedFacility = document.querySelector('input[name="facility_id"]:checked');
        const eqSection = document.getElementById('equipmentSection');
        const eqGrid = document.getElementById('equipmentGrid');
        
        eqGrid.innerHTML = '';
        
        if (!selectedFacility) {
            eqSection.style.display = 'none';
            return;
        }

        const fid = selectedFacility.value;
        const availableEq = equipmentData.filter(e => e.facility_id == fid && e.available_quantity > 0);

        if (availableEq.length === 0) {
            eqSection.style.display = 'none';
            return;
        }

        eqSection.style.display = 'block';

        availableEq.forEach(eq => {
            const label = document.createElement('label');
            label.className = 'photo-card';
            
            let imgHtml = eq.image_path 
                ? `<img src="${baseUrl}/${eq.image_path}" alt="${eq.name}">` 
                : `<div style="height: 160px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.4); color: var(--muted-foreground);">No Image</div>`;

            let currentQty = 0;
            if (initialLoad && existingEq[eq.id]) {
                currentQty = existingEq[eq.id];
            }

            let borderStyle = currentQty > 0 ? `border-color: #10b981; background: rgba(16, 185, 129, 0.1);` : '';
            
            label.style.cssText = borderStyle;

            label.innerHTML = `
                ${imgHtml}
                <div class="photo-card-body">
                    ${eq.name}
                    <div style="font-size: 0.8rem; font-weight: normal; margin-top: 0.25rem; color: var(--muted-foreground);"><span class="avail-badge">${eq.available_quantity - currentQty}</span> Available</div>
                    <div class="eq-qty-control" onclick="event.preventDefault();">
                        <button type="button" class="eq-qty-btn" onclick="updateQty(this, -1, ${eq.available_quantity})">-</button>
                        <input type="number" name="equipment[${eq.id}]" class="eq-qty-input" value="${currentQty}" min="0" max="${eq.available_quantity}" readonly>
                        <button type="button" class="eq-qty-btn" onclick="updateQty(this, 1, ${eq.available_quantity})">+</button>
                    </div>
                </div>
            `;
            eqGrid.appendChild(label);
        });
        
        if (!initialLoad) {
            document.querySelector('input[name="need_equipment"][value="no"]').checked = true;
            toggleEquipmentGrid(false);
        }
    }

    window.toggleEquipmentGrid = function(show) {
        document.getElementById('equipmentGrid').style.display = show ? 'grid' : 'none';
        if (!show) {
            document.querySelectorAll('.eq-qty-input').forEach(input => {
                input.value = 0;
                const card = input.closest('.photo-card');
                card.style.borderColor = 'transparent';
                card.style.background = 'rgba(15, 23, 42, 0.6)';
                
                const max = parseInt(input.getAttribute('max')) || 0;
                const badge = card.querySelector('.avail-badge');
                if (badge) {
                    badge.innerText = max;
                }
            });
        }
    }

    window.updateQty = function(btn, change, max) {
        // Automatically check 'Yes' for equipment if they add some
        const yesRadio = document.querySelector('input[name="need_equipment"][value="yes"]');
        if(yesRadio) yesRadio.checked = true;

        const input = btn.parentElement.querySelector('input');
        let val = parseInt(input.value) || 0;
        val += change;
        if (val < 0) val = 0;
        if (val > max) val = max;
        input.value = val;
        
        const card = btn.closest('.photo-card');
        const badge = card.querySelector('.avail-badge');
        if (badge) {
            badge.innerText = max - val;
        }
        
        if (val > 0) {
            card.style.borderColor = '#10b981';
            card.style.background = 'rgba(16, 185, 129, 0.1)';
        } else {
            card.style.borderColor = 'transparent';
            card.style.background = 'rgba(15, 23, 42, 0.6)';
        }
    }

    facilityRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            initialLoad = false;
            renderTimeBlocks();
            renderEquipment();
        });
    });

    dateInput.addEventListener('change', () => {
        initialLoad = false;
        renderTimeBlocks();
    });

    bookingForm.addEventListener('submit', function(e) {
        if (!hiddenInput.value) {
            e.preventDefault();
            alert('Please select a specific timeslot from the grid before submitting.');
        }
    });

    // Initial render
    renderTimeBlocks();
    renderEquipment();
    initialLoad = false;
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
