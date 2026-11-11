<?php
$tugas = 80;
$uts = 75;
$uas = 90;

// Menghitung nilai akhir
$nilaiAkhir = ($tugas * 0.30)
            + ($uts * 0.30)
            + ($uas * 0.40);

// Menampilkan hasil
echo "<h2>Perhitungan Nilai Akhir</h2>";

echo "Nilai Tugas: $tugas (30%)<br>";
echo "Nilai UTS: $uts (30%)<br>";
echo "Nilai UAS: $uas (40%)<br>";

echo "<hr>";

echo "Perhitungan:<br>";
echo "($tugas x 0.30) + ";
echo "($uts x 0.30) + ";
echo "($uas x 0.40)<br>";

echo "Nilai Akhir: " . $nilaiAkhir;
?>
