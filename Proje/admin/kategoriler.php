<?php
// Ana kategorileri getir
$ana_kategoriler = $db->query("SELECT * FROM t_kategori ORDER BY kategori_adi ASC");
?>

<style>
    .category-section {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .category-header {
        padding: 15px 20px;
        border-bottom: 2px solid #f1f5f9;
        background: #f8fafc;
    }
    .category-list {
        padding: 0;
        margin: 0;
        list-style: none;
        height: 400px;
        overflow-y: auto;
    }
    .category-item {
        padding: 12px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s;
        cursor: pointer;
    }
    .category-item:hover {
        background-color: #f1f5f9;
    }
    .category-item.active {
        background-color: #e2e8f0;
        border-left: 4px solid #eab308;
    }
    .level-badge {
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 5px;
    }
</style>

<!-- Üst Başlık -->
<header class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 fw-bold" style="color:#0f172a;">Kategori Yapılandırması</h2>
        <span class="text-muted" style="font-size:0.9rem;">Ana kategori ve bağlı alt kategorileri yönetin.</span>
    </div>
</header>

<div class="row g-4">
    
    <!-- 1. SEVİYE: ANA KATEGORİ -->
    <div class="col-lg-4">
        <div class="category-section shadow-sm border-light">
            <div class="category-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0"><span class="level-badge bg-primary text-white me-2">Seviye 1</span>Ana Kategoriler</h6>
                <button class="btn btn-sm btn-primary" onclick="kategoriEkleModal(null, 1)"><i class="bi bi-plus-lg"></i></button>
            </div>
            <ul class="category-list" id="list_level_1">
                <?php while($k = $ana_kategoriler->fetch_assoc()): ?>
                    <li class="category-item" onclick="loadSubCategories(<?= $k['id'] ?>, 2, this)">
                        <span><?= htmlspecialchars($k['kategori_adi']) ?></span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-link btn-sm p-0 text-primary" onclick="editKategori(<?= $k['id'] ?>, 1, '<?= htmlspecialchars(addslashes($k['kategori_adi'])) ?>', event)"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-link btn-sm p-0 text-danger" onclick="deleteKategori(<?= $k['id'] ?>, 1, event)"><i class="bi bi-trash"></i></button>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <!-- 2. SEVİYE: ALT KATEGORİ 1 -->
    <div class="col-lg-4">
        <div class="category-section shadow-sm border-light">
            <div class="category-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0"><span class="level-badge bg-success text-white me-2">Seviye 2</span>Alt Kategoriler (1)</h6>
                <button class="btn btn-sm btn-success" onclick="kategoriEkleModal(currentLevel1, 2)" id="btnAddLevel2" disabled><i class="bi bi-plus-lg"></i></button>
            </div>
            <ul class="category-list" id="list_level_2">
                <li class="p-3 text-muted text-center small">Ana kategori seçin</li>
            </ul>
        </div>
    </div>

    <!-- 3. SEVİYE: ALT KATEGORİ 2 -->
    <div class="col-lg-4">
        <div class="category-section shadow-sm border-light">
            <div class="category-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0"><span class="level-badge bg-warning text-dark me-2">Seviye 3</span>Alt Kategoriler (2)</h6>
                <button class="btn btn-sm btn-warning" onclick="kategoriEkleModal(currentLevel2, 3)" id="btnAddLevel3" disabled><i class="bi bi-plus-lg"></i></button>
            </div>
            <ul class="category-list" id="list_level_3">
                <li class="p-3 text-muted text-center small">Alt kategori (1) seçin</li>
            </ul>
        </div>
    </div>

</div>

<script>
let currentLevel1 = null;
let currentLevel2 = null;

function loadSubCategories(ustId, nextLevel, element) {
    // Aktif sınıfını ayarla
    $(element).siblings().removeClass('active');
    $(element).addClass('active');

    if(nextLevel == 2) {
        currentLevel1 = ustId;
        $('#btnAddLevel2').prop('disabled', false);
        $('#list_level_2').html('<li class="p-3 text-center"><div class="spinner-border spinner-border-sm text-success"></div></li>');
        $('#list_level_3').html('<li class="p-3 text-muted text-center small">Alt kategori (1) seçin</li>');
        $('#btnAddLevel3').prop('disabled', true);
        currentLevel2 = null;
    } else if(nextLevel == 3) {
        currentLevel2 = ustId;
        $('#btnAddLevel3').prop('disabled', false);
        $('#list_level_3').html('<li class="p-3 text-center"><div class="spinner-border spinner-border-sm text-warning"></div></li>');
    }

    $.ajax({
        url: 'admin_islem.php',
        type: 'POST',
        data: { islem: 'get_kategori', ust_id: ustId, level: nextLevel },
        success: function(response) {
            let html = '';
            let res = typeof response === 'string' ? JSON.parse(response) : response;
            if(res.length > 0) {
                res.forEach(item => {
                    let onclickStr = nextLevel < 3 ? `onclick="loadSubCategories(${item.id}, ${nextLevel + 1}, this)"` : '';
                    html += `
                    <li class="category-item" ${onclickStr}>
                        <span>${item.kategori_adi}</span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-link btn-sm p-0 text-primary" onclick="editKategori(${item.id}, ${nextLevel}, '${item.kategori_adi.replace(/'/g, "\\'")}', event)"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-link btn-sm p-0 text-danger" onclick="deleteKategori(${item.id}, ${nextLevel}, event)"><i class="bi bi-trash"></i></button>
                        </div>
                    </li>`;
                });
            } else {
                html = '<li class="p-3 text-muted text-center small">Kategori bulunamadı.</li>';
            }
            $(`#list_level_${nextLevel}`).html(html);
        }
    });
}

function kategoriEkleModal(ustId, level) {
    Swal.fire({
        title: 'Yeni Kategori Ekle',
        input: 'text',
        inputLabel: 'Kategori Adı',
        showCancelButton: true,
        confirmButtonText: 'Ekle',
        cancelButtonText: 'İptal',
        preConfirm: (val) => {
            if(!val) {
                Swal.showValidationMessage('Kategori adı boş olamaz!');
            }
            return val;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'admin_islem.php',
                type: 'POST',
                data: { islem: 'ekle_kategori', ust_id: ustId, level: level, kategori_adi: result.value },
                success: function(res) {
                    let r = JSON.parse(res);
                    if(r.status == 'success') {
                        Swal.fire('Eklendi!', r.message, 'success').then(()=>location.reload());
                    } else {
                        Swal.fire('Hata!', r.message, 'error');
                    }
                }
            });
        }
    });
}

