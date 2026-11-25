
<?php
session_start();

// Proses reset semua data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_data'])) {
    session_unset();
    session_destroy();

    header("Location: index.php");
    exit;
}

// Data awal mahasiswa
if (!isset($_SESSION['mahasiswa'])) {
    $_SESSION['mahasiswa'] = [
        [
            'nim' => '2025010097',
            'nama' => 'Rangga',
            'gmail' => 'rangga@gmail.com',
            'tugas' => 85,
            'uts' => 80,
            'uas' => 90
        ],
        [
            'nim' => '2025010098',
            'nama' => 'Bagas',
            'gmail' => 'bagas@gmail.com',
            'tugas' => 70,
            'uts' => 75,
            'uas' => 68
        ],
        [
            'nim' => '2025010099',
            'nama' => 'Alwi',
            'gmail' => 'alwi@gmail.com',
            'tugas' => 92,
            'uts' => 88,
            'uas' => 95
        ],
        [
            'nim' => '2025010100',
            'nama' => 'Rehan',
            'gmail' => 'rehan@gmail.com',
            'tugas' => 60,
            'uts' => 65,
            'uas' => 70
        ]
    ];
}

// Fungsi nilai akhir
function hitungNilai($tugas, $uts, $uas) {
    return ($tugas * 0.30) +
           ($uts * 0.30) +
           ($uas * 0.40);
}

// Fungsi predikat
function predikat($nilai) {
    if ($nilai >= 85) return "A";
    elseif ($nilai >= 70) return "B";
    elseif ($nilai >= 60) return "C";
    elseif ($nilai >= 50) return "D";
    else return "E";
}

// Proses POST
$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['reset_data'])) {

    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $gmail = trim($_POST['gmail'] ?? '');
    $tugas = $_POST['tugas'] ?? '';
    $uts = $_POST['uts'] ?? '';
    $uas = $_POST['uas'] ?? '';

    if ($nim == "" || $nama == "" || $gmail == "" ||
        $tugas === "" || $uts === "" || $uas === "") {

        $pesan = "Semua kolom wajib diisi!";

    } elseif (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {

        $pesan = "Format Gmail tidak valid!";

    } elseif (!is_numeric($tugas) ||
              !is_numeric($uts) ||
              !is_numeric($uas) ||
              $tugas < 0 || $tugas > 100 ||
              $uts < 0 || $uts > 100 ||
              $uas < 0 || $uas > 100) {

        $pesan = "Nilai harus berupa angka antara 0 sampai 100!";

    } else {

        $duplikat = false;

        foreach ($_SESSION['mahasiswa'] as $m) {
            if ($m['nim'] == $nim) {
                $duplikat = true;
                break;
            }
        }

        if ($duplikat) {
            $pesan = "NIM sudah terdaftar!";
        } else {
            $_SESSION['mahasiswa'][] = [
                'nim' => $nim,
                'nama' => $nama,
                'gmail' => $gmail,
                'tugas' => (float)$tugas,
                'uts' => (float)$uts,
                'uas' => (float)$uas
            ];

            header("Location: index.php?berhasil=1");
            exit;
        }
    }
}

// Pesan berhasil
if (isset($_GET['berhasil'])) {
    $pesan = "Data mahasiswa berhasil ditambahkan!";
}

// Proses GET pencarian
$cari = $_GET['cari'] ?? '';

$hasil = [];

foreach ($_SESSION['mahasiswa'] as $m) {
    if (
        stripos($m['nim'], $cari) !== false ||
        stripos($m['nama'], $cari) !== false ||
        stripos($m['gmail'], $cari) !== false
    ) {
        $hasil[] = $m;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Kelas</title>

    <style>
        body {
            background: white;
            color: black;
            font-family: "Times New Roman", serif;
            margin: 8px;
            font-size: 14px;
        }

        h2 {
            font-size: 18px;
            margin-top: 20px;
        }

        table {
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid black;
            padding: 5px 8px;
            text-align: left;
        }

        form {
            margin-top: 10px;
        }

        .field {
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin-bottom: 3px;
        }

        footer {
            margin-top: 25px;
        }

        .pesan {
            margin: 12px 0;
        }
    </style>
</head>

<body>

<!-- 1. PENCARIAN MAHASISWA -->
<h2>1. Pencarian Mahasiswa (GET)</h2>

<form method="GET" action="index.php">

    Cari berdasarkan NIM, nama, atau Gmail:

    <input
        type="text"
        name="cari"
        placeholder="Masukkan NIM, nama, atau Gmail"
        value="<?= htmlspecialchars($cari) ?>"
    >

    <button type="submit">Cari</button>

    <a href="index.php">Reset pencarian</a>

</form>

<!-- 2. DAFTAR MAHASISWA -->
<h2>2. Daftar Mahasiswa</h2>

<table>
    <tr>
        <th>NIM</th>
        <th>Nama</th>
        <th>Gmail</th>
        <th>Tugas</th>
        <th>UTS</th>
        <th>UAS</th>
        <th>Nilai Akhir</th>
        <th>Predikat</th>
        <th>Status</th>
    </tr>

    <?php if (count($hasil) > 0): ?>

        <?php foreach ($hasil as $m): ?>

            <?php
                $nilai = hitungNilai(
                    $m['tugas'],
                    $m['uts'],
                    $m['uas']
                );

                $grade = predikat($nilai);

                $status = $nilai >= 70
                    ? "Lulus"
                    : "Belum Lulus";
            ?>

            <tr>
                <td><?= htmlspecialchars($m['nim']) ?></td>
                <td><?= htmlspecialchars($m['nama']) ?></td>
                <td><?= htmlspecialchars($m['gmail']) ?></td>
                <td><?= $m['tugas'] ?></td>
                <td><?= $m['uts'] ?></td>
                <td><?= $m['uas'] ?></td>
                <td><?= number_format($nilai, 2) ?></td>
                <td><?= $grade ?></td>
                <td><?= $status ?></td>
            </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>
            <td colspan="9">Data mahasiswa tidak ditemukan.</td>
        </tr>

    <?php endif; ?>

</table>

<!-- TOMBOL RESET SEMUA DATA -->
<form method="POST" action="index.php"
      onsubmit="return confirm('Yakin ingin mereset semua data mahasiswa?')">

    <button type="submit" name="reset_data" value="1">
        Reset Semua Data
    </button>

</form>

<!-- 3. FORM INPUT MAHASISWA -->
<h2>3. Form Input Mahasiswa (POST)</h2>

<?php if ($pesan != ""): ?>
    <p class="pesan">
        <?= htmlspecialchars($pesan) ?>
    </p>
<?php endif; ?>

<form method="POST" action="index.php">

    <div class="field">
        <label>NIM:</label>
        <input type="text" name="nim" >
    </div>

    <div class="field">
        <label>Nama mahasiswa:</label>
        <input type="text" name="nama" >
    </div>

    <div class="field">
        <label>Nilai tugas:</label>
        <input type="number" name="tugas" min="0" max="100" >
    </div>

    <div class="field">
        <label>Nilai UTS:</label>
        <input type="number" name="uts" min="0" max="100" >
    </div>

    <div class="field">
        <label>Nilai UAS:</label>
        <input type="number" name="uas" min="0" max="100" >
    </div>

    <div class="field">
        <label>Gmail:</label>
        <input type="email" name="gmail"
               placeholder="contoh@gmail.com" 
    </div>

    <button type="submit">Proses Nilai</button>
    <button type="reset">Reset Form</button>

</form>

<footer>
    Simulasi PHP Form Processing dan Validasi Data - P11
</footer>

</body>
</html>
