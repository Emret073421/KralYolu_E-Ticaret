<?php
ob_start();
// data.php
require_once 'config.php';

/* ==========================================================================
   KULLANICI VE OTURUM BİLGİLERİ
   ========================================================================== */
$user = $_SESSION['kullanici'] ?? null;
$seller = $_SESSION['satici'] ?? null;
$isLoggedIn = is_array($user) && !empty($user['ad']) && !empty($user['soyad']); 
$isSellerLoggedIn = is_array($seller) && !empty($seller['kurum_adi']);

/* ==========================================================================
   YARDIMCI FONKSİYONLAR
   ========================================================================== */


function login($kadi , $sifre)
{
    global $db;
    
    
}

/**
 * Belirli bir kategorideki ürünleri getirir.
 * @param string $category_name Kategori adı (Örn: 'Giyim')
 * @return string JSON formatında ürün listesi
 */
function get_products_by_category($category_name, $limit = 8) {
    global $db;
    
    $query = "SELECT u.*, f.foto_url 
              FROM t_urun u 
              LEFT JOIN t_urun_kategori uk ON u.id = uk.urun_ID 
              LEFT JOIN t_kategori k ON uk.kategori_ID = k.id 
              LEFT JOIN t_foto f ON u.id = f.urun_ID AND f.foto_sira = 1
              WHERE k.kategori_adi = ? AND u.aktif = 'Evet'
              LIMIT ?";
              
    $stmt = $db->prepare($query);
    $stmt->bind_param("si", $category_name, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Puan ve yorum sayısını da alalım (t_yorum tablosundan)
        $puan_query = "SELECT AVG(puan) as ortalama_puan, COUNT(*) as yorum_sayisi FROM t_yorum WHERE urun_ID = ?";
        // Not: t_yorum tablosunda urun_ID veya siparis_detay_ID olabilir. 
        // SQL dump'a göre t_yorum'da urun_ID yok, siparis_detay_ID var.
        // Ama SQL dump'ta t_yorum yapısı şöyle: id, uye_ID, siparis_detay_ID, puan, yorum, tarih.
        // urun_ID t_siparis_detay tablosunda var.
        
        $puan_query = "SELECT AVG(puan) as ortalama_puan, COUNT(id) as yorum_sayisi FROM t_yorum WHERE urun_ID = ?";
        $stmt_puan = $db->prepare($puan_query);
        $stmt_puan->bind_param("i", $row['id']);
        $stmt_puan->execute();
        $puan_res = $stmt_puan->get_result()->fetch_assoc();
        
        $row['ortalama_puan'] = $puan_res['ortalama_puan'] ?? 0;
        $row['yorum_sayisi'] = $puan_res['yorum_sayisi'] ?? 0;
        
        $products[] = $row;
    }
    
    return json_encode($products, JSON_UNESCAPED_UNICODE);
}

/* ==========================================================================
   OTURUM VE ÜYELİK İŞLEMLERİ (GİRİŞ, KAYIT, ÇIKIŞ)
   ========================================================================== */

// Logout işlemi
if (isset($_GET['logout'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}

// Giriş İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'giris') {
    $eposta = $db->real_escape_string($_POST['eposta']);
    $sifre = $_POST['sifre'];

    $query = "SELECT * FROM t_uye WHERE e_posta = '$eposta'";
    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
        $kullanici = $result->fetch_assoc();
        // Şifre kontrolü - Eğer veritabanında hash varsa password_verify kullanılır.
        // Eğer düz metin ise $sifre === $kullanici['parola'] kullanılır.
        // Genellikle güvenlik için hash tercih edilir.
        if (password_verify($sifre, $kullanici['parola']) || $sifre === $kullanici['parola']) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['kullanici'] = [
                'id' => $kullanici['id'],
                'ad' => $kullanici['ad'],
                'soyad' => $kullanici['soyad'],
                'e_posta' => $kullanici['e_posta'],
                'telefon' => $kullanici['telefon'] ?? ''
            ];
            $son_giris = date('Y-m-d H:i:s');
            $db->query("UPDATE t_uye SET son_giris = '$son_giris' WHERE id = {$kullanici['id']}");
            header('Location: index.php');
            exit;
        } else {
            header('Location: index.php?sayfa=giris&hata=sifre');
            exit;
        }
    } else {
        header('Location: index.php?sayfa=giris&hata=kullanici');
        exit;
    }
}

