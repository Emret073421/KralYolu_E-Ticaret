<?php
$satici_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// FIX: t_satici içinde tüm bilgiler mevcut, join gereksiz
$sql = "SELECT * FROM t_satici WHERE id = $satici_id";
$res = $db->query($sql);

if (!$res || $res->num_rows == 0) {
    echo "<div class='alert alert-danger'>Satıcı bulunamadı.</div>";
    return;
}

$s = $res->fetch_assoc();
?>

<!-- Üst Başlık ve Geri Dön -->
<header class="d-flex align-items-center gap-3 mb-4">
    <a href="index.php?sayfa=satici_onay" class="btn btn-outline-secondary btn-sm" style="border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-arrow-left mt-1"></i>
    </a>
    <div>
        <h2 class="m-0 fw-bold" style="color:#0f172a;">Satıcı Başvuru Detayı</h2>
        <span class="text-muted" style="font-size:0.9rem;">Başvuru yapan mağazanın ticari bilgilerini doğrulayın.</span>
    </div>
</header>

<div class="row g-4 position-relative">
    
    <!-- SOL SÜTUN: Mağaza ve Ticari Bilgiler -->
    <div class="col-lg-7">
        <div class="info-card shadow-sm">
            <h5 class="fw-bold mb-4 border-bottom pb-2 text-primary"><i class="bi bi-building"></i> Ticari ve Mağaza Bilgileri</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-label">Mağaza Adı (Görünen)</div>
                    <div class="info-value"><?= htmlspecialchars($s['kurum_adi']) ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Şirket Türü</div>
                    <div class="info-value"><?= htmlspecialchars($s['sirket_turu'] ?? 'Belirtilmemiş') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Vergi Numarası / Vergi Dairesi</div>
                    <div class="info-value"><?= htmlspecialchars($s['vergi_no'] ?? 'Belirtilmemiş') ?> / <?= htmlspecialchars($s['vergi_dairesi'] ?? 'Belirtilmemiş') ?></div>
                </div>
                <div class="col-12">
                    <div class="info-label">Kayıtlı İş Adresi</div>
                    <div class="info-value"><?= htmlspecialchars($s['kurum_adres'] ?? 'Belirtilmemiş') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Mersis No</div>
                    <div class="info-value"><?= htmlspecialchars($s['mersis_no'] ?? 'Belirtilmemiş') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Kep Adresi</div>
                    <div class="info-value"><?= htmlspecialchars($s['kep_adresi'] ?? 'Belirtilmemiş') ?></div>
                </div>
            </div>

            <h5 class="fw-bold mt-4 mb-4 border-bottom pb-2 text-primary"><i class="bi bi-person-lines-fill"></i> Yetkili ve İletişim Bilgileri</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-label">Yönetici Ad Soyad</div>
                    <div class="info-value"><?= htmlspecialchars($s['ad'] . ' ' . $s['soyad']) ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Telefon Numarası</div>
                    <div class="info-value"><?= htmlspecialchars($s['telefon']) ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">E-Posta Adresi</div>
                    <div class="info-value"><?= htmlspecialchars($s['e_posta']) ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Banka IBAN (Hakedişler İçin)</div>
                    <div class="info-value text-break"><?= htmlspecialchars($s['iban'] ?? 'Belirtilmemiş') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SAĞ SÜTUN: Onay Ve Notlar -->
    <div class="col-lg-5">
        <div class="info-card shadow-sm h-100 d-flex flex-column">
            <h5 class="fw-bold mb-4 border-bottom pb-2 text-danger"><i class="bi bi-shield-check"></i> Onay Yönetimi</h5>
            
            <p class="text-muted small mb-4">
                Yukarıdaki ticari bilgilerin doğruluğunu beyan edilen vergi numarası ve ünvan üzerinden kontrol ediniz. Kayıtlarda bir usulsüzlük yoksa mağazayı onaylayabilirsiniz.
            </p>

            <div class="mb-4">
                <label class="form-label fw-bold small">Yönetici Notu (Opsiyonel)</label>
                <textarea class="form-control" id="adminNot" rows="3" placeholder="Satıcıya iletilecek not..."></textarea>
            </div>

            <!-- AKSİYONLAR -->
            <div class="action-container mt-auto" style="position:static; box-shadow:none; padding:0; border:none;">
                <div class="w-100 d-flex flex-column gap-2">
                     <button type="button" onclick="islemYap(<?= $s['id'] ?>, 'onayla')" class="btn btn-success fw-bold py-3 w-100 shadow-sm text-white" style="background:#10b981; border:none;">
                        <i class="bi bi-check2-circle me-1"></i> Mağazayı Aktifleştir (Onayla)
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger fw-bold py-3 w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i> Başvuruyu Reddet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reddetme Modalı -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-octagon-fill"></i> Başvuruyu Reddediyorsunuz</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <p class="text-muted fw-medium mb-3">Bu mağaza sisteme kabul edilmeyecek. Lütfen temel reddedilme nedenini seçin:</p>
        <select class="form-select mb-3" id="red_nedeni">
            <option>Sahte / Geçersiz Belge Kullanımı</option>
            <option>Yasaklı Ürün / İş Modeli Beyanı</option>
            <option>Ticari Faaliyet Durumu (Kapanmış Şirket)</option>
            <option>Güvenlik / Kara Liste Kontrolü</option>
            <option>Diğer</option>
        </select>
        <textarea class="form-control" id="red_aciklama" rows="3" placeholder="Ek açıklama (Satıcıya iletilecek)..."></textarea>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">İptal</button>
        <button type="button" onclick="islemYap(<?= $s['id'] ?>, 'reddet')" class="btn btn-danger fw-bold">Kalıcı Olarak Reddet</button>
      </div>
    </div>
  </div>
</div>

<script>
function islemYap(id, islemTur) {
    let not = $('#adminNot').val();
    let data = {
        islem: 'satici_onay_islem',
        id: id,
        tur: islemTur,
        not: not
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
                    window.location.href = 'index.php?sayfa=satici_onay';
                });
            } else {
                Swal.fire('Hata!', res.message, 'error');
            }
        }
    });
}
</script>
