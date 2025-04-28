<?php
session_start();
require_once '../app/pasien.php';
require_once '../app/dokter.php';
require_once '../app/kunjungan.php';

$dataPasien = $pasien->getAll();
$dataDokter = $dokter->getAll();
$dataKunjungan = $kunjungan->getAll();


// Form Edit Pasien
$edit_pasien = null;
if (isset($_GET['edit_pasien'])) {
    $edit_pasien = $pasien->getById($_GET['edit_pasien']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SIMRS Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">

<?php if (isset($_SESSION['pesan'])): ?>
<div class="alert alert-success"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
<?php endif; ?>

<h1>Data Pasien</h1>

<!-- Form Tambah/Edit Pasien -->
<form method="POST" class="mb-4">
    <input type="hidden" name="aksi" value="<?= $edit_pasien ? 'edit_pasien' : 'tambah_pasien' ?>">
    <?php if ($edit_pasien): ?>
        <input type="hidden" name="id" value="<?= $edit_pasien['id'] ?>">
    <?php endif; ?>
    <div class="mb-2">
        <input type="text" name="nama" class="form-control" placeholder="Nama Pasien" value="<?= $edit_pasien['nama'] ?? '' ?>" required>
    </div>
    <div class="mb-2">
        <textarea name="alamat" class="form-control" placeholder="Alamat" required><?= $edit_pasien['alamat'] ?? '' ?></textarea>
    </div>
    <button type="submit" class="btn btn-<?= $edit_pasien ? 'warning' : 'primary' ?>">
        <?= $edit_pasien ? 'Update Pasien' : 'Tambah Pasien' ?>
    </button>
    <?php if ($edit_pasien): ?>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    <?php endif; ?>
</form>

<!-- Tabel Data Pasien -->
<table class="table table-bordered">
<thead>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
<?php foreach ($pasien->getAll() as $index => $row): ?>
    <tr>
        <td><?= $index + 1 ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['alamat']) ?></td>
        <td>
            <a href="?edit_pasien=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="?hapus_pasien=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<hr>

<h1>Data Dokter</h1>

<!-- Form Tambah Dokter -->
<form method="POST" class="mb-4">
    <input type="hidden" name="aksi" value="tambah_dokter">
    <div class="mb-2">
        <input type="text" name="nama" class="form-control" placeholder="Nama Dokter" required>
    </div>
    <div class="mb-2">
        <input type="text" name="spesialis" class="form-control" placeholder="Spesialis" required>
    </div>
    <button type="submit" class="btn btn-primary">Tambah Dokter</button>
</form>

<!-- Tabel Data Dokter -->
<table class="table table-striped">
<thead>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Spesialis</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
<?php foreach ($dokter->getAll() as $index => $row): ?>
    <tr>
        <td><?= $index + 1 ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['spesialis']) ?></td>
        <td>
            <a href="?hapus_dokter=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

<hr>

<h1>Riwayat Kunjungan Pasien</h1>

<!-- Form Tambah Kunjungan -->
<form method="POST" class="mb-4">
    <input type="hidden" name="aksi" value="tambah_kunjungan">
    <div class="mb-2">
        <select name="id_pasien" class="form-control" required>
            <option value="">-- Pilih Pasien --</option>
            <?php foreach ($pasien->getAll() as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-2">
        <select name="id_dokter" class="form-control" required>
            <option value="">-- Pilih Dokter --</option>
            <?php foreach ($dokter->getAll() as $d): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nama']) ?> (<?= htmlspecialchars($d['spesialis']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-2">
        <input type="text" name="poli" class="form-control" placeholder="Poli Tujuan" required>
    </div>
    <div class="mb-2">
        <input type="date" name="tanggal" class="form-control" required>
    </div>
    <div class="mb-2">
        <textarea name="keluhan" class="form-control" placeholder="Keluhan" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Tambah Kunjungan</button>
</form>

<!-- Tabel Data Kunjungan -->
<table class="table table-bordered">
<thead>
    <tr>
        <th>Pasien</th>
        <th>Dokter</th>
        <th>Spesialis</th>
        <th>Poli</th>
        <th>Tanggal</th>
        <th>Keluhan</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
<?php foreach ($kunjungan->getAll() as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
        <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
        <td><?= htmlspecialchars($row['spesialis']) ?></td>
        <td><?= htmlspecialchars($row['poli']) ?></td>
        <td><?= htmlspecialchars($row['tanggal']) ?></td>
        <td><?= htmlspecialchars($row['keluhan']) ?></td>
        <td>
            <a href="?hapus_kunjungan=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>

</body>
</html>

<?php
// Proses Tambah/Edit/Hapus Data
if (isset($_POST['aksi'])) {
    if ($_POST['aksi'] == 'tambah_pasien') {
        $pasien->create($_POST['nama'], $_POST['alamat']);
        $_SESSION['pesan'] = "Pasien berhasil ditambahkan!";
    }
    if ($_POST['aksi'] == 'edit_pasien') {
        $pasien->update($_POST['id'], $_POST['nama'], $_POST['alamat']);
        $_SESSION['pesan'] = "Pasien berhasil diperbarui!";
    }
    if ($_POST['aksi'] == 'tambah_dokter') {
        $dokter->create($_POST['nama'], $_POST['spesialis']);
        $_SESSION['pesan'] = "Dokter berhasil ditambahkan!";
    }
    if ($_POST['aksi'] == 'tambah_kunjungan') {
        $kunjungan->create($_POST['id_pasien'], $_POST['id_dokter'], $_POST['poli'], $_POST['tanggal'], $_POST['keluhan']);
        $_SESSION['pesan'] = "Kunjungan berhasil ditambahkan!";
    }
    header('Location: index.php');
    exit;
}

if (isset($_GET['hapus_pasien'])) {
    $pasien->delete($_GET['hapus_pasien']);
    $_SESSION['pesan'] = "Pasien berhasil dihapus!";
    header('Location: index.php');
    exit;
}
if (isset($_GET['hapus_dokter'])) {
    $dokter->delete($_GET['hapus_dokter']);
    $_SESSION['pesan'] = "Dokter berhasil dihapus!";
    header('Location: index.php');
    exit;
}
if (isset($_GET['hapus_kunjungan'])) {
    $kunjungan->delete($_GET['hapus_kunjungan']);
    $_SESSION['pesan'] = "Kunjungan berhasil dihapus!";
    header('Location: index.php');
    exit;
}
?>