function editKategori(id, level, oldName, e) {
    e.stopPropagation();
    Swal.fire({
        title: 'Kategori Düzenle',
        input: 'text',
        inputValue: oldName,
        inputLabel: 'Yeni Kategori Adı',
        showCancelButton: true,
        confirmButtonText: 'Kaydet',
        cancelButtonText: 'İptal',
        preConfirm: (val) => {
            if(!val || val.trim() === '') {
                Swal.showValidationMessage('Kategori adı boş olamaz!');
            }
            return val;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value.trim() !== oldName) {
            $.ajax({
                url: 'admin_islem.php',
                type: 'POST',
                data: { islem: 'duzenle_kategori', id: id, level: level, kategori_adi: result.value.trim() },
                success: function(res) {
                    let r = typeof res === 'string' ? JSON.parse(res) : res;
                    if(r.status == 'success') {
                        Swal.fire('Güncellendi!', r.message, 'success').then(()=>location.reload());
                    } else {
                        Swal.fire('Hata!', r.message, 'error');
                    }
                }
            });
        }
    });
}

function deleteKategori(id, level, e) {
    e.stopPropagation();
    Swal.fire({
        title: 'Emin misiniz?',
        text: 'Bu kategori silinecektir!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, Sil',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: 'admin_islem.php',
                type: 'POST',
                data: { islem: 'sil_kategori', id: id, level: level },
                success: function(res) {
                    let r = JSON.parse(res);
                    if(r.status == 'success') {
                        Swal.fire('Silindi!', r.message, 'success').then(()=>location.reload());
                    }
                }
            });
        }
    });
}
</script>