// Satıcı Giriş İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'satici_giris') {
    $eposta = $db->real_escape_string($_POST['eposta']);
    $sifre = $_POST['sifre'];

    $query = "SELECT * FROM t_satici WHERE e_posta = '$eposta'";
    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
        $satici = $result->fetch_assoc();
        // Not: Gerçek projede parola hashlenmiş olmalı.
        if ($sifre === $satici['parola'] || password_verify($sifre, $satici['parola'])) {
            if (isset($satici['onay_durumu']) && $satici['onay_durumu'] !== 'Onaylandı') {
                header('Location: index.php?sayfa=satici_giris&hata=onay');
                exit;
            }
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['satici'] = [
                'id' => $satici['id'],
                'ad' => $satici['ad'],
                'soyad' => $satici['soyad'],
                'e_posta' => $satici['e_posta'],
                'kurum_adi' => $satici['kurum_adi'],
                'rol' => $satici['rol']
            ];
            header('Location: index.php?sayfa=satici_panel');
            exit;
        } else {
            header('Location: index.php?sayfa=satici_giris&hata=sifre');
            exit;
        }
    } else {
        header('Location: index.php?sayfa=satici_giris&hata=kullanici');
        exit;
    }
}

// Satıcı Kayıt (Başvuru) İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'satici_kayit') {
    $kurum_adi = $db->real_escape_string($_POST['kurum_adi']);
    $ticari_unvan = $db->real_escape_string($_POST['ticari_unvan']);
    $sirket_turu = $db->real_escape_string($_POST['sirket_turu']);
    $vergi_dairesi = $db->real_escape_string($_POST['vergi_dairesi']);
    $vergi_no = $db->real_escape_string($_POST['vergi_no']);
    $eposta = $db->real_escape_string($_POST['eposta']);
    $parola = $db->real_escape_string($_POST['parola']);
    $ad = $db->real_escape_string($_POST['ad']);
    $soyad = $db->real_escape_string($_POST['soyad']);
    $tarih = date('Y-m-d H:i:s');

    $query = "INSERT INTO t_satici (kurum_adi, ticari_unvan, sirket_turu, vergi_dairesi, vergi_no, e_posta, parola, ad, soyad, kayit_tarihi, onay_durumu) 
              VALUES ('$kurum_adi', '$ticari_unvan', '$sirket_turu', '$vergi_dairesi', '$vergi_no', '$eposta', '$parola', '$ad', '$soyad', '$tarih', 'Onay Bekliyor')";
    
    if ($db->query($query)) {
        header('Location: index.php?sayfa=satici_giris&basari=kayit');
    } else {
        header('Location: index.php?sayfa=satici_giris&hata=kayit&msg=' . urlencode($db->error));
    }
    exit;
}

// Kayıt İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'kayit') {
    $ad = $db->real_escape_string($_POST['ad']);
    $soyad = $db->real_escape_string($_POST['soyad']);
    $e_posta = $db->real_escape_string($_POST['e_posta']);
    $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);
    $kayit_tarihi = date('Y-m-d H:i:s');
    $son_giris = date('Y-m-d H:i:s');

    $query = "INSERT INTO t_uye (ad, soyad, e_posta, parola, kayit_tarihi, son_giris) VALUES ('$ad', '$soyad', '$e_posta', '$parola', '$kayit_tarihi', '$son_giris')";
    if ($db->query($query) === TRUE) {
        header('Location: index.php?sayfa=giris&basari=kayit');
    } else {
        header('Location: index.php?sayfa=giris&hata=kayit&msg=' . urlencode($db->error));
    }
    exit;
}

