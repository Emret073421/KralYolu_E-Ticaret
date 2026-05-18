<?php
$urun_id = intval($_GET['id']);
$satici_id = $satici['id'];

// Ürün bilgilerini çek (Alt kategorilerle birlikte)
$urun_res = $db->query("SELECT u.*, uk.kategori_ID, uk.alt_kategori_1_ID, uk.alt_kategori_2_ID 
                        FROM t_urun u 
                        LEFT JOIN t_urun_kategori uk ON u.id = uk.urun_ID
                        WHERE u.id = $urun_id AND u.satici_ID = $satici_id");
$urun = $urun_res->fetch_assoc();

if (!$urun) {
    echo "<div class='alert alert-danger'>Ürün bulunamadı veya yetkiniz yok.</div>";
    exit;
}

$kategoriler = $db->query("SELECT * FROM t_kategori ORDER BY kategori_adi ASC");
$fotolar = $db->query("SELECT * FROM t_foto WHERE urun_ID = $urun_id ORDER BY foto_sira ASC");
$tablo_res = $db->query("SELECT * FROM t_urun_tablo_detay WHERE urun_ID = $urun_id ORDER BY tablo_sira ASC");

// Mevcut alt kategorileri çek (Select box'ları doldurmak için)
$alt1_list = $urun['kategori_ID'] ? $db->query("SELECT id, kategori_adi FROM t_alt_kategori_1 WHERE kategori_id = " . $urun['kategori_ID']) : null;
$alt2_list = $urun['alt_kategori_1_ID'] ? $db->query("SELECT id, kategori_adi FROM t_alt_kategori_2 WHERE alt_1_ID = " . $urun['alt_kategori_1_ID']) : null;
?>

<style>
    .form-label { font-weight: 700; color: var(--navy); font-size: 0.85rem; margin-bottom: 5px; }
    .form-control, .form-select { border: 1px solid #cbd5e1; padding: 10px 15px; border-radius: 8px; font-size: 0.95rem; }
    .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25); border-color: var(--gold); }
    .upload-box { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.3s; position: relative; }
    .upload-box:hover { border-color: var(--gold); background: rgba(212, 175, 55, 0.05); }
    .upload-icon { font-size: 1.5rem; color: #94a3b8; margin-bottom: 5px; }
    .upload-box input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .photo-item { position: relative; width: 100%; aspect-ratio: 1; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; cursor: grab; }
    .photo-item img { width: 100%; height: 100%; object-fit: cover; }
    .photo-item .remove-btn { position: absolute; top: 5px; right: 5px; background: rgba(239, 68, 68, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 14px; opacity: 0; transition: 0.2s; }
    .photo-item:hover .remove-btn { opacity: 1; }
    .photo-sortable-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px; }
</style>

<header class="mb-4">
    <h2 class="m-0 fw-bold" style="color:var(--navy);">Ürün Düzenle</h2>
    <p class="text-muted small">Ürün detaylarını, kategorilerini ve fotoğraflarını buradan güncelleyin.</p>
</header>

<form id="urunGuncelleForm" enctype="multipart/form-data">
    <input type="hidden" name="islem" value="urun_guncelle">
    <input type="hidden" name="urun_id" value="<?= $urun_id ?>">
    
    <div class="row g-4">
        <!-- SOL PANEL: Temel Bilgiler -->
        <div class="col-lg-8">
            <div class="bg-white p-4 rounded-3 shadow-sm border border-light mb-4">
                <h5 class="fw-bold mb-4" style="color:var(--navy); border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Temel Bilgiler</h5>
                
                <div class="mb-3">
                    <label class="form-label">Ürün Adı</label>
                    <input type="text" name="urun_adi" class="form-control" value="<?= htmlspecialchars($urun['urun_adi']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Ürün Açıklaması</label>
                    <textarea name="aciklama" class="form-control" rows="6" required><?= htmlspecialchars($urun['urun_aciklama']) ?></textarea>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Satış Fiyatı (₺)</label>
                        <input type="number" step="0.01" name="fiyat" class="form-control" value="<?= $urun['fiyat'] ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Stok Adedi</label>
                        <input type="number" name="stok" class="form-control" value="<?= $urun['stok'] ?>" required>
                    </div>
                </div>
            </div>

            <!-- KATEGORİ SEÇİMİ -->
            <div class="bg-white p-4 rounded-3 shadow-sm border border-light mb-4">
                <h5 class="fw-bold mb-4" style="color:var(--navy); border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Kategori Yapılandırması</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Ana Kategori</label>
                        <select name="kategori_id" id="main_cat" class="form-select" required>
                            <option value="">Seçiniz</option>
                            <?php while($k = $kategoriler->fetch_assoc()): ?>
                                <option value="<?= $k['id'] ?>" <?= $k['id'] == $urun['kategori_ID'] ? 'selected' : '' ?>><?= $k['kategori_adi'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Alt Kategori 1</label>
                        <select name="alt_kategori_1_ID" id="sub_cat_1" class="form-select">
                            <option value="">Önce Ana Kategori</option>
                            <?php if($alt1_list): while($a1 = $alt1_list->fetch_assoc()): ?>
                                <option value="<?= $a1['id'] ?>" <?= $a1['id'] == $urun['alt_kategori_1_ID'] ? 'selected' : '' ?>><?= $a1['kategori_adi'] ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Alt Kategori 2</label>
                        <select name="alt_kategori_2_ID" id="sub_cat_2" class="form-select">
                            <option value="">Önce Alt Kategori 1</option>
                            <?php if($alt2_list): while($a2 = $alt2_list->fetch_assoc()): ?>
                                <option value="<?= $a2['id'] ?>" <?= $a2['id'] == $urun['alt_kategori_2_ID'] ? 'selected' : '' ?>><?= $a2['kategori_adi'] ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TEKNİK ÖZELLİKLER TABLOSU -->
            <div class="bg-white p-4 rounded-3 shadow-sm border border-light mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                    <h5 class="fw-bold m-0" style="color:var(--navy);">Teknik Özellikler Tablosu</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTableRow()"><i class="bi bi-plus-lg"></i> Satır Ekle</button>
                </div>
                <div id="tableRowsContainer">
                    <?php if($tablo_res && $tablo_res->num_rows > 0): ?>
                        <?php while($tr = $tablo_res->fetch_assoc()): ?>
                        <div class="row g-2 mb-2 table-row-item">
                            <div class="col-5">
                                <input type="text" name="tablo_baslik[]" class="form-control" value="<?= htmlspecialchars($tr['sutun_baslik']) ?>" placeholder="Örn: RAM Kapasitesi">
                            </div>
                            <div class="col-6">
                                <input type="text" name="tablo_deger[]" class="form-control" value="<?= htmlspecialchars($tr['sutun_aciklama']) ?>" placeholder="Örn: 16 GB">
                            </div>
                            <div class="col-1 text-end">
                                <button type="button" class="btn btn-danger w-100" onclick="this.closest('.table-row-item').remove()"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <div class="row g-2 mb-2 table-row-item">
                        <div class="col-5">
                            <input type="text" name="tablo_baslik[]" class="form-control" placeholder="Örn: RAM Kapasitesi">
                        </div>
                        <div class="col-6">
                            <input type="text" name="tablo_deger[]" class="form-control" placeholder="Örn: 16 GB">
                        </div>
                        <div class="col-1 text-end">
                            <button type="button" class="btn btn-danger w-100" onclick="this.closest('.table-row-item').remove()"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary py-3 fw-bold" style="background:var(--navy); border:none;">
                    <i class="bi bi-check2-circle me-2"></i> Ürün Bilgilerini Güncelle
                </button>
            </div>
        </div>

        <!-- SAĞ PANEL: Fotoğraflar -->
        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-3 shadow-sm border border-light mb-4">
                <h5 class="fw-bold mb-3" style="color:var(--navy);">Görseller</h5>
                <p class="text-muted small mb-3">Fotoğrafları sürükleyerek sıralayın. İlk fotoğraf kapak olur.</p>
                
                <div id="photoSortable" class="photo-sortable-grid mb-4">
                    <?php if ($fotolar && $fotolar->num_rows > 0): ?>
                        <?php while ($foto = $fotolar->fetch_assoc()): ?>
                            <div class="photo-item" data-id="<?= $foto['id'] ?>">
                                <img src="../<?= $foto['foto_url'] ?>" alt="Ürün">
                                <button type="button" onclick="fotoSil(<?= $foto['id'] ?>)" class="remove-btn"><i class="bi bi-trash"></i></button>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-4 w-100 text-muted small border rounded bg-light">Fotoğraf yok.</div>
                    <?php endif; ?>
                </div>

                <div class="upload-box mt-3">
                    <i class="bi bi-cloud-upload upload-icon"></i>
                    <p class="small m-0 fw-bold">Yeni Fotoğraf Yükle</p>
                    <input type="file" name="urun_fotolar[]" multiple accept="image/*" onchange="previewImages(this)">
                </div>
                <div id="imagePreviews" class="d-flex flex-wrap gap-2 mt-3"></div>
            </div>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Kategori Değişimi - Alt Kategori 1 Yükle
$('#main_cat').on('change', function() {
    let pid = $(this).val();
    $('#sub_cat_1').html('<option value="">Yükleniyor...</option>');
    $('#sub_cat_2').html('<option value="">Önce Alt Kategori 1</option>');
    if(!pid) return;
    
    fetch('satici_islem.php?islem=get_alt_kategoriler&seviye=1&parent_id=' + pid)
    .then(res => res.json())
    .then(data => {
        let html = '<option value="">Seçiniz (Opsiyonel)</option>';
        data.forEach(item => { html += `<option value="${item.id}">${item.kategori_adi}</option>`; });
        $('#sub_cat_1').html(html);
    });
});

function addTableRow() {
    const html = `
        <div class="row g-2 mb-2 table-row-item">
            <div class="col-5">
                <input type="text" name="tablo_baslik[]" class="form-control" placeholder="Örn: RAM Kapasitesi">
            </div>
            <div class="col-6">
                <input type="text" name="tablo_deger[]" class="form-control" placeholder="Örn: 16 GB">
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-danger w-100" onclick="this.closest('.table-row-item').remove()"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    `;
    $('#tableRowsContainer').append(html);
}

// Alt Kategori 1 Değişimi - Alt Kategori 2 Yükle
$('#sub_cat_1').on('change', function() {
    let pid = $(this).val();
    $('#sub_cat_2').html('<option value="">Yükleniyor...</option>');
    if(!pid) return;
    
    fetch('satici_islem.php?islem=get_alt_kategoriler&seviye=2&parent_id=' + pid)
    .then(res => res.json())
    .then(data => {
        let html = '<option value="">Seçiniz (Opsiyonel)</option>';
        data.forEach(item => { html += `<option value="${item.id}">${item.kategori_adi}</option>`; });
        $('#sub_cat_2').html(html);
    });
});

// Fotoğraf Sıralama
if(document.getElementById('photoSortable')) {
    new Sortable(document.getElementById('photoSortable'), {
        animation: 150,
        ghostClass: 'bg-light',
        onEnd: function() {
            let ids = [];
            $('.photo-item').each(function() { ids.push($(this).data('id')); });
            fetch('satici_islem.php?islem=foto_sirala', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sirali_idler: ids })
            }).then(() => Toast.fire({ icon: 'success', title: 'Sıralama güncellendi.' }));
        }
    });
}

