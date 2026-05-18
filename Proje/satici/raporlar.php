<?php
$satici_id = $satici['id'];
$yil = isset($_GET['yil']) ? intval($_GET['yil']) : date('Y');
$ay = isset($_GET['ay']) ? intval($_GET['ay']) : date('n');

$aylar_isim = [1=>'Ocak', 2=>'Şubat', 3=>'Mart', 4=>'Nisan', 5=>'Mayıs', 6=>'Haziran', 7=>'Temmuz', 8=>'Ağustos', 9=>'Eylül', 10=>'Ekim', 11=>'Kasım', 12=>'Aralık'];

// Aylık Özet
$aylik_query = "SELECT 
                    SUM(sd.birim_fiyat * sd.adet) as ciro,
                    SUM(sd.adet) as satilan_urun,
                    COUNT(DISTINCT s.id) as tamamlanan_siparis
                FROM t_siparis_detay sd
                JOIN t_siparis s ON sd.siparis_ID = s.id
                JOIN t_urun u ON sd.urun_ID = u.id
                WHERE u.satici_ID = $satici_id AND s.kargo_durum = 'Teslim Edildi'
                AND YEAR(s.siparis_tarih) = $yil AND MONTH(s.siparis_tarih) = $ay";
$aylik = $db->query($aylik_query)->fetch_assoc();

