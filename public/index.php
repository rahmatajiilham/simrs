<?php 
session_start();

// --- Database sederhana pakai session array ---
if (!isset($_SESSION['db'])) {
    $_SESSION['db'] = [
        'pasien' => [],
        'dokter' => [],
        'kunjungan' => [],
    ];
}

// --- Class Models ---
class Pasien {
    function getAll() { return $_SESSION['db']['pasien']; }
    function getById($id) { foreach ($_SESSION['db']['pasien'] as $p) { if ($p['id'] == $id) return $p; } return null; }
    function create($nama, $alamat) { $_SESSION['db']['pasien'][] = ['id' => uniqid(), 'nama' => htmlspecialchars($nama), 'alamat' => htmlspecialchars($alamat)]; }
    function update($id, $nama, $alamat) { foreach ($_SESSION['db']['pasien'] as &$p) { if ($p['id'] == $id) { $p['nama'] = htmlspecialchars($nama); $p['alamat'] = htmlspecialchars($alamat); break; } } }
    function delete($id) { $_SESSION['db']['pasien'] = array_filter($_SESSION['db']['pasien'], fn($p) => $p['id'] != $id); }
}

class Dokter {
    function getAll() { return $_SESSION['db']['dokter']; }
    function getById($id) { foreach ($_SESSION['db']['dokter'] as $d) { if ($d['id'] == $id) return $d; } return null; }
    function create($nama, $spesialis) { $_SESSION['db']['dokter'][] = ['id' => uniqid(), 'nama' => htmlspecialchars($nama), 'spesialis' => htmlspecialchars($spesialis)]; }
    function update($id, $nama, $spesialis) { foreach ($_SESSION['db']['dokter'] as &$d) { if ($d['id'] == $id) { $d['nama'] = htmlspecialchars($nama); $d['spesialis'] = htmlspecialchars($spesialis); break; } } }
    function delete($id) { $_SESSION['db']['dokter'] = array_filter($_SESSION['db']['dokter'], fn($d) => $d['id'] != $id); }
}

class Kunjungan {
    function getAll() {
        $pasien = new Pasien();
        $dokter = new Dokter();
        $list = [];
        foreach ($_SESSION['db']['kunjungan'] as $k) {
            $p = $pasien->getById($k['id_pasien']);
            $d = $dokter->getById($k['id_dokter']);
            $list[] = [
                'id' => $k['id'],
                'nama_pasien' => $p['nama'] ?? 'Tidak Ditemukan',
                'nama_dokter' => $d['nama'] ?? 'Tidak Ditemukan',
                'spesialis' => $d['spesialis'] ?? '-',
                'poli' => $k['poli'],
                'tanggal' => $k['tanggal'],
                'keluhan' => $k['keluhan'],
            ];
        }
        return $list;
    }
    function create($id_pasien, $id_dokter, $poli, $tanggal, $keluhan) {
        $_SESSION['db']['kunjungan'][] = ['id' => uniqid(), 'id_pasien' => $id_pasien, 'id_dokter' => $id_dokter, 'poli' => htmlspecialchars($poli), 'tanggal' => $tanggal, 'keluhan' => htmlspecialchars($keluhan)];
    }
    function delete($id) { $_SESSION['db']['kunjungan'] = array_filter($_SESSION['db']['kunjungan'], fn($k) => $k['id'] != $id); }
}

// --- Instansiasi objek ---
$pasien = new Pasien();
$dokter = new Dokter();
$kunjungan = new Kunjungan();

$dataPasien = $pasien->getAll();
$dataDokter = $dokter->getAll();
$dataKunjungan = $kunjungan->getAll();

$edit_pasien = isset($_GET['edit_pasien']) ? $pasien->getById($_GET['edit_pasien']) : null;
$edit_dokter = isset($_GET['edit_dokter']) ? $dokter->getById($_GET['edit_dokter']) : null;

// --- Proses Tambah/Edit/Hapus ---
if (isset($_POST['aksi'])) {
    switch ($_POST['aksi']) {
        case 'tambah_pasien': $pasien->create($_POST['nama'], $_POST['alamat']); $_SESSION['pesan'] = "Pasien ditambahkan."; break;
        case 'edit_pasien': $pasien->update($_POST['id'], $_POST['nama'], $_POST['alamat']); $_SESSION['pesan'] = "Pasien diperbarui."; break;
        case 'tambah_dokter': $dokter->create($_POST['nama'], $_POST['spesialis']); $_SESSION['pesan'] = "Dokter ditambahkan."; break;
        case 'edit_dokter': $dokter->update($_POST['id'], $_POST['nama'], $_POST['spesialis']); $_SESSION['pesan'] = "Dokter diperbarui."; break;
        case 'tambah_kunjungan': $kunjungan->create($_POST['id_pasien'], $_POST['id_dokter'], $_POST['poli'], $_POST['tanggal'], $_POST['keluhan']); $_SESSION['pesan'] = "Kunjungan ditambahkan."; break;
    }
    header('Location: ?');
    exit;
}

