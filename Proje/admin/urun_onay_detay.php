<?php
$urun_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ürün Bilgilerini Çek
$sql = "SELECT u.*, s.kurum_adi, k.kategori_adi, 
        (SELECT AVG(puan) FROM t_yorum WHERE urun_ID = u.id) as puan
        FROM t_urun u 
        JOIN t_satici s ON u.satici_ID = s.id 
        LEFT JOIN t_urun_kategori uk ON u.id = uk.urun_ID
        LEFT JOIN t_kategori k ON uk.kategori_ID = k.id
        WHERE u.id = $urun_id";
$res = $db->query($sql);

if (!$res || $res->num_rows == 0) {
    echo "<div class='alert alert-danger'>Ürün bulunamadı.</div>";
    return;
}

$u = $res->fetch_assoc();

// Ürün Fotoğrafları
$foto_res = $db->query("SELECT * FROM t_foto WHERE urun_ID = $urun_id ORDER BY foto_sira ASC");
$fotograflar = [];
while ($f = $foto_res->fetch_assoc()) {
    $fotograflar[] = $f;
}

// Ürün Varyantları
$varyant_res = $db->query("SELECT s.secenek_baslik as ozellik_adi, sd.secenek_deger as deger 
                           FROM t_urun_secenek s
                           JOIN t_urun_secenek_deger sd ON s.id = sd.secenek_ID
                           WHERE s.urun_ID = $urun_id");
$varyantlar = [];
while ($v = $varyant_res->fetch_assoc()) {
    $varyantlar[$v['ozellik_adi']][] = $v['deger'];
}
?>

<!-- Üst Başlık ve Geri Dön -->
<header class="d-flex align-items-center gap-3 mb-4">
    <a href="index.php?sayfa=urun_onay" class="btn btn-outline-secondary btn-sm" style="border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-arrow-left mt-1"></i>
    </a>
    <div>
        <h2 class="m-0 fw-bold" style="color:#0f172a;">Ürün İnceleme Detayı</h2>
        <span class="text-muted" style="font-size:0.9rem;">Ürünü detaylıca inceleyip satış onayı verebilir veya reddedebilirsiniz.</span>
    </div>
</header>

<div class="row g-4 position-relative">
    
    <!-- SOL SÜTUN: Görseller ve Açıklama -->
    <div class="col-lg-5">
        <div class="bg-white p-4 rounded-3 shadow-sm border border-light mb-4">
            <?php if (!empty($fotograflar)): ?>
                <div class="product-img-main-container">
                    <img src="<?= htmlspecialchars($fotograflar[0]['foto_url']) ?>" id="mainImg" alt="Ürün Ana Görsel" class="product-img-main">
                </div>
                <div class="thumb-container">
                    <?php foreach($fotograflar as $index => $foto): ?>
                        <img src="<?= htmlspecialchars($foto['foto_url']) ?>" alt="Thumb" class="product-img-thumb <?= $index == 0 ? 'active' : '' ?>" onclick="changeMainImage(this, '<?= htmlspecialchars($foto['foto_url']) ?>')">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Bu ürün için görsel yüklenmemiş.</div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-4 rounded-3 shadow-sm border border-light">
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color:#0f172a;">Satıcı Açıklaması</h5>
            <div class="text-muted" style="line-height: 1.8; font-size: 0.95rem;">
                <?= nl2br(htmlspecialchars($u['urun_aciklama'])) ?>
            </div>
        </div>
    </div>

    <!-- SAĞ SÜTUN: Ürün Teknik Detayları ve Onay İşlemleri -->
    <div class="col-lg-7">
        <div class="bg-white p-4 rounded-3 shadow-sm border border-light">
            
            <!-- Başlık ve Satıcı -->
            <div class="mb-4">
                <h4 class="fw-bold mb-2"><?= htmlspecialchars($u['urun_adi']) ?></h4>
                <div class="d-flex align-items-center gap-2">
                    <?php if($u['onay_durumu'] == 'Onay Bekliyor'): ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Onay Bekliyor</span>
                    <?php else:
                        $badgeClass = $u['onay_durumu'] == 'Onaylandı' ? 'bg-success' : 'bg-danger';
                    ?>
                        <span class="badge <?= $badgeClass ?>"><i class="bi bi-info-circle"></i> <?= $u['onay_durumu'] ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <div class="detail-card">
                        <div class="detail-label">Satıcı (Mağaza)</div>
                        <div class="detail-value text-primary"><i class="bi bi-shop me-1"></i> <?= htmlspecialchars($u['kurum_adi']) ?></div>
                        <small class="text-muted">Güvenilirlik Puanı: <span class="text-success fw-bold"><?= number_format($u['puan'] ?? 0, 1) ?>/10</span></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-card">
                        <div class="detail-label">Kategori Konumu</div>
                        <div class="detail-value"><?= htmlspecialchars($u['kategori_adi'] ?? 'Kategorisiz') ?></div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="detail-card border-success" style="background:#f0fdf4;">
                        <div class="detail-label text-success">Belirlenen Satış Fiyatı</div>
                        <div class="detail-value text-success fs-3"><?= number_format($u['fiyat'], 2, ',', '.') ?> ₺</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-card">
                        <div class="detail-label">Başlangıç Stoğu</div>
                        <div class="detail-value"><?= $u['stok'] ?> Adet Beyan Edildi</div>
                    </div>
                </div>
            </div>

            <!-- Varyant Analizi -->
            <?php if (!empty($varyantlar)): ?>
            <h5 class="fw-bold mb-3 border-bottom pb-2" style="color:#0f172a;">Ürün Seçenekleri (Varyantları)</h5>
            <div class="p-3 bg-light rounded border mb-4">
                <?php foreach($varyantlar as $ozellik_adi => $degerler): ?>
                    <div class="mb-2">
                        <span class="fw-bold text-dark me-2"><?= htmlspecialchars($ozellik_adi) ?>:</span> 
                        <?php 
                        $unique_degerler = array_unique($degerler);
                        foreach($unique_degerler as $deger): 
                        ?>
                            <span class="badge border border-secondary text-secondary me-1"><?= htmlspecialchars($deger) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- ONAY & RED AKSİYONLARI (Alt Bar) -->
            <?php if($u['onay_durumu'] == 'Onay Bekliyor'): ?>
            <div class="action-container mt-5">
                <div class="w-100 d-flex flex-column gap-3">
                    <p class="text-muted small m-0 fw-bold"><i class="bi bi-shield-check"></i> KRAL YOLU DENETİM İŞLEMLERİ</p>
                    <div class="d-flex gap-3">
                        <!-- Reddet Butonu -->
                        <button type="button" class="btn btn-outline-danger fw-bold py-3 px-4 w-50" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i> Uygun Değil (Reddet)
                        </button>
                        
                        <!-- Onayla Butonu -->
                        <button type="button" onclick="urunIslemYap(<?= $urun_id ?>, 'onayla')" class="btn btn-success fw-bold py-3 px-4 w-50 shadow text-white" style="background:#10b981; border:none;">
                            <i class="bi bi-check2-circle me-1"></i> Satışa Uygun (Onayla)
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<!-- Reddetme Modalı -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-octagon-fill"></i> Ürünü Reddediyorsunuz</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <p class="text-muted fw-medium mb-4">Ürün onaylanmayacak ve satıcıya geri gönderilecektir. Lütfen satıcının düzeltmesini istediğiniz sorunu belirtin.</p>
        
        <div class="mb-3">
            <label class="form-label fw-bold small">Reddedilme Nedeni Seçin</label>
            <select class="form-select" id="red_nedeni">
                <option>Hatalı / Eksik Görsel Kullanımı</option>
                <option>Uygunsuz / Kusurlu Ürün Açıklaması</option>
                <option>Hatalı Kategori Seçimi</option>
                <option>Platform Politikalarına Aykırı Ürün (Yasaklı Ürün)</option>
                <option>Fiyat Manipülasyonu / Hatalı Fiyat</option>
                <option>Diğer</option>
            </select>
        </div>
        
        <div class="mb-2">
            <label class="form-label fw-bold small">Satıcıya Özel Not / Açıklama (Zorunlu Değil)</label>
            <textarea class="form-control" id="red_aciklama" rows="3" placeholder="Örn: 2. fotoğraf flu çıkmış..."></textarea>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">İptal</button>
        <button type="button" onclick="urunIslemYap(<?= $urun_id ?>, 'reddet')" class="btn btn-danger fw-bold px-4">Ürünü Reddet</button>
      </div>
    </div>
  </div>
</div>

<script>
function changeMainImage(element, url) {
    document.getElementById('mainImg').src = url;
    document.querySelectorAll('.product-img-thumb').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
}

function urunIslemYap(id, islemTur) {
    let data = {
        islem: 'urun_onay_islem',
        id: id,
        tur: islemTur
    };

    if (islemTur == 'reddet') {
        data.red_nedeni = $('#red_nedeni').val();
        data.red_aciklama = $('#red_aciklama').val();
    }

    $.ajax({
        type: 'POST',
        url: 'admin_islem.php',
        data: data,
        dataType: 'json',
        success: function(res) {
            if (res.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'index.php?sayfa=urun_onay';
                });
            } else {
                Swal.fire('Hata!', res.message, 'error');
            }
        }
    });
}
</script>