// Aylık Detay Tablosu
$aylik_detay_res = $db->query("SELECT s.siparis_tarih, u.urun_adi, sd.adet, sd.birim_fiyat as fiyat
                      FROM t_siparis_detay sd
                      JOIN t_siparis s ON sd.siparis_ID = s.id
                      JOIN t_urun u ON sd.urun_ID = u.id
                      WHERE u.satici_ID = $satici_id AND s.kargo_durum = 'Teslim Edildi'
                      AND YEAR(s.siparis_tarih) = $yil AND MONTH(s.siparis_tarih) = $ay
                      ORDER BY s.siparis_tarih DESC");

// Yıllık Özet
$yillik_ciro = $db->query("SELECT SUM(sd.birim_fiyat * sd.adet) as yillik_ciro
                 FROM t_siparis_detay sd
                 JOIN t_siparis s ON sd.siparis_ID = s.id
                 JOIN t_urun u ON sd.urun_ID = u.id
                 WHERE u.satici_ID = $satici_id AND s.kargo_durum = 'Teslim Edildi'
                 AND YEAR(s.siparis_tarih) = $yil")->fetch_assoc()['yillik_ciro'] ?? 0;

$en_cok_satan = $db->query("SELECT u.urun_adi, SUM(sd.adet) as toplam_adet
                       FROM t_siparis_detay sd
                       JOIN t_siparis s ON sd.siparis_ID = s.id
                       JOIN t_urun u ON sd.urun_ID = u.id
                       WHERE u.satici_ID = $satici_id AND s.kargo_durum = 'Teslim Edildi' AND YEAR(s.siparis_tarih) = $yil
                       GROUP BY u.id
                       ORDER BY toplam_adet DESC LIMIT 1")->fetch_assoc();

// Yıllık Kırılım
$aylar_res = $db->query("SELECT MONTH(s.siparis_tarih) as m_ay, COUNT(DISTINCT s.id) as siparis_sayisi, SUM(sd.adet) as adet_sayisi, SUM(sd.birim_fiyat * sd.adet) as ciro
                FROM t_siparis_detay sd
                JOIN t_siparis s ON sd.siparis_ID = s.id
                JOIN t_urun u ON sd.urun_ID = u.id
                WHERE u.satici_ID = $satici_id AND s.kargo_durum = 'Teslim Edildi' AND YEAR(s.siparis_tarih) = $yil
                GROUP BY MONTH(s.siparis_tarih)");
$aylar_data = [];
while($r = $aylar_res->fetch_assoc()) { $aylar_data[$r['m_ay']] = $r; }
?>

<style>
    .report-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .report-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }
    .icon-blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .icon-gold { background: rgba(212, 175, 55, 0.1); color: var(--gold); }
    .icon-green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    
    .report-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--navy);
        margin: 0;
    }
    .report-title {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    
    .nav-pills .nav-link {
        color: #64748b;
        font-weight: 700;
        border-radius: 8px;
        padding: 10px 20px;
    }
    .nav-pills .nav-link.active {
        background-color: var(--navy);
        color: white;
    }
</style>

<div class="bg-white p-3 rounded-3 shadow-sm border border-light mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <ul class="nav nav-pills" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="aylik-tab" data-bs-toggle="pill" data-bs-target="#aylik-rapor" type="button" role="tab">Aylık Görünüm</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="yillik-tab" data-bs-toggle="pill" data-bs-target="#yillik-rapor" type="button" role="tab">Yıllık Görünüm</button>
        </li>
    </ul>
</div>

<div class="tab-content" id="reportTabsContent">
    
    <!-- AYLIK RAPOR SEKMESİ -->
    <div class="tab-pane fade show active" id="aylik-rapor" role="tabpanel">
        <form method="GET" action="index.php" class="d-flex gap-3 mb-4 w-50">
            <input type="hidden" name="sayfa" value="raporlar">
            <select name="ay" class="form-select fw-bold border-secondary shadow-sm" onchange="this.form.submit()">
                <?php for($i=1; $i<=12; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $ay ? 'selected' : '' ?>><?= $aylar_isim[$i] ?></option>
                <?php endfor; ?>
            </select>
            <select name="yil" class="form-select fw-bold border-secondary shadow-sm" onchange="this.form.submit()">
                <?php for($i=date('Y'); $i>=2020; $i--): ?>
                    <option value="<?= $i ?>" <?= $i == $yil ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </form>

        <!-- Aylık Özet Kartları -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="report-card">
                    <div class="report-icon icon-blue"><i class="bi bi-wallet2"></i></div>
                    <div>
                        <div class="report-title"><?= $aylar_isim[$ay] ?> Ayı Toplam Ciro</div>
                        <h3 class="report-value"><?= number_format($aylik['ciro'] ?? 0, 2, ',', '.') ?> ₺</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <div class="report-icon icon-gold"><i class="bi bi-box-seam"></i></div>
                    <div>
                        <div class="report-title"><?= $aylar_isim[$ay] ?> Ayı Satılan Ürün</div>
                        <h3 class="report-value"><?= intval($aylik['satilan_urun'] ?? 0) ?> Adet</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <div class="report-icon icon-green"><i class="bi bi-cart-check"></i></div>
                    <div>
                        <div class="report-title">Teslim Edilmiş Sipariş</div>
                        <h3 class="report-value"><?= intval($aylik['tamamlanan_siparis'] ?? 0) ?> Adet</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aylık Detay Tablosu -->
        <div class="bg-white p-4 rounded-3 shadow-sm border border-light">
            <h5 class="fw-bold mb-4" style="color:var(--navy);"><?= $aylar_isim[$ay] ?> Ayı Satış Özeti</h5>
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Ürün Adı</th>
                            <th>Adet</th>
                            <th>Birim Fiyat</th>
                            <th>Toplam Tutar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($aylik_detay_res && $aylik_detay_res->num_rows > 0): ?>
                            <?php while($d = $aylik_detay_res->fetch_assoc()): ?>
                            <tr>
                                <td class="text-muted"><?= date('d.m.Y H:i', strtotime($d['siparis_tarih'])) ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($d['urun_adi']) ?></td>
                                <td><?= $d['adet'] ?></td>
                                <td><?= number_format($d['fiyat'], 2, ',', '.') ?> ₺</td>
                                <td class="fw-bold text-success"><?= number_format($d['fiyat'] * $d['adet'], 2, ',', '.') ?> ₺</td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Bu aya ait satış bulunamadı.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- YILLIK RAPOR SEKMESİ -->
    <div class="tab-pane fade" id="yillik-rapor" role="tabpanel">
        <form method="GET" action="index.php" class="d-flex gap-3 mb-4 w-25">
            <input type="hidden" name="sayfa" value="raporlar">
            <select name="yil" class="form-select fw-bold border-secondary shadow-sm" onchange="this.form.submit()">
                <?php for($i=date('Y'); $i>=2020; $i--): ?>
                    <option value="<?= $i ?>" <?= $i == $yil ? 'selected' : '' ?>><?= $i ?> Yılı</option>
                <?php endfor; ?>
            </select>
        </form>

        <!-- Yıllık Özet Kartları -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="report-card" style="border-left: 4px solid #3b82f6;">
                    <div class="report-icon icon-blue"><i class="bi bi-graph-up-arrow"></i></div>
                    <div>
                        <div class="report-title">Yıllık Toplam Hasılat</div>
                        <h3 class="report-value"><?= number_format($yillik_ciro, 2, ',', '.') ?> ₺</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card" style="border-left: 4px solid var(--gold);">
                    <div class="report-icon icon-gold"><i class="bi bi-award"></i></div>
                    <div>
                        <div class="report-title">Yılın En Çok Satılanı</div>
                        <h3 class="report-value" style="font-size:1.2rem; margin-top:5px;">
                            <?= $en_cok_satan ? htmlspecialchars($en_cok_satan['urun_adi']) . ' (' . $en_cok_satan['toplam_adet'] . ' Adet)' : 'Satış Yok' ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yıllık Detay Tablosu (Aylara Göre) -->
        <div class="bg-white p-4 rounded-3 shadow-sm border border-light">
            <h5 class="fw-bold mb-4" style="color:var(--navy);"><?= $yil ?> Yılı Aylık Ciro Dağılımı</h5>
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Ay</th>
                            <th>Tamamlanan Sipariş</th>
                            <th>Satılan Ürün (Adet)</th>
                            <th>Aylık Ciro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i=1; $i<=12; $i++): 
                            $data = $aylar_data[$i] ?? null;
                        ?>
                            <?php if($data): ?>
                            <tr>
                                <td class="fw-bold" style="color:var(--navy);"><?= $aylar_isim[$i] ?></td>
                                <td><?= $data['siparis_sayisi'] ?></td>
                                <td><?= $data['adet_sayisi'] ?></td>
                                <td class="fw-bold text-success"><?= number_format($data['ciro'], 2, ',', '.') ?> ₺</td>
                            </tr>
                            <?php else: ?>
                            <tr class="bg-light">
                                <td class="text-muted"><?= $aylar_isim[$i] ?></td>
                                <td class="text-muted">-</td>
                                <td class="text-muted">-</td>
                                <td class="text-muted">-</td>
                            </tr>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
