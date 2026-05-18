-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Üretim Zamanı: 26 Nis 2026, 15:30:00
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `db_ticaret`
--
CREATE DATABASE IF NOT EXISTS `db_ticaret` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE `db_ticaret`;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ilceler`
--

CREATE TABLE `ilceler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `il_id` int(11) NOT NULL,
  `ilce_adi` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ilceler`
--

INSERT INTO `ilceler` (`id`, `il_id`, `ilce_adi`) VALUES
(1, 1, 'Seyhan'), (2, 2, 'Merkez'), (3, 3, 'Merkez'), (4, 4, 'Merkez'),
(5, 5, 'Merkez'), (6, 6, 'Çankaya'), (7, 7, 'Muratpaşa'), (8, 8, 'Merkez'),
(9, 9, 'Efeler'), (10, 10, 'Karesi'), (11, 11, 'Merkez'), (12, 12, 'Merkez'),
(13, 13, 'Merkez'), (14, 14, 'Merkez'), (15, 15, 'Merkez'), (16, 16, 'Osmangazi'),
(17, 17, 'Merkez'), (18, 18, 'Merkez'), (19, 19, 'Merkez'), (20, 20, 'Pamukkale'),
(21, 21, 'Sur'), (22, 22, 'Merkez'), (23, 23, 'Merkez'), (24, 24, 'Merkez'),
(25, 25, 'Yakutiye'), (26, 26, 'Odunpazarı'), (27, 27, 'Şahinbey'), (28, 28, 'Merkez'),
(29, 29, 'Merkez'), (30, 30, 'Merkez'), (31, 31, 'Antakya'), (32, 32, 'Merkez'),
(33, 33, 'Yenişehir'), (34, 34, 'Kadıköy'), (35, 35, 'Konak'), (36, 36, 'Merkez'),
(37, 37, 'Merkez'), (38, 38, 'Melikgazi'), (39, 39, 'Merkez'), (40, 40, 'Merkez'),
(41, 41, 'İzmit'), (42, 42, 'Selçuklu'), (43, 43, 'Merkez'), (44, 44, 'Battalgazi'),
(45, 45, 'Yunusemre'), (46, 46, 'Dulkadiroğlu'), (47, 47, 'Artuklu'), (48, 48, 'Menteşe'),
(49, 49, 'Merkez'), (50, 50, 'Merkez'), (51, 51, 'Merkez'), (52, 52, 'Altınordu'),
(53, 53, 'Merkez'), (54, 54, 'Adapazarı'), (55, 55, 'İlkadım'), (56, 56, 'Merkez'),
(57, 57, 'Merkez'), (58, 58, 'Merkez'), (59, 59, 'Süleymanpaşa'), (60, 60, 'Merkez'),
(61, 61, 'Ortahisar'), (62, 62, 'Merkez'), (63, 63, 'Haliliye'), (64, 64, 'Merkez'),
(65, 65, 'İpekyolu'), (66, 66, 'Merkez'), (67, 67, 'Merkez'), (68, 68, 'Merkez'),
(69, 69, 'Merkez'), (70, 70, 'Merkez'), (71, 71, 'Merkez'), (72, 72, 'Merkez'),
(73, 73, 'Merkez'), (74, 74, 'Merkez'), (75, 75, 'Merkez'), (76, 76, 'Merkez'),
(77, 77, 'Merkez'), (78, 78, 'Merkez'), (79, 79, 'Merkez'), (80, 80, 'Merkez'),
(81, 81, 'Merkez');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `iller`
--

CREATE TABLE `iller` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `il_adi` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `iller`
--

INSERT INTO `iller` (`id`, `il_adi`) VALUES
(1, 'Adana'), (2, 'Adıyaman'), (3, 'Afyonkarahisar'), (4, 'Ağrı'),
(5, 'Amasya'), (6, 'Ankara'), (7, 'Antalya'), (8, 'Artvin'),
(9, 'Aydın'), (10, 'Balıkesir'), (11, 'Bilecik'), (12, 'Bingöl'),
(13, 'Bitlis'), (14, 'Bolu'), (15, 'Burdur'), (16, 'Bursa'),
(17, 'Çanakkale'), (18, 'Çankırı'), (19, 'Çorum'), (20, 'Denizli'),
(21, 'Diyarbakır'), (22, 'Edirne'), (23, 'Elazığ'), (24, 'Erzincan'),
(25, 'Erzurum'), (26, 'Eskişehir'), (27, 'Gaziantep'), (28, 'Giresun'),
(29, 'Gümüşhane'), (30, 'Hakkari'), (31, 'Hatay'), (32, 'Isparta'),
(33, 'Mersin'), (34, 'İstanbul'), (35, 'İzmir'), (36, 'Kars'),
(37, 'Kastamonu'), (38, 'Kayseri'), (39, 'Kırklareli'), (40, 'Kırşehir'),
(41, 'Kocaeli'), (42, 'Konya'), (43, 'Kütahya'), (44, 'Malatya'),
(45, 'Manisa'), (46, 'Kahramanmaraş'), (47, 'Mardin'), (48, 'Muğla'),
(49, 'Muş'), (50, 'Nevşehir'), (51, 'Niğde'), (52, 'Ordu'),
(53, 'Rize'), (54, 'Sakarya'), (55, 'Samsun'), (56, 'Siirt'),
(57, 'Sinop'), (58, 'Sivas'), (59, 'Tekirdağ'), (60, 'Tokat'),
(61, 'Trabzon'), (62, 'Tunceli'), (63, 'Şanlıurfa'), (64, 'Uşak'),
(65, 'Van'), (66, 'Yozgat'), (67, 'Zonguldak'), (68, 'Aksaray'),
(69, 'Bayburt'), (70, 'Karaman'), (71, 'Kırıkkale'), (72, 'Batman'),
(73, 'Şırnak'), (74, 'Bartın'), (75, 'Ardahan'), (76, 'Iğdır'),
(77, 'Yalova'), (78, 'Karabük'), (79, 'Kilis'), (80, 'Osmaniye'),
(81, 'Düzce');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `mahalleler`
--

CREATE TABLE `mahalleler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ilce_id` int(11) NOT NULL,
  `mahalle_adi` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `mahalleler`
--

INSERT INTO `mahalleler` (`id`, `ilce_id`, `mahalle_adi`) VALUES
(1, 1, 'Reşatbey Mah.'), (2, 2, 'Altınşehir Mah.'), (3, 3, 'Erenler Mah.'),
(4, 4, 'Cumhuriyet Mah.'), (5, 5, 'Bahçelievler Mah.'), (6, 6, 'Kızılay Mah.'),
(7, 7, 'Şirinyalı Mah.'), (8, 8, 'Hürriyet Mah.'), (9, 9, 'Mimar Sinan Mah.'),
(10, 10, 'Paşaalanı Mah.'), (11, 11, 'Ertuğrulgazi Mah.'), (12, 12, 'İnönü Mah.'),
(13, 13, 'Hüsrevpaşa Mah.'), (14, 14, 'Sağlık Mah.'), (15, 15, 'Konak Mah.'),
(16, 16, 'Çekirge Mah.'), (17, 17, 'Barbaros Mah.'), (18, 18, 'Buğday Pazarı Mah.'),
(19, 19, 'Ulukavak Mah.'), (20, 20, 'Kınıklı Mah.'), (21, 21, 'Dicle Mah.'),
(22, 22, 'Şükrüpaşa Mah.'), (23, 23, 'Sürsürü Mah.'), (24, 24, 'Yunus Emre Mah.'),
(25, 25, 'Terminal Mah.'), (26, 26, 'Vişnelik Mah.'), (27, 27, 'Karataş Mah.'),
(28, 28, 'Güre Mah.'), (29, 29, 'Bağlarbaşı Mah.'), (30, 30, 'Merzan Mah.'),
(31, 31, 'Sümerler Mah.'), (32, 32, 'Fatih Mah.'), (33, 33, 'Limonluk Mah.'),
(34, 34, 'Kadıköy Merkez Mah.'), (35, 35, 'Alsancak Mah.'), (36, 36, 'Ortakapı Mah.'),
(37, 37, 'İsmailbey Mah.'), (38, 38, 'Alpaslan Mah.'), (39, 39, 'Karacaibrahim Mah.'),
(40, 40, 'Yenice Mah.'), (41, 41, 'Yahya Kaptan Mah.'), (42, 42, 'Bosna Hersek Mah.'),
(43, 43, 'Meydan Mah.'), (44, 44, 'Çöşnük Mah.'), (45, 45, 'Uncubozköy Mah.'),
(46, 46, 'Bahçelievler Mah.'), (47, 47, 'Yenişehir Mah.'), (48, 48, 'Karamehmet Mah.'),
(49, 49, 'Kültür Mah.'), (50, 50, 'Güzelyurt Mah.'), (51, 51, 'Selçuk Mah.'),
(52, 52, 'Akyazı Mah.'), (53, 53, 'Müftü Mah.'), (54, 54, 'Korucuk Mah.'),
(55, 55, 'Atakum Mah.'), (56, 56, 'Bahçelievler Mah.'), (57, 57, 'Gelincik Mah.'),
(58, 58, 'Kardeşler Mah.'), (59, 59, 'Hürriyet Mah.'), (60, 60, 'Karşıyaka Mah.'),
(61, 61, 'Çukurçayır Mah.'), (62, 62, 'Moğultay Mah.'), (63, 63, 'Karaköprü Mah.'),
(64, 64, 'Kemalöz Mah.'), (65, 65, 'Halilağa Mah.'), (66, 66, 'Karatepe Mah.'),
(67, 67, 'İncivez Mah.'), (68, 68, 'Ereğlikapı Mah.'), (69, 69, 'Şingah Mah.'),
(70, 70, 'Sekiçeşme Mah.'), (71, 71, 'Fabrikalar Mah.'), (72, 72, 'Gültepe Mah.'),
(73, 73, 'Yeşilyurt Mah.'), (74, 74, 'Gölbucağı Mah.'), (75, 75, 'Kaptanpaşa Mah.'),
(76, 76, 'Bağlar Mah.'), (77, 77, 'Rüstempaşa Mah.'), (78, 78, 'Bostanbükü Mah.'),
(79, 79, 'Yaşar Aktürk Mah.'), (80, 80, 'Raufbey Mah.'), (81, 81, 'Konuralp Mah.');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_uye`
--

