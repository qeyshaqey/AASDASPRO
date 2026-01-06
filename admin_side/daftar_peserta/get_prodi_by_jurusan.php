<?php
header('Content-Type: application/json');

 $jurusan = $_GET['jurusan'] ?? '';

 $prodiData = [
    'Teknik Informatika' => [
        'Teknik Informatika',
        'Teknologi Geomatika',
        'Animasi',
        'Teknologi Rekayasa Multimedia',
        'Rekayasa Keamanan Siber',
        'Rekayasa Perangkat Lunak',
        'Teknik Komputer',
        'Teknologi Permainan'
    ],
    'Teknik Mesin' => [
        'Teknik Mesin',
        'Teknik Perawatan Pesawat Udara',
        'Teknologi Rekayasa Konstruksi Perkapalan',
        'Teknologi Rekayasa Pengelasan dan Fabrikasi',
        'Program Profesi Insinyur (PSPPI)',
        'Teknologi Rekayasa Metalurgi'
    ],
    'Teknik Elektro' => [
        'Teknik Elektronika Manufaktur',
        'Teknologi Rekayasa Elektronika',
        'Teknik Instrumentasi',
        'Teknik Mekatronika',
        'Teknologi Rekayasa Pembangkit Energi',
        'Teknologi Rekayasa Robotika'
    ],
    'Manajemen Bisnis' => [
        'Akuntansi',
        'Akuntansi Manajerial',
        'Administrasi Bisnis Terapan',
        'Logistik Perdagangan Internasional',
        'Distribusi Barang'
    ]
];

if (isset($prodiData[$jurusan])) {
    echo json_encode($prodiData[$jurusan]);
} else {
    echo json_encode([]);
}
?>