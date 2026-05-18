<?php
// İstatistikleri Çek
$yil = date('Y');

// 1. Toplam Satış Adeti
$satis_res = $db->query("SELECT SUM(adet) as total FROM t_siparis_detay sd JOIN t_siparis s ON sd.siparis_ID = s.id WHERE YEAR(s.siparis_tarih) = $yil AND s.kargo_durum != 'İptal Edildi'");
$toplam_satis = ($satis_res && $s_row = $satis_res->fetch_assoc()) ? $s_row['total'] : 0;
if(!$toplam_satis) $toplam_satis = 0;

// 2. Toplam Müşteri - FIX: t_uye'de 'rol' sütunu yok, tüm üyeler müşteridir
$musteri_res = $db->query("SELECT COUNT(*) as total FROM t_uye WHERE YEAR(kayit_tarihi) = $yil");
$toplam_musteri = ($musteri_res && $m_row = $musteri_res->fetch_assoc()) ? $m_row['total'] : 0;

// 3. Onaylanmış Mağaza
$magaza_res = $db->query("SELECT COUNT(*) as total FROM t_satici WHERE onay_durumu = 'Onaylandı'");
$aktif_magaza = ($magaza_res && $mg_row = $magaza_res->fetch_assoc()) ? $mg_row['total'] : 0;

// 4. Aktif Ürün
$urun_res = $db->query("SELECT COUNT(*) as total FROM t_urun WHERE onay_durumu = 'Onaylandı'");
$aktif_urun = ($urun_res && $u_row = $urun_res->fetch_assoc()) ? $u_row['total'] : 0;

// Son 5 Mağaza Başvurusu
$bekleyen_magazalar = $db->query("SELECT * FROM t_satici WHERE onay_durumu = 'Onay Bekliyor' ORDER BY id DESC LIMIT 5");

// Son 5 Ürün Onay Bekleme
$bekleyen_urunler = $db->query("SELECT u.*, s.kurum_adi, k.kategori_adi 
                                FROM t_urun u 
                                JOIN t_satici s ON u.satici_ID = s.id 
                                LEFT JOIN t_urun_kategori uk ON u.id = uk.urun_ID
                                LEFT JOIN t_kategori k ON uk.kategori_ID = k.id
                                WHERE u.onay_durumu = 'Onay Bekliyor' 
                                GROUP BY u.id
                                ORDER BY u.id DESC LIMIT 5");
?>

<div class="row g-4 mb-4">
    <!-- Özet İstatistik Kartları -->
    <div class="col-xl-3 col-md-6">
        <div class="admin-stat-card" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: white; border: none;">
            <div class="stat-info">
                <span class="stat-title opacity-75 text-white">Yıllık Satış</span>
                <h3 class="stat-value m-0 fw-bold"><?= number_format($toplam_satis) ?> Adet</h3>
            </div>
            <div class="stat-icon"><i class="bi bi-cart-check"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;">
            <div class="stat-info">
                <span class="stat-title opacity-75 text-white">Yeni Müşteriler</span>
                <h3 class="stat-value m-0 fw-bold">+<?= number_format($toplam_musteri) ?></h3>
            </div>
            <div class="stat-icon"><i class="bi bi-person-plus"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none;">
            <div class="stat-info">
                <span class="stat-title opacity-75 text-white">Aktif Mağazalar</span>
                <h3 class="stat-value m-0 fw-bold"><?= $aktif_magaza ?> Mağaza</h3>
            </div>
            <div class="stat-icon"><i class="bi bi-shop"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-stat-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none;">
            <div class="stat-info">
                <span class="stat-title opacity-75 text-white">Onaylı Ürünler</span>
                <h3 class="stat-value m-0 fw-bold"><?= number_format($aktif_urun) ?> Ürün</h3>
            </div>
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Bekleyen Mağaza Başvuruları Listesi -->
    <div class="col-lg-8">
        <div class="bg-white p-4 rounded-4 shadow-sm border border-light h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="color:#0f172a;"><i class="bi bi-shop text-warning"></i> Son Mağaza Başvuruları</h5>
                <a href="index.php?sayfa=satici_onay" class="btn btn-sm btn-outline-secondary">Tümünü Yönet</a>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>Mağaza Adı</th>
                            <th>Yetkili</th>
                            <th>Tarih</th>
                            <th class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($bekleyen_magazalar && $bekleyen_magazalar->num_rows > 0): ?>
                            <?php while($m = $bekleyen_magazalar->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($m['kurum_adi']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($m['sirket_turu'] ?? '') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($m['ad'] . ' ' . $m['soyad']) ?></td>
                                    <td><small><?= date('d.m.Y', strtotime($m['kayit_tarihi'])) ?></small></td>
                                    <td class="text-end">
                                        <a href="index.php?sayfa=satici_onay_detay&id=<?= $m['id'] ?>" class="btn btn-sm btn-primary px-3">İncele</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Bekleyen başvuru bulunmuyor.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Hızlı Aksiyonlar -->
    <div class="col-lg-4">
        <div class="bg-white p-4 rounded-4 shadow-sm border border-light h-100">
            <h5 class="fw-bold mb-4" style="color:#0f172a;">Hızlı İşlemler</h5>
            <div class="d-grid gap-3">
                <a href="index.php?sayfa=satici_onay" class="btn btn-light text-start p-3 border-0 rounded-3 position-relative">
                    <i class="bi bi-shop text-warning me-2"></i> Satıcı Başvuruları
                    <span class="badge bg-danger rounded-pill position-absolute top-50 end-0 translate-middle-y me-3"><?= $bekleyen_magazalar->num_rows ?></span>
                </a>
                <a href="index.php?sayfa=urun_onay" class="btn btn-light text-start p-3 border-0 rounded-3 position-relative">
                    <i class="bi bi-bag-check text-primary me-2"></i> Ürün Onayları
                    <span class="badge bg-danger rounded-pill position-absolute top-50 end-0 translate-middle-y me-3"><?= $bekleyen_urunler->num_rows ?></span>
                </a>
                <a href="index.php?sayfa=kategoriler" class="btn btn-light text-start p-3 border-0 rounded-3">
                    <i class="bi bi-tags text-success me-2"></i> Kategori Yönetimi
                </a>
                <a href="index.php?sayfa=personel" class="btn btn-light text-start p-3 border-0 rounded-3">
                    <i class="bi bi-person-badge text-info me-2"></i> Personel Yetkileri
                </a>
            </div>
            
            <hr class="my-4">
            
            <div class="alert alert-info border-0 rounded-4 m-0">
                <h6 class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-2"></i> Sistem Durumu</h6>
                <p class="small m-0">Şu an <?= $yil ?> yılı aktif yönetim panelindesiniz. Tüm işlemler loglanmaktadır.</p>
            </div>
        </div>
    </div>
</div>
