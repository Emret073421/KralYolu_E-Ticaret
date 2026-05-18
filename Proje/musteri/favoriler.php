<?php
if (!$isLoggedIn) {
    echo "<script>window.location.href='index.php?sayfa=giris';</script>";
    exit;
}

$uye_id = $user['id'];

// Favori ürünleri çek
$query = "SELECT u.*, f.foto_url, s.kurum_adi as satici_adi
          FROM t_favori fav
          JOIN t_urun u ON fav.urun_id = u.id
          LEFT JOIN t_foto f ON u.id = f.urun_ID AND f.foto_sira = 1
          LEFT JOIN t_satici s ON u.satici_ID = s.id
          WHERE fav.uye_id = $uye_id";
$favori_res = $db->query($query);
?>

<section class="ky-container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0" style="color: var(--navy);">Favorilerim</h2>
            <p class="text-muted small mb-0">Beğendiğiniz tüm ürünler burada.</p>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2"><?= $favori_res->num_rows ?> Ürün</span>
    </div>

    <?php if ($favori_res && $favori_res->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php while ($urun = $favori_res->fetch_assoc()): ?>
                <div class="col" id="fav-item-<?= $urun['id'] ?>">
                    <div class="product-card h-100 bg-white border-light shadow-sm">
                        <!-- Favoriden Çıkar Butonu -->
                        <button onclick="toggleFavorite(<?= $urun['id'] ?>)" class="product-fav-btn active" title="Favorilerden Çıkar">
                            <i class="bi bi-heart-fill text-danger"></i>
                        </button>
                        
                        <div class="product-img-wrap">
                            <img src="<?= $urun['foto_url'] ?? 'https://via.placeholder.com/300x400' ?>" alt="<?= htmlspecialchars($urun['urun_adi']) ?>">
                        </div>
                        
                        <div class="product-info">
                            <span class="product-brand"><?= htmlspecialchars($urun['satici_adi'] ?? 'Kral Yolu') ?></span>
                            <a href="index.php?sayfa=urundetay&id=<?= $urun['id'] ?>" class="product-title"><?= htmlspecialchars($urun['urun_adi']) ?></a>
                            
                            <div class="product-price-row mt-auto">
                                <div class="price-box">
                                    <span class="current-price"><?= number_format($urun['fiyat'], 2, ',', '.') ?> TL</span>
                                </div>
                                <a href="index.php?sayfa=urundetay&id=<?= $urun['id'] ?>" class="btn btn-sm btn-navy px-3" style="border-radius: 8px;">İncele</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-heart text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <h4 class="text-muted">Henüz favori ürününüz bulunmuyor.</h4>
            <p class="text-secondary">Beğendiğiniz ürünleri favorilerinize ekleyerek burada listeleyebilirsiniz.</p>
            <a href="index.php?sayfa=anasayfa" class="btn btn-warning fw-bold px-4 mt-3" style="background-color: var(--gold); border:none; color: var(--navy);">Alışverişe Başla</a>
        </div>
    <?php endif; ?>
</section>

<script>
function toggleFavorite(id) {
    fetch('data.php?islem=favori_islem&id=' + id, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const item = document.getElementById('fav-item-' + id);
            item.style.opacity = '0';
            setTimeout(() => {
                item.remove();
                if (document.querySelectorAll('[id^="fav-item-"]').length === 0) {
                    location.reload(); // Boş ekran uyarısını göstermek için
                }
            }, 300);
        }
    });
}
</script>
