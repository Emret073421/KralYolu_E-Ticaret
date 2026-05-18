<?php
// Farklı kategorilerdeki ürünleri çek
$giyim_json = get_products_by_category('Giyim', 4);
$elektronik_json = get_products_by_category('Elektronik', 4);
$kozmetik_json = get_products_by_category('Kozmetik', 4);

$giyim_urunleri = json_decode($giyim_json, true);
$elektronik_urunleri = json_decode($elektronik_json, true);
$kozmetik_urunleri = json_decode($kozmetik_json, true);
?>

<style>
    .section-header { border-bottom: 2px solid #f0f0f0; margin-bottom: 30px; padding-bottom: 10px; }
    .section-header h3 { font-weight: 800; color: var(--navy); position: relative; display: inline-block; }
    .section-header h3::after { content: ''; position: absolute; bottom: -12px; left: 0; width: 50px; height: 3px; background: var(--gold); }
    .trust-badge { transition: transform 0.3s; padding: 15px 10px; border-radius: 12px; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.04); }
    .trust-badge:hover { transform: translateY(-5px); }
    .trust-icon { font-size: 1.8rem; color: var(--gold); margin-bottom: 10px; }
</style>

<div class="anasayfa-wrapper">
    
    <!-- 1. HERO SLIDER ALANI -->
    <section class="hero-slider-section ky-container d-flex justify-content-center">
        <div id="mainHeroCarousel" class="carousel slide hero-carousel-item w-100" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="1"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop" class="d-block w-100 hero-img" alt="Teknoloji">
                    <div class="hero-overlay">
                        <div class="hero-content-box">
                            <span class="hero-tag" style="background: #e74c3c;">Fırsat Ürünü</span>
                            <h2 class="hero-title">En Yeni Teknolojiler Kapınızda</h2>
                            <p class="hero-desc">Akıllı telefonlardan bilgisayarlara, dünyaca ünlü markalar en iyi fiyatlarla Kral Yolu'nda.</p>
                            <a href="#" class="hero-btn">Şimdi İncele <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=2070&auto=format&fit=crop" class="d-block w-100 hero-img" alt="Giyim">
                    <div class="hero-overlay">
                        <div class="hero-content-box">
                            <span class="hero-tag">Yeni Sezon</span>
                            <h2 class="hero-title">Baharın En Şık Kombinleri</h2>
                            <p class="hero-desc">Gardırobunuzu yenilemenin tam zamanı. İndirimli fiyatları kaçırmayın.</p>
                            <a href="#" class="hero-btn">Alışverişe Başla <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. KATEGORİ VİTRİNİ -->
    <section class="category-showcase ky-container mt-5">
        <div class="section-header d-flex justify-content-between align-items-end">
            <h3>Popüler Kategoriler</h3>
            <a href="index.php?sayfa=kategoriler" class="text-decoration-none text-muted fw-bold" style="font-size:0.9rem">Tümünü Gör <i class="bi bi-chevron-right"></i></a>
        </div>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-4 mt-2">
            <div class="col"><a href="index.php?sayfa=urunler&kategori=Elektronik" class="cat-item"><div class="cat-icon-wrap"><i class="bi bi-cpu"></i></div><span class="cat-name">Elektronik</span></a></div>
            <div class="col"><a href="index.php?sayfa=urunler&kategori=Giyim" class="cat-item"><div class="cat-icon-wrap"><i class="bi bi-file-person"></i></div><span class="cat-name">Giyim</span></a></div>
            <div class="col"><a href="index.php?sayfa=urunler&kategori=Kozmetik" class="cat-item"><div class="cat-icon-wrap"><i class="bi bi-magic"></i></div><span class="cat-name">Kozmetik</span></a></div>
            <div class="col"><a href="index.php?sayfa=urunler&kategori=Ayakkabı" class="cat-item"><div class="cat-icon-wrap"><i class="bi bi-footprint"></i></div><span class="cat-name">Ayakkabı</span></a></div>
            <div class="col"><a href="index.php?sayfa=urunler&kategori=Spor" class="cat-item"><div class="cat-icon-wrap"><i class="bi bi-trophy"></i></div><span class="cat-name">Spor</span></a></div>
            <div class="col"><a href="index.php?sayfa=urunler&kategori=Ev" class="cat-item"><div class="cat-icon-wrap"><i class="bi bi-house-door"></i></div><span class="cat-name">Ev & Yaşam</span></a></div>
        </div>
    </section>

    <!-- 3. ÖNE ÇIKAN ÜRÜNLER (GİYİM) -->
    <section class="product-slider ky-container mt-5">
        <div class="section-header"><h3>Trend Giyim Ürünleri</h3></div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mt-1">
            <?php renderProductList($giyim_urunleri, $isLoggedIn); ?>
        </div>
    </section>

    <!-- 4. ELEKTRONİK VİTRİNİ -->
    <section class="product-slider ky-container mt-5">
        <div class="section-header"><h3>Elektronik Dünyası</h3></div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mt-1">
            <?php renderProductList($elektronik_urunleri, $isLoggedIn); ?>
        </div>
    </section>

    <!-- 5. GÜZELLİK & BAKIM -->
    <section class="product-slider ky-container mt-5">
        <div class="section-header"><h3>Güzellik & Bakım</h3></div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mt-1">
            <?php renderProductList($kozmetik_urunleri, $isLoggedIn); ?>
        </div>
    </section>

    <!-- 6. NEDEN BİZ? GÜVENLİK ALANI -->
    <section class="ky-container mt-5 mb-5">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="trust-badge">
                    <div class="trust-icon"><i class="bi bi-truck"></i></div>
                    <h5 class="fw-bold">Hızlı Teslimat</h5>
                    <p class="text-muted mb-0">Saat 14:00'a kadar verilen siparişler aynı gün kargoda!</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="trust-badge">
                    <div class="trust-icon"><i class="bi bi-shield-lock"></i></div>
                    <h5 class="fw-bold">Güvenli Ödeme</h5>
                    <p class="text-muted mb-0">256-bit SSL sertifikası ile tüm ödemeleriniz koruma altında.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="trust-badge">
                    <div class="trust-icon"><i class="bi bi-arrow-repeat"></i></div>
                    <h5 class="fw-bold">Kolay İade</h5>
                    <p class="text-muted mb-0">15 gün içerisinde koşulsuz şartsız ücretsiz iade imkanı.</p>
                </div>
            </div>
        </div>
    </section>

