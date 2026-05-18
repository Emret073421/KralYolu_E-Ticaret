<?php
$kategoriler = $db->query("SELECT * FROM t_kategori ORDER BY kategori_adi ASC");
?>
<style>
    .form-label { font-weight: 700; color: var(--navy); font-size: 0.85rem; margin-bottom: 5px; }
    .form-control, .form-select { border: 1px solid #cbd5e1; padding: 10px 15px; border-radius: 8px; }
    .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25); border-color: var(--gold); }
    .upload-box { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 40px 20px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.3s; position: relative; }
    .upload-box:hover { border-color: var(--gold); background: rgba(212, 175, 55, 0.05); }
    .upload-icon { font-size: 2.5rem; color: #94a3b8; margin-bottom: 10px; }
    .upload-box input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    #imagePreviews img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
</style>

<header class="mb-4">
    <h2 class="m-0 fw-bold" style="color:var(--navy);">Yeni Ürün Ekle</h2>
    <p class="text-muted small">Kataloğunuza yeni bir ürün girmek için alanları doldurun.</p>
</header>

<form id="urunEkleForm" enctype="multipart/form-data">
    <input type="hidden" name="islem" value="urun_ekle">
    <div class="row g-4">
        <!-- SOL PANEL -->
        <div class="col-lg-8">
            <!-- TEMEL BİLGİLER -->
            <div class="bg-white p-4 rounded-3 shadow-sm border border-light mb-4">
                <h5 class="fw-bold mb-4" style="color:var(--navy); border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Temel Bilgiler</h5>
                
                <div class="mb-3">
                    <label class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                    <input type="text" name="urun_adi" class="form-control" placeholder="Örn: Akıllı Saat Pro V2" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Ürün Açıklaması <span class="text-danger">*</span></label>
                    <textarea name="aciklama" class="form-control" rows="6" placeholder="Ürün detaylarını buraya yazın..." required></textarea>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Fiyat (₺) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="fiyat" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Stok Adedi <span class="text-danger">*</span></label>
                        <input type="number" name="stok" class="form-control" placeholder="0" required>
                    </div>
                </div>
            </div>

            <!-- KATEGORİ YAPISI -->
            <div class="bg-white p-4 rounded-3 shadow-sm border border-light mb-4">
                <h5 class="fw-bold mb-4" style="color:var(--navy); border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Kategori Seçimi</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Ana Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id" id="main_cat" class="form-select" required>
                            <option value="" selected disabled>Seçiniz</option>
                            <?php while($k = $kategoriler->fetch_assoc()): ?>
                                <option value="<?= $k['id'] ?>"><?= $k['kategori_adi'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Alt Kategori 1</label>
                        <select name="alt_kategori_1_ID" id="sub_cat_1" class="form-select">
                            <option value="">Önce Ana Kategori</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Alt Kategori 2</label>
                        <select name="alt_kategori_2_ID" id="sub_cat_2" class="form-select">
                            <option value="">Önce Alt Kategori 1</option>
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
                </div>
            </div>
        </div>

        <!-- SAĞ PANEL -->
        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-3 shadow-sm border border-light mb-4">
                <h5 class="fw-bold mb-3" style="color:var(--navy);">Görseller <span class="text-danger">*</span></h5>
                <div class="upload-box mb-3">
                    <i class="bi bi-cloud-arrow-up-fill upload-icon"></i>
                    <h6 class="fw-bold m-0">Fotoğrafları Yükle</h6>
                    <input type="file" name="urun_fotolar[]" multiple accept="image/*" onchange="previewImages(this)">
                </div>
                <div id="imagePreviews" class="d-flex flex-wrap gap-2 mb-2"></div>
                <small class="text-muted"><i class="bi bi-info-circle"></i> İlk fotoğraf kapak olur.</small>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="background:var(--navy); border:none;">
                <i class="bi bi-send-check me-2"></i> Ürünü Yayınla
            </button>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Kategori AJAX
$('#main_cat').on('change', function() {
    let pid = $(this).val();
    $('#sub_cat_1').html('<option value="">Yükleniyor...</option>');
    $('#sub_cat_2').html('<option value="">Önce Alt Kategori 1</option>');
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

$('#sub_cat_1').on('change', function() {
    let pid = $(this).val();
    $('#sub_cat_2').html('<option value="">Yükleniyor...</option>');
    fetch('satici_islem.php?islem=get_alt_kategoriler&seviye=2&parent_id=' + pid)
    .then(res => res.json())
    .then(data => {
        let html = '<option value="">Seçiniz (Opsiyonel)</option>';
        data.forEach(item => { html += `<option value="${item.id}">${item.kategori_adi}</option>`; });
        $('#sub_cat_2').html(html);
    });
});

let selectedFiles = [];
const previewContainer = document.getElementById('imagePreviews');

function previewImages(input) {
    if (input.files) {
        let newFiles = Array.from(input.files);
        selectedFiles = selectedFiles.concat(newFiles);
        renderPreviews();
    }
    input.value = ''; // Reset input to allow selecting the same file again if needed
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
                <img src="${e.target.result}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #cbd5e1;">
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

// Yeni Fotoları Sürükle Bırak
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
        renderPreviews(); // Re-render to update indexes
    }
});

$('#urunEkleForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    // Varsayılan input dosyalarını temizle ve sıralı array'i ekle
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
            if (data.status === 'success') {
                Swal.fire('Başarılı!', data.message, 'success').then(() => {
                    window.location.href = 'index.php?sayfa=urunler';
                });
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
