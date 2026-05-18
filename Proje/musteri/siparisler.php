<?php
// siparisler.php
if (!$isSellerLoggedIn) {
    header('Location: index.php?sayfa=satici_giris');
    exit;
}

$satici_id = $seller['id'];

// Siparişleri çek
$siparisler = $db->query("SELECT s.id as siparis_no, s.siparis_tarih, u.urun_adi, u.id as urun_id, f.foto_url, uye.ad, uye.soyad, sd.adet, sd.birim_fiyat, s.kargo_durum
                          FROM t_siparis_detay sd
                          JOIN t_urun u ON sd.urun_ID = u.id
                          JOIN t_siparis s ON sd.siparis_ID = s.id
                          JOIN t_uye uye ON s.uye_ID = uye.id
                          LEFT JOIN t_foto f ON u.id = f.urun_ID AND f.foto_sira = 1
                          WHERE u.satici_ID = $satici_id
                          ORDER BY s.siparis_tarih DESC");

$bekleyen_sayisi = 0;
// Bekleyen sayısını tekrar hesaplayalım (veya session'dan alabiliriz)
$bekleyen_res = $db->query("SELECT COUNT(DISTINCT sd.siparis_ID) as total 
                            FROM t_siparis_detay sd 
                            JOIN t_urun u ON sd.urun_ID = u.id 
                            JOIN t_siparis s ON sd.siparis_ID = s.id
                            WHERE u.satici_ID = $satici_id AND s.kargo_durum = 'Hazırlanıyor'");
$bekleyen_sayisi = $bekleyen_res->fetch_assoc()['total'];
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
            <a href="index.php?sayfa=satici_panel"><i class="bi bi-grid-1x2-fill"></i> Kontrol Paneli</a>
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
            <a href="index.php?sayfa=siparisler" class="active"><i class="bi bi-cart-check-fill"></i> Gelen Siparişler <span class="badge bg-danger ms-auto rounded-pill"><?= $bekleyen_sayisi ?></span></a>
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
    
    <!-- Üst Başlık -->
    <header class="seller-header">
        <div>
            <h2 class="m-0 fw-bold" style="color:var(--navy);">Gelen Siparişler</h2>
            <span class="text-muted" style="font-size:0.9rem;">Müşterilerinizin sizden sipariş ettiği ürünleri izleyin ve teslimat süreçlerini yönetin.</span>
        </div>
    </header>

    <!-- Filtre ve Arama Alanı -->
    <div class="bg-white p-3 rounded-3 shadow-sm border border-light mb-4 d-flex flex-wrap gap-3 align-items-center">
        <div class="flex-grow-1">
            <input type="text" class="form-control" placeholder="Sipariş No, Ürün Adı veya Müşteri Adı Ara...">
        </div>
        <select class="form-select flex-shrink-0" style="width: auto;">
            <option value="">Tüm Durumlar</option>
            <option value="Hazırlanıyor">Yeni / Onay Bekleyen</option>
            <option value="Kargolandı">Kargolananlar</option>
            <option value="Teslim Edildi">Teslim Edilenler</option>
        </select>
    </div>

    <!-- Siparişler Tablosu -->
    <div class="bg-white p-4 rounded-3 shadow-sm border border-light">
        <div class="table-responsive">
            <table class="dashboard-table table-hover">
                <thead>
                    <tr>
                        <th>Sip. No / Tarih</th>
                        <th>Ürün Bilgisi</th>
                        <th>Müşteri</th>
                        <th>Adet/Fiyat</th>
                        <th>Durum</th>
                        <th class="text-end">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($siparisler->num_rows > 0): ?>
                        <?php while($s = $siparisler->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="fw-bold fs-6">#KRY-<?= $s['siparis_no'] ?></div>
                                <div class="text-muted" style="font-size:0.8rem;"><?= date('d.m.Y H:i', strtotime($s['siparis_tarih'])) ?></div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= $s['foto_url'] ?? 'https://via.placeholder.com/50' ?>" alt="Ürün" class="product-thumbnail" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <div>
                                        <div class="fw-bold" style="color:var(--navy); font-size:0.9rem;"><?= htmlspecialchars($s['urun_adi']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($s['ad'] . ' ' . $s['soyad']) ?></td>
                            <td>
                                <div class="fw-bold"><?= $s['adet'] ?> x <?= number_format($s['birim_fiyat'], 0, ',', '.') ?> ₺</div>
                                <span class="text-success fw-bold" style="font-size:0.85rem;">Ödendi</span>
                            </td>
                            <td>
                                <?php 
                                $badge_class = 'bg-info';
                                if ($s['kargo_durum'] == 'Hazırlanıyor') $badge_class = 'bg-warning text-dark';
                                elseif ($s['kargo_durum'] == 'Teslim Edildi') $badge_class = 'bg-success';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= $s['kargo_durum'] ?></span>
                            </td>
                            <td class="text-end">
                                <?php if ($s['kargo_durum'] == 'Hazırlanıyor'): ?>
                                    <button class="btn btn-sm btn-success fw-bold px-3 shadow-sm">
                                        <i class="bi bi-check2-circle"></i> Onayla & Kargola
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary fw-bold px-3 shadow-sm">
                                        Detay
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Sipariş bulunamadı.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