/* ==========================================================================
   PROFİL VE ADRES YÖNETİMİ
   ========================================================================== */

// Profil Güncelleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'profil_guncelle' && isset($_SESSION['kullanici'])) {
    $id = $_SESSION['kullanici']['id'];
    $ad = $db->real_escape_string($_POST['ad']);
    $soyad = $db->real_escape_string($_POST['soyad']);
    $e_posta = $db->real_escape_string($_POST['e_posta']);
    $telefon = $db->real_escape_string($_POST['telefon']);
    
    $mevcut_parola = $_POST['mevcut_parola'] ?? '';
    $yeni_parola = $_POST['yeni_parola'] ?? '';

    // Kullanıcının mevcut bilgilerini çek (şifre kontrolü için)
    $uye_res = $db->query("SELECT parola FROM t_uye WHERE id = $id");
    $uye = $uye_res->fetch_assoc();

    $parola_guncelle = "";
    if (!empty($yeni_parola)) {
        if (password_verify($mevcut_parola, $uye['parola']) || $mevcut_parola === $uye['parola']) {
            $hashed_parola = password_hash($yeni_parola, PASSWORD_DEFAULT);
            $parola_guncelle = ", parola = '$hashed_parola'";
        } else {
            header('Location: index.php?sayfa=profil&hata=sifre');
            exit;
        }
    }

    $query = "UPDATE t_uye SET ad = '$ad', soyad = '$soyad', e_posta = '$e_posta', telefon = '$telefon' $parola_guncelle WHERE id = $id";
    
    if ($db->query($query) === TRUE) {
        // Session'ı güncelle
        $_SESSION['kullanici']['ad'] = $ad;
        $_SESSION['kullanici']['soyad'] = $soyad;
        $_SESSION['kullanici']['e_posta'] = $e_posta;
        $_SESSION['kullanici']['telefon'] = $telefon;
        
        header('Location: index.php?sayfa=profil&tab=bilgiler&basari=guncelle');
    } else {
        header('Location: index.php?sayfa=profil&tab=bilgiler&hata=guncelle&msg=' . urlencode($db->error));
    }
    exit;
}

// Adres Kaydetme (Ekleme ve Güncelleme)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'adres_kaydet' && isset($_SESSION['kullanici'])) {
    $uye_id = $_SESSION['kullanici']['id'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $baslik = $db->real_escape_string($_POST['adres_basligi']);
    $ad = $db->real_escape_string($_POST['ad']);
    $soyad = $db->real_escape_string($_POST['soyad']);
    $telefon = $db->real_escape_string($_POST['telefon']);
    $il = $db->real_escape_string($_POST['il']);
    $ilce = $db->real_escape_string($_POST['ilce']);
    $tam_adres = $db->real_escape_string($_POST['tam_adres']);

    if ($id > 0) {
        $query = "UPDATE t_adres SET adres_basligi='$baslik', ad='$ad', soyad='$soyad', telefon='$telefon', il='$il', ilce='$ilce', tam_adres='$tam_adres' WHERE id=$id AND uye_ID=$uye_id";
    } else {
        $query = "INSERT INTO t_adres (uye_ID, adres_basligi, ad, soyad, telefon, il, ilce, tam_adres) VALUES ($uye_id, '$baslik', '$ad', '$soyad', '$telefon', '$il', '$ilce', '$tam_adres')";
    }

    if ($db->query($query)) {
        header('Location: index.php?sayfa=profil&tab=adres&basari=adres');
    } else {
        header('Location: index.php?sayfa=profil&tab=adres&hata=adres');
    }
    exit;
}

