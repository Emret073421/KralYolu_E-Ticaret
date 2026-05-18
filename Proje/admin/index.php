<?php
session_start();
require_once '../config.php';

// Yetki kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_giris.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$admin_ad = $_SESSION['admin_ad'] . ' ' . $_SESSION['admin_soyad'];
$admin_rol = $_SESSION['admin_rol'];

$sayfa = isset($_GET['sayfa']) ? $_GET['sayfa'] : 'anasayfa';

// Menü aktiflik kontrolü için yardımcı fonksiyon
function activeMenu($menuName, $currentMenu) {
    return $menuName == $currentMenu ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kral Yolu - Yönetim Merkezi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Ortak Stiller -->
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?=time()?>">
    <!-- Admin Özel Stiller -->
    <link rel="stylesheet" href="../assets/css/admin_panel.css?v=<?=time()?>">
</head>
<body>

    <!-- SOL SİDEBAR (MENÜ) -->
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <i class="bi bi-shield-lock-fill"></i>
            <div class="admin-brand-texts">
                <h4>KRAL YOLU</h4>
                <small>YÖNETİM PANELİ</small>
            </div>
        </div>
        
        <ul class="admin-nav">
            <li>
                <a href="index.php?sayfa=anasayfa" class="<?= activeMenu('anasayfa', $sayfa) ?>"><i class="bi bi-speedometer2"></i> Sistem Özeti</a>
            </li>
            
            <li class="nav-section-title">E-Ticaret Yönetimi</li>
            <li>
                <a href="index.php?sayfa=urun_onay" class="<?= activeMenu('urun_onay', $sayfa) ?>"><i class="bi bi-bag-check-fill"></i> Ürün Onayları</a>
            </li>
            <li>
                <a href="index.php?sayfa=kategoriler" class="<?= activeMenu('kategoriler', $sayfa) ?>"><i class="bi bi-tags-fill"></i> Kategori İşlemleri</a>
            </li>
            
            <li class="nav-section-title">Kullanıcı & Ağ</li>
            <li>
                <a href="index.php?sayfa=satici_onay" class="<?= activeMenu('satici_onay', $sayfa) ?>"><i class="bi bi-shop-window"></i> Satıcı Onayları</a>
            </li>
            <li>
                <a href="index.php?sayfa=personel" class="<?= activeMenu('personel', $sayfa) ?>"><i class="bi bi-person-badge-fill"></i> Personel Yönetimi</a>
            </li>
            
            <li class="nav-section-title">Sistem & Analiz</li>
            <li>
                <a href="admin_islem.php?islem=cikis"><i class="bi bi-door-open text-danger"></i> Güvenli Çıkış</a>
            </li>
        </ul>
    </aside>

    <!-- SAĞ ANA İÇERİK -->
    <main class="admin-main">
        
        <!-- Üst Başlık ve Admin Profili -->
        <header class="admin-header">
            <div>
                <h2 class="m-0 fw-bold" style="color:#0f172a;">Yönetim Merkezi</h2>
                <span class="text-muted" style="font-size:0.9rem;">Sistem istatistikleri, mağaza onayları ve yetkilendirmeler tek bir yerde.</span>
            </div>
            <div class="admin-profile-box">
                <span class="fw-bold" style="color:#0f172a;"><?= htmlspecialchars($admin_ad) ?> <small class="text-muted d-block" style="font-size:0.75rem;"><?= htmlspecialchars($admin_rol) ?></small></span>
                <div class="admin-avatar"><i class="bi bi-person-fill"></i></div>
            </div>
        </header>

        <?php
        // İlgili sayfayı dahil et
        $include_file = $sayfa . '.php';
        if (file_exists($include_file)) {
            include($include_file);
        } else {
            echo "<div class='alert alert-warning'>Sayfa bulunamadı: " . htmlspecialchars($sayfa) . "</div>";
        }
        ?>
        
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
</body>
</html>