</div>

<?php
// Ürün listeleme fonksiyonu (Tekrarı önlemek için)
function renderProductList($urunler, $isLoggedIn) {
    global $user_favorites;
    if (!empty($urunler)) {
        foreach ($urunler as $urun) {
            $isFav = in_array($urun['id'], $user_favorites);
            ?>
            <div class="col">
                <div class="product-card h-100">
                    <button class="product-fav-btn <?= $isFav ? 'active' : '' ?>" onclick="toggleFavorite(<?= $urun['id'] ?>, this)">
                        <i class="bi <?= $isFav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                    </button>
                    <div class="product-img-wrap">
                        <img src="<?= htmlspecialchars($urun['foto_url'] ?? 'https://via.placeholder.com/300x400') ?>" alt="<?= htmlspecialchars($urun['urun_adi']) ?>">
                    </div>
                    <div class="product-info">
                        <span class="product-brand"><?= htmlspecialchars($urun['satici_adi'] ?? 'Kral Yolu') ?></span>
                        <a href="index.php?sayfa=urundetay&id=<?= $urun['id'] ?>" class="product-title"><?= htmlspecialchars($urun['urun_adi']) ?></a>
                        <div class="product-rating">
                            <?php 
                            $puan = round($urun['ortalama_puan']);
                            for ($i = 1; $i <= 5; $i++) echo '<i class="bi bi-star' . ($i <= $puan ? '-fill' : '') . '"></i>';
                            ?>
                            <span class="rating-count">(<?= $urun['yorum_sayisi'] ?>)</span>
                        </div>
                        <div class="product-price-row mt-auto">
                            <div class="price-box">
                                <span class="current-price"><?= number_format($urun['fiyat'], 0, ',', '.') ?> TL</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn fw-bold px-2 py-1 home-add-cart" data-id="<?= $urun['id'] ?>" style="background-color: var(--gold); border:none; color: var(--navy); border-radius:8px; font-size:0.75rem;">Sepete Ekle</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="col-12 text-center py-4"><p class="text-muted small">Bu kategoride ürün bulunamadı.</p></div>';
    }
}
?>

<script>
    // Toast bildirim sistemi
    function showToast(message, type) {
        var existing = document.getElementById('ky-toast');
        if (existing) existing.remove();
        
        var toast = document.createElement('div');
        toast.id = 'ky-toast';
        toast.style.cssText = 'position:fixed; top:20px; right:20px; z-index:9999; padding:14px 24px; border-radius:10px; font-weight:600; font-size:0.9rem; color:#fff; box-shadow:0 8px 25px rgba(0,0,0,0.15); transform:translateX(120%); transition:transform 0.4s ease; display:flex; align-items:center; gap:10px;';
        toast.style.background = type === 'success' ? '#16a34a' : '#dc2626';
        toast.innerHTML = '<i class="bi ' + (type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill') + '"></i> ' + message;
        document.body.appendChild(toast);
        
        setTimeout(function() { toast.style.transform = 'translateX(0)'; }, 50);
        setTimeout(function() { 
            toast.style.transform = 'translateX(120%)';
            setTimeout(function() { toast.remove(); }, 400);
        }, 3000);
    }

    // Sepet sayacını güncelle
    function updateCartCount(delta) {
        var badge = document.querySelector('.ky-action-item .badge, .sepet-count');
        if (!badge) {
            // Header'daki sepet linkini bul
            var sepetLinks = document.querySelectorAll('a[href*="sepet"]');
            sepetLinks.forEach(function(link) {
                var b = link.querySelector('.badge');
                if (b) {
                    var c = parseInt(b.textContent) || 0;
                    b.textContent = c + delta;
                }
            });
        }
    }

    // Sepete ekleme
    document.querySelectorAll('.home-add-cart').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            <?php if(!$isLoggedIn): ?>
                window.location.href = 'index.php?sayfa=giris';
                return;
            <?php endif; ?>

            var originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:12px;height:12px;"></span>';
            this.disabled = true;
            var that = this;

            var urunId = this.getAttribute('data-id');
            var formData = new FormData();
            formData.append('islem', 'sepet_ekle');
            formData.append('urun_id', urunId);
            formData.append('adet', 1);

            fetch('data.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    showToast('Ürün sepete eklendi!', 'success');
                    that.innerHTML = '<i class="bi bi-check2"></i> Eklendi';
                    that.style.backgroundColor = '#16a34a';
                    that.style.color = '#fff';
                    updateCartCount(1);
                    setTimeout(function() {
                        that.innerHTML = originalText;
                        that.style.backgroundColor = '';
                        that.style.color = '';
                        that.disabled = false;
                    }, 2000);
                } else {
                    showToast(data.message || 'Hata oluştu.', 'error');
                    that.innerHTML = originalText;
                    that.disabled = false;
                }
            })
            .catch(function(err) {
                showToast('Bağlantı hatası!', 'error');
                that.innerHTML = originalText;
                that.disabled = false;
            });
        });
    });
</script>

