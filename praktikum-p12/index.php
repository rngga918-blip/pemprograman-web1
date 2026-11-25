<?php
session_start();
$students = [
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

// Inisialisasi tempat penyimpanan data tambahan.
if (!isset($_SESSION['students']) || !is_array($_SESSION['students'])) {
    $_SESSION['students'] = [];
}

// Fungsi bantuan.
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function hitungNilai($tugas, $uts, $uas) {
    return ($tugas * 0.30) + ($uts * 0.30) + ($uas * 0.40);
}

function predikat($nilai) {
    if ($nilai >= 85) return 'A';
    if ($nilai >= 70) return 'B';
    if ($nilai >= 60) return 'C';
    if ($nilai >= 50) return 'D';
    return 'E';
}

function setPreferenceCookie($name, $value, $expires) {
    $options = [
        'expires' => $expires,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    setcookie($name, $value, $options);
}

function redirectWithFlash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
    header('Location: index.php');
    exit;
}

// Semua perubahan data dilakukan melalui POST, kemudian diarahkan kembali ke GET (PRG).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Tambah mahasiswa.
    if ($action === 'add_student') {
        $nim = trim($_POST['nim'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $gmail = trim($_POST['gmail'] ?? '');
        $tugas = $_POST['tugas'] ?? '';
        $uts = $_POST['uts'] ?? '';
        $uas = $_POST['uas'] ?? '';

        if ($nim === '' || $nama === '' || $gmail === '' ||
            $tugas === '' || $uts === '' || $uas === '') {
            redirectWithFlash('Semua kolom wajib diisi.', 'error');
        }

        if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
            redirectWithFlash('Format email tidak valid.', 'error');
        }

        if (!is_numeric($tugas) || !is_numeric($uts) || !is_numeric($uas) ||
            $tugas < 0 || $tugas > 100 ||
            $uts < 0 || $uts > 100 ||
            $uas < 0 || $uas > 100) {
            redirectWithFlash('Nilai harus berupa angka antara 0 sampai 100.', 'error');
        }

        $allForCheck = array_merge($students, $_SESSION['students']);
        foreach ($allForCheck as $student) {
            if ((string) $student['nim'] === $nim) {
                redirectWithFlash('NIM sudah terdaftar. Gunakan NIM yang berbeda.', 'error');
            }
        }

        $_SESSION['students'][] = [
            'nim' => $nim,
            'nama' => $nama,
            'gmail' => $gmail,
            'tugas' => (float) $tugas,
            'uts' => (float) $uts,
            'uas' => (float) $uas
        ];

        redirectWithFlash('Data mahasiswa berhasil ditambahkan ke Session.');
    }

    // Hapus hanya data tambahan yang berada di Session.
    if ($action === 'delete_student') {
        $nimToDelete = trim($_POST['nim'] ?? '');
        $found = false;

        foreach ($_SESSION['students'] as $index => $student) {
            if ((string) $student['nim'] === $nimToDelete) {
                unset($_SESSION['students'][$index]);
                $found = true;
                break;
            }
        }

        $_SESSION['students'] = array_values($_SESSION['students']);
        redirectWithFlash(
            $found ? 'Data mahasiswa tambahan berhasil dihapus.' : 'Data tidak ditemukan pada Session.',
            $found ? 'success' : 'error'
        );
    }

    // Reset hanya data tambahan, bukan seluruh Session dan bukan data awal.
    if ($action === 'reset_students') {
        $_SESSION['students'] = [];
        redirectWithFlash('Seluruh data mahasiswa tambahan berhasil direset.');
    }

    // Mengubah preferensi bahasa.
    if ($action === 'save_language') {
        $language = $_POST['language'] ?? 'id';
        if (!in_array($language, ['id', 'en'], true)) {
            $language = 'id';
        }
        setPreferenceCookie('preferred_language', $language, time() + (7 * 24 * 60 * 60));
        redirectWithFlash('Preferensi bahasa disimpan selama tujuh hari.');
    }

    // Mengubah preferensi tampilan tabel.
    if ($action === 'save_table_mode') {
        $mode = $_POST['table_mode'] ?? 'comfortable';
        if (!in_array($mode, ['compact', 'comfortable'], true)) {
            $mode = 'comfortable';
        }
        setPreferenceCookie('table_mode', $mode, time() + (7 * 24 * 60 * 60));
        redirectWithFlash('Preferensi tampilan tabel berhasil disimpan.');
    }

    // Menghapus cookie preferensi.
    if ($action === 'clear_preferences') {
        setPreferenceCookie('preferred_language', '', time() - 3600);
        setPreferenceCookie('table_mode', '', time() - 3600);
        redirectWithFlash('Preferensi cookie dihapus. Pengaturan default digunakan kembali.');
    }

    redirectWithFlash('Permintaan tidak dikenali.', 'error');
}

// Ambil pesan flash satu kali saja.
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Baca dan validasi cookie.
$language = $_COOKIE['preferred_language'] ?? 'id';
if (!in_array($language, ['id', 'en'], true)) {
    $language = 'id';
}

$tableMode = $_COOKIE['table_mode'] ?? 'comfortable';
if (!in_array($tableMode, ['compact', 'comfortable'], true)) {
    $tableMode = 'comfortable';
}

// Label sederhana sesuai preferensi bahasa.
$labels = [
    'id' => [
        'title' => 'Rekap Nilai Kelas',
        'search' => 'Pencarian Mahasiswa',
        'search_hint' => 'Cari berdasarkan NIM, nama, atau email:',
        'search_placeholder' => 'Masukkan NIM, nama, atau email',
        'find' => 'Cari',
        'reset_search' => 'Reset pencarian',
        'list' => 'Daftar Mahasiswa',
        'add' => 'Form Input Mahasiswa',
        'nim' => 'NIM',
        'name' => 'Nama',
        'email' => 'Email',
        'task' => 'Tugas',
        'mid' => 'UTS',
        'final' => 'UAS',
        'final_score' => 'Nilai Akhir',
        'grade' => 'Predikat',
        'status' => 'Status',
        'action' => 'Aksi',
        'delete' => 'Hapus',
        'no_data' => 'Data mahasiswa tidak ditemukan.',
        'new_student' => 'Tambah Mahasiswa',
        'language' => 'Bahasa',
        'display' => 'Tampilan Tabel',
        'save' => 'Simpan Preferensi',
        'reset_data' => 'Reset Data Sementara',
        'clear_cookie' => 'Hapus Preferensi Cookie',
        'count' => 'Jumlah data tambahan di Session'
    ],
    'en' => [
        'title' => 'Class Grade Summary',
        'search' => 'Student Search',
        'search_hint' => 'Search by student ID, name, or email:',
        'search_placeholder' => 'Enter ID, name, or email',
        'find' => 'Search',
        'reset_search' => 'Reset search',
        'list' => 'Student List',
        'add' => 'Student Input Form',
        'nim' => 'Student ID',
        'name' => 'Name',
        'email' => 'Email',
        'task' => 'Assignment',
        'mid' => 'Midterm',
        'final' => 'Final Exam',
        'final_score' => 'Final Score',
        'grade' => 'Grade',
        'status' => 'Status',
        'action' => 'Action',
        'delete' => 'Delete',
        'no_data' => 'No students found.',
        'new_student' => 'Add Student',
        'language' => 'Language',
        'display' => 'Table Display',
        'save' => 'Save Preference',
        'reset_data' => 'Reset Temporary Data',
        'clear_cookie' => 'Clear Cookie Preferences',
        'count' => 'Additional records in Session'
    ]
];
$t = $labels[$language];

// Gabungkan data awal dengan data tambahan untuk kebutuhan tabel dan pencarian.
$allStudents = array_merge($students, $_SESSION['students']);
$cari = trim($_GET['cari'] ?? '');
$hasil = [];

foreach ($allStudents as $student) {
    if (
        $cari === '' ||
        stripos((string) $student['nim'], $cari) !== false ||
        stripos((string) $student['nama'], $cari) !== false ||
        stripos((string) $student['gmail'], $cari) !== false
    ) {
        $hasil[] = $student;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $language === 'en' ? 'en' : 'id' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($t['title']) ?> - P12</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 28px 16px;
            background: #f4f6f8;
            color: #202733;
            font-family: Arial, Helvetica, sans-serif;
        }
        .container {
            max-width: 1150px;
            margin: 0 auto;
        }
        .header, .panel {
            background: #fff;
            border: 1px solid #e2e7ed;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 18px;
        }
        h1 { margin: 0 0 7px; font-size: 27px; }
        h2 { margin: 0 0 16px; font-size: 19px; }
        .subtitle, .muted { color: #687386; font-size: 13px; }
        .status-line {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .pill {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            background: #edf4ff;
            color: #2456a6;
            font-size: 12px;
        }
        .message {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            background: #eaf7ee;
            color: #176534;
        }
        .message.error { background: #fff0f0; color: #a32626; }
        form { margin: 0; }
        .search-form, .preference-row {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 10px;
        }
        .field { margin-bottom: 13px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        input, select {
            width: 100%;
            max-width: 100%;
            padding: 10px 11px;
            border: 1px solid #cbd3dd;
            border-radius: 7px;
            background: white;
            font: inherit;
        }
        .search-form input { flex: 1; min-width: 220px; }
        button, .link-button {
            display: inline-block;
            border: 0;
            border-radius: 7px;
            padding: 10px 14px;
            background: #2457a7;
            color: white;
            font: inherit;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }
        button:hover, .link-button:hover { background: #194584; }
        .secondary { background: #657184; }
        .danger { background: #b83232; }
        .danger:hover { background: #922525; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
        th, td { border: 1px solid #dce2e9; padding: 11px 9px; text-align: left; white-space: nowrap; font-size: 13px; }
        th { background: #edf1f6; }
        table.compact th, table.compact td { padding: 5px 7px; font-size: 12px; }
        .student-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 14px; }
        .student-form-grid .full { grid-column: 1 / -1; }
        .form-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .inline-form { display: inline; }
        .count { font-size: 13px; color: #536174; margin: 0 0 12px; }
        footer { text-align: center; color: #7a8492; font-size: 12px; padding: 12px; }
        @media (max-width: 600px) {
            body { padding: 14px 9px; }
            .header, .panel { padding: 16px; }
            .student-form-grid { grid-template-columns: 1fr; }
            .student-form-grid .full { grid-column: auto; }
            h1 { font-size: 23px; }
        }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <h1><?= e($t['title']) ?></h1>
        <div class="subtitle">Praktikum Pemrograman Web 1 · Pertemuan 12 · PHP Session dan Cookie</div>
        <div class="status-line">
            <span class="pill">Session aktif</span>
            <span class="pill"><?= e($t['count']) ?>: <?= count($_SESSION['students']) ?></span>
            <span class="pill">Cookie: <?= $language === 'id' ? 'Indonesia' : 'English' ?> · <?= e($tableMode) ?></span>
        </div>
    </header>

    <?php if ($flash): ?>
        <div class="message <?= ($flash['type'] ?? '') === 'error' ? 'error' : '' ?>">
            <?= e($flash['message'] ?? '') ?>
        </div>
    <?php endif; ?>

    <section class="panel">
        <h2>1. <?= e($t['search']) ?> (GET)</h2>
        <form method="GET" action="index.php" class="search-form">
            <input type="text" name="cari" placeholder="<?= e($t['search_placeholder']) ?>" value="<?= e($cari) ?>">
            <button type="submit"><?= e($t['find']) ?></button>
            <a class="link-button secondary" href="index.php"><?= e($t['reset_search']) ?></a>
        </form>
    </section>

    <section class="panel">
        <h2>2. <?= e($t['list']) ?></h2>
        <p class="count"><?= e($t['count']) ?>: <strong><?= count($_SESSION['students']) ?></strong></p>
        <div class="table-wrap">
            <table class="<?= e($tableMode) ?>">
                <thead>
                    <tr>
                        <th><?= e($t['nim']) ?></th>
                        <th><?= e($t['name']) ?></th>
                        <th><?= e($t['email']) ?></th>
                        <th><?= e($t['task']) ?></th>
                        <th><?= e($t['mid']) ?></th>
                        <th><?= e($t['final']) ?></th>
                        <th><?= e($t['final_score']) ?></th>
                        <th><?= e($t['grade']) ?></th>
                        <th><?= e($t['status']) ?></th>
                        <th><?= e($t['action']) ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($hasil) > 0): ?>
                    <?php foreach ($hasil as $student): ?>
                        <?php
                            $score = hitungNilai($student['tugas'], $student['uts'], $student['uas']);
                            $grade = predikat($score);
                            $status = $score >= 70 ? 'Lulus' : 'Belum Lulus';
                            $isSessionStudent = false;
                            foreach ($_SESSION['students'] as $sessionStudent) {
                                if ((string) $sessionStudent['nim'] === (string) $student['nim']) {
                                    $isSessionStudent = true;
                                    break;
                                }
                            }
                        ?>
                        <tr>
                            <td><?= e($student['nim']) ?></td>
                            <td><?= e($student['nama']) ?></td>
                            <td><?= e($student['gmail']) ?></td>
                            <td><?= e($student['tugas']) ?></td>
                            <td><?= e($student['uts']) ?></td>
                            <td><?= e($student['uas']) ?></td>
                            <td><?= number_format($score, 2) ?></td>
                            <td><?= e($grade) ?></td>
                            <td><?= e($status) ?></td>
                            <td>
                                <?php if ($isSessionStudent): ?>
                                    <form method="POST" class="inline-form" onsubmit="return confirm('Hapus data mahasiswa ini?')">
                                        <input type="hidden" name="action" value="delete_student">
                                        <input type="hidden" name="nim" value="<?= e($student['nim']) ?>">
                                        <button type="submit" class="danger"><?= e($t['delete']) ?></button>
                                    </form>
                                <?php else: ?>
                                    <span class="muted">Data awal</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="10"><?= e($t['no_data']) ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <form method="POST" style="margin-top:15px" onsubmit="return confirm('Reset seluruh data tambahan di Session? Data awal tetap ada.')">
            <input type="hidden" name="action" value="reset_students">
            <button type="submit" class="danger"><?= e($t['reset_data']) ?></button>
        </form>
        <p class="muted">Reset hanya mengosongkan data tambahan di Session. Empat data awal tetap tersedia.</p>
    </section>

    <section class="panel">
        <h2>3. <?= e($t['add']) ?> (POST)</h2>
        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="add_student">
            <div class="student-form-grid">
                <div class="field">
                    <label for="nim"><?= e($t['nim']) ?></label>
                    <input id="nim" type="text" name="nim" required>
                </div>
                <div class="field">
                    <label for="nama"><?= e($t['name']) ?></label>
                    <input id="nama" type="text" name="nama" required>
                </div>
                <div class="field">
                    <label for="gmail"><?= e($t['email']) ?></label>
                    <input id="gmail" type="email" name="gmail" placeholder="contoh@gmail.com" required>
                </div>
                <div class="field">
                    <label for="tugas"><?= e($t['task']) ?></label>
                    <input id="tugas" type="number" name="tugas" min="0" max="100" step="any" required>
                </div>
                <div class="field">
                    <label for="uts"><?= e($t['mid']) ?></label>
                    <input id="uts" type="number" name="uts" min="0" max="100" step="any" required>
                </div>
                <div class="field">
                    <label for="uas"><?= e($t['final']) ?></label>
                    <input id="uas" type="number" name="uas" min="0" max="100" step="any" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit"><?= e($t['new_student']) ?></button>
                <button type="reset" class="secondary">Reset Form</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <h2>4. Preferensi Cookie</h2>
        <div class="preference-row">
            <form method="POST">
                <input type="hidden" name="action" value="save_language">
                <div class="field">
                    <label for="language"><?= e($t['language']) ?></label>
                    <select name="language" id="language">
                        <option value="id" <?= $language === 'id' ? 'selected' : '' ?>>Indonesia</option>
                        <option value="en" <?= $language === 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>
                <button type="submit"><?= e($t['save']) ?></button>
            </form>

            <form method="POST">
                <input type="hidden" name="action" value="save_table_mode">
                <div class="field">
                    <label for="table_mode"><?= e($t['display']) ?></label>
                    <select name="table_mode" id="table_mode">
                        <option value="comfortable" <?= $tableMode === 'comfortable' ? 'selected' : '' ?>>Comfortable</option>
                        <option value="compact" <?= $tableMode === 'compact' ? 'selected' : '' ?>>Compact</option>
                    </select>
                </div>
                <button type="submit"><?= e($t['save']) ?></button>
            </form>

            <form method="POST">
                <input type="hidden" name="action" value="clear_preferences">
                <button type="submit" class="secondary"><?= e($t['clear_cookie']) ?></button>
            </form>
        </div>
        <p class="muted">Preferensi bahasa dan tampilan disimpan selama tujuh hari. Cookie tidak menyimpan data nilai mahasiswa.</p>
    </section>

    <footer>Rekap Nilai Kelas · Pengembangan P11 menjadi P12 · PHP Session dan Cookie</footer>
</div>
</body>
</html>
