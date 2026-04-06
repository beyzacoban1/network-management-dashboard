<?php
include 'db.php';

$selected_pc = isset($_GET['pc']) ? $_GET['pc'] : 'Beyza_PC';
$devices_list = ['Beyza_PC', 'Damla_PC', 'Ilayda_PC'];
$latest_stats = [];

foreach($devices_list as $pc) {
    $res = $conn->query("SELECT * FROM device_logs WHERE device_name = '$pc' ORDER BY created_at DESC LIMIT 1");
    $latest_stats[$pc] = $res->fetch_assoc();
}

$graph_res = $conn->query("SELECT * FROM device_logs WHERE device_name = '$selected_pc' ORDER BY created_at DESC LIMIT 20");
$labels = []; $cpu_data = []; $ram_data = []; $lat_data = [];

while($row = $graph_res->fetch_assoc()) {
    $labels[] = date("H:i:s", strtotime($row['created_at']));
    $cpu_data[] = $row['cpu_usage'];
    $ram_data[] = $row['ram_usage'];
    $lat_data[] = $row['latency']; // Gecikme verisini çekiyoruz
}

$labels = array_reverse($labels); 
$cpu_data = array_reverse($cpu_data); 
$ram_data = array_reverse($ram_data); 
$lat_data = array_reverse($lat_data); 
?>

<!DOCTYPE html>
<html>
<head>

<meta http-equiv="refresh" content="5">
    <title>Multi-PC Network Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 20px; }
        .card-container { display: flex; gap: 20px; margin-bottom: 30px; justify-content: center; }
        .pc-card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 250px; text-align: center; border-top: 5px solid #ddd; cursor: pointer; transition: 0.3s; }
        .pc-card.active { border-color: #3498db; background: #ebf5fb; }
        .status-dot { height: 12px; width: 12px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .bg-up { background: #2ecc71; box-shadow: 0 0 8px #2ecc71; }
        .bg-down { background: #e74c3c; box-shadow: 0 0 8px #e74c3c; }
        .chart-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto; }
    </style>
</head>
<body>

<h2 style="text-align: center; color: #2c3e50;">Ağ ve Cihaz Performans Paneli</h2>

<div class="card-container">
    <?php foreach($latest_stats as $name => $data): ?>
        <div class="pc-card <?php echo ($selected_pc == $name) ? 'active' : ''; ?>" onclick="window.location.href='?pc=<?php echo $name; ?>'">
            <div class="status-dot <?php echo ($data['status'] == 'UP') ? 'bg-up' : 'bg-down'; ?>"></div>
            <strong><?php echo $name; ?></strong>
            <div style="margin-top:10px; font-size: 0.9em; color: #666;">
                CPU: %<?php echo $data['cpu_usage'] ?? 0; ?> | RAM: %<?php echo $data['ram_usage'] ?? 0; ?><br>
                Gecikme: <?php echo $data['latency'] ?? 0; ?>ms
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="chart-container">
    <h3 style="text-align:center;"><?php echo $selected_pc; ?> - Canlı Performans</h3>
    <canvas id="performanceChart"></canvas>
</div>

<script>
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [
                { label: 'CPU (%)', data: <?php echo json_encode($cpu_data); ?>, borderColor: '#36a2eb', backgroundColor: '#36a2eb', fill: false, tension: 0.3, yAxisID: 'y' },
                { label: 'RAM (%)', data: <?php echo json_encode($ram_data); ?>, borderColor: '#9b59b6', backgroundColor: '#9b59b6', fill: false, tension: 0.3, yAxisID: 'y' },
                { label: 'Ping (ms)', data: <?php echo json_encode($lat_data); ?>, borderColor: '#f39c12', backgroundColor: '#f39c12', fill: false, tension: 0.1, yAxisID: 'y1' }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { type: 'linear', display: true, position: 'left', min: 0, max: 100, title: { display: true, text: 'Kullanım (%)' } },
                y1: { type: 'linear', display: true, position: 'right', min: 0, title: { display: true, text: 'Gecikme (ms)' }, grid: { drawOnChartArea: false } }
            }
        }
    });
</script>


<iframe src="index.php" style="display:none;"></iframe>


</body>
</html>
