# 👑 Kral Yolu E-Ticaret ve Sipariş Yönetimi Otomasyonu

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![AJAX](https://img.shields.io/badge/AJAX-Asenkron_Mimari-005571?style=for-the-badge)

Bu proje, modern web geliştirme standartları, modüler mimari prensipleri ve zengin UI/UX tasarımı referans alınarak sıfırdan (native PHP 8) geliştirilmiş, çok kullanıcılı (Müşteri, Satıcı, Yönetici) ve kapsamlı bir E-Ticaret Otomasyon ve Sipariş Yönetimi sistemidir.

Projenin temel amacı; ilişkisel veritabanı (RDBMS) tasarımı, Front Controller tabanlı yönlendirme (routing), rol tabanlı yetkilendirme (RBAC) ve asenkron veri iletişimini (AJAX/JSON) kurumsal ve yüksek performanslı tek bir çatı altında birleştirmektir.

---

## 🚀 Öne Çıkan Özellikler ve Gelişmiş Mimari

### 1. 🏛️ Üç Katmanlı Çoklu Portal Mimarisi (Multi-Portal Architecture)
Sistem, kullanıcı tiplerine göre tamamen izole edilmiş 3 ana portalden oluşmaktadır:
* **Müşteri / Alıcı Portalı (`index.php`):** Son kullanıcıların ürün incelediği, sepet ve sipariş yönetimi yaptığı ana alışveriş arayüzü.
* **Satıcı Portalı (`satici/index.php`):** Mağaza sahiplerinin ürün eklediği, sipariş durumlarını güncellediği ve satış raporlarını incelediği özel kontrol paneli.
* **Yönetim Merkezi / Admin Paneli (`admin/index.php`):** Site yöneticilerinin ürün ve satıcı onaylarını gerçekleştirdiği, kategori ve personel denetimlerini sağladığı tam yetkili yönetim paneli.

### 2. 🎛️ Front Controller (Tek Merkezden Yönetim) Mimarisi
Tüm portaller kendi içlerinde tek bir ana giriş noktası (`index.php`) üzerinden çalışır. Sayfa geçişleri ve modül yüklemeleri `$_GET['sayfa']` parametresi ile dinamik olarak kontrol edilir. Bu yapı, kod tekrarını önler, merkezi oturum/güvenlik denetimi sağlar ve bakımı kolaylaştırır.

### 3. ⚡ Gelişmiş AJAX & JSON Arka Plan Motoru (`data.php`)
Sayfa yenilenmesi gerektiren geleneksel form işlemleri yerine, uygulamanın kalbinde yer alan `data.php` motoru sayesinde tüm kritik işlemler asenkron olarak gerçekleşir:
* **Canlı Arama ve Öneri Sistemi (Trendyol Style):** Kullanıcı arama çubuğuna yazdığı anda anlık olarak veritabanında sorgulama yapılır; eşleşen ürünler, kategoriler ve popüler arama etiketleri anında listelenir.
* **Dinamik İl-İlçe-Mahalle Seçimi:** Kayıt, profil ve ödeme sayfalarında yer alan adres formları, Türkiye coğrafi konum veritabanından (`iller`, `ilceler`, `mahalleler`) AJAX ile kademeli olarak yüklenir.
* **Sepet ve Favori Yönetimi:** Sepete ürün ekleme, adet güncelleme, sepetten çıkarma ve favorilere ekleme işlemleri sayfa yenilenmeden, anlık stok kontrolü yapılarak tamamlanır.

### 4. 🔗 Gelişmiş İlişkisel Veritabanı Tasarımı (RDBMS)
Basit CRUD işlemlerinin ötesinde, gerçek dünya e-ticaret senaryolarına uygun normalize edilmiş veritabanı yapısı:
* Ürünlerin çoklu kategori ve alt kategori (`t_kategori`, `t_alt_kategori_1`, `t_alt_kategori_2`) eşleşmeleri.
* Her ürüne ait çoklu ve sıralanabilir fotoğraf galerisi (`t_foto`).
* Siparişler ve sipariş detaylarının (`t_siparis`, `t_siparis_detay`) geçmişe dönük fiyat garantisiyle saklanması.
* Kullanıcı adresleri (`t_adres`), kayıtlı kartlar (`t_kredi_karti`) ve ürün değerlendirmeleri/yorumları (`t_yorum`).

---

## 📦 Modül ve Fonksiyon Detayları

### 🛒 1. Müşteri (Alıcı) Arayüzü Modülleri
* **Vitrin ve Ana Sayfa (`anasayfa.php`):** Dinamik bannerlar, öne çıkan ve popüler ürünler, kaydırılabilir ürün karuselleri.
* **Ürün Kataloğu ve Filtreleme (`urunler.php`):** Kategori bazlı listeleme, fiyat aralığı ve kelime bazlı filtreleme mekanizması.
* **Detaylı Ürün İnceleme (`urundetay.php`):** Ürün fotoğrafları galerisi, dinamik yıldız ortalaması, müşteri yorumları ve anlık stok durumuna bağlı sepete ekleme butonu.
* **Sepet ve Ödeme Adımları (`sepet.php`, `odeme.php`):** Dinamik ara toplam/kargo hesaplamaları, kayıtlı adreslerden seçim yapabilme veya yeni adres ekleme, sipariş tamamlama altyapısı.
* **Kullanıcı Paneli (`profil.php`, `siparisler.php`, `favoriler.php`):** Kişisel bilgi güncelleme, şifre değiştirme, çoklu adres ve kredi kartı yönetimi, sipariş durum takibi (Hazırlanıyor, Kargolandı vb.) ve favori ürünler listesi.
* **Giriş ve Kayıt (`giris_kayit.php`):** Güvenli parola doğrulama ve AJAX destekli detaylı üyelik formu.

### 🏪 2. Satıcı Portalı Modülleri
* **Satıcı Başvuru ve Giriş (`satici_giris.php`):** Vergi no, vergi dairesi, şirket türü gibi kurumsal bilgilerle satıcı başvuru akışı ve onay durumu takibi.
* **Satıcı Kontrol Paneli (`anasayfa.php` / `satici_panel.php`):** Mağaza satış özetleri, toplam gelir, bekleyen sipariş sayısı ve hızlı eylem butonları.
* **Ürün Yönetimi (`urunler.php`, `urun_ekle.php`, `urun_duzenle.php`):** Satıcının kendi ürünlerini listelemesi, düzenlemesi ve yeni ürün eklemesi. Yeni ürün ekleme modülü, **SortableJS** kütüphanesi ile sürükle-bırak fotoğraf sıralama altyapısına sahiptir.
* **Sipariş Yönetimi (`siparisler.php`):** Satıcının ürünlerini içeren siparişleri görüntülemesi ve kargo durumlarını güncellemesi.
* **Satış Raporları (`raporlar.php`):** Grafiksel ve tablosal olarak mağaza performansının, en çok satan ürünlerin ve gelir analizinin incelenmesi.
* **Mağaza Ayarları (`magaza_ayarlari.php`):** Kurumsal profil, marka adı ve mağaza detaylarının düzenlenmesi.

### 🛡️ 3. Yönetim Merkezi (Admin Paneli) Modülleri
* **Sistem Özeti (`anasayfa.php`):** Platformdaki toplam ciro, üye sayısı, satıcı sayısı ve bekleyen onayların merkezi istatistikleri.
* **Ürün Onay Sistemi (`urun_onay.php`, `urun_onay_detay.php`):** Satıcılar tarafından eklenen yeni ürünlerin platform standartlarına uygunluğunu denetleme ve yayına alma akışı.
* **Satıcı Onay Sistemi (`satici_onay.php`, `satici_onay_detay.php`):** Yeni satıcı başvurularının kurumsal belgeler ve vergi bilgileri ışığında incelenip onaylanması veya reddedilmesi.
* **Hiyerarşik Kategori Yönetimi (`kategoriler.php`):** Ana ve alt kategorilerin sistem üzerinden dinamik olarak eklenip düzenlenmesi.
* **Personel ve Yetki Yönetimi (`personel.php`):** Sistem yöneticilerinin ve alt yetkililerin atanması, rol bazlı erişim denetimi.
* **Sistem Günlükleri (`admin_debug.log`):** Yönetici işlemlerinin ve olası sistem hatalarının detaylı loglanması.

---

## 🛠️ Kullanılan Teknolojiler ve Kütüphaneler

### Back-End (Arka Plan)
* **Dil:** PHP 8.0+ (Native, OOP & Procedural Hibrit Mimarisi)
* **Veritabanı:** MySQL 8.0+ / MariaDB
* **Güvenlik ve İletişim:** PHP Sessions, PDO/MySQLi Prepared Statements, Password Hashing API (`password_hash`), Exception Handling (`try-catch`)

### Front-End (Ön Yüz & UI/UX)
* **Yapı:** HTML5, CSS3
* **CSS Framework:** Bootstrap 5.3.3 (Modern, esnek ve mobil uyumlu grid sistemi)
* **İkon Seti:** Bootstrap Icons 1.11.1
* **Tipografi:** Google Fonts (Outfit font ailesi)
* **Özel Stiller:** Modüler CSS mimarisi (`styles.css`, `anasayfa.css`, `satici_panel.css`, `admin_panel.css`, `urundetay.css`, `sepet.css` vb.)

### Asenkron Veri Akışı & Etkileşim (JavaScript)
* **Çekirdek:** JavaScript (ES6+), jQuery 3.7.1, AJAX, JSON API
* **Bildirimler & Modallar:** **SweetAlert2** (Gelişmiş, şık ve dinamik kullanıcı bildirimleri, toast mesajları)
* **Sürükle-Bırak Sıralama:** **SortableJS** (Satıcı ürün fotoğraflarının dinamik olarak sıralanması)

---

## 📁 Detaylı Proje Klasör Ağacı

```text
c:\xampp\htdocs\Internet_Programciligi_Proje\
├── README.md                          # Proje tanıtım, mimari ve kurulum belgesi
└── Proje/                             # Ana Web Uygulaması Dizini
    ├── config.php                     # Veritabanı bağlantı ayarları ve hata yönetimi
    ├── index.php                      # MÜŞTERİ / ALICI Portalı Ana Yönlendiricisi (Front Controller)
    ├── data.php                       # AJAX İsteklerini karşılayan merkezi arka plan işlem motoru
    │
    ├── musteri/                       # MÜŞTERİ (ALICI) ARAYÜZ MODÜLLERİ
    │   ├── anasayfa.php               # Dinamik vitrin, öne çıkan ürünler ve slider
    │   ├── urunler.php                # Gelişmiş filtrelemeli ürün kataloğu
    │   ├── urundetay.php              # Fotoğraf galerili ve yorumlu ürün detay sayfası
    │   ├── sepet.php                  # AJAX tabanlı anlık sepet yönetim sayfası
    │   ├── odeme.php                  # Adres seçimi ve sipariş tamamlama adımı
    │   ├── siparisler.php             # Müşteri sipariş geçmişi ve kargo takibi
    │   ├── profil.php                 # Kişisel bilgiler, adres ve kart yönetimi paneli
    │   ├── favoriler.php              # Müşteri favori ürünler listesi
    │   ├── giris_kayit.php            # AJAX il/ilçe destekli giriş ve üyelik formları
    │   ├── navbar.php                 # Üst gezinme ve arama çubuğu
    │   └── footer.php                 # Alt bilgi ve bağlantılar alanı
    │
    ├── satici/                        # SATICI (MAĞAZA) PORTALI
    │   ├── index.php                  # Satıcı Portalı Ana Yönlendiricisi ve İskeleti
    │   ├── satici_giris.php           # Satıcı giriş ve kurumsal başvuru ekranı
    │   ├── anasayfa.php               # Satıcı kontrol paneli, özet istatistikler
    │   ├── urunler.php                # Mağazaya ait mevcut ürünlerin listesi
    │   ├── urun_ekle.php              # SortableJS destekli yeni ürün ekleme formu
    │   ├── urun_duzenle.php           # Mevcut ürün bilgileri ve stok güncelleme
    │   ├── siparisler.php             # Mağazaya gelen siparişler ve kargo durum yönetimi
    │   ├── raporlar.php               # Mağaza satış grafikleri ve ciro raporları
    │   ├── magaza_ayarlari.php        # Mağaza profili ve kurumsal ayarlar
    │   ├── satici_panel.php           # (Alternatif/Eski yönlendirme eşleşmesi)
    │   └── satici_islem.php           # Satıcı oturum ve form işlemleri arka plan dosyası
    │
    ├── admin/                         # YÖNETİM MERKEZİ (ADMIN PANELİ)
    │   ├── index.php                  # Admin Paneli Ana Yönlendiricisi ve İskeleti
    │   ├── admin_giris.php            # Güvenli yönetici giriş ekranı
    │   ├── anasayfa.php               # Sistem geneli ciro, üye ve onay istatistikleri
    │   ├── urun_onay.php              # Satıcıların eklediği ürünlerin onay listesi
    │   ├── urun_onay_detay.php        # Onay bekleyen ürünün tüm detaylarının incelenmesi
    │   ├── satici_onay.php            # Yeni mağaza açma başvurularının listesi
    │   ├── satici_onay_detay.php      # Satıcı başvuru detayları ve evrak inceleme
    │   ├── kategoriler.php            # Kategori ve alt kategori ağacı yönetimi (CRUD)
    │   ├── personel.php               # Yönetici ve personel yetkilendirme işlemleri
    │   ├── admin_islem.php            # Yönetici oturum ve form işlemleri arka plan dosyası
    │   └── admin_debug.log            # Sistem günlükleri ve hata kayıt dosyası
    │
    ├── SQL/                           # VERİTABANI YEDEKLERİ VE YAMALAR
    │   ├── kral_yolu_tam_surum_v2.sql # Tüm tabloları ve örnek verileri içeren ana SQL dökümü
    │   ├── kral_yolu_tam_surum_v2_patch.sql # Veritabanı yama dosyası
    │   └── urun_cesitliligi.sql       # Ekstra ürün ve kategori çeşitliliği sağlayan veri dökümü
    │
    ├── assets/                        # STATİK SİSTEM KAYNAKLARI
    │   ├── css/                       # Özel modüler stil dosyaları (styles.css, anasayfa.css vb.)
    │   ├── js/                        # script.js (AJAX ve SweetAlert2 komuta merkezi)
    │   ├── img/                       # Ürün, afiş ve sistem görselleri
    │   └── image/                     # Ek görsel dizini
    │
    └── HTML/                          # Statik HTML arayüz prototipleri (admin, alici, satici)
```

---

## ⚙️ Kurulum ve Çalıştırma Rehberi

Uygulamayı yerel sunucunuzda (localhost) sorunsuz bir şekilde çalıştırmak için aşağıdaki adımları takip ediniz:

### 1. Gereksinimler
* **XAMPP, WAMP veya MAMP** (PHP 8.0 veya üzeri, MySQL / MariaDB servisi aktif olmalıdır).

### 2. Dosyaların Konumlandırılması
* Proje klasörünü (`Internet_Programciligi_Proje`) XAMPP kullanıyorsanız `c:\xampp\htdocs\` dizinine, WAMP kullanıyorsanız `c:\wamp\www\` dizinine kopyalayınız.

### 3. Veritabanının Oluşturulması ve İçe Aktarılması
1. Tarayıcınızdan `http://localhost/phpmyadmin` adresine gidiniz.
2. `db_ticaret` adında yeni, boş bir veritabanı oluşturunuz (Karakter setini `utf8mb4_general_ci` veya `utf8mb4_unicode_ci` olarak seçmeniz tavsiye edilir).
3. Oluşturduğunuz veritabanının içine girip üst menüden **İçe Aktar (Import)** sekmesine tıklayınız.
4. Proje dizinindeki `Proje/SQL/kral_yolu_tam_surum_v2.sql` dosyasını seçerek içe aktarma işlemini tamamlayınız. (İsteğe bağlı olarak zenginleştirilmiş veri seti için `urun_cesitliligi.sql` dosyasını da içe aktarabilirsiniz).

### 4. Bağlantı Kontrolü
* `Proje/config.php` dosyasını açarak veritabanı adı (`$dbname`), kullanıcı adı (`$username`) ve parola (`$password`) bilgilerinin yerel sunucu ayarlarınızla eşleştiğinden emin olunuz (XAMPP varsayılanında kullanıcı adı `root`, parola boştur `''`).

### 5. Uygulamaya Giriş
* Tarayıcınızdan `http://localhost/Internet_Programciligi_Proje/Proje/` adresine giderek uygulamayı başlatabilirsiniz.
* **Müşteri Portalı:** Doğrudan açılan ana sayfadır.
* **Satıcı Portalı:** `http://localhost/Internet_Programciligi_Proje/Proje/satici/`
* **Admin Paneli:** `http://localhost/Internet_Programciligi_Proje/Proje/admin/`

---

## 🔒 Güvenlik ve Geliştirme Standartları

* **SQL Injection Koruması:** Tüm veritabanı sorgularında PDO/MySQLi Prepared Statements (Hazırlanmış İfadeler) ve `real_escape_string` filtrelemeleri kullanılarak dışarıdan gelebilecek manipülasyonlar engellenmiştir.
* **XSS (Cross-Site Scripting) Koruması:** Kullanıcıdan alınan ve ekrana yazdırılan tüm veriler `htmlspecialchars()` fonksiyonu ile filtrelenmektedir.
* **Güvenli Parola Saklama:** Kullanıcı ve satıcı parolaları veritabanında asla düz metin (clear-text) olarak saklanmaz; PHP'nin native `password_hash()` fonksiyonu ile güçlü şifreleme algoritmaları kullanılarak korunur.
* **Oturum (Session) Güvenliği:** Yetkisiz erişimleri engellemek adına her portalin giriş noktasında sıkı session kontrolü yapılmakta, yetkisiz kullanıcılar anında giriş sayfalarına yönlendirilmektedir.
* **Hata Yönetimi (Exception Handling):** Olası veritabanı bağlantı hataları `try-catch` blokları ile yakalanmakta, son kullanıcıya sistem dizin yollarını açığa çıkaran PHP hataları yerine, özel tasarlanmış şık uyarı ekranları gösterilmektedir.

---
*👑 Kral Yolu E-Ticaret - Güvenli, Hızlı ve Modern Alışverişin Mimarı.*