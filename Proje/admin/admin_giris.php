<?php
session_start();
require_once '../config.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php?sayfa=anasayfa");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kral Yolu - Yönetici Girişi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #0f172a; 
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }
        
        .admin-bg-shape {
            position: absolute;
            background: rgba(234, 179, 8, 0.03);
            border-radius: 50%;
            z-index: 0;
        }
        
        .shape-1 { width: 50vw; height: 50vw; top: -20vh; left: -10vw; }
        .shape-2 { width: 40vw; height: 40vw; bottom: -10vh; right: -10vw; }

        .login-card {
            background: #1e293b;
            padding: 50px 40px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .admin-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .admin-logo i {
            font-size: 3rem;
            color: #eab308;
            margin-bottom: 15px;
            display: inline-block;
        }
        .admin-logo h3 {
            color: white;
            font-weight: 800;
            letter-spacing: 2px;
            margin: 0;
        }
        .admin-logo small {
            color: #94a3b8;
            letter-spacing: 3px;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        .form-label {
            color: #cbd5e1;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid #334155;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
        }
        .form-control:focus {
            background: rgba(15, 23, 42, 0.9);
            color: white;
            border-color: #eab308;
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.1);
        }
        .form-control::placeholder {
            color: #475569;
        }
        .btn-admin {
            background: #eab308;
            color: #0f172a;
            border: none;
            padding: 12px;
            font-weight: 800;
            font-size: 1rem;
            border-radius: 8px;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            margin-top: 20px;
        }
        .btn-admin:hover {
            background: #ca8a04;
            transform: translateY(-2px);
        }
        .locked-info {
            text-align: center;
            color: #64748b;
            font-size: 0.8rem;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <div class="admin-bg-shape shape-1"></div>
    <div class="admin-bg-shape shape-2"></div>

    <div class="login-card">
        <div class="admin-logo">
            <i class="bi bi-shield-lock-fill"></i>
            <h3>KRAL YOLU</h3>
            <small>Merkezi Yönetim Portalı</small>
        </div>

        <form id="adminLoginForm">
            <div class="mb-4">
                <label class="form-label">Yönetici E-Posta</label>
                <div class="input-group">
                    <span class="input-group-text border-0" style="background:#0f172a; color:#cbd5e1;"><i class="bi bi-person"></i></span>
                    <input type="email" name="eposta" class="form-control border-start-0 ps-0" placeholder="admin@kralyolu.com" required>
                </div>
            </div>
            
            <div class="mb-2">
                <label class="form-label">Sistem Parolası</label>
                <div class="input-group">
                    <span class="input-group-text border-0" style="background:#0f172a; color:#cbd5e1;"><i class="bi bi-key"></i></span>
                    <input type="password" name="parola" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <a href="#" class="text-decoration-none" style="font-size:0.8rem; color:#eab308;">Parolamı Unuttum</a>
            </div>

            <button type="submit" class="btn-admin">Güvenli Giriş Yap</button>
        </form>

        <div class="locked-info">
            <i class="bi bi-info-circle me-1"></i> Bu alana sadece yetkili personeller erişebilir. İzinsiz giriş denemeleri kaydedilmektedir.
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    <script>
        $('#adminLoginForm').submit(function(e){
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'admin_islem.php',
                data: $(this).serialize() + '&islem=admin_giris',
                dataType: 'json',
                success: function(response){
                    if(response.status == 'success'){
                        window.location.href = 'index.php?sayfa=anasayfa';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: response.message,
                            confirmButtonColor: '#eab308'
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>
