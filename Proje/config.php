<?php
// db.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'db_ticaret';
$username = 'root';
$password = '';

// Bağlantı hatalarında exception fırlatmasını engelle
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $db = new mysqli($host, $username, $password, $dbname);
    // Karakter setini ayarla
    $db->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("<div style='font-family:Arial; padding:40px; text-align:center;'>
        <h2 style='color:#dc2626;'>⚠️ Veritabanı Bağlantı Hatası</h2>
        <p>MySQL sunucusu çalışmıyor olabilir. Lütfen XAMPP kontrol panelinden MySQL'i başlatın.</p>
        <p style='color:#64748b; font-size:0.85rem;'>Hata: " . htmlspecialchars($e->getMessage()) . "</p>
    </div>");
}
?>