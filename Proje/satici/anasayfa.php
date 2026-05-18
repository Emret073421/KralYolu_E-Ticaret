<?php
$satici_id = $satici['id'];

// 1. İstatistik Verileri
$aktif_urun_res = $db->query("SELECT COUNT(*) as total FROM t_urun WHERE satici_ID = $satici_id AND aktif = 'Evet'");
$aktif_urun_sayisi = $aktif_urun_res->fetch_assoc()['total'];

$bekleyen_siparis_res = $db->query("SELECT COUNT(DISTINCT s.id) as total 
                                   FROM t_siparis s
                                   JOIN t_siparis_detay sd ON s.id = sd.siparis_ID
                                   JOIN t_urun u ON sd.urun_ID = u.id 
                                   WHERE u.satici_ID = $satici_id AND s.kargo_durum = 'Hazırlanıyor'");
$bekleyen_siparis_sayisi = $bekleyen_siparis_res->fetch_assoc()['total'];

$ciro_res = $db->query("SELECT SUM(sd.adet * sd.birim_fiyat) as total 
                        FROM t_siparis_detay sd 
                        JOIN t_urun u ON sd.urun_ID = u.id 
                        JOIN t_siparis s ON sd.siparis_ID = s.id
                        WHERE u.satici_ID = $satici_id AND s.siparis_tarih >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$aylik_ciro = $ciro_res->fetch_assoc()['total'] ?? 0;

$s_count_res = $db->query("SELECT SUM(sd.adet) as total FROM t_siparis_detay sd JOIN t_urun u ON sd.urun_ID = u.id JOIN t_siparis s ON sd.siparis_ID = s.id WHERE u.satici_ID = $satici_id AND s.siparis_tarih >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$aylik_satis_adet = $s_count_res->fetch_assoc()['total'] ?? 0;

// 2. En Çok Satan 10 Ürün
$top_urunler = $db->query("SELECT u.urun_adi, SUM(sd.adet) as satis_sayisi, SUM(sd.adet * sd.birim_fiyat) as urun_ciro
                           FROM t_siparis_detay sd
                           JOIN t_urun u ON sd.urun_ID = u.id
                           WHERE u.satici_ID = $satici_id
                           GROUP BY u.id
                           ORDER BY satis_sayisi DESC LIMIT 10");

// 3. Son Siparişler
$son_siparisler = $db->query("SELECT s.id as siparis_no, u.urun_adi, uye.ad, uye.soyad, sd.adet, sd.birim_fiyat, s.kargo_durum
                              FROM t_siparis_detay sd
                              JOIN t_urun u ON sd.urun_ID = u.id
                              JOIN t_siparis s ON sd.siparis_ID = s.id
                              JOIN t_uye uye ON s.uye_ID = uye.id
                              WHERE u.satici_ID = $satici_id
                              ORDER BY s.id DESC LIMIT 5");
?>

<style>
/* ==========================================================================
   PREMIUM SATICI KONTROL PANELİ - ÖZEL TASARIM (NON-GENERIC)
   ========================================================================== */

/* İstatistik Kartları - Özel Premium Tasarım */
.premium-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-bottom: 35px;
}

.p-stat-card {
    border-radius: 24px;
    padding: 28px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.04);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 185px;
    border: 1px solid rgba(255,255,255,0.8);
}

.p-stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 22px 45px rgba(0,0,0,0.08);
}

/* 1. Kart: Aylık Satış (Dark Luxury Gradient) */
.p-stat-card.card-sales {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: white;
    border: 1px solid rgba(255,255,255,0.1);
}
.p-stat-card.card-sales .p-stat-icon { background: rgba(212,175,55,0.2); color: var(--gold); }
.p-stat-card.card-sales .p-stat-title { color: #94a3b8; }
.p-stat-card.card-sales .p-stat-value { color: white; }

/* 2. Kart: Aylık Ciro (Gold Glow Gradient) */
.p-stat-card.card-rev {
    background: linear-gradient(135deg, #d4af37 0%, #f59e0b 100%);
    color: #0f172a;
}
.p-stat-card.card-rev .p-stat-icon { background: rgba(15,23,42,0.15); color: #0f172a; }
.p-stat-card.card-rev .p-stat-title { color: rgba(15,23,42,0.75); }
.p-stat-card.card-rev .p-stat-value { color: #0f172a; }

/* 3. Kart: Bekleyen Sipariş (Soft Coral Glass) */
.p-stat-card.card-orders {
    background: linear-gradient(135deg, #ffffff 0%, #fff5f5 100%);
    border-color: #fee2e2;
}
.p-stat-card.card-orders .p-stat-icon { background: #fee2e2; color: #ef4444; }
.p-stat-card.card-orders .p-stat-title { color: #64748b; }
.p-stat-card.card-orders .p-stat-value { color: #0f172a; }

/* 4. Kart: Aktif Ürünler (Emerald Breeze Glass) */
.p-stat-card.card-products {
    background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
    border-color: #dcfce7;
}
.p-stat-card.card-products .p-stat-icon { background: #dcfce7; color: #10b981; }
.p-stat-card.card-products .p-stat-title { color: #64748b; }
.p-stat-card.card-products .p-stat-value { color: #0f172a; }

.p-stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.p-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    transition: transform 0.3s ease;
}
.p-stat-card:hover .p-stat-icon {
    transform: scale(1.1) rotate(5deg);
}

.p-stat-title {
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 15px;
    display: block;
}

.p-stat-value {
    font-size: 2.3rem;
    font-weight: 800;
    line-height: 1.1;
    margin-top: 5px;
    display: flex;
    align-items: baseline;
    gap: 6px;
}
.p-stat-value small { font-size: 1rem; font-weight: 600; opacity: 0.8; }

/* Mağaza Sağlığı & Operasyonel Durum (Sütunsuz/Grafiksiz Görselleştirme) */
.p-health-widget {
    background: white;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.04);
    border: 1px solid #f1f5f9;
    height: 100%;
}

.health-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    background: #f8fafc;
    border-radius: 18px;
    margin-bottom: 18px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s;
}
.health-item:hover {
    background: white;
    border-color: var(--gold);
    box-shadow: 0 8px 20px rgba(0,0,0,0.03);
}

.health-icon-box {
    width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-right: 18px;
}
.health-icon-box.gold { background: rgba(212,175,55,0.15); color: var(--gold); }
.health-icon-box.blue { background: rgba(59,130,246,0.15); color: #3b82f6; }
.health-icon-box.green { background: rgba(16,185,129,0.15); color: #10b981; }

.health-info h6 { font-weight: 800; margin: 0; color: var(--navy); font-size: 1rem; }
.health-info p { font-size: 0.85rem; color: #64748b; margin: 0; }

.health-status-pill {
    padding: 8px 18px; border-radius: 30px; font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;
}

/* En Çok Satanlar & Son Siparişler */
.p-dashboard-card {
    background: white;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.04);
    border: 1px solid #f1f5f9;
    height: 100%;
}

.p-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
}

.p-card-title { font-size: 1.25rem; font-weight: 800; color: var(--navy); margin: 0; }

.p-top-product-item {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    border-radius: 16px;
    transition: all 0.25s;
    border: 1px solid transparent;
    margin-bottom: 12px;
    background: #fcfcfd;
}
.p-top-product-item:hover {
    background: white;
    border-color: #cbd5e1;
    box-shadow: 0 8px 25px rgba(0,0,0,0.04);
    transform: scale(1.01);
}

.product-rank {
    width: 36px; height: 36px; border-radius: 12px; background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; margin-right: 18px;
}
.p-top-product-item:nth-child(1) .product-rank { background: var(--gold); color: var(--navy); }
.p-top-product-item:nth-child(2) .product-rank { background: #cbd5e1; color: var(--navy); }
.p-top-product-item:nth-child(3) .product-rank { background: #b45309; color: white; }

.p-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; }
.p-table th { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; padding: 0 20px 10px 20px; border: none; }
.p-table tbody tr { background: #f8fafc; transition: all 0.2s; }
.p-table tbody tr td:first-child { border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
.p-table tbody tr td:last-child { border-top-right-radius: 16px; border-bottom-right-radius: 16px; }
.p-table tbody tr td { padding: 18px 20px; border: none; vertical-align: middle; }
.p-table tbody tr:hover { background: white; box-shadow: 0 8px 25px rgba(0,0,0,0.04); transform: scale(1.005); }

.badge-custom { padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.75rem; }
.badge-preparing { background: #fef3c7; color: #b45309; }
.badge-shipped { background: #e0f2fe; color: #0369a1; }
.badge-delivered { background: #dcfce7; color: #15803d; }
</style>

<!-- 1. Premium İstatistik Kartları -->
<div class="premium-stat-grid">
    <div class="p-stat-card card-sales">
        <div class="p-stat-header">
            <div class="p-stat-icon"><i class="bi bi-cart-check-fill"></i></div>
            <span class="badge bg-light text-dark fw-bold px-3 py-1 rounded-pill" style="font-size:0.7rem;">Bu Ay</span>
        </div>
        <div>
            <span class="p-stat-title">AYLIK SATIŞ HACMİ</span>
            <div class="p-stat-value"><?= number_format($aylik_satis_adet) ?> <small>Adet</small></div>
        </div>
    </div>

    <div class="p-stat-card card-rev">
        <div class="p-stat-header">
            <div class="p-stat-icon"><i class="bi bi-cash-stack"></i></div>
            <span class="badge bg-navy text-white fw-bold px-3 py-1 rounded-pill" style="font-size:0.7rem;">Ciro</span>
        </div>
        <div>
            <span class="p-stat-title">AYLIK TOPLAM KAZANÇ</span>
            <div class="p-stat-value"><?= number_format($aylik_ciro, 0, ',', '.') ?> <small>₺</small></div>
        </div>
    </div>

    <div class="p-stat-card card-orders">
        <div class="p-stat-header">
            <div class="p-stat-icon"><i class="bi bi-clock-history"></i></div>
            <span class="badge bg-danger text-white fw-bold px-3 py-1 rounded-pill" style="font-size:0.7rem;">Acil</span>
        </div>
        <div>
            <span class="p-stat-title">BEKLEYEN SİPARİŞLER</span>
            <div class="p-stat-value"><?= $bekleyen_siparis_sayisi ?> <small>Sipariş</small></div>
        </div>
    </div>

    <div class="p-stat-card card-products">
        <div class="p-stat-header">
            <div class="p-stat-icon"><i class="bi bi-box2-heart-fill"></i></div>
            <span class="badge bg-success text-white fw-bold px-3 py-1 rounded-pill" style="font-size:0.7rem;">Yayında</span>
        </div>
        <div>
            <span class="p-stat-title">AKTİF VİTRİN ÜRÜNLERİ</span>
            <div class="p-stat-value"><?= $aktif_urun_sayisi ?> <small>Ürün</small></div>
        </div>
    </div>
</div>

<!-- 2. Orta Bölüm: En Çok Satanlar & Son Siparişler -->
<div class="row g-4 mb-4">
    <!-- En Çok Satan 10 Ürün -->
    <div class="col-lg-6">
        <div class="p-dashboard-card">
            <div class="p-card-header">
                <h5 class="p-card-title">En Çok Satan 10 Ürün</h5>
                <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-bold">Top Performans</span>
            </div>
            
            <div class="top-products-container" style="max-height: 420px; overflow-y: auto; padding-right: 5px;">
                <?php if($top_urunler && $top_urunler->num_rows > 0): $rank = 1; ?>
                    <?php while($tu = $top_urunler->fetch_assoc()): ?>
                        <div class="p-top-product-item">
                            <div class="product-rank"><?= $rank++ ?></div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1" style="color:var(--navy); font-size:0.95rem;"><?= htmlspecialchars($tu['urun_adi']) ?></h6>
                                <small class="text-muted fw-semibold"><?= $tu['satis_sayisi'] ?> Adet Satış • <span class="text-dark"><?= number_format($tu['urun_ciro'], 0, ',', '.') ?> ₺</span> Kazanç</small>
                            </div>
                            <div class="ms-3">
                                <span class="badge bg-light text-dark border px-3 py-1 rounded-pill fw-bold" style="font-size:0.75rem;"><i class="bi bi-graph-up-arrow text-success me-1"></i> Trend</span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-3 fw-medium">Henüz yeterli satış verisi bulunmamaktadır.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Son Gelen Siparişler Tablosu -->
    <div class="col-lg-6">
        <div class="p-dashboard-card">
            <div class="p-card-header">
                <h5 class="p-card-title">Son Gelen Siparişler</h5>
                <a href="index.php?sayfa=siparisler" class="btn btn-sm px-4 py-2 fw-bold rounded-pill" style="background:var(--gold); color:var(--navy);">Tümünü Yönet</a>
            </div>
            
            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th>Ürün Adı</th>
                            <th>Müşteri</th>
                            <th>Tutar</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($son_siparisler && $son_siparisler->num_rows > 0): ?>
                            <?php while ($sip = $son_siparisler->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= mb_substr(htmlspecialchars($sip['urun_adi']), 0, 20) ?>...</td>
                                    <td class="fw-medium"><?= htmlspecialchars($sip['ad'] . ' ' . mb_substr($sip['soyad'],0,1).'.') ?></td>
                                    <td class="fw-bold" style="color:var(--navy); font-size:0.95rem;"><?= number_format($sip['adet'] * $sip['birim_fiyat'], 0, ',', '.') ?> ₺</td>
                                    <td>
                                        <?php 
                                        $badgeClass = 'badge-preparing';
                                        if ($sip['kargo_durum'] === 'Kargoya Verildi') $badgeClass = 'badge-shipped';
                                        if ($sip['kargo_durum'] === 'Teslim Edildi') $badgeClass = 'badge-delivered';
                                        ?>
                                        <span class="badge-custom <?= $badgeClass ?>"><?= $sip['kargo_durum'] ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted fw-medium">Henüz sipariş bulunmamaktadır.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
