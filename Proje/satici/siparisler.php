<?php
$satici_id = $satici['id'];

$siparisler = $db->query("SELECT s.id as siparis_no, u.urun_adi, u.fiyat, sd.adet, sd.birim_fiyat, 
                                 uye.ad, uye.soyad, s.siparis_tarih, s.kargo_durum, a.tam_adres, a.il, a.ilce
                          FROM t_siparis_detay sd
                          JOIN t_urun u ON sd.urun_ID = u.id
                          JOIN t_siparis s ON sd.siparis_ID = s.id
                          JOIN t_uye uye ON s.uye_ID = uye.id
                          LEFT JOIN t_adres a ON s.adres_ID = a.id
                          WHERE u.satici_ID = $satici_id
                          ORDER BY s.siparis_tarih DESC");
?>

<!-- Üst Başlık -->
<header class="seller-header">
    <div>
        <h2 class="m-0 fw-bold" style="color:var(--navy);">Gelen Siparişler</h2>
        <span class="text-muted" style="font-size:0.9rem;">Müşterilerinizden gelen siparişleri onaylayın veya kargo durumlarını güncelleyin.</span>
    </div>
</header>

<!-- Siparişler Tablosu -->
<div class="bg-white p-4 rounded-3 shadow-sm border border-light">
    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Sipariş No</th>
                    <th>Ürün Bilgisi</th>
                    <th>Müşteri</th>
                    <th>Adet</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($siparisler && $siparisler->num_rows > 0): ?>
                    <?php while ($sip = $siparisler->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $sip['siparis_no'] ?></td>
                            <td>
                                <div class="fw-bold" style="color:var(--navy);"><?= htmlspecialchars($sip['urun_adi']) ?></div>
                                <div class="text-muted small"><?= number_format($sip['birim_fiyat'], 2, ',', '.') ?> ₺ / Adet</div>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($sip['ad'] . ' ' . $sip['soyad']) ?></div>
                                <div class="text-muted small" title="<?= htmlspecialchars($sip['tam_adres']) ?>">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($sip['ilce'] . ' / ' . $sip['il']) ?>
                                </div>
                            </td>
                            <td><?= $sip['adet'] ?></td>
                            <td class="fw-bold"><?= number_format($sip['adet'] * $sip['birim_fiyat'], 2, ',', '.') ?> ₺</td>
                            <td>
                                <?php 
                                $badgeClass = 'bg-warning text-dark';
                                if ($sip['kargo_durum'] === 'Kargoya Verildi') $badgeClass = 'bg-info text-white';
                                if ($sip['kargo_durum'] === 'Teslim Edildi') $badgeClass = 'bg-success text-white';
                                ?>
                                <span class="badge <?= $badgeClass ?> px-2 py-1"><?= $sip['kargo_durum'] ?></span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn-sm-action dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Durum Değiştir
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li><a class="dropdown-item py-2" href="#" onclick="durumGuncelle(<?= $sip['siparis_no'] ?>, 'Hazırlanıyor')"><i class="bi bi-clock me-2"></i> Hazırlanıyor</a></li>
                                        <li><a class="dropdown-item py-2" href="#" onclick="durumGuncelle(<?= $sip['siparis_no'] ?>, 'Kargoya Verildi')"><i class="bi bi-truck me-2"></i> Kargoya Verildi</a></li>
                                        <li><a class="dropdown-item py-2" href="#" onclick="durumGuncelle(<?= $sip['siparis_no'] ?>, 'Teslim Edildi')"><i class="bi bi-check-circle me-2"></i> Teslim Edildi</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Henüz sipariş bulunmuyor.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function durumGuncelle(id, durum) {
    fetch('satici_islem.php?islem=siparis_durum&id=' + id + '&durum=' + encodeURIComponent(durum))
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            Toast.fire({ icon: 'success', title: 'Sipariş durumu güncellendi.' });
            setTimeout(() => location.reload(), 1000);
        } else {
            Swal.fire('Hata!', data.message, 'error');
        }
    });
}
</script>
