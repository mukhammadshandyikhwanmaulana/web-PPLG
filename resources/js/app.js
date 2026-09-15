import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Jalankan Alpine setelah DOM selesai disusun oleh browser
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        Alpine.start();
    });
} else {
    Alpine.start();
}