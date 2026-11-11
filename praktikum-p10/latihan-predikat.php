<?php
$daftarNilai = [49, 50, 64, 65, 74, 75, 84, 85];

echo "<h2>Hasil Predikat Nilai Mahasiswa</h2>";

foreach ($daftarNilai as $nilai) {

    if ($nilai >= 85) {
        $predikat = "A";
    } elseif ($nilai >= 75) {
        $predikat = "B";
    } elseif ($nilai >= 65) {
        $predikat = "C";
    } elseif ($nilai >= 50) {
        $predikat = "D";
    } else {
        $predikat = "E";
    }

    if ($nilai >= 70) {
        $status = "Lulus";
    } else {
        $status = "Belum Lulus";
    }

    echo "Nilai: $nilai";
    echo " | Predikat: $predikat";
    echo " | Status: $status";
    echo "<br>";
}
?>
