<?php
// Tüm Bekleyen veya Onaylanmış Ürünleri Getir
$sql = "SELECT u.*, s.kurum_adi, k.kategori_adi, f.foto_url
        FROM t_urun u 
        JOIN t_satici s ON u.satici_ID = s.id 
        LEFT JOIN t_urun_kategori uk ON u.id = uk.urun_ID
        LEFT JOIN t_kategori k ON uk.kategori_ID = k.id
        LEFT JOIN t_foto f ON u.id = f.urun_ID AND f.foto_sira = 1
        WHERE u.onay_durumu = 'Onay Bekliyor'
        GROUP BY u.id
        ORDER BY u.id DESC";
$res = $db->query($sql);
?>

<!-- Üst Başlık -->
<header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="m-0 fw-bold" style="color:#0f172a;">Bekleyen Ürün Onayları</h2>
        <span class="text-muted" style="font-size:0.9rem;">Şu anda sistemde onaylanmayı bekleyen <span class="text-danger fw-bold"><?= $res->num_rows ?> ürün</span> bulunmaktadır.</span>
    </div>
    <div class="search-box d-flex align-items-center">
        <i class="bi bi-search text-muted"></i>
        <input type="text" id="searchProduct" placeholder="Ürün veya satıcı ara...">
    </div>
</header>

<!-- Ürün Tablosu -->
<div class="product-list-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="productTable">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Ürün Bilgisi</th>
                    <th>Kategori</th>
                    <th>Satıcı</th>
                    <th>Fiyat</th>
                    <th>Durum</th>
                    <th class="text-end pe-4">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if($res && $res->num_rows > 0): ?>
                    <?php while($u = $res->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= htmlspecialchars($u['foto_url'] ?? 'https://via.placeholder.com/60') ?>" alt="Ürün" class="product-thumb">
                                    <div>
                                        <div class="fw-bold text-dark search-target"><?= htmlspecialchars($u['urun_adi']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['kategori_adi'] ?? 'Kategorisiz') ?></td>
                            <td>
                                <div class="fw-medium search-target"><?= htmlspecialchars($u['kurum_adi']) ?></div>
                            </td>
                            <td><span class="fw-bold"><?= number_format($u['fiyat'], 2, ',', '.') ?> ₺</span></td>
                            <td><span class="status-badge bg-warning text-dark">Onay Bekliyor</span></td>
                            <td class="text-end pe-4">
                                <a href="index.php?sayfa=urun_onay_detay&id=<?= $u['id'] ?>" class="btn btn-primary btn-sm px-3 shadow-sm" style="background:#2563eb; border:none;">
                                    <i class="bi bi-eye-fill me-1"></i> İncele
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle text-success fs-1 d-block mb-3"></i>
                            Tüm ürün onayları incelendi!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('searchProduct').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#productTable tbody tr');
    
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
