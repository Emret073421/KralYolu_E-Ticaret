<?php
// Personel listesini getir - FIX: t_personel tablosunu kullan
$sql = "SELECT * FROM t_personel ORDER BY id DESC";
$res = $db->query($sql);
?>

<style>
    .personnel-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .personnel-avatar {
        width: 45px;
        height: 45px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #64748b;
    }
    .role-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .role-admin { background: #fee2e2; color: #dc2626; }
    .role-staff { background: #dcfce7; color: #16a34a; }
</style>

<!-- Üst Başlık -->
<header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="m-0 fw-bold" style="color:#0f172a;">Personel ve Yetki Yönetimi</h2>
        <span class="text-muted" style="font-size:0.9rem;">Sistem yöneticilerini ve personelleri yönetin.</span>
    </div>
    <div>
        <button class="btn btn-primary px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#personnelModal" onclick="prepareModal('add')">
            <i class="bi bi-person-plus-fill me-2"></i> Yeni Personel Ekle
        </button>
    </div>
</header>

<!-- Personel Tablosu -->
<div class="personnel-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Personel Bilgisi</th>
                    <th>E-Posta / Telefon</th>
                    <th>Yetki Seviyesi</th>
                    <th>Kayıt Tarihi</th>
                    <th class="text-end pe-4">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if($res && $res->num_rows > 0): ?>
                    <?php while($p = $res->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="personnel-avatar">
                                        <i class="bi <?= $p['rol'] == 'Yönetici' ? 'bi-person-fill-lock' : 'bi-person-fill-gear' ?>"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['ad'] . ' ' . $p['soyad']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small"><?= htmlspecialchars($p['e_posta']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($p['telefon']) ?></div>
                            </td>
                            <td>
                                <span class="role-badge <?= $p['rol'] == 'Yönetici' ? 'role-admin' : 'role-staff' ?>">
                                    <?= $p['rol'] ?>
                                </span>
                            </td>
                            <td><small><?= $p['kayit_tarihi'] ? date('d.m.Y', strtotime($p['kayit_tarihi'])) : '-' ?></small></td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" onclick='prepareModal("edit", <?= htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") ?>)'><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="silPersonel(<?= $p['id'] ?>)"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Henüz personel bulunmuyor.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Ortak Modal (Ekleme ve Güncelleme) -->
<div class="modal fade" id="personnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTitle">Personel İşlemi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="personnelForm" onsubmit="return false;">
                    <input type="hidden" name="id" id="p_id">
                    <input type="hidden" name="islem" id="p_islem" value="personel_ekle">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Adı</label>
                            <input type="text" class="form-control" name="ad" id="p_ad" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Soyadı</label>
                            <input type="text" class="form-control" name="soyad" id="p_soyad" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">E-Posta Adresi</label>
                            <input type="email" class="form-control" name="e_posta" id="p_eposta" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Telefon</label>
                            <input type="tel" class="form-control" name="telefon" id="p_telefon">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Parola <span id="pwdLabel" class="fw-normal text-muted" style="display:none;">(Boş bırakılabilir)</span></label>
                            <input type="password" class="form-control" name="parola" id="p_parola">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Yetki Rolü</label>
                            <select class="form-select" name="rol" id="p_rol" required>
                                <option value="Personel">Personel</option>
                                <option value="Yönetici">Yönetici</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary fw-bold px-4" id="btnKaydet" onclick="submitPersonelForm()">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<script>
function prepareModal(mode, data) {
    document.getElementById('personnelForm').reset();
    if (mode == 'add') {
        $('#modalTitle').text('Yeni Personel Ekle');
        $('#p_islem').val('personel_ekle');
        $('#p_id').val('');
        $('#p_parola').prop('required', true);
        $('#pwdLabel').hide();
    } else {
        $('#modalTitle').text('Personel Bilgilerini Güncelle');
        $('#p_islem').val('personel_guncelle');
        $('#p_id').val(data.id);
        $('#p_ad').val(data.ad);
        $('#p_soyad').val(data.soyad);
        $('#p_eposta').val(data.e_posta);
        $('#p_telefon').val(data.telefon);
        $('#p_rol').val(data.rol);
        $('#p_parola').prop('required', false);
        $('#pwdLabel').show();
        $('#personnelModal').modal('show');
    }
}

function submitPersonelForm() {
    // HTML5 form validasyonu
    var form = document.getElementById('personnelForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Kaydet butonunu devre dışı bırak (çift tıklama engeli)
    var btn = document.getElementById('btnKaydet');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Kaydediliyor...';
    
    $.ajax({
        type: 'POST',
        url: 'admin_islem.php',
        data: $('#personnelForm').serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status == 'success') {
                $('#personnelModal').modal('hide');
                Swal.fire('Başarılı!', res.message, 'success').then(function() { location.reload(); });
            } else {
                Swal.fire('Hata!', res.message, 'error');
                btn.disabled = false;
                btn.innerHTML = 'Kaydet';
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Hatası:", status, error, xhr.responseText);
            // JSON parse hatası olabilir - raw text'i kontrol et
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.status == 'success') {
                    $('#personnelModal').modal('hide');
                    Swal.fire('Başarılı!', res.message, 'success').then(function() { location.reload(); });
                    return;
                }
                Swal.fire('Hata!', res.message || 'Bilinmeyen hata', 'error');
            } catch(e) {
                Swal.fire('Hata!', 'İşlem sırasında bir bağlantı hatası oluştu. Konsolu kontrol edin.', 'error');
            }
            btn.disabled = false;
            btn.innerHTML = 'Kaydet';
        }
    });
}

function silPersonel(id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: 'Bu personel silinecektir!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, Sil',
        cancelButtonText: 'İptal'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: 'admin_islem.php',
                type: 'POST',
                data: { islem: 'personel_sil', id: id },
                dataType: 'json',
                success: function(res) {
                    if (res.status == 'success') {
                        Swal.fire('Silindi!', res.message, 'success').then(function() { location.reload(); });
                    } else {
                        Swal.fire('Hata!', res.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Silme Hatası:", xhr.responseText);
                    try {
                        var r = JSON.parse(xhr.responseText);
                        if (r.status == 'success') {
                            Swal.fire('Silindi!', r.message, 'success').then(function() { location.reload(); });
                            return;
                        }
                        Swal.fire('Hata!', r.message, 'error');
                    } catch(e) {
                        Swal.fire('Hata!', 'Silme işlemi başarısız oldu.', 'error');
                    }
                }
            });
        }
    });
}
</script>
