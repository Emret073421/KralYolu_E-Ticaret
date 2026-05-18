<?php
ob_start();
require_once '../config.php';

// Satıcı oturum kontrolü (Giriş ve Kayıt hariç)
$istisna_islemler = ['logout', 'satici_giris', 'satici_kayit'];
if (!isset($_SESSION['satici']) && !in_array($_GET['islem'] ?? '', $istisna_islemler) && !in_array($_POST['islem'] ?? '', $istisna_islemler)) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum bulunamadı.']);
    exit;
}

$satici_id = $_SESSION['satici']['id'] ?? null;
$islem = $_GET['islem'] ?? ($_POST['islem'] ?? '');

// Alt Kategorileri Getir (AJAX)
if ($islem === 'get_alt_kategoriler') {
    $parent_id = intval($_GET['parent_id']);
    $seviye = intval($_GET['seviye']);
    $res = null;
    if ($seviye === 1) {
        $res = $db->query("SELECT id, kategori_adi FROM t_alt_kategori_1 WHERE kategori_id = $parent_id");
    } else {
        $res = $db->query("SELECT id, kategori_adi FROM t_alt_kategori_2 WHERE alt_1_ID = $parent_id");
    }
    $output = [];
    while($row = $res->fetch_assoc()) $output[] = $row;
    echo json_encode($output);
    exit;
}

// Logout
if ($islem === 'logout') {
    unset($_SESSION['satici']);
    header('Location: index.php?sayfa=giris');
    exit;
}

// Ürün Silme
if ($islem === 'urun_sil') {
    $id = intval($_GET['id']);
    $check = $db->query("SELECT id FROM t_urun WHERE id = $id AND satici_ID = $satici_id");
    if ($check->num_rows > 0) {
        $db->query("DELETE FROM t_urun WHERE id = $id");
        $db->query("DELETE FROM t_foto WHERE urun_ID = $id");
        echo json_encode(['status' => 'success', 'message' => 'Ürün başarıyla silindi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Yetkisiz işlem!']);
    }
    exit;
}

// Sipariş Durumu Güncelleme
if ($islem === 'siparis_durum') {
    $id = intval($_GET['id']);
    $durum = $db->real_escape_string($_GET['durum']);
    $check = $db->query("SELECT sd.id FROM t_siparis_detay sd JOIN t_urun u ON sd.urun_ID = u.id WHERE sd.siparis_ID = $id AND u.satici_ID = $satici_id");
    if ($check->num_rows > 0) {
        $db->query("UPDATE t_siparis SET kargo_durum = '$durum' WHERE id = $id");
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Yetkisiz işlem!']);
    }
    exit;
}

