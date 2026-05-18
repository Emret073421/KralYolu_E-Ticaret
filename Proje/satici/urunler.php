<?php
$satici_id = $satici['id'];
$urunler = $db->query("SELECT u.*, f.foto_url, k.kategori_adi 
                       FROM t_urun u 
                       LEFT JOIN t_foto f ON u.id = f.urun_ID AND f.foto_sira = 1
                       LEFT JOIN t_urun_kategori uk ON u.id = uk.urun_ID
                       LEFT JOIN t_kategori k ON uk.kategori_ID = k.id
                       WHERE u.satici_ID = $satici_id 
                       ORDER BY u.id DESC");
?>

<style>
    .product-thumbnail { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .action-icon { font-size: 1.1rem; padding: 5px 8px; border-radius: 6px; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; }
    .action-edit { color: #3b82f6; background: rgba(59, 130, 246, 0.1); }
    .action-edit:hover { background: #3b82f6; color: white; }
    .action-delete { color: #ef4444; background: rgba(239, 68, 68, 0.1); border: none; }
    .action-delete:hover { background: #ef4444; color: white; }
</style>

<!-- Üst Başlık -->
<header class="seller-header">
    <div>
        <h2 class="m-0 fw-bold" style="color:var(--navy);">Mevcut Ürünlerim</h2>
        <span class="text-muted" style="font-size:0.9rem;">Satıştaki tüm ürünlerinizi yönetin ve stoklarınızı güncelleyin.</span>
    </div>
    <div>
        <a href="index.php?sayfa=urun_ekle" class="btn btn-primary" style="background-color: var(--gold); border-color: var(--gold); font-weight:600;">
            <i class="bi bi-plus-lg"></i> Yeni Ürün Ekle
        </a>
    </div>
</header>

<!-- Filtre ve Arama Alanı (Opsiyonel: Aktif hale getirmek için JS gerekir) -->
<div class="bg-white p-3 rounded-3 shadow-sm border border-light mb-4 d-flex justify-content-between align-items-center">
    <div class="d-flex gap-3 w-50">
        <input type="text" class="form-control" placeholder="Ürün Adı veya Kategorisinde Ara..." id="tableSearch">
    </div>
    <div>
        <span class="text-muted fw-bold">Toplam <?= $urunler->num_rows ?> Ürün</span>
    </div>
</div>

<!-- Ürünler Tablosu -->
<div class="bg-white p-4 rounded-3 shadow-sm border border-light">
    <div class="table-responsive">
        <table class="dashboard-table table-hover">
            <thead>
                <tr>
                    <th>Görsel</th>
                    <th>Ürün Adı & Kategori</th>
                    <th>Fiyat</th>
                    <th>Stok</th>
                    <th>Durum</th>
                    <th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($urunler && $urunler->num_rows > 0): ?>
                    <?php while ($urun = $urunler->fetch_assoc()): ?>
                        <tr id="urun-row-<?= $urun['id'] ?>">
                            <td>
                                <img src="../<?= $urun['foto_url'] ?? 'assets/img/no-image.jpg' ?>" class="product-thumbnail" alt="Ürün">
                            </td>
                            <td>
                                <div class="fw-bold" style="color:var(--navy);"><?= htmlspecialchars($urun['urun_adi']) ?></div>
                                <div class="text-muted" style="font-size:0.8rem;"><?= htmlspecialchars($urun['kategori_adi'] ?? 'Kategori Seçilmedi') ?></div>
                            </td>
                            <td class="fw-bold"><?= number_format($urun['fiyat'], 2, ',', '.') ?> ₺</td>
                            <td>
                                <?php if ($urun['stok'] <= 5): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><?= $urun['stok'] ?> Adet (Kritik)</span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success"><?= $urun['stok'] ?> Adet</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $urun['aktif'] === 'Evet' ? 'bg-primary' : 'bg-warning text-dark' ?>">
                                    <?= $urun['aktif'] === 'Evet' ? 'Yayında' : 'Pasif' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="index.php?sayfa=urun_duzenle&id=<?= $urun['id'] ?>" class="action-icon action-edit" title="Güncelle">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button onclick="urunSil(<?= $urun['id'] ?>)" class="action-icon action-delete ms-1" title="Sil">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Henüz ürün eklemediniz.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Tablo Arama Fonksiyonu
$("#tableSearch").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("table tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});

function urunSil(id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Ürün ve tüm fotoğrafları silinecektir!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, sil!',
        cancelButtonText: 'Vazgeç'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('satici_islem.php?islem=urun_sil&id=' + id)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Toast.fire({ icon: 'success', title: data.message });
                    $(`#urun-row-${id}`).fadeOut(400, function() { $(this).remove(); });
                } else {
                    Swal.fire('Hata!', data.message, 'error');
                }
            });
        }
    });
}
</script>
