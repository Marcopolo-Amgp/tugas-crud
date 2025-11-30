<?php
require_once __DIR__ . '/../inc/config.php';

class Mahasiswa {
    public $nim;
    public $nama;
    public $prodi;
    public $angkatan;
    public $foto;
    public $keterangan;

    public function __construct($nim, $nama, $prodi, $angkatan, $foto = null, $keterangan = 'aktif') {
        $this->nim = $nim;
        $this->nama = $nama;
        $this->prodi = $prodi;
        $this->angkatan = $angkatan;
        $this->foto = $foto;
        $this->keterangan = $keterangan;
    }

    // Getter methods
    public function getNim() { return $this->nim; }
    public function getNama() { return $this->nama; }
    public function getProdi() { return $this->prodi; }
    public function getAngkatan() { return $this->angkatan; }
    public function getFoto() { return $this->foto; }
    public function getKeterangan() { return $this->keterangan; }

    // Method untuk mendapatkan nama prodi lengkap
    public function getNamaProdi() {
        global $prodiOptions;
        return isset($prodiOptions[$this->prodi]) ? $prodiOptions[$this->prodi] : 'Unknown';
    }

    // Validasi data
    public function validate() {
        $errors = [];

        if (empty($this->nim)) {
            $errors[] = "NIM tidak boleh kosong";
        }

        if (empty($this->nama)) {
            $errors[] = "Nama tidak boleh kosong";
        }

        if (!in_array($this->prodi, ['1', '2', '3'])) {
            $errors[] = "Prodi tidak valid";
        }

        if ($this->angkatan < 2000 || $this->angkatan > 2099) {
            $errors[] = "Angkatan tidak valid";
        }

        if (!in_array($this->keterangan, ['aktif', 'tidak'])) {
            $errors[] = "Keterangan tidak valid";
        }

        return $errors;
    }
}
?>