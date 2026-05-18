<?php
if (!$isLoggedIn) {
    echo "<script>window.location.href='index.php?sayfa=giris';</script>";
    exit;
}

$uye_id = $user['id'];

// Kayıtlı Adresleri Çek
$adres_res = $db->query("SELECT * FROM t_adres WHERE uye_ID = $uye_id");
$adresler = [];
while($a = $adres_res->fetch_assoc()) $adresler[] = $a;

// Kayıtlı Kartları Çek
$kart_res = $db->query("SELECT * FROM t_kredi_karti WHERE uye_ID = $uye_id");
$kartlar = [];
while($k = $kart_res->fetch_assoc()) $kartlar[] = $k;

// Sepet Özetini Hesapla
$sepet_res = $db->query("SELECT s.*, u.fiyat FROM t_sepet s JOIN t_urun u ON s.urun_ID = u.id WHERE s.uye_ID = $uye_id");
$ara_toplam = 0;
$toplam_adet = 0;
while($s = $sepet_res->fetch_assoc()) {
    $ara_toplam += ($s['fiyat'] * $s['adet']);
    $toplam_adet += $s['adet'];
}

if ($toplam_adet == 0) {
    echo "<div class='ky-container my-5 alert alert-warning text-center'>Sepetiniz boş olduğu için ödeme sayfasına erişemezsiniz. <br><a href='index.php?sayfa=anasayfa' class='btn btn-warning mt-3'>Alışverişe Başla</a></div>";
    return;
}

$kargo_ucreti = $ara_toplam > 1000 ? 0 : 50;
$genel_toplam = $ara_toplam + $kargo_ucreti;
?>

