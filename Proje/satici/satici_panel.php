<?php
// satici_panel.php
if (!$isSellerLoggedIn) {
    header('Location: index.php?sayfa=satici_giris');
    exit;
}

// İstatistikleri çek (Gerçek verilerle)
$satici_id = $seller['id'];

// Aktif Ürün Sayısı
$aktif_urun_res = $db->query("SELECT COUNT(*) as total FROM t_urun WHERE satici_ID = $satici_id AND aktif = 'Evet'");
$aktif_urun_sayisi = $aktif_urun_res->fetch_assoc()['total'];

// Bekleyen Sipariş Sayısı
// Not: Siparişlerin hangi satıcıya ait olduğunu anlamak için t_siparis_detay üzerinden gitmeliyiz.
$bekleyen_siparis_res = $db->query("SELECT COUNT(DISTINCT sd.siparis_ID) as total 
                                   FROM t_siparis_detay sd 
                                   JOIN t_urun u ON sd.urun_ID = u.id 
                                   JOIN t_siparis s ON sd.siparis_ID = s.id
                                   WHERE u.satici_ID = $satici_id AND s.kargo_durum = 'Hazırlanıyor'");
$bekleyen_siparis_sayisi = $bekleyen_siparis_res->fetch_assoc()['total'];

// Aylık Ciro (Basitçe son 30 gün)
$ciro_res = $db->query("SELECT SUM(sd.adet * sd.birim_fiyat) as total 
                        FROM t_siparis_detay sd 
                        JOIN t_urun u ON sd.urun_ID = u.id 
                        JOIN t_siparis s ON sd.siparis_ID = s.id
                        WHERE u.satici_ID = $satici_id AND s.siparis_tarih >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$aylik_ciro = $ciro_res->fetch_assoc()['total'] ?? 0;

// Son Siparişler
$son_siparisler = $db->query("SELECT s.id as siparis_no, u.urun_adi, uye.ad, uye.soyad, sd.adet, sd.birim_fiyat, s.kargo_durum
                              FROM t_siparis_detay sd
                              JOIN t_urun u ON sd.urun_ID = u.id
                              JOIN t_siparis s ON sd.siparis_ID = s.id
                              JOIN t_uye uye ON s.uye_ID = uye.id
                              WHERE u.satici_ID = $satici_id
                              ORDER BY s.siparis_tarih DESC LIMIT 5");
?>

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
            <a href="index.php?sayfa=satici_panel" class="active"><i class="bi bi-grid-1x2-fill"></i> Kontrol Paneli</a>
        </li>
        
        <li class="nav-section-title">Ürün Yönetimi</li>
        <li>
            <a href="index.php?sayfa=mevcut_urunler"><i class="bi bi-box-seam"></i> Mevcut Ürünlerim</a>
        </li>
        <li>
            <a href="index.php?sayfa=yeni_urun_ekle"><i class="bi bi-plus-circle-fill"></i> Yeni Ürün Ekle</a>
        </li>
        
        <li class="nav-section-title">Satış & Operasyon</li>
        <li>
            <a href="index.php?sayfa=siparisler"><i class="bi bi-cart-check-fill"></i> Gelen Siparişler <span class="badge bg-danger ms-auto rounded-pill"><?= $bekleyen_siparis_sayisi ?></span></a>
        </li>
        <li>
            <a href="index.php?sayfa=raporlar"><i class="bi bi-bar-chart-line-fill"></i> Satış Raporları</a>
        </li>
        
        <li class="nav-section-title">Mağaza Yönetimi</li>
        <li>
            <a href="index.php?sayfa=magaza_ayarlari"><i class="bi bi-gear-fill"></i> Mağaza Ayarları</a>
        </li>
        <li>
            <a href="data.php?logout=1"><i class="bi bi-door-open text-danger"></i> Güvenli Çıkış</a>
        </li>
    </ul>
</aside>

<!-- SAĞ ANA İÇERİK -->
<main class="seller-main">
    
    <!-- Üst Başlık ve Kullanıcı -->
    <header class="seller-header">
        <div>
            <h2 class="m-0 fw-bold" style="color:var(--navy);">Hoş Geldiniz, <?= htmlspecialchars($seller['kurum_adi']) ?></h2>
            <span class="text-muted" style="font-size:0.9rem;">Satışlarınızı ve mağazalarınızı buradan yönetebilirsiniz.</span>
        </div>
        <div class="seller-profile-box">
            <a href="index.php" class="btn btn-outline-secondary btn-sm fw-bold"><i class="bi bi-eye"></i> Mağazamı Gör</a>
            <div class="seller-avatar"><?= mb_substr($seller['kurum_adi'], 0, 2, 'UTF-8') ?></div>
        </div>
    </header>

    <!-- Özet İstatistik Kartları -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-title">Aylık Toplam Satış</span>
                <span class="stat-value"><?= $son_siparisler->num_rows ?> Adet</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card blue-border">
                <span class="stat-title">Aylık Ciro</span>
                <span class="stat-value"><?= number_format($aylik_ciro, 0, ',', '.') ?> ₺</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card red-border">
                <span class="stat-title">Bekleyen Sipariş</span>
                <span class="stat-value"><?= $bekleyen_siparis_sayisi ?> Yeni</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card green-border">
                <span class="stat-title">Aktif Ürün Sayısı</span>
                <span class="stat-value"><?= $aktif_urun_sayisi ?> Ürün</span>
            </div>
        </div>
    </div>

    <!-- Son Siparişler Tablosu -->
    <div class="bg-white p-4 rounded-3 shadow-sm border border-light">
        <h4 class="fw-bold mb-4" style="color:var(--navy);">Son Siparişleriniz</h4>
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Sipariş No</th>
                        <th>Ürün Adı</th>
                        <th>Müşteri</th>
                        <th>Adet</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($son_siparisler->num_rows > 0): ?>
                        <?php while($s = $son_siparisler->fetch_assoc()): ?>
                        <tr>
                            <td>#KRY-<?= $s['siparis_no'] ?></td>
                            <td><?= htmlspecialchars($s['urun_adi']) ?></td>
                            <td><?= htmlspecialchars($s['ad'] . ' ' . $s['soyad']) ?></td>
                            <td><?= $s['adet'] ?></td>
                            <td><?= number_format($s['adet'] * $s['birim_fiyat'], 0, ',', '.') ?> ₺</td>
                            <td><span class="badge bg-info px-2 py-1"><?= $s['kargo_durum'] ?></span></td>
                            <td>
                                <a href="index.php?sayfa=siparisler" class="btn-sm-action">Detay</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Henüz sipariş bulunmamaktadır.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>
