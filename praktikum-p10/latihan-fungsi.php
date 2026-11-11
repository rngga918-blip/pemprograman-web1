<?php

// Fungsi menghitung luas persegi panjang
function hitungLuasPersegiPanjang($panjang, $lebar) {
    return $panjang * $lebar;
}

// Fungsi menentukan kelulusan
function tentukanKelulusan($nilai) {
    if ($nilai >= 70) {
        return "Lulus";
    } else {
        return "Belum Lulus";
    }
}

// Menguji fungsi luas persegi panjang
echo "<h2>Menghitung Luas Persegi Panjang</h2>";

echo "Panjang 10, Lebar 5 = ";
echo hitungLuasPersegiPanjang(10, 5);
echo "<br>";

echo "Panjang 8, Lebar 4 = ";
echo hitungLuasPersegiPanjang(8, 4);
echo "<br>";

echo "Panjang 12, Lebar 6 = ";
echo hitungLuasPersegiPanjang(12, 6);
echo "<br>";

// Menguji fungsi kelulusan
echo "<h2>Menentukan Kelulusan</h2>";

$nilaiMahasiswa = [85, 65, 75];

foreach ($nilaiMahasiswa as $nilai) {
    echo "Nilai: $nilai";
    echo " | Status: ";
    echo tentukanKelulusan($nilai);
    echo "<br>";
}

?>
