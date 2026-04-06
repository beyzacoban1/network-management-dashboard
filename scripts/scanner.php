<?php
include 'db.php';

// İzlemek istediğin cihazların listesi
$devices = [
    ['ip' => '127.0.0.1', 'name' => 'Beyza_PC'],
    ['ip' => '192.168.20.253', 'name' => 'Damla_PC'],
    ['ip' => '192.168.10.188', 'name' => 'Ilayda_PC']
];

$community = "public";
$check_list = [80, 443, 445, 3389]; // Kritik portlar

foreach ($devices as $device) {
    $host = $device['ip'];
    $deviceName = $device['name'];

    // --- 1. PİNG & DURUM ---
    $ping_check = shell_exec("ping -n 1 -w 1000 $host");
    $status = (strpos($ping_check, 'Reply from') !== false || strpos($ping_check, 'TTL=') !== false) ? 'UP' : 'DOWN';
    
    preg_match('/(?:Süre|time)[=<]([0-9]+)ms/', $ping_check, $matches);
    $latency = isset($matches[1]) ? (int)$matches[1] : 0;

    // --- 2. SNMP (Sadece cihaz UP ise) ---
    $cpu = 0; $ram = 0;
    if ($status == 'UP') {
        // CPU Bilgisi
        $cpu_raw = @snmp2_real_walk($host, $community, ".1.3.6.1.2.1.25.3.3.1.2");
        if ($cpu_raw) {
            $total_load = 0;
            foreach ($cpu_raw as $val) { $total_load += (int)preg_replace('/[^0-9]/', '', $val); }
            $cpu = round($total_load / count($cpu_raw), 2);
        }
        // RAM Bilgisi
        $res_total = @snmp2_get($host, $community, ".1.3.6.1.2.1.25.2.3.1.5.3");
        $res_used = @snmp2_get($host, $community, ".1.3.6.1.2.1.25.2.3.1.6.3");
        if ($res_total && $res_used) {
            $t = (int)preg_replace('/[^0-9]/', '', $res_total);
            $u = (int)preg_replace('/[^0-9]/', '', $res_used);
            $ram = ($t > 0) ? round(($u / $t) * 100, 2) : 0;
        }
    }

    // --- 3. PORT TARAMA ---
    $found = [];
    foreach ($check_list as $port) {
        $fp = @fsockopen($host, $port, $errno, $errstr, 0.1); 
        if ($fp) {
            $found[] = $port;
            fclose($fp);
        }
    }
    $port_results = !empty($found) ? implode(", ", $found) : "Kapalı";

    // --- 4. VERİTABANI KAYDI (Artık değişkenler dolu) ---
    $sql = "INSERT INTO device_logs (device_name, cpu_usage, ram_usage, latency, status, open_ports) 
            VALUES ('$deviceName', '$cpu', '$ram', '$latency', '$status', '$port_results')";
    
    if (!$conn->query($sql)) {
        echo "Hata: " . $conn->error;
    }
}

echo "Taramalar tamamlandı! Dashboard'u kontrol edebilirsin.";
$conn->close();
?>
