<style>
    .auth-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
        width: calc(100% - 30px);
        max-width: 1000px;
        display: flex;
        min-height: 600px;
        margin: 40px auto;
    }

    .auth-brand {
        background: linear-gradient(135deg, var(--navy) 0%, #1e293b 100%);
        width: 45%;
        padding: 50px 40px;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    
    .auth-brand::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('https://images.unsplash.com/photo-1557821552-17105176677c?w=800&q=80') center/cover;
        opacity: 0.1;
        z-index: 0;
    }

    .brand-content { z-index: 1; }
    .brand-logo i { color: var(--gold); font-size: 2.5rem; }
    .brand-logo h3 { font-weight: 800; letter-spacing: 1px; margin-top: 10px; margin-bottom: 0; }
    .brand-slogan h2 { font-weight: 700; font-size: 2rem; line-height: 1.3; margin-bottom: 15px; }
    .brand-slogan p { color: #cbd5e1; font-size: 0.95rem; }

    .auth-form {
        width: 55%;
        padding: 40px 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
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

    .btn-auth {
        background: var(--navy);
        color: white;
        font-weight: 700;
        padding: 12px;
        border-radius: 8px;
        width: 100%;
        margin-top: 10px;
        transition: all 0.2s;
    }
    .btn-auth:hover { background: var(--gold); color: var(--navy); }

    @media (max-width: 768px) {
        .auth-container {
            flex-direction: column;
            min-height: auto;
        }
        .auth-brand {
            width: 100%;
            padding: 30px 20px;
        }
        .auth-form {
            width: 100%;
            padding: 30px 20px;
        }
    }
</style>

<div class="auth-container">
    
    <!-- Sol Pazarlama Kısmı -->
    <div class="auth-brand">
        <div class="brand-content brand-logo">
            <i class="bi bi-person-circle"></i>
            <h3>KRAL YOLU</h3>
            <small style="color:var(--gold); letter-spacing: 2px;">MÜŞTERİ PANELİ</small>
        </div>
        
        <div class="brand-content brand-slogan mt-5">
            <h2>Alışverişin Keyfini Çıkarın!</h2>
            <p>Kral Yolu dünyasına katılın, binlerce ürün arasından dilediğinizi seçin ve ayrıcalıklı fırsatlardan yararlanın.</p>
        </div>
        
        <div class="brand-content mt-4" style="font-size: 0.8rem; color: #94a3b8;">
            &copy; 2026 Kral Yolu Bilişim Sistemleri
        </div>
    </div>

    <!-- Sağ Giriş/Kayıt Kısmı -->
    <div class="auth-form">
        
        <?php if (isset($_GET['basari']) && $_GET['basari'] == 'kayit'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Kayıt başarılı! Şimdi giriş yapabilirsiniz.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['hata'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    if ($_GET['hata'] == 'sifre') echo "Geçersiz şifre.";
                    elseif ($_GET['hata'] == 'kullanici') echo "Kullanıcı bulunamadı.";
                    elseif ($_GET['hata'] == 'kayit') echo "Kayıt sırasında hata oluştu: " . htmlspecialchars($_GET['msg'] ?? '');
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Sekmeler -->
        <ul class="nav nav-tabs mb-4" id="authTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Giriş Yap</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Üye Ol</button>
            </li>
        </ul>

        <div class="tab-content" id="authTabContent">
            
            <!-- Giriş Yap -->
            <div class="tab-pane fade show active" id="login" role="tabpanel">
                <h4 class="fw-bold mb-4" style="color:var(--navy);">Hesabınıza Giriş Yapın</h4>
                
                <form action="data.php" method="POST">
                    <input type="hidden" name="islem" value="giris">
                    <div class="mb-3">
                        <label class="form-label">E-Posta</label>
                        <input type="email" name="eposta" class="form-control" placeholder="ornek@mail.com" required>
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
                    <button type="submit" class="btn btn-auth border-0">Giriş Yap</button>
                </form>
            </div>

            <!-- Üye Ol -->
            <div class="tab-pane fade" id="register" role="tabpanel">
                <h4 class="fw-bold mb-4" style="color:var(--navy);">Aramıza Katılın</h4>
                
                <form action="data.php" method="POST">
                    <input type="hidden" name="islem" value="kayit">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Ad</label>
                            <input type="text" name="ad" class="form-control" placeholder="Adınız" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Soyad</label>
                            <input type="text" name="soyad" class="form-control" placeholder="Soyadınız" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-Posta</label>
                        <input type="email" name="e_posta" class="form-control" placeholder="ornek@mail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şifre Belirleyin</label>
                        <input type="password" name="parola" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-auth border-0">Üye Ol</button>
                </form>
            </div>
        </div>
    </div>
</div>