// Adres Silme
if (isset($_GET['islem']) && $_GET['islem'] === 'adres_sil' && isset($_SESSION['kullanici'])) {
    $id = intval($_GET['id']);
    $uye_id = $_SESSION['kullanici']['id'];
    $db->query("DELETE FROM t_adres WHERE id = $id AND uye_ID = $uye_id");
    header('Location: index.php?sayfa=profil&tab=adres&basari=silindi');
    exit;
}

/* ==========================================================================
   ÖDEME YÖNTEMLERİ (KART YÖNETİMİ)
   ========================================================================== */

// Kart Kaydetme (Ekleme ve Güncelleme)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'kart_kaydet' && isset($_SESSION['kullanici'])) {
    $uye_id = $_SESSION['kullanici']['id'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $baslik = $db->real_escape_string($_POST['kart_baslik']);
    $isim = $db->real_escape_string($_POST['kart_isim']);
    $no = $db->real_escape_string($_POST['kart_numarasi']);
    $skt = $db->real_escape_string($_POST['son_kullanma']);
    $cvv = $db->real_escape_string($_POST['cvv']);

    if ($id > 0) {
        $query = "UPDATE t_kredi_karti SET kart_baslik='$baslik', kart_isim='$isim', kart_numarasi='$no', son_kullanma='$skt', cvv='$cvv' WHERE id=$id AND uye_ID=$uye_id";
    } else {
        $query = "INSERT INTO t_kredi_karti (uye_ID, kart_baslik, kart_isim, kart_numarasi, son_kullanma, cvv) VALUES ($uye_id, '$baslik', '$isim', '$no', '$skt', '$cvv')";
    }
    
    if ($db->query($query)) {
        header('Location: index.php?sayfa=profil&tab=kartlar&basari=kart');
    } else {
        header('Location: index.php?sayfa=profil&tab=kartlar&hata=kart');
    }
    exit;
}

// Kart Silme
if (isset($_GET['islem']) && $_GET['islem'] === 'kart_sil' && isset($_SESSION['kullanici'])) {
    $id = intval($_GET['id']);
    $uye_id = $_SESSION['kullanici']['id'];
    $db->query("DELETE FROM t_kredi_karti WHERE id = $id AND uye_ID = $uye_id");
    header('Location: index.php?sayfa=profil&tab=kartlar&basari=silindi');
    exit;
}

/* ==========================================================================
   İL / İLÇE / MAHALLE AJAX API
   ========================================================================== */

// İlleri getir
if (isset($_GET['islem']) && $_GET['islem'] === 'get_iller') {
    header('Content-Type: application/json; charset=utf-8');
    $res = $db->query("SELECT id, il_adi FROM iller ORDER BY il_adi ASC");
    $data = [];
    if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
    exit;
}

// İlçeleri getir (il_id parametresi ile)
if (isset($_GET['islem']) && $_GET['islem'] === 'get_ilceler') {
    header('Content-Type: application/json; charset=utf-8');
    $il_id = intval($_GET['il_id'] ?? 0);
    $res = $db->query("SELECT id, ilce_adi FROM ilceler WHERE il_id = $il_id ORDER BY ilce_adi ASC");
    $data = [];
    if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
    exit;
}

// Mahalleleri getir (ilce_id parametresi ile)
if (isset($_GET['islem']) && $_GET['islem'] === 'get_mahalleler') {
    header('Content-Type: application/json; charset=utf-8');
    $ilce_id = intval($_GET['ilce_id'] ?? 0);
    $res = $db->query("SELECT id, mahalle_adi FROM mahalleler WHERE ilce_id = $ilce_id ORDER BY mahalle_adi ASC");
    $data = [];
    if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
    exit;
}

/* ==========================================================================
   FAVORİ VE SEPET İŞLEMLERİ
   ========================================================================== */

