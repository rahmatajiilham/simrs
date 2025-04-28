-- Hapus tabel dulu kalau sudah ada
DROP TABLE IF EXISTS kunjungan;
DROP TABLE IF EXISTS dokter;
DROP TABLE IF EXISTS pasien;
DROP TABLE IF EXISTS poli;

-- Buat tabel poli
CREATE TABLE poli (
    id CHAR(13) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL
);

-- Buat tabel pasien
CREATE TABLE pasien ( 
    id CHAR(13) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL
);

-- Buat tabel dokter
CREATE TABLE dokter (
    id CHAR(13) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    spesialis VARCHAR(100) NOT NULL
);

-- Buat tabel kunjungan
CREATE TABLE kunjungan (
    id CHAR(13) PRIMARY KEY,
    id_pasien CHAR(13) NOT NULL,
    id_dokter CHAR(13) NOT NULL,
    id_poli CHAR(13) NOT NULL,
    tanggal DATE NOT NULL,
    keluhan TEXT NOT NULL,
    CONSTRAINT fk_pasien FOREIGN KEY (id_pasien) REFERENCES pasien(id) ON DELETE CASCADE,
    CONSTRAINT fk_dokter FOREIGN KEY (id_dokter) REFERENCES dokter(id) ON DELETE CASCADE,
    CONSTRAINT fk_poli FOREIGN KEY (id_poli) REFERENCES poli(id) ON DELETE CASCADE
);
