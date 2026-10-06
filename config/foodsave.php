<?php

return [
    /*
     | Persentase service fee yang dibebankan ke MITRA (bukan konsumen).
     | PRD Open Question #1: angka final belum diputuskan, jadi default 0.
     | Isi lewat .env setelah keputusan bisnis final. Fase admin nanti
     | memindahkannya ke tabel settings agar bisa diubah tanpa deploy.
     */
    'service_fee_percent' => (float) env('FOODSAVE_SERVICE_FEE_PERCENT', 0),

    /*
     | Produk disebut "Flash Deal" bila available dan waktu pengambilannya
     | berakhir dalam N jam ke depan (design.md §11.6: harus berbasis data).
     */
    'flash_deal_window_hours' => (int) env('FOODSAVE_FLASH_WINDOW_HOURS', 3),

    /*
     | Wilayah layanan untuk filter lokasi. Ubah sesuai area soft launch.
     | (Tidak memakai GPS — itu non-goal MVP.)
     */
    'areas' => [
        'Mendalo',
        'Telanaipura',
        'Kota Baru',
        'Lainnya',
    ],
];
