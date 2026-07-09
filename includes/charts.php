<?php
$isStudent = (isset($user) && $user['role'] === 'student');

if ($isStudent) {
    $facStats = $pdo->query("SELECT f.name, COUNT(b.id) as count FROM bookings b JOIN facilities f ON f.id = b.facility_id WHERE b.user_id = {$user['id']} GROUP BY f.id")->fetchAll();
    $eqStats = $pdo->query("SELECT e.name, IFNULL(SUM(be.quantity),0) as count FROM equipment_bookings be JOIN equipments e ON e.id = be.equipment_id WHERE be.user_id = {$user['id']} GROUP BY e.id")->fetchAll();
    $timeStats = $pdo->query("SELECT DATE(created) as date, COUNT(*) as count FROM bookings WHERE user_id = {$user['id']} AND created >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created) ORDER BY date")->fetchAll();
    $statusStats = $pdo->query("SELECT status, COUNT(*) as count FROM bookings WHERE user_id = {$user['id']} GROUP BY status")->fetchAll();
} else {
    $facStats = $pdo->query("SELECT f.name, COUNT(b.id) as count FROM facilities f LEFT JOIN bookings b ON f.id = b.facility_id GROUP BY f.id")->fetchAll();
    $eqStats = $pdo->query("SELECT e.name, IFNULL(SUM(be.quantity),0) as count FROM equipments e LEFT JOIN equipment_bookings be ON e.id = be.equipment_id GROUP BY e.id")->fetchAll();
    $timeStats = $pdo->query("SELECT DATE(created) as date, COUNT(*) as count FROM bookings WHERE created >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created) ORDER BY date")->fetchAll();
    $statusStats = $pdo->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status")->fetchAll();
}

if (empty($facStats)) {
    $facLabels = json_encode(['No Data']);
    $facData = json_encode([0]);
} else {
    $facLabels = json_encode(array_column($facStats, 'name'));
    $facData = json_encode(array_map('intval', array_column($facStats, 'count')));
}

if (empty($eqStats)) {
    $eqLabels = json_encode(['No Data']);
    $eqData = json_encode([0]);
} else {
    $eqLabels = json_encode(array_column($eqStats, 'name'));
    $eqData = json_encode(array_map('intval', array_column($eqStats, 'count')));
}

if (empty($timeStats)) {
    $timeLabels = json_encode([date('Y-m-d')]);
    $timeData = json_encode([0]);
} else {
    $timeLabels = json_encode(array_column($timeStats, 'date'));
    $timeData = json_encode(array_map('intval', array_column($timeStats, 'count')));
}

if (empty($statusStats)) {
    $statusLabels = json_encode(['No Data']);
    $statusData = json_encode([1]); // Dummy value to prevent donut crash
} else {
    $statusLabels = json_encode(array_column($statusStats, 'status'));
    $statusData = json_encode(array_map('intval', array_column($statusStats, 'count')));
}
?>

<div class="sb-page-header" style="margin-top: 3rem;">
    <h2><?= render_icon('bar-chart', '', 20) ?> Analytics</h2>
</div>

<div class="sb-chart-grid" style="margin-bottom:2rem;">
    <div class="sb-card sb-fade">
        <div class="sb-card-header"><h3><?= render_icon('pie-chart', '', 16) ?> Facility Popularity</h3></div>
        <div class="sb-card-body" style="min-height: 350px;">
            <div id="facChart"></div>
        </div>
    </div>
    
    <div class="sb-card sb-fade" style="animation-delay: 0.1s;">
        <div class="sb-card-header"><h3><?= render_icon('trending-up', '', 16) ?> Bookings Over Time</h3></div>
        <div class="sb-card-body" style="min-height: 350px;">
            <div id="timeChart"></div>
        </div>
    </div>
    
    <div class="sb-card sb-fade" style="animation-delay: 0.2s;">
        <div class="sb-card-header"><h3><?= render_icon('box', '', 16) ?> Equipment Usage</h3></div>
        <div class="sb-card-body" style="min-height: 350px;">
            <div id="eqChart"></div>
        </div>
    </div>
    
    <div class="sb-card sb-fade" style="animation-delay: 0.3s;">
        <div class="sb-card-header"><h3><?= render_icon('activity', '', 16) ?> Booking Status Breakdown</h3></div>
        <div class="sb-card-body" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
            <div id="statusChart" style="width: 100%;"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof ApexCharts === 'undefined') {
        document.querySelectorAll('.sb-card-body').forEach(el => {
            if(el.querySelector('[id$="Chart"]')) {
                el.innerHTML = '<div style="color:var(--muted-foreground); text-align:center; padding-top: 5rem;">Unable to load charts. Please check your internet connection or disable adblockers.</div>';
            }
        });
        return;
    }

    // Global ApexCharts Settings for Dark Premium Theme
    const apexOptions = {
        chart: {
            background: 'transparent',
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        theme: { mode: 'dark' },
        colors: ['#5ce1e6', '#38bdf8', '#818cf8', '#34d399', '#fbbf24', '#f87171'],
        grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
        dataLabels: { enabled: false },
        tooltip: { theme: 'dark', style: { fontSize: '14px' } }
    };

    try {
        // 1. Facility Chart (Bar)
        new ApexCharts(document.querySelector("#facChart"), {
            ...apexOptions,
            series: [{ name: 'Bookings', data: <?= $facData ?> }],
            chart: { ...apexOptions.chart, type: 'bar', height: 320 },
            plotOptions: { bar: { borderRadius: 6, horizontal: false, columnWidth: '45%' } },
            xaxis: { categories: <?= $facLabels ?>, axisBorder: { show: false }, axisTicks: { show: false } },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: "vertical", gradientToColors: ['#38bdf8'], stops: [0, 100] }
            }
        }).render();
    } catch(e) { console.error("facChart error:", e); }

    try {
        // 2. Bookings Over Time (Area Spline)
        new ApexCharts(document.querySelector("#timeChart"), {
            ...apexOptions,
            series: [{ name: 'Bookings', data: <?= $timeData ?> }],
            chart: { ...apexOptions.chart, type: 'area', height: 320 },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: <?= $timeLabels ?>, type: 'datetime', axisBorder: { show: false } },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] }
            },
            colors: ['#34d399']
        }).render();
    } catch(e) { console.error("timeChart error:", e); }

    try {
        // 3. Equipment Usage (Bar)
        new ApexCharts(document.querySelector("#eqChart"), {
            ...apexOptions,
            series: [{ name: 'Quantity Used', data: <?= $eqData ?> }],
            chart: { ...apexOptions.chart, type: 'bar', height: 320 },
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            xaxis: { categories: <?= $eqLabels ?> },
            colors: ['#818cf8']
        }).render();
    } catch(e) { console.error("eqChart error:", e); }

    try {
        // 4. Status Breakdown (Donut with glowing shadow)
        new ApexCharts(document.querySelector("#statusChart"), {
            ...apexOptions,
            series: <?= $statusData ?: '[]' ?>,
            labels: <?= $statusLabels ?: '[]' ?>,
            chart: { ...apexOptions.chart, type: 'donut', height: 320 },
            stroke: { show: true, colors: ['#1e293b'], width: 2 },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: { fontSize: '14px', color: '#94a3b8' },
                            value: { fontSize: '24px', fontWeight: 'bold', color: '#f8fafc' },
                            total: { show: true, showAlways: true, label: 'Total' }
                        }
                    }
                }
            }
        }).render();
    } catch(e) { console.error("statusChart error:", e); }
});
</script>