// Favori Ekleme / Çıkarma
if (isset($_GET['islem']) && $_GET['islem'] === 'favori_islem') {
    if (!isset($_SESSION['kullanici'])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'error', 'message' => 'auth']);
        } else {
            header('Location: index.php?sayfa=giris');
        }
        exit;
    }

    $uye_id = $_SESSION['kullanici']['id'];
    $urun_id = intval($_GET['id']);
    
    // Zaten favoride mi?
    $kontrol = @$db->query("SELECT id FROM t_favori WHERE uye_id = $uye_id AND urun_id = $urun_id");
    if (!$kontrol) {
        echo json_encode(['status' => 'error', 'message' => 'Favori özelliği şu anda kullanılamıyor.']);
        exit;
    }
    
    if ($kontrol->num_rows > 0) {
        // Varsa çıkar
        $db->query("DELETE FROM t_favori WHERE uye_id = $uye_id AND urun_id = $urun_id");
        $durum = 'cikarildi';
    } else {
        // Yoksa ekle
        $db->query("INSERT INTO t_favori (uye_id, urun_id) VALUES ($uye_id, $urun_id)");
        $durum = 'eklendi';
    }
    
    // AJAX isteği ise JSON dön, değilse geri yönlendir
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['status' => 'success', 'durum' => $durum]);
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    exit;
}

// Sepete Ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'sepet_ekle' && isset($_SESSION['kullanici'])) {
    $uye_id = $_SESSION['kullanici']['id'];
    $urun_id = intval($_POST['urun_id']);
    $adet = intval($_POST['adet'] ?? 1);
    
    // Seçenekleri topla (Beden: M, Renk: Mavi gibi)
    $secenekler = [];
    if (isset($_POST['secenek']) && is_array($_POST['secenek'])) {
        foreach ($_POST['secenek'] as $key => $val) {
            // Seçenek ID'sine göre başlık ve değer çekilebilir ama şimdilik basitçe ID'leri saklayalım
            // veya doğrudan değerleri formdan göndertebiliriz.
            $secenekler[] = "$key:$val";
        }
    }
    $secenek_detay = $db->real_escape_string(implode('|', $secenekler));

    // Ürün fiyatını ve STOK durumunu çek
    $urun_res = $db->query("SELECT fiyat, stok FROM t_urun WHERE id = $urun_id");
    $urun_data = $urun_res->fetch_assoc();
    $birim_fiyat = $urun_data['fiyat'] ?? 0;
    $stok = $urun_data['stok'] ?? 0;

    // Aynı ürün ve aynı seçeneklerle sepette var mı?
    // secenek_detay sütunu yoksa sadece urun_ID ile kontrol et
    $kontrol = @$db->query("SELECT id, adet FROM t_sepet WHERE uye_ID = $uye_id AND urun_ID = $urun_id AND secenek_detay = '$secenek_detay'");
    if (!$kontrol) {
        // secenek_detay sütunu yoksa fallback
        $kontrol = $db->query("SELECT id, adet FROM t_sepet WHERE uye_ID = $uye_id AND urun_ID = $urun_id");
    }
    
    $sepet_adet = 0;
    $sepet_id = 0;
    if ($kontrol->num_rows > 0) {
        $mevcut = $kontrol->fetch_assoc();
        $sepet_adet = $mevcut['adet'];
        $sepet_id = $mevcut['id'];
    }

    // Stok Kontrolü
    if (($sepet_adet + $adet) > $stok) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'error', 'message' => "Yetersiz stok! Mevcut stok: $stok"]);
            exit;
        } else {
            header("Location: index.php?sayfa=urundetay&id=$urun_id&hata=stok");
            exit;
        }
    }
    
    if ($sepet_id > 0) {
        // Varsa adedi arttır
        $yeni_adet = $sepet_adet + $adet;
        $db->query("UPDATE t_sepet SET adet = $yeni_adet, birim_fiyat = $birim_fiyat, guncelleme_tarihi = NOW() WHERE id = $sepet_id");
    } else {
        // Yoksa yeni ekle
        // secenek_detay sütunu yoksa onsuz INSERT dene
        $insert_result = @$db->query("INSERT INTO t_sepet (uye_ID, urun_ID, adet, secenek_detay, birim_fiyat, guncelleme_tarihi) VALUES ($uye_id, $urun_id, $adet, '$secenek_detay', $birim_fiyat, NOW())");
        if (!$insert_result) {
            $db->query("INSERT INTO t_sepet (uye_ID, urun_ID, adet, birim_fiyat, guncelleme_tarihi) VALUES ($uye_id, $urun_id, $adet, $birim_fiyat, NOW())");
        }
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['status' => 'success', 'message' => 'Ürün sepete eklendi']);
        exit;
    } else {
        header('Location: index.php?sayfa=sepet&basari=eklendi');
        exit;
    }
}


