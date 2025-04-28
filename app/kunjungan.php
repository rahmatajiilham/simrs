<?php
require_once 'database.php';

class Kunjungan extends Database {
    public function getAll() {
        $sql = "SELECT k.id, p.nama AS nama_pasien, d.nama AS nama_dokter, d.spesialis, k.poli, k.tanggal, k.keluhan 
                FROM kunjungan k
                JOIN pasien p ON k.pasien_id = p.id
                JOIN dokter d ON k.dokter_id = d.id
                ORDER BY k.tanggal DESC";
        return $this->query($sql);
    }

    public function create($pasien_id, $dokter_id, $poli, $tanggal, $keluhan) {
        $pasien_id = (int)$pasien_id;
        $dokter_id = (int)$dokter_id;
        $poli = $this->escape($poli);
        $keluhan = $this->escape($keluhan);
        $tanggal = $this->escape($tanggal);

        $sql = "INSERT INTO kunjungan (pasien_id, dokter_id, poli, tanggal, keluhan) 
                VALUES ($pasien_id, $dokter_id, '$poli', '$tanggal', '$keluhan')";
        return $this->query($sql);
    }

    public function delete($id) {
        $id = (int)$id;
        return $this->query("DELETE FROM kunjungan WHERE id = $id");
    }
}
?>
