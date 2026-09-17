import Alpine from 'alpinejs';

window.Alpine = Alpine;

// 1. Register Komponen Alpine Sebelum Start
Alpine.data('globalImageCropper', () => ({
    open: false,
    title: 'Potong Gambar',
    cropper: null,
    imageSrc: '',
    targetInput: null,
    onCropComplete: null,
    aspectRatio: null,

    initCrop(detail) {
        if (!detail || !detail.file) return;

        this.title = detail.title || 'Potong Gambar';
        this.aspectRatio = detail.aspectRatio ?? null;
        this.targetInput = detail.targetInput || null;
        this.onCropComplete = detail.onCropComplete || null;

        if (this.imageSrc && this.imageSrc.startsWith('blob:')) {
            URL.revokeObjectURL(this.imageSrc);
        }

        this.imageSrc = URL.createObjectURL(detail.file);
        
        // Hancurkan cropper lama jika ada
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }

        // Buka modal terlebih dahulu agar elemen DOM dirender
        this.open = true;

        // Gunakan setTimeout kecil untuk memastikan elemen <img> sudah ada di DOM
        setTimeout(() => {
            this.$nextTick(() => {
                const imageElement = this.$refs.cropImage;
                if (imageElement && typeof Cropper !== 'undefined') {
                    this.cropper = new Cropper(imageElement, {
                        aspectRatio: this.aspectRatio,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                    });
                }
            });
        }, 50);
    },

    applyCrop() {
        if (!this.cropper) {
            alert('Gagal memproses gambar. Silakan pilih ulang foto.');
            this.closeModal();
            return;
        }

        this.cropper.getCroppedCanvas().toBlob((blob) => {
            if (!blob) return;

            const croppedFile = new File([blob], 'cropped-image.webp', {
                type: 'image/webp',
                lastModified: Date.now(),
            });

            if (this.targetInput) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                this.targetInput.files = dataTransfer.files;
                // Picu event change agar form mendeteksi pembaruan file
                this.targetInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (typeof this.onCropComplete === 'function') {
                this.onCropComplete(croppedFile);
            }

            this.closeModal();
        }, 'image/webp', 0.9);
    },

    closeModal() {
        this.open = false;
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
        if (this.imageSrc && this.imageSrc.startsWith('blob:')) {
            URL.revokeObjectURL(this.imageSrc);
            this.imageSrc = '';
        }
    }
}));

// 2. Eksekusi Alpine.start()
Alpine.start();