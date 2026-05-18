<?php
// Kullanıcı girişi kontrolü
if (!$isLoggedIn) {
    echo "<script>window.location.href='index.php?sayfa=giris';</script>";
    exit;
}

$uye_id = $user['id'];

// Siparişleri çekelim
$siparisler_query = "SELECT s.*, 
                    (SELECT COUNT(*) FROM t_siparis_detay WHERE siparis_ID = s.id) as urun_sayisi,
                    (SELECT u.urun_adi FROM t_siparis_detay sd JOIN t_urun u ON sd.urun_ID = u.id WHERE sd.siparis_ID = s.id LIMIT 1) as ilk_urun_adi,
                    (SELECT f.foto_url FROM t_siparis_detay sd JOIN t_foto f ON sd.urun_ID = f.urun_ID WHERE sd.siparis_ID = s.id AND f.foto_sira = 1 LIMIT 1) as ilk_urun_foto
                    FROM t_siparis s 
                    WHERE s.uye_ID = $uye_id 
                    ORDER BY s.siparis_tarih DESC";
$siparisler_res = $db->query($siparisler_query);

// Adresleri çekelim
$adresler_res = $db->query("SELECT * FROM t_adres WHERE uye_ID = $uye_id");

// Kartları çekelim
$kartlar_res = $db->query("SELECT * FROM t_kredi_karti WHERE uye_ID = $uye_id");

// Baş harfleri alalım
$bas_harfler = mb_strtoupper(mb_substr($user['ad'], 0, 1) . mb_substr($user['soyad'], 0, 1), "UTF-8");

// Aktif tab'ı belirle (URL'den gelen tab parametresi varsa onu kullan, yoksa siparişleri kullan)
$activeTab = $_GET['tab'] ?? 'siparis';
?>

