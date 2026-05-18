<?php
if (!$isLoggedIn) {
    echo "<script>window.location.href='index.php?sayfa=giris';</script>";
    exit;
}

$uye_id = $user['id'];

// Sepet ürünlerini çek
$query = "SELECT s.*, u.urun_adi, u.fiyat, u.stok, f.foto_url, sat.kurum_adi as satici_adi
          FROM t_sepet s
          JOIN t_urun u ON s.urun_ID = u.id
          LEFT JOIN t_foto f ON u.id = f.urun_ID AND f.foto_sira = 1
          LEFT JOIN t_satici sat ON u.satici_ID = sat.id
          WHERE s.uye_ID = $uye_id";
$sepet_res = $db->query($query);

$urunler = [];
$ara_toplam = 0;
$toplam_adet = 0;

while ($row = $sepet_res->fetch_assoc()) {
    $row['toplam_fiyat'] = $row['fiyat'] * $row['adet'];
    $ara_toplam += $row['toplam_fiyat'];
    $toplam_adet += $row['adet'];
    $urunler[] = $row;
}

$kargo_ucreti = ($ara_toplam > 1000 || $toplam_adet == 0) ? 0 : 50.00;
$genel_toplam = $ara_toplam + $kargo_ucreti;
?>

<section class="ky-container my-5" style="min-height: 55vh;">
    
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background:transparent; padding:0;">
            <li class="breadcrumb-item"><a href="index.php?sayfa=anasayfa" class="text-decoration-none text-muted"><i class="bi bi-house-door-fill me-1"></i>Anasayfa</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color:var(--gold); font-weight:700;">Sepetim</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center gap-3 mb-4 mt-2">
        <h1 class="fw-bold mb-0" style="color: var(--navy);">Sepetim</h1>
        <span class="badge rounded-pill bg-light text-navy border px-3 py-2" style="font-size: 0.9rem; color:var(--navy)"><?= $toplam_adet ?> Ürün</span>
    </div>

    <?php if (isset($_GET['hata']) && $_GET['hata'] === 'stok'): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> Üzgünüz, seçtiğiniz ürün için yeterli stok bulunmamaktadır.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (count($urunler) > 0): ?>
        <div class="row g-4">
            
            <!-- SOL TARAFI: SEPETTEKİ ÜRÜNLER LİSTESİ -->
            <div class="col-lg-8">
                <?php foreach ($urunler as $item): ?>
                    <div class="cart-item-card">
                        <div class="row align-items-center">
                            <div class="col-4 col-md-2 text-center text-md-start">
                                <a href="index.php?sayfa=urundetay&id=<?= $item['urun_ID'] ?>">
                                    <img src="<?= $item['foto_url'] ?? 'https://via.placeholder.com/300' ?>" class="cart-item-img" alt="Ürün">
                                </a>
                            </div>
                            <div class="col-8 col-md-5">
                                <span class="cart-item-brand"><?= htmlspecialchars($item['satici_adi'] ?? 'Kral Yolu') ?></span>
                                <a href="index.php?sayfa=urundetay&id=<?= $item['urun_ID'] ?>" class="cart-item-title"><?= htmlspecialchars($item['urun_adi']) ?></a>
                                <?php if (!empty($item['secenek_detay'])): ?>
                                    <div class="cart-variant-text mt-2">Seçenekler: <strong><?= str_replace('|', ', ', $item['secenek_detay']) ?></strong></div>
                                <?php endif; ?>
                                <div class="text-success mt-2" style="font-size:0.85rem; font-weight:600;"><i class="bi bi-truck"></i> Stokta Var</div>
                            </div>
                            <div class="col-6 col-md-2 mt-4 mt-md-0 d-flex justify-content-start justify-content-md-center">
                                <div class="cart-qty-wrapper">
                                    <a href="data.php?islem=sepet_guncelle&id=<?= $item['id'] ?>&adet=<?= $item['adet'] - 1 ?>" class="cart-qty-btn text-decoration-none">-</a>
                                    <input type="text" class="cart-qty-input" value="<?= $item['adet'] ?>" readonly>
                                    <a href="data.php?islem=sepet_guncelle&id=<?= $item['id'] ?>&adet=<?= $item['adet'] + 1 ?>" class="cart-qty-btn text-decoration-none">+</a>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mt-4 mt-md-0 d-flex justify-content-end align-items-center gap-3">
                                <div class="cart-item-price"><?= number_format($item['toplam_fiyat'], 2, ',', '.') ?> TL</div>
                                <a href="javascript:void(0)" onclick="confirmRemove('data.php?islem=sepet_sil&id=<?= $item['id'] ?>', 'Bu ürünü sepetten çıkarmak istediğinize emin misiniz?')" class="cart-btn-remove" title="Ürünü Sil"><i class="bi bi-trash3"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mt-4 text-end">
                    <a href="javascript:void(0)" onclick="confirmRemove('data.php?islem=sepet_temizle', 'Tüm sepeti boşaltmak istediğinize emin misiniz?')" class="btn btn-outline-danger btn-sm fw-bold"><i class="bi bi-trash"></i> Sepeti Temizle</a>
                </div>
            </div>

            <!-- SAĞ TARAFI: SİPARİŞ ÖZETİ -->
            <div class="col-lg-4">
                <div class="summary-card shadow-sm border-0 bg-white">
                    <h2 class="summary-title border-bottom pb-3 mb-4"><i class="bi bi-receipt me-2"></i>Sipariş Özeti</h2>
                    
                    <div class="summary-row">
                        <span class="text-muted">Ürün Toplamı</span>
                        <span class="fw-bold text-dark"><?= number_format($ara_toplam, 2, ',', '.') ?> TL</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="text-muted">Kargo Ücreti</span>
                        <span class="fw-bold <?= $kargo_ucreti == 0 ? 'text-success' : 'text-dark' ?>">
                            <?= $kargo_ucreti == 0 ? 'Bedava' : number_format($kargo_ucreti, 2, ',', '.') . ' TL' ?>
                        </span>
                    </div>

                    <?php if ($ara_toplam > 1000): ?>
                        <div class="summary-row text-success fw-bold">
                            <span class="small"><i class="bi bi-check2-circle me-1"></i>1000 TL Üzeri Ücretsiz Kargo</span>
                            <span>Uygulandı</span>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info py-2 px-3 mt-3 border-0" style="font-size: 0.8rem; background-color: rgba(212,175,55,0.08); color: #856404; border-left: 3px solid var(--gold) !important;">
                            <i class="bi bi-info-circle-fill me-1"></i> <strong><?= number_format(1000 - $ara_toplam, 2, ',', '.') ?> TL</strong> daha ürün ekleyin, kargo bedava olsun!
                        </div>
                    <?php endif; ?>

                    <div class="summary-row total-row border-top-0 pt-4 mt-4" style="border-top: 1px solid #eee !important;">
                        <span class="fs-5">Ödenecek Tutar</span>
                        <span class="fs-4" style="color: var(--gold);"><?= number_format($genel_toplam, 2, ',', '.') ?> TL</span>
                    </div>

                    <a href="index.php?sayfa=odeme" class="btn-checkout text-decoration-none mt-4 py-3 shadow-sm">
                        Alışverişi Tamamla <i class="bi bi-arrow-right-short fs-4"></i>
                    </a>
                    
                    <div class="mt-3 text-center">
                        <img src="https://img.icons8.com/color/48/000000/visa.png" width="30" alt="visa">
                        <img src="https://img.icons8.com/color/48/000000/mastercard.png" width="30" alt="mastercard">
                        <img src="https://img.icons8.com/color/48/000000/troy.png" width="30" alt="troy">
                        <p class="text-muted small mt-2 mb-0"><i class="bi bi-shield-lock-fill me-1"></i>Güvenli Ödeme Altyapısı</p>
                    </div>
                </div>
            </div>

        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-cart-x text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
            </div>
            <h2 class="fw-bold text-muted">Sepetiniz Boş</h2>
            <p class="text-secondary">Sepetinizde henüz ürün bulunmuyor. Hemen alışverişe başlayıp harika fırsatları yakalayın!</p>
            <a href="index.php?sayfa=anasayfa" class="btn btn-warning btn-lg fw-bold px-5 mt-3" style="background-color:var(--gold); border:none; color:var(--navy);">Alışverişe Başla</a>
        </div>
    <?php endif; ?>
</section>

<script>
function confirmRemove(url, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Emin misiniz?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<i class="bi bi-trash3"></i> Evet, Sil!',
            cancelButtonText: 'İptal',
            background: '#ffffff',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    } else {
        // SweetAlert yüklenemezse native fallback
        if (confirm(message)) {
            window.location.href = url;
        }
    }
}
</script>
