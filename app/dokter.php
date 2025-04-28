<?php
require_once 'database.php';

class Dokter extends Database {
    public function getAll() {
        return $this->query("SELECT * FROM dokter");
    }

    public function create($nama, $spesialis) {
        $nama = $this->escape($nama);
        $spesialis = $this->escape($spesialis);
        return $this->query("INSERT INTO dokter (nama, spesialis) VALUES ('$nama', '$spesialis')");
    }

    public function delete($id) {
        $id = (int)$id;
        return $this->query("DELETE FROM dokter WHERE id = $id");
    }
}
?>
