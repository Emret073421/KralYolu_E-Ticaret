<?php
// Ürün ID'sini URL'den al
$urun_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($urun_id <= 0) {
    echo "<div class='ky-container my-5 alert alert-danger'>Geçersiz ürün!</div>";
    return;
}

// 1. Ürün Temel Bilgilerini Çek
$query = "SELECT u.*, s.kurum_adi as satici_adi 
          FROM t_urun u 
          LEFT JOIN t_satici s ON u.satici_ID = s.id 
          WHERE u.id = $urun_id AND u.aktif = 'Evet'";
$urun_res = $db->query($query);
$product = $urun_res->fetch_assoc();

if (!$product) {
    echo "<div class='ky-container my-5 alert alert-danger'>Ürün bulunamadı veya satışta değil!</div>";
    return;
}

// 2. Kategorileri Çek (Breadcrumb için)
$kat_query = "SELECT k.kategori_adi, a1.kategori_adi as alt1_adi, a2.kategori_adi as alt2_adi
              FROM t_urun_kategori uk
              LEFT JOIN t_kategori k ON uk.kategori_ID = k.id
              LEFT JOIN t_alt_kategori_1 a1 ON uk.alt_kategori_1_ID = a1.id
              LEFT JOIN t_alt_kategori_2 a2 ON uk.alt_kategori_2_ID = a2.id
              WHERE uk.urun_ID = $urun_id LIMIT 1";
$kat_res = $db->query($kat_query);
$categories = $kat_res->fetch_assoc();

// 3. Fotoğrafları Çek
$foto_res = $db->query("SELECT * FROM t_foto WHERE urun_ID = $urun_id ORDER BY foto_sira ASC");
$photos = [];
while($f = $foto_res->fetch_assoc()) $photos[] = $f;
$main_photo = !empty($photos) ? $photos[0]['foto_url'] : 'https://via.placeholder.com/800';

// 4. Değerlendirmeleri ve Ortalama Puanı Çek
$yorum_query = "SELECT y.*, u.ad, u.soyad 
                FROM t_yorum y 
                JOIN t_uye u ON y.uye_ID = u.id 
                WHERE y.urun_ID = $urun_id 
                ORDER BY y.tarih DESC";
// Not: Eğer t_yorum'da urun_ID yoksa (önceki konuşmalarda sd.urun_ID üzerinden bağlanmıştı), 
// SQL dump'a göre kontrol etmeliyiz. SQL dump'ta t_yorum içinde urun_ID VAR.
$yorum_res = $db->query($yorum_query);
$reviews = [];
$toplam_puan = 0;
while($y = $yorum_res->fetch_assoc()) {
    $reviews[] = $y;
    $toplam_puan += $y['puan'];
}
$yorum_sayisi = count($reviews);
$ortalama_puan = $yorum_sayisi > 0 ? round($toplam_puan / $yorum_sayisi, 1) : 0;

// 5. Ürün Seçeneklerini Çek (Beden, Renk vb.)
$secenek_res = $db->query("SELECT * FROM t_urun_secenek WHERE urun_ID = $urun_id");
$options = [];
while($s = $secenek_res->fetch_assoc()) {
    $deger_res = $db->query("SELECT * FROM t_urun_secenek_deger WHERE secenek_ID = {$s['id']}");
    $s['degerler'] = [];
    while($d = $deger_res->fetch_assoc()) $s['degerler'][] = $d;
    $options[] = $s;
}

// 6. Teknik Özellikleri Çek (Tablo Detay)
$detay_res = $db->query("SELECT * FROM t_urun_tablo_detay WHERE urun_ID = $urun_id ORDER BY tablo_sira ASC");
$specs = [];
while($sp = $detay_res->fetch_assoc()) $specs[] = $sp;

?>