// Fotoğraf Sıralama (SortableJS)
if ($islem === 'foto_sirala') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data && isset($data['sirali_idler'])) {
        foreach ($data['sirali_idler'] as $index => $foto_id) {
            $sira = $index + 1;
            $db->query("UPDATE t_foto SET foto_sira = $sira WHERE id = " . intval($foto_id));
        }
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// Fotoğraf Silme
if ($islem === 'foto_sil') {
    $id = intval($_GET['id']);
    $db->query("DELETE FROM t_foto WHERE id = $id");
    echo json_encode(['status' => 'success']);
    exit;
}

// POST İşlemleri (Ekleme / Güncelleme / Giriş / Kayıt)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if ($islem === 'urun_ekle' || $islem === 'urun_guncelle') {
        $ad = $db->real_escape_string($_POST['urun_adi']);
        $aciklama = $db->real_escape_string($_POST['aciklama']);
        $fiyat = floatval($_POST['fiyat']);
        $stok = intval($_POST['stok']);
        $kategori_id = intval($_POST['kategori_id']);
        
        $is_ekle = ($islem === 'urun_ekle');
        
        if ($is_ekle) {
            $query = "INSERT INTO t_urun (urun_adi, urun_aciklama, fiyat, stok, satici_ID, aktif) 
                      VALUES ('$ad', '$aciklama', $fiyat, $stok, $satici_id, 'Evet')";
            if ($db->query($query)) {
                $urun_id = $db->insert_id;
                $alt1 = !empty($_POST['alt_kategori_1_ID']) ? intval($_POST['alt_kategori_1_ID']) : "NULL";
                $alt2 = !empty($_POST['alt_kategori_2_ID']) ? intval($_POST['alt_kategori_2_ID']) : "NULL";
                if ($db->query("INSERT INTO t_urun_kategori (urun_ID, kategori_ID, alt_kategori_1_ID, alt_kategori_2_ID) 
                            VALUES ($urun_id, $kategori_id, $alt1, $alt2)")) {
                    handlePhotos($urun_id);
                    handleTabloDetay($urun_id, $db);
                    ob_clean();
                    echo json_encode(['status' => 'success', 'message' => 'Ürün başarıyla eklendi.', 'id' => $urun_id]);
                } else {
                    ob_clean();
                    echo json_encode(['status' => 'error', 'message' => 'Kategori ekleme hatası: ' . $db->error]);
                }
            } else {
                ob_clean();
                echo json_encode(['status' => 'error', 'message' => 'Ürün ekleme hatası: ' . $db->error]);
            }
        } else {
            $urun_id = intval($_POST['urun_id']);
            $query = "UPDATE t_urun SET urun_adi = '$ad', urun_aciklama = '$aciklama', fiyat = $fiyat, stok = $stok 
                      WHERE id = $urun_id AND satici_ID = $satici_id";
            if ($db->query($query)) {
                $alt1 = !empty($_POST['alt_kategori_1_ID']) ? intval($_POST['alt_kategori_1_ID']) : "NULL";
                $alt2 = !empty($_POST['alt_kategori_2_ID']) ? intval($_POST['alt_kategori_2_ID']) : "NULL";
                if ($db->query("UPDATE t_urun_kategori SET kategori_ID = $kategori_id, alt_kategori_1_ID = $alt1, alt_kategori_2_ID = $alt2 WHERE urun_ID = $urun_id")) {
                    handlePhotos($urun_id);
                    handleTabloDetay($urun_id, $db);
                    ob_clean();
                    echo json_encode(['status' => 'success', 'message' => 'Ürün güncellendi.']);
                } else {
                    ob_clean();
                    echo json_encode(['status' => 'error', 'message' => 'Kategori güncelleme hatası: ' . $db->error]);
                }
            } else {
                ob_clean();
                echo json_encode(['status' => 'error', 'message' => 'Ürün güncelleme hatası: ' . $db->error]);
            }
        }
    }

    // Satıcı Giriş
    if ($islem === 'satici_giris') {
        $eposta = $db->real_escape_string($_POST['eposta']);
        $sifre = $_POST['sifre'];
        $res = $db->query("SELECT * FROM t_satici WHERE e_posta = '$eposta'");
        if ($res && $res->num_rows > 0) {
            $s = $res->fetch_assoc();
            if (password_verify($sifre, $s['parola']) || $sifre === $s['parola']) {
                if (isset($s['onay_durumu']) && $s['onay_durumu'] !== 'Onaylandı') {
                    ob_clean();
                    echo json_encode(['status' => 'error', 'message' => 'Mağaza başvurunuz onay sürecindedir. Yönetici onayından sonra giriş yapabilirsiniz.']);
                    exit;
                }
                $_SESSION['satici'] = ['id' => $s['id'], 'kurum_adi' => $s['kurum_adi']];
                ob_clean();
                echo json_encode(['status' => 'success', 'message' => 'Giriş başarılı!']);
            } else { 
                ob_clean();
                echo json_encode(['status' => 'error', 'message' => 'Hatalı şifre.']); 
            }
        } else { 
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Mağaza bulunamadı.']); 
        }
    }

    // Satıcı Kayıt (Başvuru)
    if ($islem === 'satici_kayit') {
        $kurum = $db->real_escape_string($_POST['kurum_adi']);
        $eposta = $db->real_escape_string($_POST['eposta']);
        $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);
        $ad = $db->real_escape_string($_POST['ad']);
        $soyad = $db->real_escape_string($_POST['soyad']);
        $ticari_unvan = $db->real_escape_string($_POST['ticari_unvan']);
        $sirket_turu = $db->real_escape_string($_POST['sirket_turu']);
        
        $query = "INSERT INTO t_satici (kurum_adi, e_posta, parola, ticari_unvan, sirket_turu, ad, soyad) 
                  VALUES ('$kurum', '$eposta', '$parola', '$ticari_unvan', '$sirket_turu', '$ad', '$soyad')";
        if ($db->query($query)) { 
            ob_clean();
            echo json_encode(['status' => 'success', 'message' => 'Başvurunuz alındı. Giriş yapabilirsiniz.']); 
        }
        else { 
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Kayıt hatası: ' . $db->error]); 
        }
    }

    // Mağaza Ayarlarını Güncelleme
    if ($islem === 'magaza_ayarlarini_guncelle') {
        $kurum = $db->real_escape_string($_POST['kurum_adi']);
        $ticari_unvan = $db->real_escape_string($_POST['ticari_unvan']);
        $sirket_turu = $db->real_escape_string($_POST['sirket_turu']);
        $vergi_dairesi = $db->real_escape_string($_POST['vergi_dairesi']);
        $vergi_no = $db->real_escape_string($_POST['vergi_no']);
        $mersis_no = $db->real_escape_string($_POST['mersis_no']);
        $kep_adresi = $db->real_escape_string($_POST['kep_adresi']);
        $kurumsal_telefon = $db->real_escape_string($_POST['kurumsal_telefon']);
        $yetkili_ad_soyad = explode(' ', $_POST['yetkili_ad_soyad'] ?? '', 2);
        $ad = $db->real_escape_string($yetkili_ad_soyad[0]);
        $soyad = $db->real_escape_string($yetkili_ad_soyad[1] ?? '');
        $iban = $db->real_escape_string($_POST['iban']);
        $sehir = $db->real_escape_string($_POST['sehir']);
        $ilce = $db->real_escape_string($_POST['ilce']);
        $mahalle = $db->real_escape_string($_POST['mahalle']);
        $kurum_adres = $db->real_escape_string($_POST['kurum_adres']);
        
        $parola_query = "";
        if (!empty($_POST['yeni_parola'])) {
            $mevcut_parola = $_POST['mevcut_parola'];
            $res = $db->query("SELECT parola FROM t_satici WHERE id = $satici_id");
            if ($res && $row = $res->fetch_assoc()) {
                if (password_verify($mevcut_parola, $row['parola']) || $mevcut_parola === $row['parola']) {
                    $yeni_parola_hash = password_hash($_POST['yeni_parola'], PASSWORD_DEFAULT);
                    $parola_query = ", parola = '$yeni_parola_hash'";
                } else {
                    ob_clean();
                    echo json_encode(['status' => 'error', 'message' => 'Mevcut şifrenizi yanlış girdiniz.']);
                    exit;
                }
            }
        }

        $query = "UPDATE t_satici SET 
                    kurum_adi = '$kurum', 
                    ticari_unvan = '$ticari_unvan', 
                    sirket_turu = '$sirket_turu', 
                    vergi_dairesi = '$vergi_dairesi', 
                    vergi_no = '$vergi_no', 
                    mersis_no = '$mersis_no', 
                    kep_adresi = '$kep_adresi', 
                    kurumsal_telefon = '$kurumsal_telefon', 
                    ad = '$ad', 
                    soyad = '$soyad', 
                    iban = '$iban', 
                    il = '$sehir', 
                    ilce = '$ilce', 
                    mahalle = '$mahalle', 
                    kurum_adres = '$kurum_adres'
                    $parola_query
                  WHERE id = $satici_id";
                  
        if ($db->query($query)) { 
            ob_clean();
            echo json_encode(['status' => 'success', 'message' => 'Mağaza ayarları başarıyla güncellendi.']); 
        } else { 
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Güncelleme hatası: ' . $db->error]); 
        }
    }
    exit;
}

