<?php
require_once __DIR__ . '/includes/auth.php';
requireRole(['admin', 'staff']);
require_once __DIR__ . '/includes/fpdf/fpdf.php';

$month_year = $_GET['month'] ?? date('Y-m');
$user = getUser();
$exported_by = $user['name'];

class PDF extends FPDF {
    public $reportMonth;
    public $exportedBy;

    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Monthly Booking Report', 0, 1, 'C');
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Report ID: ' . uniqid('RPT-'), 0, 1, 'C');
        $this->Cell(0, 6, 'Month and Year: ' . date('F Y', strtotime($this->reportMonth . '-01')), 0, 1, 'C');
        $this->Cell(0, 6, 'Export Date: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
        $this->Cell(0, 6, 'Exported By: ' . $this->exportedBy, 0, 1, 'C');
        $this->Ln(5);

        // Table Header
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(15, 8, 'Booking ID', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Student ID', 1, 0, 'C', true);
        $this->Cell(35, 8, 'Student Name', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Type', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Item Name', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Date', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Time', 1, 0, 'C', true);
        $this->Cell(10, 8, 'Qty', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Status', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Approved By', 1, 0, 'C', true);
        $this->Cell(42, 8, 'Remarks', 1, 1, 'C', true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' / {nb}', 0, 0, 'C');
    }
}

// Fetch Facility Bookings
$f_stmt = $pdo->prepare("
    SELECT b.id, u.student_id, u.name as student_name, 'Facility' as type, f.name as item_name, 
           b.booking_date, t.start_time, t.end_time, 1 as quantity, b.status, b.notes 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    JOIN facilities f ON b.facility_id = f.id 
    LEFT JOIN availability t ON b.timeslot_id = t.id 
    WHERE DATE_FORMAT(b.booking_date, '%Y-%m') = ?
");
$f_stmt->execute([$month_year]);
$facilities = $f_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Equipment Bookings
$e_stmt = $pdo->prepare("
    SELECT eb.id, u.student_id, u.name as student_name, 'Equipment' as type, e.name as item_name, 
           eb.booking_date, NULL as start_time, NULL as end_time, eb.quantity, eb.status, eb.notes 
    FROM equipment_bookings eb 
    JOIN users u ON eb.user_id = u.id 
    JOIN equipments e ON eb.equipment_id = e.id 
    WHERE DATE_FORMAT(eb.booking_date, '%Y-%m') = ?
");
$e_stmt->execute([$month_year]);
$equipments = $e_stmt->fetchAll(PDO::FETCH_ASSOC);

$all_bookings = array_merge($facilities, $equipments);
usort($all_bookings, function($a, $b) {
    return strtotime($a['booking_date']) - strtotime($b['booking_date']);
});

$pdf = new PDF('L', 'mm', 'A4'); // Landscape
$pdf->reportMonth = $month_year;
$pdf->exportedBy = $exported_by;
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

$totals = [
    'all' => count($all_bookings),
    'facility' => count($facilities),
    'equipment' => count($equipments),
    'approved' => 0,
    'other' => 0
];

foreach ($all_bookings as $b) {
    if ($b['status'] === 'approved') {
        $totals['approved']++;
    } else {
        $totals['other']++;
    }

    $time_str = $b['start_time'] ? substr($b['start_time'], 0, 5) . '-' . substr($b['end_time'], 0, 5) : 'N/A';
    $notes_str = strlen((string)$b['notes']) > 30 ? substr($b['notes'], 0, 27) . '...' : (string)$b['notes'];
    
    // Calculate widths must sum to 277 for A4 landscape
    $w = [15, 20, 35, 20, 45, 20, 25, 10, 20, 25, 42];
    
    $pdf->Cell($w[0], 6, $b['id'], 1, 0, 'C');
    $pdf->Cell($w[1], 6, $b['student_id'] ?: 'N/A', 1, 0, 'C');
    $pdf->Cell($w[2], 6, mb_substr($b['student_name'], 0, 20), 1, 0, 'L');
    $pdf->Cell($w[3], 6, $b['type'], 1, 0, 'C');
    $pdf->Cell($w[4], 6, mb_substr($b['item_name'], 0, 30), 1, 0, 'L');
    $pdf->Cell($w[5], 6, $b['booking_date'], 1, 0, 'C');
    $pdf->Cell($w[6], 6, $time_str, 1, 0, 'C');
    $pdf->Cell($w[7], 6, $b['quantity'], 1, 0, 'C');
    $pdf->Cell($w[8], 6, ucfirst($b['status']), 1, 0, 'C');
    $pdf->Cell($w[9], 6, 'System', 1, 0, 'C'); // Approved By is System / N/A as requested
    $pdf->Cell($w[10], 6, $notes_str, 1, 1, 'L');
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Summary', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 6, 'Total Bookings:', 0, 0); $pdf->Cell(20, 6, $totals['all'], 0, 1);
$pdf->Cell(60, 6, 'Total Facility Bookings:', 0, 0); $pdf->Cell(20, 6, $totals['facility'], 0, 1);
$pdf->Cell(60, 6, 'Total Equipment Bookings:', 0, 0); $pdf->Cell(20, 6, $totals['equipment'], 0, 1);
$pdf->Cell(60, 6, 'Total Approved Bookings:', 0, 0); $pdf->Cell(20, 6, $totals['approved'], 0, 1);
$pdf->Cell(60, 6, 'Total Pending/Rejected/Cancelled:', 0, 0); $pdf->Cell(20, 6, $totals['other'], 0, 1);

ob_end_clean();
$pdf->Output('D', 'Monthly_Booking_Report_' . $month_year . '.pdf');