CREATE TABLE `t_uye` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `ad` varchar(255) DEFAULT NULL,
  `soyad` varchar(255) DEFAULT NULL,
  `e_posta` varchar(255) DEFAULT NULL,
  `parola` varchar(255) DEFAULT NULL,
  `telefon` varchar(16) DEFAULT NULL,
  `kayit_tarihi` datetime DEFAULT NULL,
  `son_giris` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_uye`
--

INSERT INTO `t_uye` (`id`, `ad`, `soyad`, `e_posta`, `parola`, `telefon`, `kayit_tarihi`, `son_giris`) VALUES
(1, 'Ahmet', 'Yılmaz', 'ahmet@mail.com', '$2y$10$y5R6y1O2EwM/uU/5wS0.BefK4P53Z.T/3Z9mQ1Jv2PzX.T.H83x.S', '05321112233', '2026-03-21 10:00:00', '2026-03-21 14:00:00'),
(2, 'Ayşe', 'Kaya', 'ayse@mail.com', '$2y$10$y5R6y1O2EwM/uU/5wS0.BefK4P53Z.T/3Z9mQ1Jv2PzX.T.H83x.S', '05554443322', '2026-03-20 15:30:00', NULL),
(3, 'Emre', 'Tuncer', 'admin1@gmail.com', '$2y$10$ixOvsuYzgj.pdzisoqRd4OAu5DEnXcH4E8AuRSOcN1yTLm5D8uKuO', NULL, '2026-03-24 00:05:58', '2026-04-02 09:24:11');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_adres`
--

