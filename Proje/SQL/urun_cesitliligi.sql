-- KRAL YOLU E-TİCARET VERİ GENİŞLETME SQL
-- Kategoriler, Ürünler, Fotoğraflar ve Seçenekler

-- 1. YENİ KATEGORİLER
INSERT INTO t_kategori (kategori_adi, aktif) VALUES 
('Elektronik', 'Evet'),
('Kozmetik', 'Evet'),
('Ayakkabı', 'Evet'),
('Spor & Outdoor', 'Evet');

-- 2. ALT KATEGORİLER (Elektronik için)
INSERT INTO t_alt_kategori_1 (kategori_ID, kategori_adi, aktif) VALUES 
((SELECT id FROM t_kategori WHERE kategori_adi='Elektronik'), 'Telefon', 'Evet'),
((SELECT id FROM t_kategori WHERE kategori_adi='Elektronik'), 'Bilgisayar', 'Evet'),
((SELECT id FROM t_kategori WHERE kategori_adi='Kozmetik'), 'Parfüm', 'Evet'),
((SELECT id FROM t_kategori WHERE kategori_adi='Ayakkabı'), 'Spor Ayakkabı', 'Evet');

-- 3. ÜRÜNLER

-- ELEKTRONİK ÜRÜNLERİ
INSERT INTO t_urun (satici_ID, urun_adi, urun_aciklama, fiyat, stok, aktif) VALUES 
(1, 'Akıllı Telefon X Pro', 'En yeni nesil işlemci, 128GB depolama ve muhteşem kamera kalitesi.', 45000, 50, 'Evet'),
(1, 'Dizüstü Bilgisayar Air 13', 'Hafif tasarım, uzun pil ömrü ve yüksek performanslı ekran.', 32000, 20, 'Evet');

-- KOZMETİK ÜRÜNLERİ
INSERT INTO t_urun (satici_ID, urun_adi, urun_aciklama, fiyat, stok, aktif) VALUES 
(1, 'Premium Erkek Parfümü 100ml', 'Odunsu ve ferah notalarıyla gün boyu kalıcı etki.', 1250, 100, 'Evet'),
(1, 'Nemlendirici Cilt Bakım Kremi', 'Doğal özlerle zenginleştirilmiş, tüm cilt tiplerine uygun.', 450, 200, 'Evet');

-- AYAKKABI ÜRÜNLERİ
INSERT INTO t_urun (satici_ID, urun_adi, urun_aciklama, fiyat, stok, aktif) VALUES 
(1, 'Ultra Rahat Koşu Ayakkabısı', 'Özel taban teknolojisi ile yorgunluk hissini azaltır.', 2400, 80, 'Evet'),
(1, 'Klasik Deri Ayakkabı', 'Hakiki deri, el işçiliği ile üretilmiş şık tasarım.', 3800, 30, 'Evet');

-- 4. ÜRÜN KATEGORİ EŞLEŞTİRMELERİ
INSERT INTO t_urun_kategori (urun_ID, kategori_ID) VALUES 
((SELECT id FROM t_urun WHERE urun_adi='Akıllı Telefon X Pro'), (SELECT id FROM t_kategori WHERE kategori_adi='Elektronik')),
((SELECT id FROM t_urun WHERE urun_adi='Dizüstü Bilgisayar Air 13'), (SELECT id FROM t_kategori WHERE kategori_adi='Elektronik')),
((SELECT id FROM t_urun WHERE urun_adi='Premium Erkek Parfümü 100ml'), (SELECT id FROM t_kategori WHERE kategori_adi='Kozmetik')),
((SELECT id FROM t_urun WHERE urun_adi='Ultra Rahat Koşu Ayakkabısı'), (SELECT id FROM t_kategori WHERE kategori_adi='Ayakkabı'));

-- 5. ÜRÜN FOTOĞRAFLARI
INSERT INTO t_foto (urun_ID, foto_url, foto_sira) VALUES 
((SELECT id FROM t_urun WHERE urun_adi='Akıllı Telefon X Pro'), 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=800', 1),
((SELECT id FROM t_urun WHERE urun_adi='Dizüstü Bilgisayar Air 13'), 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=800', 1),
((SELECT id FROM t_urun WHERE urun_adi='Premium Erkek Parfümü 100ml'), 'https://images.unsplash.com/photo-1541643600914-78b084683601?q=80&w=800', 1),
((SELECT id FROM t_urun WHERE urun_adi='Ultra Rahat Koşu Ayakkabısı'), 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=800', 1);

-- 6. ÜRÜN SEÇENEKLERİ (Örn: Ayakkabı Numarası)
INSERT INTO t_urun_secenek (urun_ID, secenek_baslik) VALUES 
((SELECT id FROM t_urun WHERE urun_adi='Ultra Rahat Koşu Ayakkabısı'), 'Numara'),
((SELECT id FROM t_urun WHERE urun_adi='Akıllı Telefon X Pro'), 'Renk');

-- 7. SEÇENEK DEĞERLERİ
INSERT INTO t_urun_secenek_deger (secenek_ID, secenek_deger, ek_fiyat) VALUES 
((SELECT id FROM t_urun_secenek WHERE secenek_baslik='Numara' LIMIT 1), '41', 0),
((SELECT id FROM t_urun_secenek WHERE secenek_baslik='Numara' LIMIT 1), '42', 0),
((SELECT id FROM t_urun_secenek WHERE secenek_baslik='Numara' LIMIT 1), '43', 0),
((SELECT id FROM t_urun_secenek WHERE secenek_baslik='Renk' LIMIT 1), 'Siyah', 0),
((SELECT id FROM t_urun_secenek WHERE secenek_baslik='Renk' LIMIT 1), 'Gümüş', 1000);