// Sepet Güncelleme (Adet)
if (isset($_GET['islem']) && $_GET['islem'] === 'sepet_guncelle' && isset($_SESSION['kullanici'])) {
    $id = intval($_GET['id']);
    $adet = intval($_GET['adet']);
    $uye_id = $_SESSION['kullanici']['id'];
    
    if ($adet > 0) {
        // Stok Kontrolü
        $stok_res = $db->query("SELECT u.stok FROM t_sepet s JOIN t_urun u ON s.urun_ID = u.id WHERE s.id = $id AND s.uye_ID = $uye_id");
        $stok_data = $stok_res->fetch_assoc();
        if ($adet > $stok_data['stok']) {
            header('Location: index.php?sayfa=sepet&hata=stok');
            exit;
        }
        $db->query("UPDATE t_sepet SET adet = $adet WHERE id = $id AND uye_ID = $uye_id");
    } else {
        $db->query("DELETE FROM t_sepet WHERE id = $id AND uye_ID = $uye_id");
    }
    header('Location: index.php?sayfa=sepet');
    exit;
}

// Sepetten Çıkarma
if (isset($_GET['islem']) && $_GET['islem'] === 'sepet_sil' && isset($_SESSION['kullanici'])) {
    $id = intval($_GET['id']);
    $uye_id = $_SESSION['kullanici']['id'];
    $db->query("DELETE FROM t_sepet WHERE id = $id AND uye_ID = $uye_id");
    header('Location: index.php?sayfa=sepet&basari=silindi');
    exit;
}

// Sepeti Temizle
if (isset($_GET['islem']) && $_GET['islem'] === 'sepet_temizle' && isset($_SESSION['kullanici'])) {
    $uye_id = $_SESSION['kullanici']['id'];
    $db->query("DELETE FROM t_sepet WHERE uye_ID = $uye_id");
    header('Location: index.php?sayfa=sepet&basari=temizlendi');
    exit;
}

/* ==========================================================================
   SİPARİŞ VE ÖDEME TAMAMLAMA
   ========================================================================== */