<section class="ky-container my-5">
    
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?sayfa=anasayfa"><i class="bi bi-house"></i> Anasayfa</a></li>
            <?php if($categories['kategori_adi']): ?><li class="breadcrumb-item"><a href="#"><?= $categories['kategori_adi'] ?></a></li><?php endif; ?>
            <?php if($categories['alt1_adi']): ?><li class="breadcrumb-item"><a href="#"><?= $categories['alt1_adi'] ?></a></li><?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['urun_adi']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Sol: Ürün Galerisi -->
        <div class="col-lg-6">
            <div class="product-gallery">
                <div class="main-image-wrap">
                    <img src="<?= $main_photo ?>" id="mainProdImage" alt="Ürün Ana Görsel">
                </div>
                <?php if(count($photos) > 1): ?>
                <div class="thumbnail-slider">
                    <?php foreach($photos as $index => $photo): ?>
                    <div class="thumb-item <?= $index === 0 ? 'active' : '' ?>" onclick="changeImage(this, '<?= $photo['foto_url'] ?>')">
                        <img src="<?= $photo['foto_url'] ?>" alt="Thumbnail">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sağ: Ürün Bilgileri -->
        <div class="col-lg-6">
            <div class="product-meta">
                <span class="brand"><?= htmlspecialchars($product['satici_adi'] ?? 'Kral Yolu') ?></span>
                <h1 class="title"><?= htmlspecialchars($product['urun_adi']) ?></h1>
                
                <div class="rating-row">
                    <div class="stars">
                        <?php 
                        for($i=1; $i<=5; $i++) {
                            if($i <= $ortalama_puan) echo '<i class="bi bi-star-fill"></i>';
                            elseif($i - 0.5 <= $ortalama_puan) echo '<i class="bi bi-star-half"></i>';
                            else echo '<i class="bi bi-star"></i>';
                        }
                        ?>
                    </div>
                    <div class="review-count">
                        <?= $ortalama_puan ?> | <a href="#reviews"><?= $yorum_sayisi ?> Değerlendirme</a>
                    </div>
                </div>

                <div class="price-box">
                    <?php if($product['fiyat'] > 0): ?>
                        <span class="current-price"><?= number_format($product['fiyat'], 2, ',', '.') ?> TL</span>
                    <?php endif; ?>
                </div>

                <!-- Seçenekler ve Butonlar Formu -->
                <form id="add-to-cart-form">
                    <input type="hidden" name="urun_id" value="<?= $urun_id ?>">
                    <div class="row g-3 option-group-wrapper">
                        <?php foreach($options as $opt): ?>
                        <div class="col-sm-6 option-group">
                            <label class="form-label" for="opt-<?= $opt['id'] ?>"><?= htmlspecialchars($opt['secenek_baslik'] ?? 'Seçenek') ?>:</label>
                            <div class="ky-select-wrapper">
                                <select class="form-select ky-select" id="opt-<?= $opt['id'] ?>" name="secenek[<?= $opt['id'] ?>]" required>
                                    <option value="" disabled selected>Seçiniz...</option>
                                    <?php foreach($opt['degerler'] as $val): ?>
                                        <option value="<?= $val['id'] ?>"><?= htmlspecialchars($val['secenek_deger']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="bi bi-chevron-down select-icon"></i>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Stok Durumu -->
                    <div class="mt-3 mb-4">
                        <?php if($product['stok'] > 0): ?>
                            <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Stokta Var (<?= $product['stok'] ?> adet)</span>
                            <span class="text-muted ms-2" style="font-size: 0.85rem;">Hemen teslim!</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Stokta Yok</span>
                        <?php endif; ?>
                    </div>

                    <!-- Butonlar -->
                    <div class="action-box">
                        <div class="qty-input-group">
                            <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
                            <input type="text" name="adet" class="qty-input" id="qtyInput" value="1" readonly>
                            <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                        </div>
                        <button type="submit" class="add-btn" <?= $product['stok'] <= 0 ? 'disabled' : '' ?>>
                            <i class="bi bi-cart-plus"></i> Sepete Ekle
                        </button>
                        <?php
                        $is_fav = false;
                        if ($isLoggedIn) {
                            $u_id = $user['id'];
                            $fav_check = $db->query("SELECT id FROM t_favori WHERE uye_id = $u_id AND urun_id = $urun_id");
                            $is_fav = ($fav_check && $fav_check->num_rows > 0);
                        }
                        ?>
                        <button type="button" onclick="toggleFavorite(<?= $urun_id ?>, this)" class="fav-btn <?= $is_fav ? 'active' : '' ?>" title="<?= $is_fav ? 'Favorilerden Çıkar' : 'Favorilere Ekle' ?>" style="border:none; background:none;">
                            <i class="bi <?= $is_fav ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
                        </button>
                    </div>
                </form>

                <!-- Kısa Bilgiler -->
                <ul class="list-unstyled text-muted mt-4" style="font-size: 0.95rem; line-height: 1.8;">
                    <li><i class="bi bi-truck me-2 text-primary"></i> <b>Hızlı Teslimat Seçeneği</b></li>
                    <li><i class="bi bi-arrow-return-left me-2 text-primary"></i> <b>15 Gün İçinde Ücretsiz İade</b></li>
                    <li><i class="bi bi-shield-check me-2 text-primary"></i> <b>Güvenli Alışveriş</b></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Ürün Detay Sekmeleri -->
    <div class="product-tabs-section">
        <div class="ky-nav-tabs">
            <button class="ky-tab-link active" onclick="openTab(event, 'desc')">Ürün Açıklaması</button>
            <button class="ky-tab-link" onclick="openTab(event, 'specs')">Özellikler</button>
            <button class="ky-tab-link" onclick="openTab(event, 'reviews')">Değerlendirmeler (<?= $yorum_sayisi ?>)</button>
        </div>

        <!-- Tab: Açıklama -->
        <div id="desc" class="tab-content-pane active">
            <h4><?= htmlspecialchars($product['urun_adi']) ?></h4>
            <div class="product-description">
                <?= nl2br(htmlspecialchars($product['urun_aciklama'])) ?>
            </div>
        </div>

        <!-- Tab: Özellikler -->
        <div id="specs" class="tab-content-pane">
            <table class="spec-table">
                <?php foreach($specs as $spec): ?>
                <tr>
                    <th><?= htmlspecialchars($spec['sutun_baslik']) ?></th>
                    <td><?= htmlspecialchars($spec['sutun_aciklama']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($specs)): ?>
                    <tr><td colspan="2" class="text-muted text-center">Teknik özellik belirtilmemiş.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Tab: Yorumlar -->
        <div id="reviews" class="tab-content-pane">
            <style>
                .rating-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 5px; }
                .rating-input input { display: none; }
                .rating-input label { cursor: pointer; font-size: 1.5rem; color: #e2e8f0; transition: color 0.2s; margin:0; }
                .rating-input label:hover, .rating-input label:hover ~ label, .rating-input input:checked ~ label { color: #ffc107; }
            </style>
            
            <div class="row mb-5">
                <!-- Sol Taraf: Ortalama Puan -->
                <div class="col-md-4 border-end text-center mb-4 mb-md-0 d-flex flex-column justify-content-center">
                    <h1 class="display-3 fw-bold m-0" style="color:var(--navy);"><?= $ortalama_puan ?></h1>
                    <div class="stars mb-2 fs-4">
                        <?php 
                        for($i=1; $i<=5; $i++) {
                            echo '<i class="bi bi-star-fill '.($i <= $ortalama_puan ? 'text-warning' : 'text-light').'"></i>';
                        }
                        ?>
                    </div>
                    <p class="text-muted mb-0"><?= $yorum_sayisi ?> Değerlendirme</p>
                </div>

                <!-- Sağ Taraf: Yorum Ekleme Formu -->
                <div class="col-md-8 ps-md-5">
                    <h5 class="fw-bold mb-3" style="color:var(--navy);">Ürünü Değerlendir</h5>
                    <?php if($isLoggedIn): ?>
                        <form id="yorumEkleForm">
                            <input type="hidden" name="urun_id" value="<?= $urun_id ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Puanınız</label>
                                <div class="rating-input">
                                    <input type="radio" name="puan" value="5" id="star5" required><label for="star5" title="5 Yıldız"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="puan" value="4" id="star4"><label for="star4" title="4 Yıldız"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="puan" value="3" id="star3"><label for="star3" title="3 Yıldız"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="puan" value="2" id="star2"><label for="star2" title="2 Yıldız"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="puan" value="1" id="star1"><label for="star1" title="1 Yıldız"><i class="bi bi-star-fill"></i></label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Yorumunuz</label>
                                <textarea name="yorum" class="form-control" rows="3" placeholder="Ürün hakkındaki düşüncelerinizi paylaşın..." style="border-radius:10px; border-color:#e2e8f0;" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning fw-bold px-4" style="background-color:var(--gold); border:none; color:var(--navy); border-radius:8px;">Yorumu Gönder</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-light border border-warning" style="border-radius:10px;">
                            <i class="bi bi-info-circle-fill text-warning me-2"></i> Yorum yapabilmek için lütfen <a href="index.php?sayfa=giris" class="fw-bold text-decoration-none" style="color:var(--navy);">Giriş Yapın</a> veya kayıt olun.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="my-4" style="border-color:#e2e8f0;">
            <h5 class="fw-bold mb-4" style="color:var(--navy);">Son Yorumlar</h5>

            <?php if(!empty($reviews)): ?>
                <?php foreach($reviews as $rev): ?>
                    <div class="card border-0 border-bottom rounded-0 mb-4 pb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <strong class="text-dark"><?= htmlspecialchars($rev['ad'] . ' ' . mb_substr($rev['soyad'], 0, 1) . '.') ?></strong>
                            <span class="text-muted" style="font-size:0.85rem"><?= date('d.m.Y', strtotime($rev['tarih'])) ?></span>
                        </div>
                        <div class="stars mb-2" style="font-size:0.8rem">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="bi bi-star-fill <?= $i <= $rev['puan'] ? 'text-warning' : 'text-light' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-secondary mb-0"><?= nl2br(htmlspecialchars($rev['yorum'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Bu ürün için henüz yorum yapılmamış.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    // Miktar Arttır/Azalt
    function updateQty(change) {
        const input = document.getElementById('qtyInput');
        let val = parseInt(input.value) + change;
        if(val < 1) val = 1;
        input.value = val;
    }

    // Ana Resmi Değiştir
    function changeImage(element, srcUrl) {
        document.getElementById('mainProdImage').src = srcUrl;
        document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
    }

    // Tab Değiştirme
    function openTab(evt, tabId) {
        document.querySelectorAll('.tab-content-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.ky-tab-link').forEach(el => el.classList.remove('active'));
        
        document.getElementById(tabId).classList.add('active');
        evt.currentTarget.classList.add('active');
    }

    // Sepete Ekle Butonu JS
    document.querySelector('.add-btn')?.addEventListener('click', function(e) {
        e.preventDefault();
        
        <?php if(!$isLoggedIn): ?>
            window.location.href = 'index.php?sayfa=giris';
            return;
        <?php endif; ?>

        const form = document.getElementById('add-to-cart-form');
        if(!form.reportValidity()) return;
        
        const formData = new FormData(form);
        formData.append('islem', 'sepet_ekle');

        fetch('data.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert("Ürün başarıyla sepete eklendi!");
                // İsteğe bağlı: Sepet ikonundaki sayıyı güncelle
            } else {
                alert("Bir hata oluştu: " + (data.message || "Bilinmiyor"));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Sistem hatası oluştu.");
        });
    });

    // Yorum Ekleme Butonu JS
    document.getElementById('yorumEkleForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('islem', 'yorum_ekle');

        fetch('data.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert(data.message);
                location.reload(); // Yorumların güncel halini görmek için sayfayı yenile
            } else {
                alert("Hata: " + (data.message || "Bilinmiyor"));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Sistem hatası oluştu.");
        });
    });
</script>
