<?php

return [
    App\Providers\AppServiceProvider::class,
    // Tambahkan dua baris ini untuk mendaftarkan paket ikon secara eksplisit:
    BladeUI\Icons\BladeIconsServiceProvider::class,
    BladeUI\Heroicons\BladeHeroiconsServiceProvider::class,
];
