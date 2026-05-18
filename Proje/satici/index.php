<?php
require_once '../config.php';

// Satıcı oturum kontrolü
if (!isset($_SESSION['satici']) && (!isset($_GET['sayfa']) || $_GET['sayfa'] !== 'giris')) {
    header('Location: index.php?sayfa=giris');
    exit;
}

$satici = $_SESSION['satici'] ?? null;
$sayfa = $_GET['sayfa'] ?? 'anasayfa';

// Giriş sayfası özel durum (Header/Sidebar istemiyoruz)
if ($sayfa === 'giris') {
    include 'satici_giris.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kral Yolu E-Ticaret - Satıcı Paneli</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Ortak Stiller -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <!-- Satıcı Özel Stiller -->
    <link rel="stylesheet" href="../assets/css/satici_panel.css">

    <style>
        /* Photo Sortable Grid Fixes for SortableJS */
        .photo-sortable-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; padding: 15px; border: 2px dashed #e2e8f0; border-radius: 12px; background: #f8fafc; }
        .photo-item { position: relative; border-radius: 10px; overflow: hidden; cursor: move; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .photo-item img { width: 100%; height: 120px; object-fit: cover; }
        .photo-item .remove-btn { position: absolute; top: 5px; right: 5px; background: rgba(239, 68, 68, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; }
    </style>
</head>
<body>

    <!-- SOL SİDEBAR (MENÜ) -->
    <aside class="seller-sidebar">
        <div class="seller-brand">
            <i class="bi bi-shop"></i>
            <div class="seller-brand-texts">
                <h4>KRAL YOLU</h4>
                <small>Satıcı Portalı</small>
            </div>
        </div>
        
        <ul class="seller-nav">
            <li>
                <a href="index.php?sayfa=anasayfa" class="<?= in_array($sayfa, ['anasayfa', 'satici_panel']) ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i> Kontrol Paneli</a>
            </li>
            
            <li class="nav-section-title">Ürün Yönetimi</li>
            <li>
                <a href="index.php?sayfa=urunler" class="<?= $sayfa === 'urunler' ? 'active' : '' ?>"><i class="bi bi-box-seam"></i> Mevcut Ürünlerim</a>
            </li>
            <li>
                <a href="index.php?sayfa=urun_ekle" class="<?= $sayfa === 'urun_ekle' ? 'active' : '' ?>"><i class="bi bi-plus-circle-fill"></i> Yeni Ürün Ekle</a>
            </li>
            
            <li class="nav-section-title">Satış & Operasyon</li>
            <li>
                <a href="index.php?sayfa=siparisler" class="<?= $sayfa === 'siparisler' ? 'active' : '' ?>">
                    <i class="bi bi-cart-check-fill"></i> Gelen Siparişler 
                    <?php 
                    $p_count_res = $db->query("SELECT COUNT(*) as c FROM t_siparis s JOIN t_siparis_detay sd ON s.id = sd.siparis_ID JOIN t_urun u ON sd.urun_ID = u.id WHERE u.satici_ID = {$satici['id']} AND s.kargo_durum = 'Hazırlanıyor'");
                    $p_count = $p_count_res->fetch_assoc()['c'] ?? 0;
                    if($p_count > 0) echo "<span class='badge bg-danger ms-auto rounded-pill'>$p_count</span>";
                    ?>
                </a>
            </li>
            <li>
                <a href="index.php?sayfa=raporlar" class="<?= $sayfa === 'raporlar' ? 'active' : '' ?>"><i class="bi bi-bar-chart-line-fill"></i> Satış Raporları</a>
            </li>
            
            <li class="nav-section-title">Mağaza Yönetimi</li>
            <li>
                <a href="index.php?sayfa=magaza_ayarlari" class="<?= $sayfa === 'magaza_ayarlari' ? 'active' : '' ?>"><i class="bi bi-gear-fill"></i> Mağaza Ayarları</a>
            </li>
            <li>
                <a href="satici_islem.php?islem=logout"><i class="bi bi-door-open text-danger"></i> Güvenli Çıkış</a>
            </li>
        </ul>
    </aside>

    <!-- SAĞ ANA İÇERİK -->
    <main class="seller-main">
        
        <!-- Üst Başlık ve Kullanıcı -->
        <header class="seller-header">
            <div>
                <h2 class="m-0 fw-bold" style="color:var(--navy);">Hoş Geldiniz, <?= htmlspecialchars($satici['kurum_adi']) ?></h2>
                <span class="text-muted" style="font-size:0.9rem;">Satışlarınızı ve mağazanızı buradan yönetebilirsiniz.</span>
            </div>
            <div class="seller-profile-box">
                <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm fw-bold"><i class="bi bi-eye"></i> Mağazamı Gör</a>
                <div class="seller-avatar"><?= mb_substr($satici['kurum_adi'], 0, 2) ?></div>
            </div>
        </header>

        <div class="content-body">
            <?php 
            switch ($sayfa) {
                case 'anasayfa': 
                case 'satici_panel': // Eski isim uyumu
                    include 'anasayfa.php'; break;
                case 'urunler': 
                case 'mevcut_urunler': // Eski isim uyumu
                    include 'urunler.php'; break;
                case 'urun_ekle': 
                case 'yeni_urun_ekle': // Eski isim uyumu
                    include 'urun_ekle.php'; break;
                case 'urun_duzenle': include 'urun_duzenle.php'; break;
                case 'siparisler': include 'siparisler.php'; break;
                case 'raporlar': include 'raporlar.php'; break;
                case 'magaza_ayarlari': include 'magaza_ayarlari.php'; break;
                default: include 'anasayfa.php'; break;
            }
            ?>
        </div>
    </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});
</script>

</body>
</html>
