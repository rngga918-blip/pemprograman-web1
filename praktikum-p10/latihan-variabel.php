<?php
$nama = "Rangga Maulana";
$nim = "2025010097";
$programStudi = "Rekayasa Perangkat Lunak";
$semester = 3;
$ipk = 3.75;

// Menampilkan seluruh variabel
echo "<h2>Data Mahasiswa</h2>";

echo "Nama: " . $nama . "<br>";
echo "NIM: " . $nim . "<br>";
echo "Program Studi: " . $programStudi . "<br>";
echo "Semester: " . $semester . "<br>";
echo "IPK: " . $ipk . "<br>";

// Memeriksa tipe data
echo "<h3>Tipe Data Variabel</h3>";

var_dump($nama);
echo "<br>";

var_dump($nim);
echo "<br>";

var_dump($programStudi);
echo "<br>";

var_dump($semester);
echo "<br>";

var_dump($ipk);
?>
