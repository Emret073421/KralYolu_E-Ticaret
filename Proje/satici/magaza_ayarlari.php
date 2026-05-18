<?php
$satici_id = $satici['id'];
$satici_detay = $db->query("SELECT * FROM t_satici WHERE id = $satici_id")->fetch_assoc();
?>
<style>
    .form-label {
        font-weight: 700;
        color: var(--navy);
        font-size: 0.9rem;
    }
    .form-control, .form-select {
        border: 1px solid #cbd5e1;
        padding: 10px 15px;
        border-radius: 8px;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        border-color: var(--gold);
    }
    .setting-section {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
    }
    .setting-header {
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    .setting-icon {
        width: 45px;
        height: 45px;
        background: rgba(212, 175, 55, 0.1);
        color: var(--gold);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.3rem;
    }
</style>

<header class="seller-header">
    <div>
        <h2 class="m-0 fw-bold" style="color:var(--navy);">Mağaza ve Profil Ayarları</h2>
        <span class="text-muted" style="font-size:0.9rem;">Temas ve güvenlik bilgilerinizi güncel tutarak müşterilerinize güven verin.</span>
    </div>
</header>

<form id="magazaAyarlariForm">
    <input type="hidden" name="islem" value="magaza_ayarlarini_guncelle">
    <div class="row g-4">
        
        <!-- SOL SÜTUN -->
        <div class="col-lg-8">
            
            <!-- Mağaza Profil Bilgileri -->
            <div class="setting-section">
                <div class="setting-header">
                    <div class="setting-icon"><i class="bi bi-building"></i></div>
                    <div>
                        <h5 class="fw-bold m-0" style="color:var(--navy);">Kurumsal Profil Bilgileri</h5>
                        <small class="text-muted">Mağazanızın sitede görünen adı ve resmi ticari bilgileriniz.</small>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Mağaza Adı (Görünen)</label>
                        <input type="text" name="kurum_adi" class="form-control fw-bold" value="<?= htmlspecialchars($satici_detay['kurum_adi'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ticari Ünvan (Resmi)</label>
                        <input type="text" name="ticari_unvan" class="form-control" value="<?= htmlspecialchars($satici_detay['ticari_unvan'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Şirket Türü</label>
                        <?php $tur = $satici_detay['sirket_turu'] ?? ''; ?>
                        <select name="sirket_turu" class="form-select">
                            <option <?= $tur == 'Şahıs Şirketi' ? 'selected' : '' ?>>Şahıs Şirketi</option>
                            <option <?= $tur == 'Limited Şirket (Ltd. Şti.)' ? 'selected' : '' ?>>Limited Şirket (Ltd. Şti.)</option>
                            <option <?= $tur == 'Anonim Şirket (A.Ş.)' ? 'selected' : '' ?>>Anonim Şirket (A.Ş.)</option>
                            <option <?= $tur == 'Kolektif/Komandit Şirket' ? 'selected' : '' ?>>Kolektif/Komandit Şirket</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Vergi Dairesi / No</label>
                        <div class="input-group">
                            <input type="text" name="vergi_dairesi" class="form-control w-50" placeholder="Dairesi" value="<?= htmlspecialchars($satici_detay['vergi_dairesi'] ?? '') ?>">
                            <input type="text" name="vergi_no" class="form-control w-50" placeholder="Numarası" value="<?= htmlspecialchars($satici_detay['vergi_no'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="form-label">Mersis No</label>
                        <input type="text" name="mersis_no" class="form-control" value="<?= htmlspecialchars($satici_detay['mersis_no'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">KEP Adresi</label>
                        <input type="text" name="kep_adresi" class="form-control" value="<?= htmlspecialchars($satici_detay['kep_adresi'] ?? '') ?>">
                    </div>

                    <div class="col-md-6 mt-4">
                        <label class="form-label">Yetkili Ad Soyad</label>
                        <input type="text" name="yetkili_ad_soyad" class="form-control" value="<?= htmlspecialchars(($satici_detay['ad'] ?? '') . ' ' . ($satici_detay['soyad'] ?? '')) ?>" required>
                    </div>
                    <div class="col-md-6 mt-4">
                        <label class="form-label">Kurumsal Telefon</label>
                        <input type="text" name="kurumsal_telefon" class="form-control" value="<?= htmlspecialchars($satici_detay['kurumsal_telefon'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Finansal Bilgiler -->
            <div class="setting-section">
                <div class="setting-header">
                    <div class="setting-icon"><i class="bi bi-bank"></i></div>
                    <div>
                        <h5 class="fw-bold m-0" style="color:var(--navy);">Banka ve Ödeme Bilgileri</h5>
                        <small class="text-muted">Hakediş ödemelerinizin yatırılacağı IBAN adresi.</small>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">IBAN Numarası</label>
                        <input type="text" name="iban" class="form-control fw-bold" value="<?= htmlspecialchars($satici_detay['iban'] ?? '') ?>">
                        <small class="text-muted">Lütfen TR ile başlayan 26 haneli IBAN'ınızı boşluklu veya boşluksuz giriniz.</small>
                    </div>
                </div>
            </div>

            <!-- Mağaza Fiziksel Adres -->
            <div class="setting-section">
                <div class="setting-header">
                    <div class="setting-icon"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <h5 class="fw-bold m-0" style="color:var(--navy);">Fiziksel Mağaza / Depo Adresi</h5>
                        <small class="text-muted">Müşteri iadelerinin gönderileceği resmi yasal adresiniz.</small>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Şehir (İl)</label>
                        <?php $sehir = $satici_detay['il'] ?? ''; ?>
                        <select name="sehir" class="form-select">
                            <option value="" <?= empty($sehir) ? 'selected' : '' ?>>Seçiniz</option>
                            <option value="İzmir" <?= $sehir == 'İzmir' ? 'selected' : '' ?>>İzmir</option>
                            <option value="İstanbul" <?= $sehir == 'İstanbul' ? 'selected' : '' ?>>İstanbul</option>
                            <option value="Ankara" <?= $sehir == 'Ankara' ? 'selected' : '' ?>>Ankara</option>
                            <!-- Daha fazla şehir eklenebilir -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">İlçe</label>
                        <input type="text" name="ilce" class="form-control" value="<?= htmlspecialchars($satici_detay['ilce'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mahalle</label>
                        <input type="text" name="mahalle" class="form-control" value="<?= htmlspecialchars($satici_detay['mahalle'] ?? '') ?>">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">Açık Adres / Sokak / Kapı No</label>
                        <textarea name="kurum_adres" class="form-control" rows="3"><?= htmlspecialchars($satici_detay['kurum_adres'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

        </div>

        <!-- SAĞ SÜTUN -->
        <div class="col-lg-4">
            
            <div class="setting-section">
                <div class="setting-header">
                    <div class="setting-icon"><i class="bi bi-shield-lock"></i></div>
                    <div>
                        <h5 class="fw-bold m-0" style="color:var(--navy);">Güvenlik</h5>
                        <small class="text-muted">Parola işlemleri</small>
                    </div>
                </div>
                
                <div class="alert alert-warning border-0 small px-3 py-2" role="alert">
                    <i class="bi bi-info-circle-fill me-1"></i> Şifrenizi değiştirmek istemiyorsanız bu alanları boş bırakın.
                </div>

                <div class="mb-3 mt-4">
                    <label class="form-label">Mevcut Şifreniz</label>
                    <input type="password" name="mevcut_parola" class="form-control" placeholder="Kimlik doğrulaması için...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Yeni Şifreniz</label>
                    <input type="password" name="yeni_parola" id="yeni_parola" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Yeni Şifreniz (Tekrar)</label>
                    <input type="password" id="yeni_parola_tekrar" class="form-control">
                </div>
            </div>

            <!-- Kaydet Butonu -->
            <div class="setting-section text-center p-4">
                <p class="text-muted small mb-3">Yaptığınız değişikliklerin sisteme yansıması için ayarları kaydetmeniz gerekmektedir.</p>
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 fs-6" style="background-color: var(--navy); border-color: var(--navy);">
                    <i class="bi bi-floppy me-1"></i> Ayarları Kaydet
                </button>
            </div>

        </div>

    </div>
</form>

<script>
    $('#magazaAyarlariForm').on('submit', function(e) {
        e.preventDefault();
        
        // Şifre kontrolü
        let yp = $('#yeni_parola').val();
        let ypt = $('#yeni_parola_tekrar').val();
        
        if (yp !== '' && yp !== ypt) {
            Swal.fire('Hata!', 'Yeni şifreleriniz birbiriyle uyuşmuyor.', 'error');
            return;
        }

        $.ajax({
            url: 'satici_islem.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    Swal.fire('Başarılı!', data.message, 'success').then(() => {
                        // Sayfayı yenile
                        location.reload();
                    });
                } else {
                    Swal.fire('Hata!', data.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Hata!', 'Sunucuyla iletişim kurulurken bir sorun oluştu.', 'error');
            }
        });
    });
</script>