function handlePhotos($urun_id) {
    global $db;
    if (isset($_FILES['urun_fotolar'])) {
        $files = $_FILES['urun_fotolar'];
        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] === 0) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newName = "urun_" . $urun_id . "_" . time() . "_" . $key . "." . $ext;
                $targetPath = "../assets/img/urunler/" . $newName;
                if (!is_dir("../assets/img/urunler/")) mkdir("../assets/img/urunler/", 0777, true);
                if (move_uploaded_file($files['tmp_name'][$key], $targetPath)) {
                    $url = "assets/img/urunler/" . $newName;
                    $sira_res = $db->query("SELECT MAX(foto_sira) as max_sira FROM t_foto WHERE urun_ID = $urun_id");
                    $sira = ($sira_res->fetch_assoc()['max_sira'] ?? 0) + 1;
                    $db->query("INSERT INTO t_foto (urun_ID, foto_url, foto_sira) VALUES ($urun_id, '$url', $sira)");
                }
            }
        }
    }
}

function handleTabloDetay($urun_id, $db) {
    $db->query("DELETE FROM t_urun_tablo_detay WHERE urun_ID = $urun_id");
    if (isset($_POST['tablo_baslik']) && isset($_POST['tablo_deger'])) {
        $basliklar = $_POST['tablo_baslik'];
        $degerler = $_POST['tablo_deger'];
        foreach ($basliklar as $index => $b) {
            $b = trim($db->real_escape_string($b));
            $d = trim($db->real_escape_string($degerler[$index] ?? ''));
            if (!empty($b) && !empty($d)) {
                $sira = $index + 1;
                $db->query("INSERT INTO t_urun_tablo_detay (urun_ID, sutun_baslik, sutun_aciklama, tablo_sira) 
                            VALUES ($urun_id, '$b', '$d', $sira)");
            }
        }
    }
}
?>
