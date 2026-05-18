<header class="ky-header-wrapper">
    <!-- Main white back container ensuring top row and logo merge -->
    <div class="ky-top-bg">
        <div class="ky-container flex-header">
            <!-- LOGO SECTION -->
            <a href="index.php?sayfa=anasayfa" class="ky-logo-card">
                <div class="ky-logo-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="#d4af37" stroke="#d4af37" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 22l4-10 4 4 2-8 2 8 4-4 4 10z"/>
                    </svg>
                </div>
                <div class="ky-logo-text">
                    <span class="ky-title">KRAL YOLU</span>
                    <span class="ky-subtitle">E-TİCARET</span>
                </div>
            </a>
            
            <!-- TOP RIGHT SECTION -->
            <div class="ky-top-right">
                <!-- Search -->
                <form action="index.php" method="GET" class="ky-search-box">
                    <input type="hidden" name="sayfa" value="urunler">
                    <input type="text" name="ara" placeholder="Ürün, kategori veya marka ara..." value="<?= isset($_GET['ara']) ? htmlspecialchars($_GET['ara']) : '' ?>">
                    <button type="submit">Ara</button>
                </form>

                <!-- Action Links -->
                <div class="ky-action-links">
                    <?php if (!$isLoggedIn): ?>
                        <a href="index.php?sayfa=giris" class="ky-action-item">
                            <i class="bi bi-person-fill ky-gold"></i> <span class="d-none d-md-inline">Giriş Yap</span>
                        </a>
                    <?php else: ?>
                        <div class="user-menu-container">
                            <div class="ky-action-item dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                                <i class="bi bi-person-check-fill ky-gold"></i>
                                <span class="d-none d-md-inline">Profil</span>
                            </div>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?sayfa=profil">Profilimi Görüntüle</a></li>
                                <li><a class="dropdown-item" href="data.php?logout=1">Çıkış Yap</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <a href="index.php?sayfa=favoriler" class="ky-action-item">
                        <i class="bi bi-heart-fill ky-gold"></i> <span class="d-none d-md-inline">Favoriler</span>
                    </a>
                    
                    <a href="index.php?sayfa=sepet" class="ky-action-item">
                        <i class="bi bi-cart-fill ky-gold"></i> 
                        <span class="d-none d-md-inline">Sepetim</span>
                        <?php 
                        if ($isLoggedIn) {
                            $cart_count_res = $db->query("SELECT SUM(adet) as total FROM t_sepet WHERE uye_ID = " . $user['id']);
                            $cart_count = $cart_count_res->fetch_assoc()['total'] ?? 0;
                            if ($cart_count > 0) {
                                echo '<span class="badge rounded-pill bg-danger" style="font-size: 0.6rem; position: absolute; top: -5px; right: -5px;">' . $cart_count . '</span>';
                            }
                        }
                        ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM NAVBAR SECTION -->
    <div class="ky-bottom-nav">
        <div class="ky-container flex-header-bottom">
            
            <!-- Mobile Menu Toggle Button -->
            <button class="btn d-lg-none d-flex align-items-center text-white border-0 py-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                <i class="bi bi-list fs-3 ky-gold"></i> <span class="ms-2 fw-bold text-white">Kategoriler</span>
            </button>

            <!-- Desktop Navigation Links -->
            <nav class="ky-nav-links d-none d-lg-flex">
                <a href="index.php?sayfa=urunler" class="ky-nav-item">Kategoriler <i class="bi bi-chevron-down ms-1" style="font-size:0.8rem"></i></a>
                <div class="ky-nav-sep"></div>
                
                <a href="index.php?sayfa=urunler&kategori=1" class="ky-nav-item"><i class="bi bi-cpu ky-gold-icon"></i> Elektronik</a>
                <div class="ky-nav-sep"></div>
                
                <a href="index.php?sayfa=urunler&kategori=7" class="ky-nav-item"><i class="bi bi-person-running ky-gold-icon"></i> Spor</a>
                <div class="ky-nav-sep"></div>
                
                <a href="index.php?sayfa=urunler&kategori=2" class="ky-nav-item"><i class="bi bi-file-person ky-gold-icon"></i> Giyim</a>
                <div class="ky-nav-sep"></div>
                
                <a href="index.php?sayfa=urunler&kategori=3" class="ky-nav-item"><i class="bi bi-book ky-gold-icon"></i> Kitap</a>
                <div class="ky-nav-sep"></div>

                <a href="index.php?sayfa=urunler&kategori=6" class="ky-nav-item"><i class="bi bi-footprint ky-gold-icon"></i> Ayakkabı</a>
                <div class="ky-nav-sep"></div>
                
                <a href="index.php?sayfa=urunler&kategori=5" class="ky-nav-item"><i class="bi bi-magic ky-gold-icon"></i> Kozmetik</a>
                <div class="ky-nav-sep"></div>

                <a href="index.php?sayfa=urunler" class="ky-nav-item"><i class="bi bi-controller ky-gold-icon"></i> Tüm Ürünler</a>
            </nav>
        </div>      
    </div>

    <!-- Mobile Offcanvas Drawer -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel" style="background-color: #152238;">
        <div class="offcanvas-header border-bottom" style="border-color: rgba(204,166,89,0.3) !important;">
            <h5 class="offcanvas-title ky-gold fw-bold" id="mobileMenuLabel">
                 <i class="bi bi-grid-3x3-gap-fill me-2"></i> Menü
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush ky-mobile-nav">
                <a href="index.php?sayfa=urunler" class="list-group-item list-group-item-action bg-transparent text-white" style="border-color: rgba(255,255,255,0.05); padding: 15px 20px;"><i class="bi bi-grid ms-1 me-3 ky-gold"></i> Tüm Kategoriler</a>
                <a href="index.php?sayfa=urunler&kategori=Elektronik" class="list-group-item list-group-item-action bg-transparent text-white" style="border-color: rgba(255,255,255,0.05); padding: 15px 20px;"><i class="bi bi-cpu ms-1 me-3 ky-gold-icon"></i> Elektronik</a>
                <a href="index.php?sayfa=urunler&kategori=Spor" class="list-group-item list-group-item-action bg-transparent text-white" style="border-color: rgba(255,255,255,0.05); padding: 15px 20px;"><i class="bi bi-person-running ms-1 me-3 ky-gold-icon"></i> Spor</a>
                <a href="index.php?sayfa=urunler&kategori=Giyim" class="list-group-item list-group-item-action bg-transparent text-white" style="border-color: rgba(255,255,255,0.05); padding: 15px 20px;"><i class="bi bi-file-person ms-1 me-3 ky-gold-icon"></i> Giyim</a>
                <a href="index.php?sayfa=urunler&kategori=Kitap" class="list-group-item list-group-item-action bg-transparent text-white" style="border-color: rgba(255,255,255,0.05); padding: 15px 20px;"><i class="bi bi-book ms-1 me-3 ky-gold-icon"></i> Kitap</a>
                <a href="index.php?sayfa=urunler&kategori=Ev" class="list-group-item list-group-item-action bg-transparent text-white" style="border-color: rgba(255,255,255,0.05); padding: 15px 20px;"><i class="bi bi-house-door ms-1 me-3 ky-gold-icon"></i> Ev & Yaşam</a>
                <a href="index.php?sayfa=urunler&kategori=Kozmetik" class="list-group-item list-group-item-action bg-transparent text-white" style="border-color: rgba(255,255,255,0.05); padding: 15px 20px;"><i class="bi bi-magic ms-1 me-3 ky-gold-icon"></i> Kozmetik</a>
                <a href="index.php?sayfa=urunler&kategori=Oyuncak" class="list-group-item list-group-item-action bg-transparent text-white" style="border-color: rgba(255,255,255,0.05); padding: 15px 20px;"><i class="bi bi-controller ms-1 me-3 ky-gold-icon"></i> Oyuncak & Hobi</a>
            </div>
        </div>
    </div>