if (isset($_GET['hapus_pasien'])) { $pasien->delete($_GET['hapus_pasien']); $_SESSION['pesan'] = "Pasien dihapus."; header('Location: ?'); exit; }
if (isset($_GET['hapus_dokter'])) { $dokter->delete($_GET['hapus_dokter']); $_SESSION['pesan'] = "Dokter dihapus."; header('Location: ?'); exit; }
if (isset($_GET['hapus_kunjungan'])) { $kunjungan->delete($_GET['hapus_kunjungan']); $_SESSION['pesan'] = "Kunjungan dihapus."; header('Location: ?'); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIMRS Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { margin-bottom: 2rem; }
        .table-hover tbody tr:hover { background-color: #e9ecef; }
        .nav-tabs .nav-link.active { background-color: #0d6efd; color: white; border: none; }
        .btn { transition: all 0.3s ease; }
        .btn:hover { transform: scale(1.05); }
    </style>
</head>
<body class="p-5">
<div class="container">

<?php if (isset($_SESSION['pesan'])): ?>
<div class="alert alert-success"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
<?php endif; ?>

<!-- Nav Tabs -->
<ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="pasien-tab" data-bs-toggle="tab" data-bs-target="#pasien" type="button" role="tab">Pasien</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="dokter-tab" data-bs-toggle="tab" data-bs-target="#dokter" type="button" role="tab">Dokter</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="kunjungan-tab" data-bs-toggle="tab" data-bs-target="#kunjungan" type="button" role="tab">Kunjungan</button>
  </li>
</ul>

<div class="tab-content" id="myTabContent">
  
  <!-- Tab Pasien -->
  <div class="tab-pane fade show active" id="pasien" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?= $edit_pasien ? 'Edit Pasien' : 'Tambah Pasien' ?></h5>
            <form method="POST">
                <input type="hidden" name="aksi" value="<?= $edit_pasien ? 'edit_pasien' : 'tambah_pasien' ?>">
                <?php if ($edit_pasien): ?><input type="hidden" name="id" value="<?= $edit_pasien['id'] ?>"><?php endif; ?>
                <div class="mb-2">
                    <input type="text" name="nama" class="form-control" placeholder="Nama Pasien" value="<?= htmlspecialchars($edit_pasien['nama'] ?? '') ?>" required>
                </div>
                <div class="mb-2">
                    <textarea name="alamat" class="form-control" placeholder="Alamat" required><?= htmlspecialchars($edit_pasien['alamat'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-<?= $edit_pasien ? 'warning' : 'primary' ?>">
                    <?= $edit_pasien ? 'Update' : 'Tambah' ?>
                </button>
                <?php if ($edit_pasien): ?>
                    <a href="?" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <table class="table table-bordered table-hover">
      <thead><tr><th>No</th><th>Nama</th><th>Alamat</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($dataPasien as $i => $p): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($p['nama']) ?></td>
            <td><?= htmlspecialchars($p['alamat']) ?></td>
            <td>
                <a href="?edit_pasien=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="?hapus_pasien=<?= $p['id'] ?>" onclick="return confirm('Yakin?')" class="btn btn-danger btn-sm">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Tab Dokter -->
  <div class="tab-pane fade" id="dokter" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?= $edit_dokter ? 'Edit Dokter' : 'Tambah Dokter' ?></h5>
            <form method="POST">
                <input type="hidden" name="aksi" value="<?= $edit_dokter ? 'edit_dokter' : 'tambah_dokter' ?>">
                <?php if ($edit_dokter): ?><input type="hidden" name="id" value="<?= $edit_dokter['id'] ?>"><?php endif; ?>
                <div class="mb-2">
                    <input type="text" name="nama" class="form-control" placeholder="Nama Dokter" value="<?= htmlspecialchars($edit_dokter['nama'] ?? '') ?>" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="spesialis" class="form-control" placeholder="Spesialis" value="<?= htmlspecialchars($edit_dokter['spesialis'] ?? '') ?>" required>
                </div>
                <button type="submit" class="btn btn-<?= $edit_dokter ? 'warning' : 'primary' ?>">
                    <?= $edit_dokter ? 'Update' : 'Tambah' ?>
                </button>
                <?php if ($edit_dokter): ?>
                    <a href="?" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <table class="table table-bordered table-hover">
      <thead><tr><th>No</th><th>Nama</th><th>Spesialis</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($dataDokter as $i => $d): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($d['nama']) ?></td>
            <td><?= htmlspecialchars($d['spesialis']) ?></td>
            <td>
                <a href="?edit_dokter=<?= $d['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="?hapus_dokter=<?= $d['id'] ?>" onclick="return confirm('Yakin?')" class="btn btn-danger btn-sm">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Tab Kunjungan -->
  <div class="tab-pane fade" id="kunjungan" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Tambah Kunjungan</h5>
            <form method="POST">
                <input type="hidden" name="aksi" value="tambah_kunjungan">
                <div class="mb-2">
                    <select name="id_pasien" class="form-control" required>
                        <option value="">-- Pilih Pasien --</option>
                        <?php foreach ($dataPasien as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <select name="id_dokter" class="form-control" required>
                        <option value="">-- Pilih Dokter --</option>
                        <?php foreach ($dataDokter as $d): ?>
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
        </div>
    </div>

    <table class="table table-bordered table-hover">
      <thead><tr><th>Pasien</th><th>Dokter</th><th>Spesialis</th><th>Poli</th><th>Tanggal</th><th>Keluhan</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($dataKunjungan as $k): ?>
        <tr>
            <td><?= htmlspecialchars($k['nama_pasien']) ?></td>
            <td><?= htmlspecialchars($k['nama_dokter']) ?></td>
            <td><?= htmlspecialchars($k['spesialis']) ?></td>
            <td><?= htmlspecialchars($k['poli']) ?></td>
            <td><?= htmlspecialchars($k['tanggal']) ?></td>
            <td><?= htmlspecialchars($k['keluhan']) ?></td>
            <td>
                <a href="?hapus_kunjungan=<?= $k['id'] ?>" onclick="return confirm('Yakin?')" class="btn btn-danger btn-sm">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