CREATE TABLE `t_adres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uye_ID` int(8) DEFAULT NULL,
  `ad` varchar(255) DEFAULT NULL,
  `soyad` varchar(255) DEFAULT NULL,
  `adres_basligi` varchar(255) DEFAULT NULL,
  `il` varchar(255) DEFAULT NULL,
  `ilce` varchar(255) DEFAULT NULL,
  `mahalle` varchar(255) DEFAULT NULL,
  `tam_adres` text DEFAULT NULL,
  `telefon` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_adres_uye` FOREIGN KEY (`uye_ID`) REFERENCES `t_uye` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_adres`
--

INSERT INTO `t_adres` (`id`, `uye_ID`, `ad`, `soyad`, `adres_basligi`, `il`, `ilce`, `mahalle`, `tam_adres`, `telefon`) VALUES
(1, 1, 'Ahmet', 'Yılmaz', 'Ev Adresim', 'İstanbul', 'Kadıköy', 'Caferağa Mah.', 'Moda Cad. No:12 D:4', '05321112233'),
(2, 1, 'Ahmet', 'Yılmaz', 'İş Adresim', 'İstanbul', 'Şişli', 'Mecidiyeköy Mah.', 'Büyükdere Cad. No:55 Plaza:1', '05321112233'),
(3, 2, 'Ayşe', 'Kaya', 'Ev Adresim', 'Ankara', 'Çankaya', 'Kızılay Mah.', 'Atatürk Bulvarı No:10', '05554443322'),
(4, 3, '1', '1', '3', 'İstanbul', 'Kadıköy', 'Kadıköy Merkez Mah.', '3', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_kategori`
--

CREATE TABLE `t_kategori` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `kategori_adi` varchar(255) DEFAULT NULL,
  `kategori_turu` varchar(255) DEFAULT NULL,
  `kategori_aciklama` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_kategori`
--

INSERT INTO `t_kategori` (`id`, `kategori_adi`, `kategori_turu`, `kategori_aciklama`) VALUES
(1, 'Elektronik', 'Fiziksel Ürün', 'Teknolojik ve elektronik aletler'),
(2, 'Giyim', 'Fiziksel Ürün', 'Erkek, Kadın ve Çocuk Giyim Ürünleri'),
(3, 'Kitap', 'Fiziksel Ürün', 'Tüm okuma ve roman kitapları');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_alt_kategori_1`
--

CREATE TABLE `t_alt_kategori_1` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `kategori_adi` varchar(255) DEFAULT NULL,
  `kategori_turu` varchar(255) DEFAULT NULL,
  `kategori_id` int(5) DEFAULT NULL,
  `kategori_aciklama` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_alt1_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `t_kategori` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_alt_kategori_1`
--

INSERT INTO `t_alt_kategori_1` (`id`, `kategori_adi`, `kategori_turu`, `kategori_id`, `kategori_aciklama`) VALUES
(1, 'Bilgisayar', 'Teknoloji', 1, 'Masaüstü ve Dizüstü Bilgisayarlar'),
(2, 'Cep Telefonu', 'Teknoloji', 1, 'Akıllı Cep Telefonları'),
(3, 'Erkek Giyim', 'Tekstil', 2, 'Erkek kıyafetleri'),
(4, 'Bilim Kurgu Kitapları', 'Yayın', 3, 'Gelecek temalı romanlar');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_alt_kategori_2`
--

CREATE TABLE `t_alt_kategori_2` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `kategori_adi` varchar(255) DEFAULT NULL,
  `kategori_turu` varchar(255) DEFAULT NULL,
  `alt_1_ID` int(5) DEFAULT NULL,
  `kategori_aciklama` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_alt2_alt1` FOREIGN KEY (`alt_1_ID`) REFERENCES `t_alt_kategori_1` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_alt_kategori_2`
--

INSERT INTO `t_alt_kategori_2` (`id`, `kategori_adi`, `kategori_turu`, `alt_1_ID`, `kategori_aciklama`) VALUES
(1, 'Dizüstü Bilgisayar', 'Cihaz', 1, 'Taşınabilir Bilgisayarlar (Laptop)'),
(2, 'Tişört', 'Üst Giyim', 3, 'Erkek Tişörtleri');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_satici`
--

CREATE TABLE `t_satici` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad` varchar(255) DEFAULT NULL,
  `soyad` varchar(255) DEFAULT NULL,
  `e_posta` varchar(255) DEFAULT NULL,
  `parola` varchar(255) DEFAULT NULL,
  `telefon` varchar(16) DEFAULT NULL,
  `kayit_tarihi` datetime DEFAULT NULL,
  `kurum_adi` varchar(255) DEFAULT NULL COMMENT 'Mağaza Adı (Görünen)',
  `ticari_unvan` varchar(255) DEFAULT NULL COMMENT 'Resmi Şirket Ünvanı',
  `sirket_turu` varchar(100) DEFAULT NULL COMMENT 'Şahıs, Ltd, A.Ş. vb.',
  `vergi_dairesi` varchar(100) DEFAULT NULL,
  `vergi_no` varchar(20) DEFAULT NULL,
  `mersis_no` varchar(30) DEFAULT NULL,
  `kep_adresi` varchar(255) DEFAULT NULL,
  `kurumsal_telefon` varchar(20) DEFAULT NULL,
  `il` varchar(255) DEFAULT NULL,
  `ilce` varchar(255) DEFAULT NULL,
  `mahalle` varchar(255) DEFAULT NULL,
  `kurum_adres` text DEFAULT NULL,
  `iban` varchar(34) DEFAULT NULL COMMENT 'Banka IBAN Numarası',
  `rol` varchar(60) DEFAULT NULL,
  `onay_durumu` varchar(50) DEFAULT 'Onay Bekliyor' COMMENT 'Onay Bekliyor, Onaylandı, Reddedildi, Revize Bekliyor',
  `personel_ID` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_satici`
--

INSERT INTO `t_satici` (`id`, `ad`, `soyad`, `e_posta`, `parola`, `telefon`, `kayit_tarihi`, `kurum_adi`, `ticari_unvan`, `sirket_turu`, `il`, `ilce`, `mahalle`, `kurum_adres`, `rol`, `onay_durumu`) VALUES
(1, 'Mehmet', 'Demir', 'satici1@mail.com', '123456', '08501112233', '2026-03-01 09:00:00', 'Tekno Bilişim A.Ş.', 'Tekno Bilişim Pazarlama San. ve Tic. Ltd. Şti.', 'Limited Şirket (Ltd. Şti.)', 'İzmir', 'Bornova', 'Kazımdirik', 'Üniversite Cad. No:3', 'Premium Satıcı', 'Onaylandı'),
(2, 'Zeynep', 'Çelik', 'satici2@mail.com', '123456', '08502223344', '2026-03-05 10:00:00', 'Trend Moda Dünyası', 'Trend Moda Tekstil A.Ş.', 'Anonim Şirket (A.Ş.)', 'İstanbul', 'Beşiktaş', 'Levent', 'Moda Sok. No:5', 'Standart Satıcı', 'Onaylandı');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_urun`
--

CREATE TABLE `t_urun` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `urun_adi` varchar(255) DEFAULT NULL,
  `urun_aciklama` text DEFAULT NULL,
  `fiyat` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `aktif` varchar(60) DEFAULT NULL,
  `onay_durumu` varchar(50) DEFAULT 'Onay Bekliyor' COMMENT 'Onay Bekliyor, Onaylandı, Reddedildi',
  `eklenme_tarihi` datetime DEFAULT NULL,
  `satici_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_urun_satici` FOREIGN KEY (`satici_ID`) REFERENCES `t_satici` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_urun`
--

INSERT INTO `t_urun` (`id`, `urun_adi`, `urun_aciklama`, `fiyat`, `stok`, `aktif`, `onay_durumu`, `eklenme_tarihi`, `satici_ID`) VALUES
(1, 'Oyuncu Dizüstü Bilgisayarı', 'Yüksek performanslı 16GB RAM ve RTX ekran kartlı oyuncu bilgisayarı.', 35000, 50, 'Evet', 'Onaylandı', '2026-03-10 10:00:00', 1),
(2, 'Pamuklu Erkek Tişört', 'Yazlık, %100 pamuk, siyah renkli rahat tişört.', 250, 200, 'Evet', 'Onaylandı', '2026-03-12 11:30:00', 2),
(3, 'Dune (Çöl Gezegeni)', 'Frank Herbert - Bilim kurgu klasiği muazzam eser, ciltli.', 120, 100, 'Evet', 'Onaylandı', '2026-03-15 09:15:00', 1),
(4, 'iPhone 15 Pro Max 256 GB - Naturel Titanyum', 'Apple tarafından üretilen en yeni amiral gemisi akıllı telefon. Tasarım harikası.', 82999, 10, 'Evet', 'Onaylandı', '2026-03-24 02:03:28', 1),
(5, 'Sony WH-1000XM5 Kablosuz Gürültü Engelleyici Kulaklık', 'Sektör lideri gürültü engelleme özellikli kablosuz kulaklık. Üstün ses deneyimi.', 12450, 25, 'Evet', 'Onaylandı', '2026-03-24 02:03:28', 1),
(6, 'MacBook Air M3 Çip - 16GB RAM 512GB SSD', 'Güçlü M3 çipiyle donatılmış ince ve hafif dizüstü bilgisayar. Taşınabilir güç.', 54200, 15, 'Evet', 'Onaylandı', '2026-03-24 02:03:28', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_urun_kategori`
--

CREATE TABLE `t_urun_kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urun_ID` int(9) DEFAULT NULL,
  `kategori_ID` int(5) DEFAULT NULL,
  `alt_kategori_1_ID` int(5) DEFAULT NULL,
  `alt_kategori_2_ID` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_urunkat_alt1` FOREIGN KEY (`alt_kategori_1_ID`) REFERENCES `t_alt_kategori_1` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_urunkat_alt2` FOREIGN KEY (`alt_kategori_2_ID`) REFERENCES `t_alt_kategori_2` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_urunkat_kat` FOREIGN KEY (`kategori_ID`) REFERENCES `t_kategori` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_urunkat_urun` FOREIGN KEY (`urun_ID`) REFERENCES `t_urun` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_urun_kategori`
--

INSERT INTO `t_urun_kategori` (`id`, `urun_ID`, `kategori_ID`, `alt_kategori_1_ID`, `alt_kategori_2_ID`) VALUES
(1, 1, 1, 1, 1),
(2, 2, 2, 3, 2),
(3, 3, 3, 4, NULL),
(4, 4, 1, 2, NULL),
(5, 5, 1, 1, NULL),
(6, 6, 1, 1, 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_foto`
--

CREATE TABLE `t_foto` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `urun_ID` int(11) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `foto_turu` varchar(255) DEFAULT NULL,
  `foto_sira` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_foto_urun` FOREIGN KEY (`urun_ID`) REFERENCES `t_urun` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_foto`
--

INSERT INTO `t_foto` (`id`, `urun_ID`, `foto_url`, `foto_turu`, `foto_sira`) VALUES
(1, 1, 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&q=80', 'Kapak', 1),
(2, 2, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500&q=80', 'Kapak', 1),
(3, 3, 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=500&q=80', 'Kapak', 1),
(4, 4, 'https://images.unsplash.com/photo-1696446701796-da61225697cc?w=500&q=80', 'Kapak', 1),
(5, 5, 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&q=80', 'Kapak', 1),
(6, 6, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&q=80', 'Kapak', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_urun_tablo_detay`
--

CREATE TABLE `t_urun_tablo_detay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urun_ID` int(9) DEFAULT NULL,
  `sutun_baslik` varchar(255) DEFAULT NULL,
  `sutun_aciklama` text DEFAULT NULL,
  `tablo_sira` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_urun_tablodetay` FOREIGN KEY (`urun_ID`) REFERENCES `t_urun` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_urun_tablo_detay`
--

INSERT INTO `t_urun_tablo_detay` (`id`, `urun_ID`, `sutun_baslik`, `sutun_aciklama`, `tablo_sira`) VALUES
(1, 1, 'İşlemci Türü', 'Intel Core i7 13. Nesil', 1),
(2, 1, 'RAM', '16 GB DDR5', 2),
(3, 1, 'Ekran Kartı', 'NVIDIA GeForce RTX 4060', 3),
(4, 2, 'Kumaş Türü', '%100 Pamuk', 1),
(5, 2, 'Beden', 'L', 2),
(6, 3, 'Sayfa Sayısı', '712', 1),
(7, 3, 'Yazar', 'Frank Herbert', 2),
(8, 4, 'İşletim Sistemi', 'iOS 17', 1),
(9, 4, 'İşlemci', 'A17 Pro Çip (6 Çekirdekli)', 2),
(10, 4, 'Kamera', '48 MP Ana Kamera + 12 MP Ultra Geniş', 3),
(11, 4, 'Pil Kapasitesi', '4422 mAh (Hızlı Şarj Destekli)', 4),
(12, 4, 'Ekran Boyutu', '6.7 inç Super Retina XDR OLED', 5),
(13, 5, 'Bağlantı Tipi', 'Bluetooth 5.2 (Kablosuz)', 1),
(14, 5, 'Pil Ömrü', '30 Saat (Aktif Gürültü Engelleme Açık)', 2),
(15, 5, 'Şarj Süresi', 'Hızlı Özellik: 3 dk şarj ile 3 saatlik çalma', 3),
(16, 5, 'Özellik', 'Çift cihaz desteği ve Yapay Zeka Ses İyileştirme', 4),
(17, 6, 'İşlemci', 'Apple M3 Çip (8 Çekirdekli CPU)', 1),
(18, 6, 'Depolama', '512 GB NVMe SSD', 2),
(19, 6, 'Bellek (RAM)', '16 GB Birleşik Bellek', 3),
(20, 6, 'Ekran', '13.6 inç Liquid Retina (500 nit parlaklık)', 4),
(21, 6, 'Ağırlık', '1.24 kg (Kompakt ve Hafif Tasarım)', 5);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_sepet`
--

CREATE TABLE `t_sepet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uye_ID` int(8) DEFAULT NULL,
  `urun_ID` int(9) DEFAULT NULL,
  `adet` int(11) DEFAULT NULL,
  `birim_fiyat` int(11) DEFAULT NULL,
  `guncelleme_tarihi` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_sepet_urun` FOREIGN KEY (`urun_ID`) REFERENCES `t_urun` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sepet_uye` FOREIGN KEY (`uye_ID`) REFERENCES `t_uye` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_sepet`
--

INSERT INTO `t_sepet` (`id`, `uye_ID`, `urun_ID`, `adet`, `birim_fiyat`, `guncelleme_tarihi`) VALUES
(1, 1, 1, 1, 35000, '2026-03-21 11:00:00'),
(2, 1, 3, 1, 120, '2026-03-21 11:00:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_siparis`
--

CREATE TABLE `t_siparis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uye_ID` int(8) DEFAULT NULL,
  `adres_ID` int(11) DEFAULT NULL,
  `kargo_durum` varchar(60) DEFAULT NULL,
  `siparis_tarih` datetime DEFAULT NULL,
  `tahmini_tarih` datetime DEFAULT NULL,
  `teslim_tarihi` datetime DEFAULT NULL,
  `toplam_tutar` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_siparis_adres` FOREIGN KEY (`adres_ID`) REFERENCES `t_adres` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_siparis_uye` FOREIGN KEY (`uye_ID`) REFERENCES `t_uye` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_siparis`
--

INSERT INTO `t_siparis` (`id`, `uye_ID`, `adres_ID`, `kargo_durum`, `siparis_tarih`, `tahmini_tarih`, `teslim_tarihi`, `toplam_tutar`) VALUES
(1, 1, 1, 'Teslim Edildi', '2026-03-16 14:00:00', '2026-03-18 10:00:00', '2026-03-18 11:20:00', 500),
(101, 1, 1, 'Teslim Edildi', '2026-03-20 10:00:00', '2026-03-22 10:00:00', '2026-03-22 14:00:00', 88900),
(102, 2, NULL, 'Teslim Edildi', '2026-03-21 11:30:00', '2026-03-23 10:00:00', '2026-03-23 09:15:00', 54200);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_siparis_detay`
--

CREATE TABLE `t_siparis_detay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siparis_ID` int(11) DEFAULT NULL,
  `urun_ID` int(9) DEFAULT NULL,
  `adet` int(11) DEFAULT NULL,
  `birim_fiyat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_siparisdetay_siparis` FOREIGN KEY (`siparis_ID`) REFERENCES `t_siparis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_siparisdetay_urun` FOREIGN KEY (`urun_ID`) REFERENCES `t_urun` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_siparis_detay`
--

INSERT INTO `t_siparis_detay` (`id`, `siparis_ID`, `urun_ID`, `adet`, `birim_fiyat`) VALUES
(1, 1, 2, 2, 250),
(201, 101, 4, 1, 74900),
(202, 101, 5, 1, 14000),
(203, 102, 6, 1, 54200);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_yorum`
--

CREATE TABLE `t_yorum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uye_ID` int(8) DEFAULT NULL,
  `urun_ID` int(9) DEFAULT NULL,
  `siparis_detay_ID` int(11) DEFAULT NULL,
  `puan` decimal(3,1) DEFAULT NULL,
  `yorum` text DEFAULT NULL,
  `tarih` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_yorum_siparisdetay` FOREIGN KEY (`siparis_detay_ID`) REFERENCES `t_siparis_detay` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_yorum_urun` FOREIGN KEY (`urun_ID`) REFERENCES `t_urun` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_yorum_uye` FOREIGN KEY (`uye_ID`) REFERENCES `t_uye` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_yorum`
--

INSERT INTO `t_yorum` (`id`, `uye_ID`, `urun_ID`, `siparis_detay_ID`, `puan`, `yorum`, `tarih`) VALUES
(1, 1, 2, 1, 4.5, 'Kumaşı çok kaliteli tam yazlık bir ürün, ancak kalıbı biraz dar. Bir beden büyük almanızı tavsiye ederim.', '2026-03-19 12:45:00'),
(2, 1, 4, 201, 5.0, 'Ürün tek kelimeyle muazzam bir teknoloji harikası. A17 Pro işlemcisi menülerde yağ gibi akıyor. Naturel titanyum rengi çok klas duruyor ama kılıfsız kullanmaya kıyamazsınız. Kargolama süreci de harikaydı, hiç zorluk yaşadım.', '2026-03-23 15:00:00'),
(3, 1, 5, 202, 4.5, 'Ses kalitesi ve gürültü engelleme (ANC) özelliği gerçekten piyasadaki en iyisi diyebilirim. Uçakta motor sesini resmen sıfıra indirdi. Tek puan kırdığım nokta orijinal kılıfının bir önceki model olan XM4 gibi içeri doğru katlanabilir olmaması, çantada biraz kalın duruyor.', '2026-03-23 15:05:00'),
(4, 2, 6, 203, 5.0, 'Eski nesil M1 işlemciden M3 e geçiş yaptım. Gündelik işlerde bile programların açılış hızı inanılmaz. Isınma diye bir kavram kalmamış cihaz adeta buz gibi çalışıyor ve şarjı 2 gün rahat gidiyor. Hızlı teslimat için Kral Yolu ekibine teşekkürler.', '2026-03-24 10:30:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_personel`
--

CREATE TABLE `t_personel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad` varchar(255) DEFAULT NULL,
  `soyad` varchar(255) DEFAULT NULL,
  `e_posta` varchar(255) DEFAULT NULL,
  `parola` varchar(255) DEFAULT NULL,
  `telefon` varchar(16) DEFAULT NULL,
  `kayit_tarihi` datetime DEFAULT NULL,
  `personel_ID` varchar(255) DEFAULT NULL,
  `rol` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `t_personel`
--

INSERT INTO `t_personel` (`id`, `ad`, `soyad`, `e_posta`, `parola`, `telefon`, `kayit_tarihi`, `personel_ID`, `rol`) VALUES
(1, 'Admin', 'Kullanıcı', 'admin@kralyolu.com', '123456', '02120000000', '2026-01-01 00:00:00', 'PR-001', 'Yönetici');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_kredi_karti`
--

CREATE TABLE `t_kredi_karti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uye_ID` int(8) DEFAULT NULL,
  `kart_baslik` varchar(100) DEFAULT NULL,
  `kart_isim` varchar(100) DEFAULT NULL,
  `kart_numarasi` varchar(30) DEFAULT NULL,
  `son_kullanma` varchar(10) DEFAULT NULL,
  `cvv` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_kkarti_uye` FOREIGN KEY (`uye_ID`) REFERENCES `t_uye` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_urun_secenek`
--

CREATE TABLE `t_urun_secenek` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urun_ID` int(9) DEFAULT NULL,
  `secenek_baslik` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ursec_urun` FOREIGN KEY (`urun_ID`) REFERENCES `t_urun` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `t_urun_secenek_deger`
--

CREATE TABLE `t_urun_secenek_deger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `secenek_ID` int(11) DEFAULT NULL,
  `secenek_deger` varchar(255) DEFAULT NULL,
  `ek_fiyat` int(11) DEFAULT 0,
  `stok_farki` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_deger_sec` FOREIGN KEY (`secenek_ID`) REFERENCES `t_urun_secenek` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
