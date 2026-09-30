<!-- Overlay Loading Global -->
<div id="globalLoadingOverlay" class="loading-overlay active">
    <div class="loading-card">
        <div class="spinner-ring"></div>
        <div class="loading-text-container">
            <h5 class="loading-title">Memproses Data</h5>
            <p class="loading-subtitle">Mohon tunggu sebentar, sistem sedang memproses...</p>
        </div>
    </div>
</div>

<style>
/* Style Glassmorphism Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 99999;
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.loading-overlay.active {
    opacity: 1;
    visibility: visible;
}

.loading-card {
    background: rgba(30, 41, 59, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 2rem 2.5rem;
    border-radius: 1.25rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    max-width: 320px;
    text-align: center;
}

.spinner-ring {
    width: 48px;
    height: 48px;
    border: 3px solid rgba(99, 102, 241, 0.2);
    border-radius: 50%;
    border-top-color: #6366f1;
    border-right-color: #818cf8;
    animation: spin 0.8s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-title {
    color: #f8fafc;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.loading-subtitle {
    color: #94a3b8;
    font-size: 0.85rem;
    margin: 0;
    line-height: 1.4;
}
</style>

<script>
    // Fungsi Global untuk Mengontrol Loading
    window.showLoading = function() {
        const overlay = document.getElementById('globalLoadingOverlay');
        if (overlay) overlay.classList.add('active');
    };

    window.hideLoading = function() {
        const overlay = document.getElementById('globalLoadingOverlay');
        if (overlay) overlay.classList.remove('active');
    };

    document.addEventListener('DOMContentLoaded', function () {
        // Sembunyikan loading otomatis begitu halaman selesai di-render sepenuhnya
        hideLoading();

        // Hanya tampilkan loading jika form dikirim oleh user
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                if (form.checkValidity()) {
                    showLoading();
                }
            });
        });
    });

    // Otomatis tutup jika user menekan tombol 'Back' browser
    window.addEventListener('pageshow', function () {
        hideLoading();
    });
</script>   