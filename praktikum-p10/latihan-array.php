<?php
$mahasiswa = [
    [
        "nim" => "2025010097",
        "nama" => "Rangga Maulana",
        "nilai" => 88
    ],
    [
        "nim" => "2025010098",
        "nama" => "Alya",
        "nilai" => 85
    ],
    [
        "nim" => "2025010099",
        "nama" => "Bima",
        "nilai" => 78
    ]
];

// Menambahkan mahasiswa baru
$mahasiswa[] = [
    "nim" => "2025010100",
    "nama" => "Citra",
    "nilai" => 92
];

echo "<h2>Data Mahasiswa</h2>";

foreach ($mahasiswa as $data) {
    echo "NIM: " . $data["nim"] . "<br>";
    echo "Nama: " . $data["nama"] . "<br>";
    echo "Nilai: " . $data["nilai"] . "<br>";
    echo "<hr>";
}
?>
