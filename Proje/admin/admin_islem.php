<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // JSON çıktısını bozmamak için hataları gösterme

// Debug Log Fonksiyonu
function debug_log($msg) {
    file_put_contents('admin_debug.log', date('[Y-m-d H:i:s] ') . $msg . "\n", FILE_APPEND);
}

require_once '../config.php';

$islem = isset($_POST['islem']) ? $_POST['islem'] : (isset($_GET['islem']) ? $_GET['islem'] : '');
debug_log("İşlem Başladı: " . $islem);

// Çıkış işlemi redirect yapar, JSON header uygulanmaz
if ($islem !== 'cikis') {
    header('Content-Type: application/json; charset=utf-8');
}

if ($islem == 'admin_giris') {
    $eposta = $db->real_escape_string($_POST['eposta']);
    $parola = $_POST['parola'];

    // FIX: t_personel tablosunu kullan (t_uye'de 'rol' sütunu yok)
    $sql = "SELECT * FROM t_personel WHERE e_posta = '$eposta' LIMIT 1";
    $res = $db->query($sql);

    if ($res && $res->num_rows > 0) {
        $admin = $res->fetch_assoc();
        // Hem hash hem düz metin şifre desteği
        if (password_verify($parola, $admin['parola']) || $parola === $admin['parola']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_ad'] = $admin['ad'];
            $_SESSION['admin_soyad'] = $admin['soyad'];
            $_SESSION['admin_rol'] = $admin['rol'];
            echo json_encode(['status' => 'success', 'message' => 'Giriş başarılı']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Hatalı parola!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Bu e-posta adresine ait yetkili hesabı bulunamadı!']);
    }
    exit;
}

// ADMIN OTURUM KONTROLÜ
if (!isset($_SESSION['admin_id'])) {
    debug_log("HATA: Yetkisiz Erişim (Oturum Yok)");
    echo json_encode(['status' => 'error', 'message' => 'Yetkisiz erişim! Lütfen tekrar giriş yapın.']);
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Kategori İşlemleri (Önceki Turn'de güncellenen yapı)
if ($islem == 'get_kategori') {
    $ust_id = isset($_POST['ust_id']) ? (int)$_POST['ust_id'] : 0;
    $level = isset($_POST['level']) ? (int)$_POST['level'] : 1;
    if ($level == 1) $sql = "SELECT * FROM t_kategori ORDER BY kategori_adi ASC";
    elseif ($level == 2) $sql = "SELECT * FROM t_alt_kategori_1 WHERE kategori_id = $ust_id ORDER BY kategori_adi ASC";
    elseif ($level == 3) $sql = "SELECT * FROM t_alt_kategori_2 WHERE alt_1_ID = $ust_id ORDER BY kategori_adi ASC";
    $res = $db->query($sql);
    $kategoriler = [];
    if($res) while($row = $res->fetch_assoc()) $kategoriler[] = $row;
    echo json_encode($kategoriler);
    exit;
}

if ($islem == 'ekle_kategori') {
    $ust_id = isset($_POST['ust_id']) ? (int)$_POST['ust_id'] : 0;
    $level = isset($_POST['level']) ? (int)$_POST['level'] : 1;
    $kategori_adi = $db->real_escape_string($_POST['kategori_adi']);
    if ($level == 1) $db->query("INSERT INTO t_kategori (kategori_adi) VALUES ('$kategori_adi')");
    elseif ($level == 2) $db->query("INSERT INTO t_alt_kategori_1 (kategori_adi, kategori_id) VALUES ('$kategori_adi', $ust_id)");
    elseif ($level == 3) $db->query("INSERT INTO t_alt_kategori_2 (kategori_adi, alt_1_ID) VALUES ('$kategori_adi', $ust_id)");
    echo json_encode(['status' => 'success', 'message' => 'Kategori eklendi']);
    exit;
}

if ($islem == 'sil_kategori') {
    $id = (int)$_POST['id'];
    $level = isset($_POST['level']) ? (int)$_POST['level'] : 1;
    if ($level == 1) $db->query("DELETE FROM t_kategori WHERE id = $id");
    elseif ($level == 2) $db->query("DELETE FROM t_alt_kategori_1 WHERE id = $id");
    elseif ($level == 3) $db->query("DELETE FROM t_alt_kategori_2 WHERE id = $id");
    echo json_encode(['status' => 'success', 'message' => 'Kategori silindi']);
    exit;
}

if ($islem == 'duzenle_kategori') {
    $id = (int)$_POST['id'];
    $level = isset($_POST['level']) ? (int)$_POST['level'] : 1;
    $kategori_adi = $db->real_escape_string($_POST['kategori_adi']);
    if ($level == 1) $db->query("UPDATE t_kategori SET kategori_adi = '$kategori_adi' WHERE id = $id");
    elseif ($level == 2) $db->query("UPDATE t_alt_kategori_1 SET kategori_adi = '$kategori_adi' WHERE id = $id");
    elseif ($level == 3) $db->query("UPDATE t_alt_kategori_2 SET kategori_adi = '$kategori_adi' WHERE id = $id");
    echo json_encode(['status' => 'success', 'message' => 'Kategori adı güncellendi']);
    exit;
}

// PERSONEL İŞLEMLERİ - FIX: t_personel tablosunu kullan
if ($islem == 'personel_ekle') {
    $ad = $db->real_escape_string($_POST['ad']);
    $soyad = $db->real_escape_string($_POST['soyad']);
    $eposta = $db->real_escape_string($_POST['e_posta']);
    $telefon = $db->real_escape_string($_POST['telefon']);
    $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);
    $rol = $db->real_escape_string($_POST['rol']);
    
    debug_log("Personel Ekleme Talebi: $eposta");

    $check = $db->query("SELECT id FROM t_personel WHERE e_posta = '$eposta'");
    if ($check && $check->num_rows > 0) {
        debug_log("HATA: E-posta zaten var");
        echo json_encode(['status' => 'error', 'message' => 'Bu e-posta adresi zaten kullanılıyor.']);
        exit;
    }
    
    $kayit_tarihi = date('Y-m-d H:i:s');
    // Personel ID oluştur
    $count_res = $db->query("SELECT COUNT(*) as c FROM t_personel");
    $count = $count_res->fetch_assoc()['c'] + 1;
    $personel_id = 'PR-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO t_personel (ad, soyad, e_posta, telefon, parola, rol, kayit_tarihi, personel_ID) 
            VALUES ('$ad', '$soyad', '$eposta', '$telefon', '$parola', '$rol', '$kayit_tarihi', '$personel_id')";
            
    if ($db->query($sql)) {
        debug_log("BAŞARILI: Personel eklendi");
        echo json_encode(['status' => 'success', 'message' => 'Personel başarıyla eklendi.']);
    } else {
        debug_log("HATA: " . $db->error);
        echo json_encode(['status' => 'error', 'message' => 'Ekleme Hatası: ' . addslashes($db->error)]);
    }
    exit;
}

if ($islem == 'personel_guncelle') {
    $id = (int)$_POST['id'];
    $ad = $db->real_escape_string($_POST['ad']);
    $soyad = $db->real_escape_string($_POST['soyad']);
    $eposta = $db->real_escape_string($_POST['e_posta']);
    $telefon = $db->real_escape_string($_POST['telefon']);
    $rol = $db->real_escape_string($_POST['rol']);
    
    debug_log("Personel Güncelleme Talebi ID: $id");

    $sql = "UPDATE t_personel SET ad='$ad', soyad='$soyad', e_posta='$eposta', telefon='$telefon', rol='$rol'";
    if (!empty($_POST['parola'])) {
        $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);
        $sql .= ", parola='$parola'";
    }
    $sql .= " WHERE id = $id";
    
    if ($db->query($sql)) {
        debug_log("BAŞARILI: Personel güncellendi");
        echo json_encode(['status' => 'success', 'message' => 'Personel bilgileri başarıyla güncellendi.']);
    } else {
        debug_log("HATA: " . $db->error);
        echo json_encode(['status' => 'error', 'message' => 'Güncelleme Hatası: ' . addslashes($db->error)]);
    }
    exit;
}

if ($islem == 'personel_sil') {
    $id = (int)$_POST['id'];
    if ($id == $_SESSION['admin_id']) {
        echo json_encode(['status' => 'error', 'message' => 'Kendi hesabınızı silemezsiniz!']);
        exit;
    }
    if($db->query("DELETE FROM t_personel WHERE id = $id")) {
        echo json_encode(['status' => 'success', 'message' => 'Personel silindi']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Silme hatası: ' . $db->error]);
    }
    exit;
}

// Satıcı ve Ürün Onay İşlemleri
if ($islem == 'satici_onay_islem') {
    $id = (int)$_POST['id'];
    $tur = $_POST['tur'];
    $st = ($tur == 'onayla') ? 'Onaylandı' : 'Reddedildi';
    $db->query("UPDATE t_satici SET onay_durumu = '$st' WHERE id = $id");
    echo json_encode(['status' => 'success', 'message' => 'İşlem tamamlandı.']);
    exit;
}

if ($islem == 'urun_onay_islem') {
    $id = (int)$_POST['id'];
    $tur = $_POST['tur'];
    $st = ($tur == 'onayla') ? 'Onaylandı' : 'Reddedildi';
    $db->query("UPDATE t_urun SET onay_durumu = '$st' WHERE id = $id");
    echo json_encode(['status' => 'success', 'message' => 'İşlem tamamlandı.']);
    exit;
}

if ($islem == 'cikis') {
    session_destroy();
    header("Location: admin_giris.php");
    exit;
}
?>
