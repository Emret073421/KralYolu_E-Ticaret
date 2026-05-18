<?php
require_once '../config.php';
if (isset($_SESSION['satici'])) {
    header('Location: index.php?sayfa=anasayfa');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kral Yolu - Satıcı Girişi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Ortak CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 40px 20px;
        }

        .seller-auth-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            display: flex;
            min-height: 600px;
        }

        .seller-auth-brand {
            background: linear-gradient(135deg, var(--navy) 0%, #1e293b 100%);
            width: 45%;
            padding: 50px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }
        
        .seller-auth-brand::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&q=80') center/cover;
            opacity: 0.1;
            z-index: 0;
        }

        .brand-content {
            z-index: 1;
        }

        .brand-logo i {
            color: var(--gold);
            font-size: 2.5rem;
        }
        .brand-logo h3 {
            font-weight: 800;
            letter-spacing: 1px;
            margin-top: 10px;
            margin-bottom: 0;
        }

        .brand-slogan h2 {
            font-weight: 700;
            font-size: 2rem;
            line-height: 1.3;
            margin-bottom: 15px;
        }
        .brand-slogan p {
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        .seller-auth-form {
            width: 55%;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .nav-tabs {
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 30px;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            font-weight: 700;
            padding: 10px 0;
            margin-right: 30px;
            border-bottom: 3px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: var(--navy);
            border-bottom-color: var(--gold);
            background: transparent;
        }

        .form-label {
            font-weight: 600;
            color: var(--navy);
            font-size: 0.9rem;
        }
        .form-control, .form-select {
            border: 1px solid #cbd5e1;
            padding: 12px 18px;
            border-radius: 8px;
            background: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: none;
            border-color: var(--navy);
            background: white;
        }

        .btn-seller-login {
            background: var(--navy);
            color: white;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            margin-top: 10px;
            transition: all 0.2s;
        }
        .btn-seller-login:hover {
            background: var(--gold);
            color: var(--navy);
        }

        @media (max-width: 768px) {
            .seller-auth-container {
                flex-direction: column;
                margin: 20px 0;
            }
            .seller-auth-brand, .seller-auth-form {
                width: 100%;
                padding: 30px;
            }
            .seller-auth-brand {
                min-height: 250px;
            }
        }
    </style>
</head>
<body>

    <div class="seller-auth-container">
        
        <div class="seller-auth-brand">
            <div class="brand-content brand-logo">
                <i class="bi bi-shop-window"></i>
                <h3>KRAL YOLU</h3>
                <small style="color:var(--gold); letter-spacing: 2px;">SATICI PORTALI</small>
            </div>
            
            <div class="brand-content brand-slogan mt-5">
                <h2>Satışlarınızı Milyonlara Ulaştırın!</h2>
                <p>Kral Yolu İş Ortaklığı ile mağazanızı hemen kurun, güvenli ödeme altyapımızla işinizi büyütmeye bugün başlayın.</p>
            </div>
            
            <div class="brand-content mt-4" style="font-size: 0.8rem; color: #94a3b8;">
                &copy; 2026 Kral Yolu Bilişim Sistemleri
            </div>
        </div>

        <div class="seller-auth-form">
            
            <ul class="nav nav-tabs" id="sellerTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Mağaza Girişi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Satıcı Ol (Başvuru)</button>
                </li>
            </ul>

            <div class="tab-content" id="sellerTabContent">
                
                <!-- Giriş Yap -->
                <div class="tab-pane fade show active" id="login" role="tabpanel">
                    <h4 class="fw-bold mb-4" style="color:var(--navy);">Hesabınıza Giriş Yapın</h4>
                    
                    <form id="saticiGirisForm">
                        <input type="hidden" name="islem" value="satici_giris">
                        <div class="mb-3">
                            <label class="form-label">Kurumsal E-Posta</label>
                            <input type="email" name="eposta" class="form-control" placeholder="ornek@magaza.com" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Şifre</label>
                            <input type="password" name="sifre" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label text-muted small" for="rememberMe">Beni Hatırla</label>
                            </div>
                            <a href="#" class="text-decoration-none small" style="color:var(--gold); font-weight:600;">Şifremi Unuttum</a>
                        </div>
                        <button type="submit" class="btn btn-seller-login border-0">Giriş Yap</button>
                    </form>
                </div>

                <!-- Kayıt -->
                <div class="tab-pane fade" id="register" role="tabpanel">
                    <h4 class="fw-bold mb-4" style="color:var(--navy);">İş Ortağımız Olun</h4>
                    
                    <form id="saticiKayitForm">
                        <input type="hidden" name="islem" value="satici_kayit">
                        
                        <!-- Temel Mağaza Bilgileri -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mağaza Adı (Görünen)</label>
                                <input type="text" name="kurum_adi" class="form-control" placeholder="Müşterilerin göreceği ad" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ticari Ünvan (Resmi)</label>
                                <input type="text" name="ticari_unvan" class="form-control" placeholder="Resmi tescilli ünvanınız" required>
                            </div>
                        </div>

                        <!-- Şirket Türü ve İletişim -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Şirket Türü</label>
                                <select name="sirket_turu" class="form-select border-1" style="background:#f8fafc; padding: 12px 18px; border-radius: 8px;" required>
                                    <option value="" selected disabled>Seçiniz</option>
                                    <option value="Şahıs Şirketi">Şahıs Şirketi</option>
                                    <option value="Limited Şirket (Ltd. Şti.)">Limited Şirket (Ltd. Şti.)</option>
                                    <option value="Anonim Şirket (A.Ş.)">Anonim Şirket (A.Ş.)</option>
                                    <option value="Kolektif/Komandit Şirket">Kolektif/Komandit Şirket</option>
                                </select>
                            </div>
                        </div>

                        <!-- Yetkili -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Yetkili Ad</label>
                                <input type="text" name="ad" class="form-control" placeholder="Adınız" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Yetkili Soyad</label>
                                <input type="text" name="soyad" class="form-control" placeholder="Soyadınız" required>
                            </div>
                        </div>

                        <!-- Giriş Bilgileri -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Kurumsal E-Posta</label>
                                <input type="email" name="eposta" class="form-control" placeholder="iletisim@firma.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Şifre Belirleyin</label>
                                <input type="password" name="parola" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-seller-login border-0">Başvuru Talebini Gönder</button>
                        <p class="text-muted text-center mt-3" style="font-size:0.75rem;">Gönderilen bilgiler KRAL YOLU adminleri tarafından incelendikten sonra onay süreci tamamlanacaktır.</p>
                    </form>
                </div>

            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $('#saticiGirisForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'satici_islem.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') window.location.href = 'index.php?sayfa=anasayfa';
                else Swal.fire('Hata!', data.message, 'error');
            },
            error: function() {
                Swal.fire('Hata!', 'Sunucuyla iletişim kurulurken bir sorun oluştu.', 'error');
            }
        });
    });

    $('#saticiKayitForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'satici_islem.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    Swal.fire('Başarılı!', data.message, 'success').then(() => {
                        $('#login-tab').tab('show');
                        $('#saticiKayitForm')[0].reset();
                    });
                }
                else Swal.fire('Hata!', data.message, 'error');
            },
            error: function() {
                Swal.fire('Hata!', 'Sunucuyla iletişim kurulurken bir sorun oluştu.', 'error');
            }
        });
    });
    </script>
</body>
</html>
