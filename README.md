# KralYolu_E-Ticaret
## 🛒 Dinamik E-Ticaret ve Sipariş Yönetimi Sistemi

Bu proje, sınıf içi notlar ve modern web geliştirme prensipleri referans alınarak sıfırdan (native) geliştirilmiş, kapsamlı bir e-ticaret otomasyonudur. 

Projenin temel amacı; modüler kod yazımı, ilişkisel veritabanı (RDBMS) tasarımı ve asenkron veri iletişimi (AJAX/JSON) gibi yapıları tek bir çatı altında, profesyonel bir mimariyle birleştirmektir.

## 🚀 Öne Çıkan Özellikler ve Mimari

* **Front Controller (Tek Merkezden Yönetim) Mimarisi:** Sitenin tüm sayfa geçişleri tek bir ana dosya (`index.php`) içindeki `if-else` yapısıyla dinamik olarak kontrol edilmektedir. Bu sayede kod okunabilirliği maksimize edilmiştir.
* **Separation of Concerns (İşlerin Ayrılması):** HTML/Tasarım kodları (Ön yüz) ile PHP/SQL işlemleri (`data.php` - Arka plan) birbirinden tamamen izole edilmiştir.
* **Gelişmiş AJAX & JSON Entegrasyonu:** Kullanıcı kaydı, oturum açma, sepete ürün ekleme ve **Dinamik İl-İlçe Seçimi** gibi tüm veri işlemleri, sayfa yenilenmeden (arka planda) AJAX ile gerçekleştirilmektedir.
* **İlişkisel Veritabanı (Many-to-Many):** Sadece basit CRUD işlemleri yerine; ürünlerin birden fazla kategoride yer almasını sağlayan ara köprü tabloları ve detaylı sipariş geçmişi kurgulanmıştır.
* **Oturum (Session) Güvenliği:** Kullanıcı girişleri ve yetkilendirmeleri PHP Session yapısıyla güven altına alınmıştır.

## 🛠️ Kullanılan Teknolojiler

* **Back-End:** PHP 8 (Native), MySQL
* **Front-End:** HTML5, CSS3, Bootstrap 5
* **Asenkron Veri Akışı:** JavaScript, jQuery, AJAX, JSON
* **Veritabanı Yönetimi:** phpMyAdmin

## 📁 Proje Klasör Ağacı

```text
E-TICARET-PROJESI/
├── index.php              # Ana iskelet, yönlendirici (Master Page)
├── db.php                 # MySQL Veritabanı bağlantı dosyası
├── data.php               # Tüm AJAX isteklerini karşılayan arka plan motoru
├── sayfalar/              # Arayüzü (HTML/PHP) barındıran modüller
│   ├── vitrin.php         # Ana sayfa ürün listeleme
│   ├── detay.php          # Ürün detayı
│   ├── giris.php          # Giriş formu
│   ├── kayit.php          # Kayıt formu (İl/İlçe AJAX destekli)
│   └── profil.php         # Kullanıcı paneli
└── assets/                
    ├── css/               # Özel stil dosyaları
    ├── js/script.js       # Asenkron (AJAX) komuta merkezi
    └── img/               # Ürün ve sistem görselleri