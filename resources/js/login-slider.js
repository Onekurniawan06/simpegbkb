// resources/js/login-slider.js

// --- Fungsi Umum: Toggle Password Visibility (Dapat digunakan di kedua halaman) ---
// Fungsi ini dibuat global (window.) agar bisa dipanggil dari atribut onclick="..." di HTML
window.togglePasswordVisibility = (inputId, openIconId, closedIconId) => {
    const input = document.getElementById(inputId);
    const iconOpen = document.getElementById(openIconId);
    const iconClosed = document.getElementById(closedIconId);

    if (input && iconOpen && iconClosed) {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);

        iconOpen.classList.toggle('hidden');
        iconClosed.classList.toggle('hidden');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // Dapatkan referensi elemen popup error yang mungkin ada di halaman login atau registrasi
    const errorPopup = document.getElementById('error-popup');
    const errorMessageElement = document.getElementById('error-message');

    // --- Fungsi Notifikasi Popup ---
    // Definisikan window.closeErrorPopup agar dapat diakses dari HTML (onclick)
    window.closeErrorPopup = () => {
        if (errorPopup) {
            errorPopup.classList.add('hidden');
        }
    };

    const showErrorMessage = (message) => {
        if (errorMessageElement && errorPopup) {
            errorMessageElement.textContent = message;
            errorPopup.classList.remove('hidden');
        }
    };

    if (errorPopup) {
        const closePopupButton = errorPopup.querySelector('button');
        if (closePopupButton) {
            closePopupButton.addEventListener('click', window.closeErrorPopup);
        }
    }


    // =============================================================
    // KODE SPESIFIK UNTUK SLIDER HANYA DI HALAMAN LOGIN
    // =============================================================
    const sliderContainer = document.getElementById('sliderContainer');

    // Cek apakah elemen slider ada di halaman (hanya ada di login.blade.php)
    if (sliderContainer) {
        // Elemen-elemen ini hanya ada di halaman login, jadi aman dideklarasikan di sini
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const loginButton = document.getElementById('loginButton');
        const isVerifiedInput = document.getElementById('isVerified');
        const sliderHandle = document.getElementById('sliderHandle');
        const sliderFill = document.getElementById('sliderFill');
        const sliderText = document.getElementById('sliderText');

        let isDragging = false;
        let isVerified = false;

        // Sesuaikan fungsi popup lokal untuk slider (menyembunyikan slider saat error)
        const showErrorMessageForLogin = (message) => {
            showErrorMessage(message); // Panggil fungsi umum
            sliderContainer.classList.add('hidden');
        };

        // Sesuaikan fungsi tutup popup lokal untuk slider (menampilkan slider kembali)
        window.closeErrorPopup = () => {
            if (errorPopup) errorPopup.classList.add('hidden');
            sliderContainer.classList.remove('hidden');
        };

        const resetSlider = () => {
            isVerified = false;
            if(isVerifiedInput) isVerifiedInput.value = '0';
            if(loginButton) loginButton.classList.add('hidden');
            sliderContainer.classList.remove('hidden');
            if(sliderText) sliderText.classList.remove('hidden');
            if(sliderHandle) sliderHandle.style.transform = `translateX(0px)`;
            if(sliderFill) sliderFill.style.width = `0%`;
            sliderContainer.style.cursor = 'pointer';
        };

        // --- Event Listeners Slider (Mouse Down, Move, Up) ---
        sliderHandle.addEventListener('mousedown', (e) => {
            if (isVerified) return;
            isDragging = true;
            sliderHandle.style.transition = 'none';
            sliderFill.style.transition = 'none';
            document.body.classList.add('select-none');
        });

        document.addEventListener('mousemove', (e) => {
            if (!isDragging || isVerified) return;
            const containerRect = sliderContainer.getBoundingClientRect();
            let newX = e.clientX - containerRect.left - (sliderHandle.offsetWidth / 2);
            if (newX < 0) newX = 0;
            const maxDrag = containerRect.width - sliderHandle.offsetWidth;
            if (newX > maxDrag) newX = maxDrag;

            sliderHandle.style.transform = `translateX(${newX}px)`;
            sliderFill.style.width = `${newX + sliderHandle.offsetWidth / 2}px`;
        });

        document.addEventListener('mouseup', () => {
            if (!isDragging) return;
            isDragging = false;
            document.body.classList.remove('select-none');

            const containerRect = sliderContainer.getBoundingClientRect();
            const maxDrag = containerRect.width - sliderHandle.offsetWidth;
            const currentX = parseFloat(sliderHandle.style.transform.replace('translateX(', '').replace('px)', '')) || 0;

            if (currentX >= maxDrag * 0.9) {
                if (usernameInput.value.trim() === '' || passwordInput.value.trim() === '') {
                    showErrorMessageForLogin("Username dan password harus diisi.");
                    resetSlider();
                    return;
                }
                isVerified = true;
                if(isVerifiedInput) isVerifiedInput.value = '1';
                sliderContainer.classList.add('hidden');
                if(loginButton) loginButton.classList.remove('hidden');
                if(sliderText) sliderText.classList.add('hidden');
            } else {
                sliderHandle.style.transition = 'transform 0.3s ease';
                sliderFill.style.transition = 'width 0.3s ease';
                sliderHandle.style.transform = `translateX(0px)`;
                sliderFill.style.width = `0%`;
            }
        });

        // --- Fitur Toggle Password SPESIFIK UNTUK HALAMAN LOGIN (hanya 1 input) ---
        // Ini menggunakan ID spesifik yang ada di login.blade.php
        const togglePasswordButton = document.getElementById('togglePassword');
        const iconOpen = document.getElementById('icon-open');
        const iconClosed = document.getElementById('icon-closed');

        if (togglePasswordButton && passwordInput && iconOpen && iconClosed) {
            togglePasswordButton.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                iconOpen.classList.toggle('hidden');
                iconClosed.classList.toggle('hidden');
            });
        }
        // --- Akhir Fitur Toggle Password Login ---


        resetSlider(); // Panggil reset awal saat DOM ready

        const laravelErrorContainer = document.getElementById('laravel-error-container');
        if (laravelErrorContainer && laravelErrorContainer.textContent.trim().length > 0) {
            showErrorMessageForLogin(laravelErrorContainer.textContent.trim());
        }
    }
    // =============================================================
    // AKHIR KODE SPESIFIK UNTUK SLIDER
    // =============================================================
});
