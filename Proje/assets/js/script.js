/**
 * Giriş ve Kayıt formları arasında geçiş yapar
 * @param {string} viewName - 'login' veya 'register'
 */
function toggleAuthView(viewName) {
    const login = document.getElementById('login-view');
    const register = document.getElementById('register-view');
    if (viewName === 'register') {
        login.style.display = 'none';
        register.style.display = 'block';
    } else {
        register.style.display = 'none';
        login.style.display = 'block';
    }
}

/**
 * Ürünü favorilere ekler veya favorilerden çıkarır
 * @param {number} id - Ürün ID'si
 * @param {HTMLElement} button - Tıklanan buton elementi
 */
function toggleFavorite(id, button) {
    // Buton animasyonu
    if (button) {
        button.style.transform = 'scale(1.3)';
        setTimeout(function() { button.style.transform = ''; }, 200);
    }
    
    fetch('data.php?islem=favori_islem&id=' + id, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            if (button) {
                if (data.durum === 'eklendi') {
                    button.classList.add('active');
                    button.innerHTML = '<i class="bi bi-heart-fill"></i>';
                } else {
                    button.classList.remove('active');
                    button.innerHTML = '<i class="bi bi-heart"></i>';
                }
            }
        } else if (data.status === 'error' && data.message === 'auth') {
            window.location.href = 'index.php?sayfa=giris';
        } else if (data.status === 'error') {
            // Toast varsa kullan
            if (typeof showToast === 'function') {
                showToast(data.message || 'Favori işlemi başarısız.', 'error');
            }
        }
    })
    .catch(function(err) {
        console.error('Favori hatası:', err);
        if (typeof showToast === 'function') {
            showToast('Bağlantı hatası!', 'error');
        }
    });
}