</header>

<style>
    .ky-search-box { position: relative; overflow: visible !important; }
    .search-results-dropdown { position: absolute; top: 100%; left: 0; width: 600px; background: #fff; border-radius: 12px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); z-index: 100000; display: none; overflow: hidden; border: 1px solid #e2e8f0; margin-top: 8px; padding: 15px 0; }
    @media (max-width: 768px) { .search-results-dropdown { width: 100%; left: 0; right: 0; } }
    
    .search-res-section { padding: 0 20px; margin-bottom: 20px; }
    .search-res-section:last-child { margin-bottom: 0; }
    .search-res-title { font-size: 0.85rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .search-res-title i { color: var(--gold); font-size: 1rem; }
    
    /* Trendyol Style Suggestions */
    .search-suggestion-item { display: block; padding: 10px 20px; color: #475569; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; border-left: 3px solid transparent; }
    .search-suggestion-item:hover { background: #f8fafc; color: var(--navy); border-left-color: var(--gold); }
    .search-suggestion-item i { margin-right: 10px; color: #94a3b8; }
    .search-suggestion-item.active, .search-suggestion-item:hover { background: #f8fafc; color: var(--gold); }
    .search-suggestion-item strong { color: var(--gold); font-weight: 800; }

    /* Pill Styles */
    .pill-container { display: flex; flex-wrap: wrap; gap: 8px; }
    .search-pill { display: inline-block; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 20px; font-size: 0.85rem; font-weight: 600; color: var(--navy); text-decoration: none; transition: all 0.2s; }
    .search-pill:hover { background: var(--gold); color: var(--navy); border-color: var(--gold); transform: translateY(-2px); }

    /* Product Item Styles */
    .search-product-list { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .search-prod-item { display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 10px; border: 1px solid transparent; text-decoration: none; transition: all 0.2s; }
    .search-prod-item.active, .search-prod-item:hover { background: #fffaf0; border-color: var(--gold); }
    .search-prod-img { width: 40px; height: 40px; object-fit: cover; border-radius: 6px; }
    .search-prod-info { flex: 1; min-width: 0; }
    .search-prod-name { display: block; font-size: 0.8rem; font-weight: 700; color: var(--navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
    .search-prod-price { font-size: 0.8rem; font-weight: 800; color: var(--gold); }
    
    /* Highlight class for matching text */
    .highlight { color: var(--gold); font-weight: 800; }
    
    /* Search Divider */
    .search-divider { height: 1px; background: #f1f5f9; margin: 10px 20px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.ky-search-box input[name="ara"]');
    const searchBox = document.querySelector('.ky-search-box');
    if (!searchInput || !searchBox) return;

    const dropdown = document.createElement('div');
    dropdown.className = 'search-results-dropdown';
    searchBox.appendChild(dropdown);

    let selectedIndex = -1;

    function highlightText(text, query) {
        if (!query || query.length < 1) return text;
        // Özel karakterleri kaçır (Regex hatası almamak için)
        const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapedQuery})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    function triggerSearch(val) {
        // Harf sayısından bağımsız olarak paneli güncellemek için fetch'i her zaman çalıştırıyoruz.
        // PHP tarafı zaten boş veya kısa kelimelere göre popüler sonuçları döndürüyor.

        fetch('data.php?islem=arama_oneri&q=' + encodeURIComponent(val), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            console.log('Arama Verisi:', data); // Hata ayıklama için eklendi
            
            let html = '';
            selectedIndex = -1;
            
            // 1. Popular Searches (Pills) - Boşken veya çok kısa aramada göster
            if (val.length < 1 && data.populer_aramalar) {
                html += `<div class="search-res-section">
                            <div class="search-res-title"><i class="bi bi-fire"></i> Popüler Aramalar</div>
                            <div class="pill-container">`;
                data.populer_aramalar.forEach(p => {
                    html += `<a href="index.php?sayfa=urunler&ara=${encodeURIComponent(p)}" class="search-pill">${p}</a>`;
                });
                html += `   </div>
                         </div>`;
                
                if (data.kategoriler && data.kategoriler.length > 0) {
                    html += `<div class="search-divider"></div>`;
                }
            }

            // 2. Suggestions (Arama Önerileri)
            if (val.length >= 1 && data.oneriler && data.oneriler.length > 0) {
                data.oneriler.forEach(oneri => {
                    html += `<a href="${oneri.url}" class="search-suggestion-item selectable"><i class="bi bi-search"></i>${oneri.text}</a>`;
                });
            }

            // 3. Kategoriler (Pills)
            if (data.kategoriler && data.kategoriler.length > 0) {
                html += `<div class="search-res-section ${val.length > 0 ? 'mt-3' : ''}">
                            <div class="search-res-title"><i class="bi bi-grid"></i> ${val.length >= 1 ? 'İlgili Kategoriler' : 'Popüler Kategoriler'}</div>
                            <div class="pill-container">`;
                data.kategoriler.forEach(k => {
                    const kName = highlightText(k.kategori_adi, val);
                    html += `<a href="index.php?sayfa=urunler&kategori[]=${k.id}" class="search-pill">${kName}</a>`;
                });
                html += `   </div>
                         </div>`;
            }

            // 4. Ürünler
            if (val.length >= 1 && data.urunler && data.urunler.length > 0) {
                if (data.kategoriler && data.kategoriler.length > 0) {
                    html += `<div class="search-divider"></div>`;
                }

                html += `<div class="search-res-section mt-3">
                            <div class="search-res-title"><i class="bi bi-box-seam"></i> Ürünler</div>
                            <div class="search-product-list">`;
                data.urunler.forEach(u => {
                    const img = u.foto_url || 'https://via.placeholder.com/100';
                    const name = highlightText(u.urun_adi, val);
                    html += `
                    <a href="index.php?sayfa=urundetay&id=${u.id}" class="search-prod-item selectable">
                        <img src="${img}" class="search-prod-img" alt="Ürün">
                        <div class="search-prod-info">
                            <span class="search-prod-name">${name}</span>
                            <span class="search-prod-price">${new Intl.NumberFormat('tr-TR').format(u.fiyat)} TL</span>
                        </div>
                    </a>`;
                });
                html += `   </div>
                         </div>`;
            }

            if (html === '' && val.length >= 1) {
                html = '<div class="p-4 text-center text-muted small">Sonuç bulunamadı. <br> <span style="font-size:0.75rem">Farklı anahtar kelimeler deneyebilirsiniz.</span></div>';
            }

            if (html !== '') {
                dropdown.innerHTML = html;
                dropdown.style.display = 'block';
            } else {
                dropdown.innerHTML = ''; // İçeriği temizle
                dropdown.style.display = 'none';
            }
        })
        .catch(err => {
            console.error('Arama hatası:', err);
            dropdown.style.display = 'none';
        });
    }

    searchInput.addEventListener('input', function() {
        triggerSearch(this.value.trim());
    });

    searchInput.addEventListener('focus', function() {
        triggerSearch(this.value.trim());
    });

    // Keyboard Navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.selectable');
        if (items.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            updateSelection(items);
        } else if (e.key === 'Enter') {
            if (selectedIndex > -1) {
                e.preventDefault();
                items[selectedIndex].click();
            }
        }
    });

    function updateSelection(items) {
        items.forEach((item, idx) => {
            if (idx === selectedIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (!searchBox.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
});
</script>