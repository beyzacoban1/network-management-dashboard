<?php
$servername = "localhost";
$username = "root"; // XAMPP varsayılan kullanıcı adı
$password = "";     // XAMPP varsayılan şifre boştur
$dbname = "network_monitor";

// Bağlantı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>
