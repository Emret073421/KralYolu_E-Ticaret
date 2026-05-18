<?php
/* ==========================================================================
   KRAL YOLU E-TİCARET - ANA GİRİŞ DOSYASI (INDEX.PHP)
   ========================================================================== */

// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Oturum yönetimi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Veri ve veritabanı fonksiyonlarını dahil et
require_once 'data.php';

// Kullanıcı oturum kontrolü
$user = $_SESSION['kullanici'] ?? null;
$isLoggedIn = is_array($user) && !empty($user['ad']);

// Kullanıcının favori ürünlerini çek (kalp ikonlarının dolu/boş görünmesi için)
$user_favorites = [];
if ($isLoggedIn) {
    $uye_id = $user['id'];
    // Güvenli sorgu - t_favori tablosu yoksa hata vermez
    $fav_res = @$db->query("SELECT urun_id FROM t_favori WHERE uye_id = $uye_id");
    if ($fav_res) {
        while($f = $fav_res->fetch_assoc()) $user_favorites[] = $f['urun_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kral Yolu E-Ticaret - Güvenli ve hızlı alışverişin adresi. Elektronik, giyim, kozmetik ve daha fazlası.">
    <meta name="keywords" content="e-ticaret, alışveriş, kral yolu, elektronik, giyim, kozmetik">
    <meta name="author" content="Kral Yolu">

    <!-- CSS Dosyaları -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/anasayfa.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/urunler.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/urundetay.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/sepet.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/odeme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/profil.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/giris.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/satici_panel.css?v=<?= time() ?>">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Kral Yolu | Güvenli Alışverişin Adresi</title>
</head>
<body>
    <?php 
    /* ----------------------------------------------------------------------
       NAVBAR (ÜST MENÜ)
       ---------------------------------------------------------------------- */
    $sayfa = $_GET['sayfa'] ?? 'anasayfa';
    $seller_pages = ['satici_panel', 'mevcut_urunler', 'yeni_urun_ekle', 'siparisler', 'raporlar', 'magaza_ayarlari', 'satici_giris'];
    
    // Satıcı sayfalarında standart navbar gösterilmez
    if (!in_array($sayfa, $seller_pages)) {
        include 'musteri/navbar.php'; 
    }
    ?>
    
    <main>
        <?php 
        /* ----------------------------------------------------------------------
           SAYFA YÖNLENDİRME (ROUTING)
           ---------------------------------------------------------------------- */
        switch ($sayfa) {
            case 'anasayfa': 
                include 'musteri/anasayfa.php'; 
                break;
            case 'urunler': 
                include 'musteri/urunler.php'; 
                break;
            case 'urundetay': 
                include 'musteri/urundetay.php'; 
                break;
            case 'sepet': 
                include 'musteri/sepet.php'; 
                break;
            case 'odeme': 
                include 'musteri/odeme.php'; 
                break;
            case 'profil': 
                include 'musteri/profil.php'; 
                break;
            case 'favoriler': 
                include 'musteri/favoriler.php'; 
                break;
            case 'giris': 
                include 'musteri/giris_kayit.php'; 
                break;
            
            // Satıcı Sayfaları - Satıcı paneli artık satici/index.php üzerinden çalışıyor
            case 'satici_giris':
                // Satıcı giriş sayfasına yönlendir
                header('Location: satici/index.php?sayfa=giris');
                exit;
            case 'satici_panel':
            case 'mevcut_urunler':
            case 'yeni_urun_ekle':
            case 'siparisler':
            case 'raporlar':
            case 'magaza_ayarlari':
                // Satıcı paneline yönlendir
                header('Location: satici/index.php?sayfa=' . $sayfa);
                exit;

            default: 
                include 'musteri/anasayfa.php'; 
                break;
        }
        ?>
    </main>

    <?php 
    /* ----------------------------------------------------------------------
       FOOTER (ALT BİLGİ)
       ---------------------------------------------------------------------- */
    if (!in_array($sayfa, $seller_pages)) {
        include 'musteri/footer.php'; 
    }
    ?>

    
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="assets/js/script.js"></script>
</body>
</html>