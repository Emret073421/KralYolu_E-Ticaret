<?php
// Filtre Parametrelerini Al
$kat_ids_raw = isset($_GET['kategori']) ? (is_array($_GET['kategori']) ? $_GET['kategori'] : [$_GET['kategori']]) : [];
$kat_ids = [];
if (!empty($kat_ids_raw)) {
    foreach ($kat_ids_raw as $k) {
        if (!is_numeric($k)) {
            $k_esc = $db->real_escape_string($k);
            $res = $db->query("SELECT id FROM t_kategori WHERE kategori_adi = '$k_esc'");
            if ($res && $row = $res->fetch_assoc()) {
                $kat_ids[] = (int)$row['id'];
            }
        } else {
            $kat_ids[] = (int)$k;
        }
    }
}
$alt1_ids_raw = isset($_GET['alt_kategori_1']) ? (is_array($_GET['alt_kategori_1']) ? $_GET['alt_kategori_1'] : [$_GET['alt_kategori_1']]) : [];
$alt1_ids = array_map('intval', array_filter($alt1_ids_raw, 'is_numeric'));

$alt2_ids_raw = isset($_GET['alt_kategori_2']) ? (is_array($_GET['alt_kategori_2']) ? $_GET['alt_kategori_2'] : [$_GET['alt_kategori_2']]) : [];
$alt2_ids = array_map('intval', array_filter($alt2_ids_raw, 'is_numeric'));

