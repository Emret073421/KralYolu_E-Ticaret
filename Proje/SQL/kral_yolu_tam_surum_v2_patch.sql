-- =========================================
-- KRAL YOLU - VERİTABANI YAMA (PATCH)
-- Eksik tabloları ve sütunları ekler.
-- =========================================

USE `db_ticaret`;

-- 1. Eksik t_favori tablosu
CREATE TABLE IF NOT EXISTS `t_favori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uye_id` int(8) DEFAULT NULL,
  `urun_id` int(9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favori` (`uye_id`, `urun_id`),
  CONSTRAINT `fk_favori_uye` FOREIGN KEY (`uye_id`) REFERENCES `t_uye` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_favori_urun` FOREIGN KEY (`urun_id`) REFERENCES `t_urun` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- 2. Eksik secenek_detay sütunu t_sepet tablosuna
ALTER TABLE `t_sepet` ADD COLUMN IF NOT EXISTS `secenek_detay` varchar(500) DEFAULT '' AFTER `adet`;
