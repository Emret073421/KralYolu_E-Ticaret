<?php
// Tüm Bekleyen Satıcıları Getir
$sql = "SELECT *, kayit_tarihi as basvuru_tarihi 
        FROM t_satici 
        WHERE onay_durumu = 'Onay Bekliyor' 
        ORDER BY id DESC";
$res = $db->query($sql);
?>

<!-- Üst Başlık -->
<header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="m-0 fw-bold" style="color:#0f172a;">Bekleyen Satıcı Başvuruları</h2>
        <span class="text-muted" style="font-size:0.9rem;">Sisteme yeni kayıt olan ve onay bekleyen <span class="text-warning fw-bold"><?= $res->num_rows ?> satıcı başvurusu</span> bulunmaktadır.</span>
    </div>
    <div class="search-box d-flex align-items-center">
        <i class="bi bi-search text-muted"></i>
        <input type="text" id="searchSeller" placeholder="Mağaza veya yetkili ara...">
    </div>
</header>

<!-- Satıcı Tablosu -->
<div class="shop-list-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="sellerTable">
            <thead class="bg-light text-uppercase small fw-bold">
                <tr>
                    <th class="ps-4">Mağaza Bilgisi</th>
                    <th>Yetkili Kişi</th>
                    <th>Şirket Türü</th>
                    <th>Başvuru Tarihi</th>
                    <th>Durum</th>
                    <th class="text-end pe-4">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if($res && $res->num_rows > 0): ?>
                    <?php while($s = $res->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="shop-avatar">
                                        <i class="bi bi-shop"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark search-target"><?= htmlspecialchars($s['kurum_adi']) ?></div>
                                        <small class="text-muted">Vergi No: <?= htmlspecialchars($s['vergi_no'] ?? 'Belirtilmemiş') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium search-target"><?= htmlspecialchars($s['ad'] . ' ' . $s['soyad']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($s['e_posta']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($s['sirket_turu'] ?? 'Belirtilmemiş') ?></td>
                            <td><small><?= date('d.m.Y H:i', strtotime($s['basvuru_tarihi'])) ?></small></td>
                            <td><span class="status-badge bg-warning-subtle text-warning border border-warning-subtle">Beklemede</span></td>
                            <td class="text-end pe-4">
                                <a href="index.php?sayfa=satici_onay_detay&id=<?= $s['id'] ?>" class="btn btn-primary btn-sm px-3 shadow-sm" style="background:#2563eb; border:none;">
                                    <i class="bi bi-file-earmark-person me-1"></i> Başvuruyu İncele
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle text-success fs-1 d-block mb-3"></i>
                            Tüm satıcı başvuruları incelendi!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Basit JS Arama Filtresi
document.getElementById('searchSeller').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#sellerTable tbody tr');
    
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        if(text.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