let selectedFiles = [];
const previewContainer = document.getElementById('imagePreviews');

function previewImages(input) {
    if (input.files) {
        let newFiles = Array.from(input.files);
        selectedFiles = selectedFiles.concat(newFiles);
        renderPreviews();
    }
    input.value = ''; // Inputu temizle ki aynı dosyayı tekrar seçebilsin
}

function renderPreviews() {
    previewContainer.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.style.position = 'relative';
            div.style.cursor = 'grab';
            div.dataset.index = index;
            div.innerHTML = `
                <img src="${e.target.result}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid #cbd5e1;">
                <button type="button" onclick="removeNewPhoto(${index})" class="btn btn-danger btn-sm p-0 rounded-circle d-flex align-items-center justify-content-center" style="position:absolute; top:-5px; right:-5px; width:20px; height:20px; font-size:12px;">
                    <i class="bi bi-x"></i>
                </button>
            `;
            previewContainer.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

function removeNewPhoto(index) {
    selectedFiles.splice(index, 1);
    renderPreviews();
}

// Yeni Eklenen Fotoları Sürükle Bırak (isteğe bağlı)
new Sortable(previewContainer, {
    animation: 150,
    ghostClass: 'opacity-50',
    onEnd: function() {
        let newOrder = [];
        $(previewContainer).children().each(function() {
            let oldIndex = $(this).data('index');
            newOrder.push(selectedFiles[oldIndex]);
        });
        selectedFiles = newOrder;
        renderPreviews();
    }
});

function fotoSil(id) {
    Swal.fire({
        title: 'Fotoğrafı sil?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sil',
        cancelButtonText: 'İptal'
    }).then((res) => {
        if(res.isConfirmed) {
            fetch('satici_islem.php?islem=foto_sil&id=' + id)
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success') {
                    $(`.photo-item[data-id="${id}"]`).remove();
                    Toast.fire({ icon: 'success', title: 'Fotoğraf silindi.' });
                }
            });
        }
    });
}

$('#urunGuncelleForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    formData.delete('urun_fotolar[]');
    selectedFiles.forEach(file => {
        formData.append('urun_fotolar[]', file);
    });

    $.ajax({
        url: 'satici_islem.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(data) {
            if(data.status === 'success') {
                Swal.fire('Başarılı!', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Hata!', data.message || 'Bilinmeyen bir hata oluştu.', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            Swal.fire('Hata!', 'Sunucuyla iletişimde bir hata oluştu. Lütfen eksik alanları kontrol edin.', 'error');
        }
    });
});
</script>