<style>
    /* Gerekli ek stiller */
    .address-select-box { border: 2px solid #e2e8f0; padding: 20px; border-radius: 12px; cursor: pointer; transition: all 0.3s; position: relative; height: 100%; background: #fff; }
    .address-select-box.active { border-color: var(--navy); background: #f8fafc; }
    .address-title { display: flex; justify-content: space-between; align-items: center; font-weight: 700; color: var(--navy); }
    .checkout-step-card { background: #fff; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    .step-header { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }
    .step-number { width: 35px; height: 35px; background: var(--navy); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; }
    .step-title { font-size: 1.25rem; font-weight: 700; color: var(--navy); margin: 0; }
    .cc-wrapper { background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; }
    .cc-label { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px; display: block; }
    .cc-input { width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; color: var(--navy); margin-bottom: 20px; }
    .cc-input:focus { border-color: var(--navy); outline: none; box-shadow: 0 0 0 3px rgba(29, 45, 68, 0.1); }
    .saved-cards-dropdown { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 25px; font-weight: 600; }
</style>

<main class="ky-container my-5" style="min-height: 60vh;">
    
    <div class="row g-4">
        
        <!-- SOL TARAFI: ADRES VE ÖDEME FORMLARI -->
        <div class="col-lg-8">
            
            <!-- ADIM 1: TESLİMAT ADRESİ -->
            <div class="checkout-step-card">
                <div class="step-header">
                    <div class="step-number">1</div>
                    <h2 class="step-title">Teslimat Adresi</h2>
                </div>
                
                <div class="row g-3">
                    <?php if(!empty($adresler)): ?>
                        <?php foreach($adresler as $index => $adr): ?>
                            <div class="col-md-6">
                                <div class="address-select-box <?= $index === 0 ? 'active' : '' ?>" data-id="<?= $adr['id'] ?>" onclick="selectAddress(this)">
                                    <div class="address-title">
                                        <span><i class="bi bi-house-door-fill text-muted me-1"></i> <?= htmlspecialchars($adr['adres_basligi']) ?></span>
                                        <?php if($index === 0): ?>
                                            <i class="bi bi-check-circle-fill text-success" style="font-size: 1.1rem;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="address-detail mt-2">
                                        <strong><?= htmlspecialchars($adr['ad'] . ' ' . $adr['soyad']) ?></strong><br>
                                        <?= htmlspecialchars($adr['tam_adres']) ?><br>
                                        <?= htmlspecialchars($adr['ilce'] . ' / ' . $adr['il']) ?><br>
                                        <?= htmlspecialchars($adr['telefon']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-3">
                            <p class="text-muted">Kayıtlı adresiniz bulunmuyor.</p>
                            <a href="index.php?sayfa=profil&tab=adres" class="btn btn-sm btn-primary">Yeni Adres Ekle</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <input type="hidden" id="selected_address_id" value="<?= !empty($adresler) ? $adresler[0]['id'] : '' ?>">
            </div>

            <!-- ADIM 2: ÖDEME BİLGİLERİ -->
            <div class="checkout-step-card">
                <div class="step-header">
                    <div class="step-number">2</div>
                    <h2 class="step-title">Ödeme Yöntemi</h2>
                </div>
                
                <?php if(!empty($kartlar)): ?>
                    <select class="saved-cards-dropdown" onchange="fillCardDetails(this.value)">
                        <option value="">Kayıtlı Kartlarımdan Seçin...</option>
                        <?php foreach($kartlar as $k): ?>
                            <option value="<?= $k['id'] ?>" data-name="<?= $k['kart_isim'] ?>" data-no="<?= $k['kart_numarasi'] ?>" data-skt="<?= $k['son_kullanma'] ?>">
                                <?= htmlspecialchars($k['kart_baslik']) ?> - <?= substr($k['kart_numarasi'], 0, 4) ?> **** **** <?= substr($k['kart_numarasi'], -4) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <h5 class="fw-bold mb-3" style="color:var(--navy);">Banka veya Kredi Kartı</h5>
                
                <div class="cc-wrapper">
                    <div class="row">
                        <div class="col-12 cc-form-group">
                            <label class="cc-label">Kart Üzerindeki İsim</label>
                            <input type="text" class="cc-input" id="cc-name" placeholder="AD SOYAD">
                        </div>
                        <div class="col-12 cc-form-group">
                            <label class="cc-label">Kart Numarası</label>
                            <div class="position-relative">
                                <input type="text" class="cc-input" id="cc-number" placeholder="0000 0000 0000 0000" maxlength="19">
                                <i class="bi bi-credit-card-2-front position-absolute" style="right: 15px; top: 12px; font-size: 1.2rem; color: #94a3b8;"></i>
                            </div>
                        </div>
                        <div class="col-6 cc-form-group">
                            <label class="cc-label">Son Kullanma Tarihi</label>
                            <input type="text" class="cc-input" id="cc-exp" placeholder="AA / YY" maxlength="5">
                        </div>
                        <div class="col-6 cc-form-group">
                            <label class="cc-label">CVV / CVC</label>
                            <input type="password" class="cc-input" id="cc-cvv" placeholder="•••" maxlength="3">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- SAĞ TARAFI: SİPARİŞ ÖZETİ -->
        <div class="col-lg-4">
            <div class="summary-card">
                <h2 class="summary-title">Sipariş Özeti</h2>
                
                <div class="summary-row">
                    <span>Ürün Toplamı (<?= $toplam_adet ?> Adet)</span>
                    <span><?= number_format($ara_toplam, 2, ',', '.') ?> TL</span>
                </div>
                
                <div class="summary-row">
                    <span>Kargo Ücreti</span>
                    <span><?= number_format($kargo_ucreti, 2, ',', '.') ?> TL</span>
                </div>

                <?php if($kargo_ucreti == 0): ?>
                    <div class="summary-row text-success">
                        <span>Ücretsiz Kargo</span>
                        <span>-50,00 TL</span>
                    </div>
                <?php endif; ?>

                <div class="summary-row total-row">
                    <span>Ödenecek Tutar</span>
                    <span><?= number_format($genel_toplam, 2, ',', '.') ?> TL</span>
                </div>

                <!-- Sözleşme Onayları -->
                <div class="mt-4 mb-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="chk-sozlesme" checked style="accent-color: var(--gold);">
                        <label class="form-check-label text-muted" for="chk-sozlesme" style="font-size:0.8rem;">
                            <a href="#" class="text-primary text-decoration-none">Mesafeli Satış Sözleşmesi</a>'ni ve <a href="#" class="text-primary text-decoration-none">Ön Bilgilendirme Formu</a>'nu okudum, onaylıyorum.
                        </label>
                    </div>
                </div>

                <button class="btn-pay w-100" onclick="siparişiTamamla(this)">
                    <i class="bi bi-lock-fill"></i> Siparişi Onayla
                </button>

                <div class="secure-badge mt-3 text-center">
                    <i class="bi bi-shield-lock-fill text-success fs-5"></i> 256-Bit SSL Güvenli Ödeme
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    // Adres Seçimi
    function selectAddress(element) {
        document.querySelectorAll('.address-select-box').forEach(box => {
            box.classList.remove('active');
            const check = box.querySelector('.bi-check-circle-fill');
            if(check) check.remove();
        });

        element.classList.add('active');
        element.querySelector('.address-title').innerHTML += '<i class="bi bi-check-circle-fill text-success" style="font-size: 1.1rem;"></i>';
        document.getElementById('selected_address_id').value = element.getAttribute('data-id');
    }

    // Kayıtlı Kart Seçimi
    function fillCardDetails(val) {
        const select = document.querySelector('.saved-cards-dropdown');
        const opt = select.options[select.selectedIndex];
        if(val) {
            document.getElementById('cc-name').value = opt.getAttribute('data-name');
            document.getElementById('cc-number').value = opt.getAttribute('data-no');
            document.getElementById('cc-exp').value = opt.getAttribute('data-skt');
            document.getElementById('cc-cvv').focus();
        } else {
            document.getElementById('cc-name').value = '';
            document.getElementById('cc-number').value = '';
            document.getElementById('cc-exp').value = '';
            document.getElementById('cc-cvv').value = '';
        }
    }

    // Sipariş Tamamlama AJAX
    function siparişiTamamla(btn) {
        if(!document.getElementById('chk-sozlesme').checked) {
            alert("Lütfen Sözleşmeleri Onaylayın.");
            return;
        }

        const addressId = document.getElementById('selected_address_id').value;
        if(!addressId) {
            alert("Lütfen bir teslimat adresi seçin.");
            return;
        }
        
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> İşleniyor...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('islem', 'siparis_tamamla');
        formData.append('adres_id', addressId);

        fetch('data.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                setTimeout(() => {
                    document.querySelector('main').innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-patch-check-fill text-success" style="font-size: 6rem;"></i>
                        <h1 class="fw-bold mt-4" style="color:var(--navy);">Siparişiniz Alındı!</h1>
                        <p class="fs-5 text-muted mt-2">Ödemeniz başarıyla gerçekleşti. Bizi tercih ettiğiniz için teşekkür ederiz.</p>
                        <div class="mt-4">
                            <strong class="d-block" style="font-size: 1.2rem; color:var(--navy);">Sipariş Numaranız: ${data.siparis_no}</strong>
                            <p class="text-muted mt-2">Sipariş detaylarınızı profilinizden güncel olarak takip edebilirsiniz.</p>
                        </div>
                        <a href="index.php?sayfa=profil&tab=siparis" class="btn btn-outline-primary fw-bold mt-4 px-4 py-2" style="border-color:var(--navy); color:var(--navy);">Siparişimi Takip Et</a>
                        <a href="index.php?sayfa=anasayfa" class="btn fw-bold mt-4 ms-2 px-4 py-2" style="background-color:var(--gold); color:var(--navy);">Alışverişe Devam</a>
                    </div>
                    `;
                }, 1500);
            } else {
                alert("Hata: " + data.message);
                btn.innerHTML = '<i class="bi bi-lock-fill"></i> Siparişi Onayla';
                btn.disabled = false;
            }
        })
        .catch(err => {
            alert("Sistem hatası oluştu.");
            btn.innerHTML = '<i class="bi bi-lock-fill"></i> Siparişi Onayla';
            btn.disabled = false;
        });
    }

    // CC Formatlama
    document.getElementById('cc-number').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
    });
    document.getElementById('cc-exp').addEventListener('input', function (e) {
        let v = e.target.value.replace(/[^\d]/g, '');
        if(v.length >= 2) e.target.value = v.substring(0,2) + '/' + v.substring(2,4);
        else e.target.value = v;
    });
</script>
