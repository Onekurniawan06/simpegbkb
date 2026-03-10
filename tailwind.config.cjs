// tailwind.config.cjs
module.exports = {
    content: [
        // Tambahkan jalur berikut agar Tailwind memindai semua file Blade PHP Anda
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            // Hapus baris 'bkb-blue': '#2146C7' dari sini jika masih ada
        },
    },
    plugins: [],
};