$min_price = isset($_GET['min']) ? intval($_GET['min']) : 0;
$max_price = isset($_GET['max']) ? intval($_GET['max']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$search = isset($_GET['ara']) ? $db->real_escape_string($_GET['ara']) : '';

// Başlık Belirle
$page_title = "Tüm Ürünler";
if (!empty($kat_ids)) {
    $ids_str = implode(',', array_map('intval', $kat_ids));
    $k_res = $db->query("SELECT GROUP_CONCAT(kategori_adi SEPARATOR ', ') as names FROM t_kategori WHERE id IN ($ids_str)");
    if ($k_row = $k_res->fetch_assoc()) $page_title = $k_row['names'];
}

// SQL Sorgusu Oluştur
$sql = "SELECT DISTINCT u.*, f.foto_url, k.kategori_adi,
               (SELECT AVG(puan) FROM t_yorum WHERE urun_ID = u.id) as ortalama_puan,
               (SELECT COUNT(id) FROM t_yorum WHERE urun_ID = u.id) as yorum_sayisi
        FROM t_urun u 
        LEFT JOIN t_foto f ON u.id = f.urun_ID AND f.foto_sira = 1
        LEFT JOIN t_urun_kategori uk ON u.id = uk.urun_ID
        LEFT JOIN t_kategori k ON uk.kategori_ID = k.id
        WHERE u.aktif = 'Evet' AND u.onay_durumu = 'Onaylandı'";

if (!empty($kat_ids)) {
    $ids_str = implode(',', array_map('intval', $kat_ids));
    $sql .= " AND uk.kategori_ID IN ($ids_str)";
}
if (!empty($alt1_ids)) {
    $alt1_str = implode(',', $alt1_ids);
    $sql .= " AND uk.alt_kategori_1_ID IN ($alt1_str)";
}
if (!empty($alt2_ids)) {
    $alt2_str = implode(',', $alt2_ids);
    $sql .= " AND uk.alt_kategori_2_ID IN ($alt2_str)";
}
if ($search != '') $sql .= " AND (u.urun_adi LIKE '%$search%' OR u.urun_aciklama LIKE '%$search%')";
if ($min_price > 0) $sql .= " AND u.fiyat >= $min_price";
if ($max_price > 0) $sql .= " AND u.fiyat <= $max_price";

// Sıralama
switch ($sort) {
    case 'price_asc': $sql .= " ORDER BY u.fiyat ASC"; break;
    case 'price_desc': $sql .= " ORDER BY u.fiyat DESC"; break;
    case 'newest': default: $sql .= " ORDER BY u.id DESC"; break;
}

$urun_res = $db->query($sql);
$toplam_urun = $urun_res->num_rows;

// Kategorileri Çek (Sidebar için)
$categories = $db->query("SELECT * FROM t_kategori");
?>

<div class="ky-container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Anasayfa</a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 300px;"><?= htmlspecialchars($page_title) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- SOL FİLTRE PANELİ -->
        <aside class="col-lg-3">
            <div class="filter-sidebar bg-white p-4 rounded-4 shadow-sm border border-light">
                <h5 class="fw-bold mb-4" style="color:var(--navy);">Filtrele</h5>
                
                <form action="index.php" method="GET">
                    <input type="hidden" name="sayfa" value="urunler">
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                    <?php if($search != ''): ?><input type="hidden" name="ara" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>

                    <!-- Kategoriler (Çoklu Seçim) -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold small text-uppercase text-muted mb-3">Kategoriler</h6>
                        <div class="category-checkbox-list" style="max-height: 250px; overflow-y: auto;">
                            <?php while($cat = $categories->fetch_assoc()): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input main-cat-cb" type="checkbox" name="kategori[]" value="<?= $cat['id'] ?>" id="cat-<?= $cat['id'] ?>" <?= in_array($cat['id'], $kat_ids) ? 'checked' : '' ?>>
                                    <label class="form-check-label small fw-medium" for="cat-<?= $cat['id'] ?>" style="cursor:pointer;">
                                        <?= htmlspecialchars($cat['kategori_adi']) ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <!-- Alt Kategori 1 -->
                    <div class="filter-section mb-4" id="alt-kat-1-container" style="display: none;">
                        <h6 class="fw-bold small text-uppercase text-muted mb-3">Ürün Grubu</h6>
                        <div class="category-checkbox-list" id="alt-kat-1-list" style="max-height: 200px; overflow-y: auto;">
                            <!-- Dinamik Doldurulacak -->
                        </div>
                    </div>
                    
                    <!-- Alt Kategori 2 -->
                    <div class="filter-section mb-4" id="alt-kat-2-container" style="display: none;">
                        <h6 class="fw-bold small text-uppercase text-muted mb-3">Ürün Tipi</h6>
                        <div class="category-checkbox-list" id="alt-kat-2-list" style="max-height: 200px; overflow-y: auto;">
                            <!-- Dinamik Doldurulacak -->
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Fiyat Aralığı -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold small text-uppercase text-muted mb-3">Fiyat Aralığı</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min" class="form-control form-control-sm" placeholder="Min" value="<?= $min_price > 0 ? $min_price : '' ?>">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max" class="form-control form-control-sm" placeholder="Max" value="<?= $max_price > 0 ? $max_price : '' ?>">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-navy w-100 py-2 fw-bold" style="background: var(--navy); color:#fff; border-radius:10px;">Filtreleri Uygula</button>
                    <a href="index.php?sayfa=urunler" class="btn btn-link btn-sm w-100 mt-2 text-decoration-none text-muted">Temizle</a>
                </form>
            </div>
        </aside>

        <!-- SAĞ ÜRÜN LİSTESİ -->
        <section class="col-lg-9">
            <!-- Sıralama ve Bilgi Barı -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm border border-light">
                <div class="mb-2 mb-md-0">
                    <span class="text-muted"><strong><?= $toplam_urun ?></strong> ürün bulundu</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <label class="text-muted small fw-bold text-nowrap">Sırala:</label>
                    <select id="sort-select" class="form-select form-select-sm border-0 bg-light" style="width: 180px; border-radius:8px;" onchange="applySort(this.value)">
                        <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>En Yeniler</option>
                        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Fiyat (Düşükten Yükseğe)</option>
                        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Fiyat (Yüksekten Düşüğe)</option>
                    </select>
                </div>
            </div>

<script>
function applySort(sortVal) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortVal);
    window.location.search = urlParams.toString();
}
</script>

            <!-- Ürün Grid -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                <?php if ($toplam_urun > 0): ?>
                    <?php while ($urun = $urun_res->fetch_assoc()): ?>
                        <div class="col">
                            <div class="product-card h-100 bg-white border-light shadow-sm">
                                <?php $isFav = in_array($urun['id'], $user_favorites); ?>
                                <button onclick="toggleFavorite(<?= $urun['id'] ?>, this)" class="product-fav-btn <?= $isFav ? 'active' : '' ?>" style="top:10px; right:10px;">
                                    <i class="bi <?= $isFav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                </button>
                                <div class="product-img-wrap">
                                    <img src="<?= htmlspecialchars($urun['foto_url'] ?? 'https://via.placeholder.com/300x400') ?>" alt="<?= htmlspecialchars($urun['urun_adi']) ?>">
                                    <?php if($urun['stok'] <= 5 && $urun['stok'] > 0): ?>
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-3">Son <?= $urun['stok'] ?> Ürün</span>
                                    <?php elseif($urun['stok'] == 0): ?>
                                        <span class="badge bg-secondary position-absolute top-0 start-0 m-3">Tükendi</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info p-3">
                                    <span class="product-brand"><?= htmlspecialchars($urun['kategori_adi'] ?? 'Kral Yolu') ?></span>
                                    <a href="index.php?sayfa=urundetay&id=<?= $urun['id'] ?>" class="product-title d-block text-truncate"><?= htmlspecialchars($urun['urun_adi']) ?></a>
                                    
                                    <!-- Ürün Puanı -->
                                    <div class="product-rating mt-2 mb-1 d-flex align-items-center" style="color: #ffc107; font-size: 0.85rem;">
                                        <?php 
                                        $puan = round($urun['ortalama_puan'] ?? 0);
                                        for ($i = 1; $i <= 5; $i++) echo '<i class="bi bi-star' . ($i <= $puan ? '-fill' : '') . '"></i>';
                                        ?>
                                        <span class="rating-count text-muted ms-1 fw-bold" style="font-size: 0.8rem;">(<?= $urun['yorum_sayisi'] ?? 0 ?>)</span>
                                    </div>

                                    <div class="product-price-row mt-2">
                                        <div class="price-box">
                                            <span class="current-price"><?= number_format($urun['fiyat'], 0, ',', '.') ?> TL</span>
                                        </div>
                                        <button class="btn home-add-cart p-2" data-id="<?= $urun['id'] ?>" style="background: var(--gold); border-radius: 10px; color: var(--navy);">
                                            <i class="bi bi-cart-plus-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 4rem; opacity:0.2;"></i>
                        <h4 class="mt-3 text-muted">Aradığınız kriterlere uygun ürün bulunamadı.</h4>
                        <a href="index.php?sayfa=urunler" class="btn btn-navy mt-3">Tüm Ürünleri Gör</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<style>
    .filter-link { display: block; padding: 8px 0; color: #64748b; text-decoration: none; transition: all 0.3s; font-weight: 500; font-size: 0.95rem; }
    .filter-link:hover { color: var(--navy); padding-left: 5px; }
    .filter-link.active { color: var(--navy); font-weight: 700; }
    .btn-navy { background-color: var(--navy); border-color: var(--navy); color: white; }
    .btn-navy:hover { background-color: #1a2a3a; color: white; }
</style>

<script>
    // Sepete Ekle AJAX (Anasayfadaki ile aynı)
    document.querySelectorAll('.home-add-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            <?php if(!$isLoggedIn): ?>
                window.location.href = 'index.php?sayfa=giris';
                return;
            <?php endif; ?>

            const urunId = this.getAttribute('data-id');
            const formData = new FormData();
            formData.append('islem', 'sepet_ekle');
            formData.append('urun_id', urunId);
            formData.append('adet', 1);

            fetch('data.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    alert("Ürün sepete eklendi!");
                    location.reload();
                } else {
                    alert(data.message || "Hata oluştu.");
                }
            })
            .catch(err => alert("Sistem hatası."));
        });
    });

    // Filtre Dinamik Kategori Yükleme İşlemleri
    const selectedAlt1Ids = <?= json_encode($alt1_ids) ?>;
    const selectedAlt2Ids = <?= json_encode($alt2_ids) ?>;

    function getSelectedCheckboxes(selector) {
        return Array.from(document.querySelectorAll(selector + ':checked')).map(cb => cb.value);
    }

    function loadAltKategoriler(seviye, parentIds, targetContainerId, targetListId, selectedIds) {
        const container = document.getElementById(targetContainerId);
        const list = document.getElementById(targetListId);
        
        if (parentIds.length === 0) {
            container.style.display = 'none';
            list.innerHTML = '';
            // Eğer Alt 1 boşaldıysa Alt 2'yi de gizle
            if(seviye === 1) {
                document.getElementById('alt-kat-2-container').style.display = 'none';
                document.getElementById('alt-kat-2-list').innerHTML = '';
            }
            return;
        }

        fetch(`data.php?islem=get_alt_kategoriler_multi&seviye=${seviye}&parent_ids=${parentIds.join(',')}`)
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                container.style.display = 'block';
                let html = '';
                const inputName = seviye === 1 ? 'alt_kategori_1[]' : 'alt_kategori_2[]';
                const inputClass = seviye === 1 ? 'alt-cat-1-cb' : 'alt-cat-2-cb';
                
                data.forEach(item => {
                    const isChecked = selectedIds.includes(parseInt(item.id)) ? 'checked' : '';
                    html += `
                        <div class="form-check mb-2">
                            <input class="form-check-input ${inputClass}" type="checkbox" name="${inputName}" value="${item.id}" id="subcat-${seviye}-${item.id}" ${isChecked}>
                            <label class="form-check-label small fw-medium" for="subcat-${seviye}-${item.id}" style="cursor:pointer;">
                                ${item.kategori_adi}
                            </label>
                        </div>
                    `;
                });
                list.innerHTML = html;
                
                // Yeni checkboxlara event listener ekle
                if (seviye === 1) {
                    document.querySelectorAll('.alt-cat-1-cb').forEach(cb => {
                        cb.addEventListener('change', () => {
                            const newParentIds = getSelectedCheckboxes('.alt-cat-1-cb');
                            loadAltKategoriler(2, newParentIds, 'alt-kat-2-container', 'alt-kat-2-list', selectedAlt2Ids);
                        });
                    });
                    
                    // Sayfa ilk yüklendiğinde, eğer alt kategori 1'den seçili olanlar varsa alt kategori 2'yi de yükle
                    const initialAlt1Selected = getSelectedCheckboxes('.alt-cat-1-cb');
                    if(initialAlt1Selected.length > 0) {
                        loadAltKategoriler(2, initialAlt1Selected, 'alt-kat-2-container', 'alt-kat-2-list', selectedAlt2Ids);
                    }
                }
            } else {
                container.style.display = 'none';
                list.innerHTML = '';
                if(seviye === 1) {
                    document.getElementById('alt-kat-2-container').style.display = 'none';
                    document.getElementById('alt-kat-2-list').innerHTML = '';
                }
            }
        });
    }

    // Ana kategori checkboxları değiştiğinde
    document.querySelectorAll('.main-cat-cb').forEach(cb => {
        cb.addEventListener('change', () => {
            const parentIds = getSelectedCheckboxes('.main-cat-cb');
            // Yeni kategori seçildiğinde alt kategori seçimlerini temizlemek iyi bir UX olabilir, ancak state korumak da önemli.
            // Biz var olan selectedAlt1Ids'i gönderiyoruz.
            loadAltKategoriler(1, parentIds, 'alt-kat-1-container', 'alt-kat-1-list', selectedAlt1Ids);
        });
    });

    // Sayfa Yüklendiğinde ilk durumları kontrol et
    document.addEventListener('DOMContentLoaded', () => {
        // İlk açılışta seçili ana kategoriler varsa, Alt Kategori 1'i yükle
        const parentIds = getSelectedCheckboxes('.main-cat-cb');
        if (parentIds.length > 0) {
            loadAltKategoriler(1, parentIds, 'alt-kat-1-container', 'alt-kat-1-list', selectedAlt1Ids);
        }
    });
</script>