<div class="ky-container my-5">
    <div class="profile-layout">
        
        <!-- SOL MENÜ SİDEBAR -->
        <aside class="profile-sidebar">
            <div class="profile-user-greeting">
                <div class="profile-avatar">
                    <?= $bas_harfler ?>
                </div>
                <div>
                    <span style="font-size:0.85rem; color:#64748b; font-weight:600;">Merhaba,</span><br>
                    <strong style="color: var(--navy); font-size:1.1rem;"><?= htmlspecialchars($user['ad'] . ' ' . $user['soyad']) ?></strong>
                </div>
            </div>

            <ul class="profile-menu">
                <li class="<?= $activeTab == 'siparis' ? 'active' : '' ?>" onclick="switchTab(this, 'tab-siparis')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    Tüm Siparişlerim
                </li>
                <li class="<?= $activeTab == 'bilgiler' ? 'active' : '' ?>" onclick="switchTab(this, 'tab-bilgiler')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Kullanıcı Bilgilerim
                </li>
                <li class="<?= $activeTab == 'adres' ? 'active' : '' ?>" onclick="switchTab(this, 'tab-adres')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    Adres Bilgilerim
                </li>
                <li class="<?= $activeTab == 'kartlar' ? 'active' : '' ?>" onclick="switchTab(this, 'tab-kartlar')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                    Ödeme Kartlarım
                </li>
                <li class="logout-btn" onclick="if(confirm('Çıkış yapmak istediğinize emin misiniz?')) window.location.href='data.php?logout=1'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Güvenli Çıkış Yap
                </li>
            </ul>
        </aside>

        <!-- SAĞ İÇERİK ALANI -->
        <section class="profile-content">
            
            <?php if (isset($_GET['basari'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        if ($_GET['basari'] == 'guncelle') echo "Bilgileriniz başarıyla güncellendi.";
                        elseif ($_GET['basari'] == 'adres') echo "Adres başarıyla kaydedildi.";
                        elseif ($_GET['basari'] == 'kart') echo "Kart başarıyla eklendi.";
                        elseif ($_GET['basari'] == 'silindi') echo "Kayıt başarıyla silindi.";
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- 1. SİPARİŞLERİM TAB -->
            <div id="tab-siparis" class="profile-tab <?= $activeTab == 'siparis' ? 'active' : '' ?>">
                <h2 class="tab-title">Siparişlerim</h2>
                
                <?php if ($siparisler_res && $siparisler_res->num_rows > 0): ?>
                    <?php while ($siparis = $siparisler_res->fetch_assoc()): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <div class="order-info mb-1">
                                        <span>Sipariş Tarihi</span>
                                        <strong><?= date('d F Y', strtotime($siparis['siparis_tarih'])) ?></strong>
                                    </div>
                                </div>
                                <div>
                                    <div class="order-info mb-1">
                                        <span>Sipariş Özeti</span>
                                        <strong><?= $siparis['urun_sayisi'] ?> Ürün</strong>
                                    </div>
                                </div>
                                <div>
                                    <div class="order-info mb-1">
                                        <span>Toplam Tutar</span>
                                        <strong><?= number_format($siparis['toplam_tutar'], 2, ',', '.') ?> TL</strong>
                                    </div>
                                </div>
                                <button type="button" onclick="siparisDetayGoster(<?= $siparis['id'] ?>)" class="btn btn-sm btn-outline-secondary fw-bold px-3">Detayları Gör</button>
                            </div>
                            <div class="order-body flex-column flex-md-row gap-3">
                                <div class="order-product">
                                    <img src="<?= $siparis['ilk_urun_foto'] ?? 'https://via.placeholder.com/200' ?>" class="order-img" alt="Ürün">
                                    <div>
                                        <div class="order-status <?= $siparis['kargo_durum'] == 'Teslim Edildi' ? 'delivered' : '' ?> mb-2" style="width: max-content;">
                                            <i class="bi <?= $siparis['kargo_durum'] == 'Teslim Edildi' ? 'bi-check-circle-fill' : 'bi-truck' ?>"></i> 
                                            <?= $siparis['kargo_durum'] ?>
                                        </div>
                                        <h5 class="mb-1 fw-bold fs-6" style="color:var(--navy)"><?= htmlspecialchars($siparis['ilk_urun_adi']) ?> <?= $siparis['urun_sayisi'] > 1 ? '(+'.($siparis['urun_sayisi']-1).' ürün daha)' : '' ?></h5>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($siparis['kargo_durum'] == 'Teslim Edildi'): ?>
                                        <button class="btn btn-warning btn-sm fw-bold px-3" style="background-color:var(--gold); border:none; color:var(--navy);"><i class="bi bi-arrow-clockwise"></i> Tekrar Satın Al</button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-primary btn-sm fw-bold px-3" style="color:var(--navy); border-color:var(--navy);"><i class="bi bi-box-seam"></i> Kargo Takip</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info py-4 text-center">
                        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                        Henüz bir siparişiniz bulunmamaktadır.
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. BİLGİLERİM TAB -->
            <div id="tab-bilgiler" class="profile-tab <?= $activeTab == 'bilgiler' ? 'active' : '' ?>">
                <h2 class="tab-title">Kullanıcı Bilgilerim</h2>
                <form action="data.php" method="POST" style="max-width: 600px;">
                    <input type="hidden" name="islem" value="profil_guncelle">
                    <div class="row g-3">
                        <div class="col-md-6 ky-form-group">
                            <label for="isim">Adınız</label>
                            <input type="text" name="ad" id="isim" class="ky-form-control" value="<?= htmlspecialchars($user['ad']) ?>" required>
                        </div>
                        <div class="col-md-6 ky-form-group">
                            <label for="soyisim">Soyadınız</label>
                            <input type="text" name="soyad" id="soyisim" class="ky-form-control" value="<?= htmlspecialchars($user['soyad']) ?>" required>
                        </div>
                    </div>
                    <div class="ky-form-group">
                        <label for="email">E-Posta Adresi</label>
                        <input type="email" name="e_posta" id="email" class="ky-form-control" value="<?= htmlspecialchars($user['e_posta']) ?>" required>
                    </div>
                    <div class="ky-form-group">
                        <label for="gsm">Cep Telefonu</label>
                        <input type="tel" name="telefon" id="gsm" class="ky-form-control" value="<?= htmlspecialchars($user['telefon'] ?? '') ?>" placeholder="05XX XXX XX XX">
                    </div>
                    <hr class="my-4" style="border-color:#edf0f5;">
                    <h4 class="mb-3" style="color:var(--navy); font-size:1.1rem; font-weight:700;">Şifre Değişikliği</h4>
                    <div class="ky-form-group mb-3">
                        <label for="eski_sifre">Mevcut Şifreniz <span class="text-danger" title="Güvenliğiniz için mevcut şifrenizi girmelisiniz">*</span></label>
                        <input type="password" name="mevcut_parola" id="eski_sifre" class="ky-form-control" placeholder="Mevcut şifrenizi girin">
                    </div>
                    <div class="ky-form-group mb-4">
                        <label for="yeni_sifre">Yeni Şifre <span class="text-muted fw-normal" style="font-size:0.85rem;">(Değiştirmek istemiyorsanız boş bırakın)</span></label>
                        <input type="password" name="yeni_parola" id="yeni_sifre" class="ky-form-control" placeholder="••••••••">
                    </div>
                    <button type="submit" class="ky-btn-submit">
                        <i class="bi bi-save"></i> BİLGİLERİ GÜNCELLE
                    </button>
                </form>
            </div>

            <!-- 3. ADRESLER TAB -->
            <div id="tab-adres" class="profile-tab <?= $activeTab == 'adres' ? 'active' : '' ?>">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="tab-title m-0">Adres Bilgilerim</h2>
                </div>
                <div class="address-grid">
                    <div class="address-card address-add-new" data-bs-toggle="modal" data-bs-target="#adresModal" onclick="prepareAdresModal()">
                        <i class="bi bi-plus-circle" style="font-size: 2.5rem; margin-bottom: 10px;"></i>
                        Yeni Adres Ekle
                    </div>
                    <?php if ($adresler_res && $adresler_res->num_rows > 0): ?>
                        <?php while ($adres = $adresler_res->fetch_assoc()): ?>
                            <div class="address-card">
                                <div class="address-card-header">
                                    <i class="bi <?= strpos(strtolower($adres['adres_basligi']), 'ev') !== false ? 'bi-house-door-fill' : 'bi-building' ?>" style="color:var(--gold); font-size:1.4rem;"></i>
                                    <h4><?= htmlspecialchars($adres['adres_basligi']) ?></h4>
                                </div>
                                <div class="address-card-body">
                                    <strong><?= htmlspecialchars($adres['ad'] . ' ' . $adres['soyad']) ?></strong><br>
                                    <?= htmlspecialchars($adres['tam_adres']) ?><br>
                                    <?= htmlspecialchars($adres['ilce'] . ' / ' . $adres['il']) ?><br>
                                    <?= htmlspecialchars($adres['telefon']) ?>
                                </div>
                                <div class="address-card-footer">
                                    <a href="data.php?islem=adres_sil&id=<?= $adres['id'] ?>" class="address-action-btn delete" onclick="return confirm('Silmek istediğinize emin misiniz?')"><i class="bi bi-trash3"></i> Sil</a>
                                    <button class="address-action-btn" data-bs-toggle="modal" data-bs-target="#adresModal" onclick='prepareAdresModal(<?= json_encode($adres) ?>)'><i class="bi bi-pencil-square"></i> Düzenle</button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 4. KARTLARIM TAB -->
            <div id="tab-kartlar" class="profile-tab <?= $activeTab == 'kartlar' ? 'active' : '' ?>">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="tab-title m-0">Kayıtlı Kredi Kartlarım</h2>
                </div>
                <div class="address-grid">
                    <div class="address-card address-add-new" data-bs-toggle="modal" data-bs-target="#kartModal" onclick="prepareKartModal()">
                        <i class="bi bi-plus-circle" style="font-size: 2.5rem; margin-bottom: 10px;"></i>
                        Yeni Kart Ekle
                    </div>
                    <?php if ($kartlar_res && $kartlar_res->num_rows > 0): ?>
                        <?php while ($kart = $kartlar_res->fetch_assoc()): ?>
                            <div class="address-card" style="background: linear-gradient(135deg, #1d2d44, #0a1120); color:white; border:none; display:flex; flex-direction:column; justify-content:space-between; min-height:180px; padding:25px; box-shadow: 0 10px 20px rgba(0,0,0,0.15);">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span style="font-weight:800; font-size:1.1rem; color:var(--gold); letter-spacing:1px; font-style:italic;"><?= htmlspecialchars($kart['kart_baslik']) ?></span>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-link text-white p-0" data-bs-toggle="modal" data-bs-target="#kartModal" onclick='prepareKartModal(<?= json_encode($kart) ?>)'><i class="bi bi-pencil-square"></i></button>
                                        <a href="data.php?islem=kart_sil&id=<?= $kart['id'] ?>" class="text-danger" onclick="return confirm('Kartı silmek istediğinize emin misiniz?')"><i class="bi bi-trash3"></i></a>
                                    </div>
                                </div>
                                <div class="mb-3 text-center">
                                    <span style="font-family:monospace; font-size:1.4rem; letter-spacing:3px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                                        <?= substr($kart['kart_numarasi'], 0, 4) ?> •••• •••• <?= substr($kart['kart_numarasi'], -4) ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <div style="font-size:0.7rem; color:#94a3b8; text-transform:uppercase;">Kart Sahibi</div>
                                        <div style="font-weight:600; font-size:0.9rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"><?= htmlspecialchars($kart['kart_isim']) ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div style="font-size:0.7rem; color:#94a3b8; text-transform:uppercase;">SKT</div>
                                        <div style="font-weight:600; font-size:0.9rem;"><?= htmlspecialchars($kart['son_kullanma']) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

        </section>
    </div>
</div>

<!-- ADRES MODAL -->
<div class="modal fade" id="adresModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="data.php" method="POST" class="modal-content">
            <input type="hidden" name="islem" value="adres_kaydet">
            <input type="hidden" name="id" id="modal_adres_id" value="0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="adresModalLabel">Adres Bilgisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12 ky-form-group">
                        <label>Adres Başlığı (Örn: Ev, İş)</label>
                        <input type="text" name="adres_basligi" id="modal_adres_baslik" class="ky-form-control" required>
                    </div>
                    <div class="col-md-6 ky-form-group">
                        <label>Alıcı Adı</label>
                        <input type="text" name="ad" id="modal_adres_ad" class="ky-form-control" required>
                    </div>
                    <div class="col-md-6 ky-form-group">
                        <label>Alıcı Soyadı</label>
                        <input type="text" name="soyad" id="modal_adres_soyad" class="ky-form-control" required>
                    </div>
                    <div class="col-md-6 ky-form-group">
                        <label>Telefon</label>
                        <input type="tel" name="telefon" id="modal_adres_tel" class="ky-form-control" required>
                    </div>
                    <div class="col-md-6 ky-form-group">
                        <label>İl</label>
                        <select name="il" id="modal_adres_il" class="ky-form-control" required>
                            <option value="">İl Seçiniz</option>
                        </select>
                    </div>
                    <div class="col-md-6 ky-form-group">
                        <label>İlçe</label>
                        <select name="ilce" id="modal_adres_ilce" class="ky-form-control" required>
                            <option value="">Önce İl Seçiniz</option>
                        </select>
                    </div>
                    <div class="col-md-6 ky-form-group">
                        <label>Mahalle</label>
                        <select name="mahalle" id="modal_adres_mahalle" class="ky-form-control">
                            <option value="">Önce İlçe Seçiniz</option>
                        </select>
                    </div>
                    <div class="col-12 ky-form-group">
                        <label>Açık Adres (Sokak, Bina No, Daire)</label>
                        <textarea name="tam_adres" id="modal_adres_tam" class="ky-form-control" rows="3" placeholder="Sokak adı, bina no, daire no..." required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">İptal</button>
                <button type="submit" class="btn btn-warning fw-bold px-4" style="background-color:var(--gold); color:var(--navy); border:none;">KAYDET</button>
            </div>
        </form>
    </div>
</div>

<!-- KART MODAL -->
<div class="modal fade" id="kartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="data.php" method="POST" class="modal-content">
            <input type="hidden" name="islem" value="kart_kaydet">
            <input type="hidden" name="id" id="modal_kart_id" value="0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="kartModalLabel">Kart Bilgisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12 ky-form-group">
                        <label>Kart Başlığı (Örn: Maaş Kartı)</label>
                        <input type="text" name="kart_baslik" id="modal_kart_baslik" class="ky-form-control" required>
                    </div>
                    <div class="col-12 ky-form-group">
                        <label>Kart Üzerindeki İsim</label>
                        <input type="text" name="kart_isim" id="modal_kart_isim" class="ky-form-control" required>
                    </div>
                    <div class="col-12 ky-form-group">
                        <label>Kart Numarası</label>
                        <input type="text" name="kart_numarasi" id="modal_kart_no" class="ky-form-control" maxlength="16" placeholder="0000000000000000" required>
                    </div>
                    <div class="col-6 ky-form-group">
                        <label>Son Kullanma (AA/YY)</label>
                        <input type="text" name="son_kullanma" id="modal_kart_skt" class="ky-form-control" placeholder="01/28" required>
                    </div>
                    <div class="col-6 ky-form-group">
                        <label>CVV</label>
                        <input type="text" name="cvv" id="modal_kart_cvv" class="ky-form-control" maxlength="3" placeholder="000" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">İptal</button>
                <button type="submit" class="btn btn-warning fw-bold px-4" style="background-color:var(--gold); color:var(--navy); border:none;">KAYDET</button>
            </div>
        </form>
    </div>
</div>

<!-- SİPARİŞ DETAY MODAL -->
<div class="modal fade" id="siparisDetayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="siparisDetayModalLabel">Sipariş Detayı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="siparisDetayIcerik">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function siparisDetayGoster(siparis_id) {
        var myModal = new bootstrap.Modal(document.getElementById('siparisDetayModal'));
        myModal.show();
        
        document.getElementById('siparisDetayIcerik').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            </div>`;
            
        fetch('data.php?islem=get_siparis_detay&id=' + siparis_id)
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                var s = data.siparis;
                var urunlerHTML = '';
                data.urunler.forEach(function(u) {
                    urunlerHTML += `
                    <div class="d-flex mb-3 border-bottom pb-3">
                        <img src="${u.foto_url ? u.foto_url : 'https://via.placeholder.com/80'}" class="rounded" style="width:80px; height:80px; object-fit:cover; margin-right:15px; border: 1px solid #eee;">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1" style="color:var(--navy);">${u.urun_adi}</h6>
                            <div class="text-muted small mb-1">Adet: ${u.adet}</div>
                            <div class="fw-bold mt-1" style="color:var(--gold); font-size:1.1rem;">${parseFloat(u.birim_fiyat).toLocaleString('tr-TR', {minimumFractionDigits: 2})} TL</div>
                        </div>
                    </div>`;
                });
                
                var d = new Date(s.siparis_tarih);
                var aylar = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
                var tarih = d.getDate() + ' ' + aylar[d.getMonth()] + ' ' + d.getFullYear();
                
                document.getElementById('siparisDetayIcerik').innerHTML = `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Sipariş No</p>
                            <h6 class="fw-bold">#KRY-${s.id}</h6>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <p class="mb-1 text-muted small">Sipariş Tarihi</p>
                            <h6 class="fw-bold">${tarih}</h6>
                        </div>
                    </div>
                    
                    <div class="card mb-4 border-0" style="background-color: #f8fafc; border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <h6 class="fw-bold mb-3" style="color:var(--navy);"><i class="bi bi-geo-alt-fill me-2" style="color:var(--gold);"></i>Teslimat Adresi</h6>
                                    <p class="mb-0" style="font-size:0.9rem;"><strong>${s.adres_basligi || 'Adres Bilgisi'}</strong><br>${s.tam_adres || 'Belirtilmedi'}<br>${s.ilce || ''} / ${s.il || ''}</p>
                                </div>
                                <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                                    <h6 class="fw-bold mb-3" style="color:var(--navy);"><i class="bi bi-credit-card-fill me-2" style="color:var(--gold);"></i>Ödeme Bilgileri</h6>
                                    <p class="mb-1 text-muted" style="font-size:0.9rem;">Toplam Tutar</p>
                                    <h5 class="fw-bold" style="color:var(--navy);">${parseFloat(s.toplam_tutar).toLocaleString('tr-TR', {minimumFractionDigits: 2})} TL</h5>
                                    
                                    <div class="mt-3">
                                        <p class="mb-1 text-muted" style="font-size:0.9rem;">Sipariş Durumu</p>
                                        <span class="badge ${s.kargo_durum === 'Teslim Edildi' ? 'bg-success' : 'bg-warning text-dark'} px-3 py-2">${s.kargo_durum}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mb-3 border-bottom pb-2" style="color:var(--navy);">Sipariş Edilen Ürünler</h6>
                    <div class="urun-listesi mt-3" style="max-height:350px; overflow-y:auto; padding-right:10px;">
                        ${urunlerHTML}
                    </div>
                `;
            } else {
                document.getElementById('siparisDetayIcerik').innerHTML = `<div class="alert alert-danger">${data.message || 'Sipariş detayları alınamadı.'}</div>`;
            }
        })
        .catch(error => {
            document.getElementById('siparisDetayIcerik').innerHTML = `<div class="alert alert-danger">Bir hata oluştu. Lütfen tekrar deneyin.</div>`;
        });
    }

    function switchTab(element, tabId) {
        document.querySelectorAll('.profile-menu li').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.profile-tab').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        document.getElementById(tabId).classList.add('active');
        const tabName = tabId.replace('tab-', '');
        const newUrl = window.location.pathname + '?sayfa=profil&tab=' + tabName;
        window.history.pushState({path: newUrl}, '', newUrl);
    }

    // ========== İL / İLÇE / MAHALLE CASCADE DROPDOWN ==========
    function loadIller(selectedIl) {
        fetch('data.php?islem=get_iller')
        .then(r => r.json())
        .then(data => {
            var s = document.getElementById('modal_adres_il');
            s.innerHTML = '<option value="">İl Seçiniz</option>';
            data.forEach(function(il) {
                var o = document.createElement('option');
                o.value = il.il_adi;
                o.textContent = il.il_adi;
                o.dataset.ilId = il.id;
                if (selectedIl && il.il_adi === selectedIl) o.selected = true;
                s.appendChild(o);
            });
            if (selectedIl) {
                var sel = s.querySelector('option[selected]');
                if (sel && sel.dataset.ilId) loadIlceler(sel.dataset.ilId);
            }
        });
    }
    
    function loadIlceler(ilId, selectedIlce) {
        var s = document.getElementById('modal_adres_ilce');
        s.innerHTML = '<option value="">Yükleniyor...</option>';
        document.getElementById('modal_adres_mahalle').innerHTML = '<option value="">Önce İlçe Seçiniz</option>';
        fetch('data.php?islem=get_ilceler&il_id=' + ilId)
        .then(r => r.json())
        .then(data => {
            s.innerHTML = '<option value="">İlçe Seçiniz</option>';
            data.forEach(function(ilce) {
                var o = document.createElement('option');
                o.value = ilce.ilce_adi;
                o.textContent = ilce.ilce_adi;
                o.dataset.ilceId = ilce.id;
                if (selectedIlce && ilce.ilce_adi === selectedIlce) o.selected = true;
                s.appendChild(o);
            });
            if (selectedIlce) {
                var sel = s.querySelector('option[selected]');
                if (sel && sel.dataset.ilceId) loadMahalleler(sel.dataset.ilceId);
            }
        });
    }
    
    function loadMahalleler(ilceId, selectedMahalle) {
        var s = document.getElementById('modal_adres_mahalle');
        s.innerHTML = '<option value="">Yükleniyor...</option>';
        fetch('data.php?islem=get_mahalleler&ilce_id=' + ilceId)
        .then(r => r.json())
        .then(data => {
            s.innerHTML = '<option value="">Mahalle Seçiniz</option>';
            data.forEach(function(m) {
                var o = document.createElement('option');
                o.value = m.mahalle_adi;
                o.textContent = m.mahalle_adi;
                if (selectedMahalle && m.mahalle_adi === selectedMahalle) o.selected = true;
                s.appendChild(o);
            });
        });
    }
    
    document.getElementById('modal_adres_il').addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        if (opt.dataset.ilId) loadIlceler(opt.dataset.ilId);
        else {
            document.getElementById('modal_adres_ilce').innerHTML = '<option value="">Önce İl Seçiniz</option>';
            document.getElementById('modal_adres_mahalle').innerHTML = '<option value="">Önce İlçe Seçiniz</option>';
        }
    });
    
    document.getElementById('modal_adres_ilce').addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        if (opt.dataset.ilceId) loadMahalleler(opt.dataset.ilceId);
        else document.getElementById('modal_adres_mahalle').innerHTML = '<option value="">Önce İlçe Seçiniz</option>';
    });

    // ========== MODAL HAZIRLIK ==========
    function prepareAdresModal(data) {
        if (data) {
            loadIller(data.il);
            document.getElementById('modal_adres_id').value = data.id;
            document.getElementById('modal_adres_baslik').value = data.adres_basligi;
            document.getElementById('modal_adres_ad').value = data.ad;
            document.getElementById('modal_adres_soyad').value = data.soyad;
            document.getElementById('modal_adres_tel').value = data.telefon;
            document.getElementById('modal_adres_tam').value = data.tam_adres;
            setTimeout(function() {
                var ilSel = document.getElementById('modal_adres_il');
                for (var i = 0; i < ilSel.options.length; i++) {
                    if (ilSel.options[i].value === data.il) {
                        ilSel.selectedIndex = i;
                        var ilId = ilSel.options[i].dataset.ilId;
                        if (ilId) loadIlceler(ilId, data.ilce);
                        break;
                    }
                }
            }, 500);
        } else {
            loadIller();
            document.getElementById('modal_adres_id').value = 0;
            document.getElementById('modal_adres_baslik').value = '';
            document.getElementById('modal_adres_ad').value = '';
            document.getElementById('modal_adres_soyad').value = '';
            document.getElementById('modal_adres_tel').value = '';
            document.getElementById('modal_adres_ilce').innerHTML = '<option value="">Önce İl Seçiniz</option>';
            document.getElementById('modal_adres_mahalle').innerHTML = '<option value="">Önce İlçe Seçiniz</option>';
            document.getElementById('modal_adres_tam').value = '';
        }
    }

    function prepareKartModal(data) {
        if (data) {
            document.getElementById('modal_kart_id').value = data.id;
            document.getElementById('modal_kart_baslik').value = data.kart_baslik;
            document.getElementById('modal_kart_isim').value = data.kart_isim;
            document.getElementById('modal_kart_no').value = data.kart_numarasi;
            document.getElementById('modal_kart_skt').value = data.son_kullanma;
            document.getElementById('modal_kart_cvv').value = data.cvv;
        } else {
            document.getElementById('modal_kart_id').value = 0;
            document.querySelectorAll('#kartModal input').forEach(el => {
                if(el.name !== 'islem' && el.name !== 'id') el.value = '';
            });
        }
    }
</script>