// Siparişi Tamamlama
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'siparis_tamamla' && isset($_SESSION['kullanici'])) {
    $uye_id = $_SESSION['kullanici']['id'];
    $adres_id = intval($_POST['adres_id']);
    
    // Sepet toplamını ve ürünleri çek
    $sepet_res = $db->query("SELECT * FROM t_sepet WHERE uye_ID = $uye_id");
    if ($sepet_res->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Sepetiniz boş.']);
        exit;
    }

    $toplam_tutar = 0;
    $items = [];
    while ($row = $sepet_res->fetch_assoc()) {
        $items[] = $row;
        $toplam_tutar += ($row['birim_fiyat'] * $row['adet']);
    }

    // Kargo ücreti mantığı (Örn: 1000 TL altı 50 TL)
    if ($toplam_tutar < 1000) {
        $toplam_tutar += 50;
    }

    // 1. Sipariş Oluştur
    $tarih = date('Y-m-d H:i:s');
    $query = "INSERT INTO t_siparis (uye_ID, adres_ID, kargo_durum, siparis_tarih, toplam_tutar) VALUES ($uye_id, $adres_id, 'Hazırlanıyor', '$tarih', $toplam_tutar)";
    
    if ($db->query($query)) {
        $siparis_id = $db->insert_id;

        // 2. Sipariş Detaylarını Aktar ve Stok Düş
        foreach ($items as $item) {
            $u_id = $item['urun_ID'];
            $adet = $item['adet'];
            $fiyat = $item['birim_fiyat'];
            
            // Detay ekle
            $db->query("INSERT INTO t_siparis_detay (siparis_ID, urun_ID, adet, birim_fiyat) VALUES ($siparis_id, $u_id, $adet, $fiyat)");
            
            // Stoktan düş
            $db->query("UPDATE t_urun SET stok = stok - $adet WHERE id = $u_id");
        }

        // 3. Sepeti Temizle
        $db->query("DELETE FROM t_sepet WHERE uye_ID = $uye_id");

        echo json_encode(['status' => 'success', 'siparis_no' => 'KRY-' . $siparis_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Sipariş oluşturulurken hata: ' . $db->error]);
    }
    exit;
}

/* ==========================================================================
   ARAMA VE ÖNERİ SİSTEMİ (AJAX)
   ========================================================================== */

// Arama Önerileri (Trendyol Style)
if (isset($_GET['islem']) && $_GET['islem'] === 'arama_oneri') {
    if (ob_get_length()) ob_clean(); // Önceki çıktıları temizle
    $q = isset($_GET['q']) ? $_GET['q'] : '';
    $sonuclar = ['kategoriler' => [], 'urunler' => [], 'oneriler' => []];

    if (mb_strlen($q, 'UTF-8') >= 1) {
        $search_q = "%$q%";
        
        // 1. Kategori Önerileri (Prepared Statement)
        $stmt_k = $db->prepare("SELECT id, kategori_adi FROM t_kategori WHERE kategori_adi LIKE ? LIMIT 10");
        $stmt_k->bind_param("s", $search_q);
        $stmt_k->execute();
        $k_res = $stmt_k->get_result();
        while($k = $k_res->fetch_assoc()) {
            $sonuclar['kategoriler'][] = $k;
            $sonuclar['oneriler'][] = [
                'type' => 'category_search',
                'text' => "<strong>$q</strong> kategorisi: <span>" . $k['kategori_adi'] . "</span>",
                'url' => "index.php?sayfa=urunler&ara=$q&kategori[]=" . $k['id']
            ];
        }

        // 2. Ürün Önerileri (Prepared Statement)
        $stmt_u = $db->prepare("SELECT u.id, u.urun_adi, u.fiyat, f.foto_url 
                                 FROM t_urun u 
                                 LEFT JOIN t_foto f ON u.id = f.urun_ID AND f.foto_sira = 1 
                                 WHERE (u.urun_adi LIKE ? OR u.urun_aciklama LIKE ?) AND u.aktif='Evet' 
                                 LIMIT 10");
        $stmt_u->bind_param("ss", $search_q, $search_q);
        $stmt_u->execute();
        $u_res = $stmt_u->get_result();
        while($u = $u_res->fetch_assoc()) $sonuclar['urunler'][] = $u;
    } else {
        // Boşken popüler kategorileri ve aramaları getir
        $pop_res = $db->query("SELECT id, kategori_adi FROM t_kategori LIMIT 10");
        if ($pop_res) {
            while($p = $pop_res->fetch_assoc()) $sonuclar['kategoriler'][] = $p;
        }
        $sonuclar['populer_aramalar'] = ['Elbise', 'Telefon', 'Ayakkabı', 'T-shirt', 'Kulaklık', 'Saat', 'Parfüm'];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($sonuclar, JSON_UNESCAPED_UNICODE);
    exit;
}

// Sipariş Detaylarını Getir
if (isset($_GET['islem']) && $_GET['islem'] === 'get_siparis_detay' && isset($_SESSION['kullanici'])) {
    header('Content-Type: application/json; charset=utf-8');
    $uye_id = $_SESSION['kullanici']['id'];
    $siparis_id = intval($_GET['id']);
    
    // Sipariş bilgileri ve adres
    $siparis_query = "SELECT s.*, a.adres_basligi, a.tam_adres, a.il, a.ilce 
                      FROM t_siparis s 
                      LEFT JOIN t_adres a ON s.adres_ID = a.id 
                      WHERE s.id = $siparis_id AND s.uye_ID = $uye_id";
    $s_res = $db->query($siparis_query);
    if (!$s_res || $s_res->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Sipariş bulunamadı.']);
        exit;
    }
    $siparis = $s_res->fetch_assoc();
    
    // Sipariş ürünleri
    $urunler_query = "SELECT sd.*, u.urun_adi, f.foto_url 
                      FROM t_siparis_detay sd 
                      JOIN t_urun u ON sd.urun_ID = u.id 
                      LEFT JOIN t_foto f ON u.id = f.urun_ID AND f.foto_sira = 1 
                      WHERE sd.siparis_ID = $siparis_id";
    $u_res = $db->query($urunler_query);
    $urunler = [];
    if ($u_res) {
        while($u = $u_res->fetch_assoc()) $urunler[] = $u;
    }
    
    echo json_encode(['status' => 'success', 'siparis' => $siparis, 'urunler' => $urunler]);
    exit;
}

// Alt Kategorileri Getir (Çoklu Seçim İçin)
if (isset($_GET['islem']) && $_GET['islem'] === 'get_alt_kategoriler_multi') {
    header('Content-Type: application/json; charset=utf-8');
    $seviye = intval($_GET['seviye'] ?? 1);
    $parent_ids_raw = $_GET['parent_ids'] ?? '';
    
    // Güvenlik için sadece rakamları ve virgülleri bırak
    $parent_ids = preg_replace('/[^0-9,]/', '', $parent_ids_raw);
    
    if (empty($parent_ids)) {
        echo json_encode([]);
        exit;
    }
    
    $output = [];
    if ($seviye === 1) {
        $res = $db->query("SELECT id, kategori_adi, kategori_ID as parent_id FROM t_alt_kategori_1 WHERE kategori_ID IN ($parent_ids) ORDER BY kategori_adi ASC");
    } else {
        $res = $db->query("SELECT id, kategori_adi, alt_1_ID as parent_id FROM t_alt_kategori_2 WHERE alt_1_ID IN ($parent_ids) ORDER BY kategori_adi ASC");
    }
    
    if ($res) {
        while($row = $res->fetch_assoc()) $output[] = $row;
    }
    echo json_encode($output);
    exit;
}

// Yorum Ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem']) && $_POST['islem'] === 'yorum_ekle') {
    if (!isset($_SESSION['kullanici'])) {
        echo json_encode(['status' => 'error', 'message' => 'Yorum yapabilmek için giriş yapmalısınız.']);
        exit;
    }
    
    $uye_id = $_SESSION['kullanici']['id'];
    $urun_id = intval($_POST['urun_id']);
    $puan = intval($_POST['puan']);
    $yorum = $db->real_escape_string(trim($_POST['yorum']));
    $tarih = date('Y-m-d H:i:s');
    
    if ($puan < 1 || $puan > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz puan.']);
        exit;
    }
    if (empty($yorum)) {
        echo json_encode(['status' => 'error', 'message' => 'Yorum boş olamaz.']);
        exit;
    }
    
    $query = "INSERT INTO t_yorum (uye_ID, urun_ID, puan, yorum, tarih) VALUES ($uye_id, $urun_id, $puan, '$yorum', '$tarih')";
    if ($db->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Yorumunuz başarıyla eklendi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Yorum eklenirken hata: ' . $db->error]);
    }
    exit;
}

// AJAX istekleri için hata yakalayıcı (Eğer hiçbir blok çalışmadıysa)
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if (isset($_POST['islem']) || isset($_GET['islem'])) {
        echo json_encode(['status' => 'error', 'message' => 'Yetkisiz erişim veya işlem tanımlanamadı.']);
        exit;
    }
}
?>