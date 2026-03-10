import './bootstrap';
import Alpine from 'alpinejs';


window.Alpine = Alpine;
Alpine.start();

/**
 * Fungsi Universal untuk menambah input field
 * @param {string} containerId - ID dari container (ex: 'children-container' atau 'wife-container')
 * @param {string} groupClass - Nama class pembungkus (ex: 'child-input-group' atau 'wife-input-group')
 * @param {string} labelText - Teks untuk placeholder (ex: 'Anak' atau 'Istri')
 */
window.addDynamicField = function(containerId, groupClass, labelText) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Kloning elemen pertama sebagai template
    const firstGroup = container.querySelector(`.${groupClass}`);
    const newGroup = firstGroup.cloneNode(true);

    // Bersihkan nilai input di klon baru
    const input = newGroup.querySelector('input');
    input.value = '';

    // Tampilkan tombol hapus (karena di elemen pertama biasanya tersembunyi)
    const deleteButton = newGroup.querySelector('button');
    if (deleteButton) {
        deleteButton.classList.remove('hidden');
    }

    // Tambahkan ke container
    container.appendChild(newGroup);

    // Perbarui semua placeholder agar urutannya benar (1, 2, 3...)
    updateDynamicPlaceholders(containerId, groupClass, labelText);
};

/**
 * Fungsi Universal untuk menghapus input field
 */
window.removeDynamicField = function(buttonElement, containerId, groupClass, labelText) {
    const group = buttonElement.closest(`.${groupClass}`);
    if (group) {
        // Cek jika ini bukan satu-satunya input yang tersisa
        const container = document.getElementById(containerId);
        const allGroups = container.querySelectorAll(`.${groupClass}`);

        if (allGroups.length > 1) {
            group.remove();
            // Perbarui urutan nomor setelah dihapus
            updateDynamicPlaceholders(containerId, groupClass, labelText);
        } else {
            // Jika tinggal satu, cukup kosongkan nilainya saja daripada menghapusnya
            group.querySelector('input').value = '';
        }
    }
};

/**
 * Fungsi bantuan untuk mengurutkan nomor di placeholder secara otomatis
 */
function updateDynamicPlaceholders(containerId, groupClass, labelText) {
    const container = document.getElementById(containerId);
    const inputs = container.querySelectorAll(`.${groupClass} input`);
    inputs.forEach((input, index) => {
        input.placeholder = `Nama ${labelText} ke-${index + 1}`;
    });
}


