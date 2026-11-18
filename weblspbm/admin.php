<?php
// 1. MEMULAI SESSION
session_start();

// 2. CEK STATUS LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// 3. MEMANGGIL KONEKSI DATABASE
include 'koneksi.php';

$pesan = "";
$status = "";

// ========================================================================
// SMART TAB STATE: MENJAGA SUPAYA TAB TIDAK MELOMPAT SETELAH REFRESH
// ========================================================================
$tab_aktif = "umum"; 
if (isset($_GET['tab'])) {
    $tab_aktif = $_GET['tab'];
} elseif (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit') { $tab_aktif = "skema"; }
    elseif ($_GET['action'] == 'edit_galeri') { $tab_aktif = "galeri"; }
    elseif ($_GET['action'] == 'edit_jadwal') { $tab_aktif = "jadwal"; }
    elseif ($_GET['action'] == 'edit_tuk') { $tab_aktif = "tuk"; }
    elseif ($_GET['action'] == 'edit_asesor') { $tab_aktif = "asesor_tab"; }
    elseif ($_GET['action'] == 'edit_sertifikat') { $tab_aktif = "sertifikat"; }
}

// ========================================================================
// SYSTEM STANDARDIZATION: AUTO-CREATE TABEL DATABASE
// ========================================================================
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS admin (id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(50) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL)");
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS statistik (id INT PRIMARY KEY AUTO_INCREMENT, total_asesi VARCHAR(20), total_skema VARCHAR(20), total_tuk VARCHAR(20), total_asesor VARCHAR(20))");
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS pengaturan (id INT PRIMARY KEY AUTO_INCREMENT, nama_web VARCHAR(100), no_wa_admin VARCHAR(20), pesan_default TEXT)");

// STRUKTUR TABEL SKEMA
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS skema (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    kode_skema VARCHAR(50) NOT NULL, 
    nama_skema VARCHAR(100) NOT NULL, 
    jenis_skema VARCHAR(50) NULL,
    deskripsi TEXT NOT NULL, 
    unit_kompetensi TEXT NULL, 
    icon VARCHAR(50) NULL
)");

$cek_kolom_jenis = mysqli_query($koneksi, "SHOW COLUMNS FROM skema LIKE 'jenis_skema'");
if (mysqli_num_rows($cek_kolom_jenis) == 0) {
    mysqli_query($koneksi, "ALTER TABLE skema ADD COLUMN jenis_skema VARCHAR(50) AFTER nama_skema");
}

mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS galeri (id INT PRIMARY KEY AUTO_INCREMENT, judul VARCHAR(150) NOT NULL, deskripsi TEXT, foto VARCHAR(255) NOT NULL)");
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS jadwal (id INT PRIMARY KEY AUTO_INCREMENT, nama_kegiatan VARCHAR(150) NOT NULL, tanggal VARCHAR(50) NOT NULL, waktu VARCHAR(30) NOT NULL, lokasi VARCHAR(150) NOT NULL, keterangan VARCHAR(255))");
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS tuk (id INT PRIMARY KEY AUTO_INCREMENT, nama_tuk VARCHAR(100) NOT NULL, jenis_tuk VARCHAR(50) NOT NULL, alamat TEXT NOT NULL, kontak VARCHAR(50) NOT NULL)");

mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS asesor (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    met_asesor VARCHAR(50) NOT NULL UNIQUE, 
    nama_asesor VARCHAR(100) NOT NULL, 
    bidang_keahlian VARCHAR(150) NOT NULL, 
    status_asesor ENUM('Aktif', 'Masa Perpanjangan', 'Tidak Aktif') DEFAULT 'Aktif'
)");

// STRUKTUR TABEL SERTIFIKAT
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS sertifikat (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nik VARCHAR(50) NOT NULL, 
    nama_asesi VARCHAR(100) NOT NULL, 
    no_sertifikat VARCHAR(50) NOT NULL, 
    skema VARCHAR(100) NOT NULL, 
    bidang VARCHAR(100) NULL,
    status ENUM('Aktif', 'Kedaluwarsa', 'Dalam Proses') DEFAULT 'Dalam Proses', 
    tanggal_expired DATE,
    file_sertifikat TEXT NULL
)");

$cek_kolom_file = mysqli_query($koneksi, "SHOW COLUMNS FROM sertifikat LIKE 'file_sertifikat'");
if (mysqli_num_rows($cek_kolom_file) == 0) {
    mysqli_query($koneksi, "ALTER TABLE sertifikat ADD COLUMN file_sertifikat TEXT NULL AFTER tanggal_expired");
}

$cek_kolom_bidang = mysqli_query($koneksi, "SHOW COLUMNS FROM sertifikat LIKE 'bidang'");
if (mysqli_num_rows($cek_kolom_bidang) == 0) {
    mysqli_query($koneksi, "ALTER TABLE sertifikat ADD COLUMN bidang VARCHAR(100) NULL AFTER skema");
}

// Cek & Suntik Baris Data Awal Secara Aman
$cek_row_stats = mysqli_query($koneksi, "SELECT * FROM statistik WHERE id=1");
if ($cek_row_stats && mysqli_num_rows($cek_row_stats) == 0) {
    mysqli_query($koneksi, "INSERT INTO statistik (id, total_asesi, total_skema, total_tuk, total_asesor) VALUES (1, '228.095', '85', '1.529', '10.230')");
}
$cek_row_setting = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1");
if ($cek_row_setting && mysqli_num_rows($cek_row_setting) == 0) {
    mysqli_query($koneksi, "INSERT INTO pengaturan (id, nama_web, no_wa_admin, pesan_default) VALUES (1, 'LSP Bank Mandiri', '6281234567890', 'Halo Admin LSP Bank Mandiri...')");
}

if (isset($_GET['status']) && isset($_GET['pesan']) && empty($pesan)) {
    $status = $_GET['status']; $pesan  = $_GET['pesan'];
}

// ========================================================================
// PROSES DATA: HANDLING KONTROLLER BACKEND OPERASIONAL CMS
// ========================================================================
if (isset($_POST['update_pengaturan'])) {
    $nama_web = mysqli_real_escape_string($koneksi, $_POST['nama_web']);
    $no_wa_admin = mysqli_real_escape_string($koneksi, $_POST['no_wa_admin']);
    $pesan_default = mysqli_real_escape_string($koneksi, $_POST['pesan_default']);
    if(mysqli_query($koneksi, "UPDATE pengaturan SET nama_web='$nama_web', no_wa_admin='$no_wa_admin', pesan_default='$pesan_default' WHERE id=1")) { $pesan = "Pengaturan sukses!"; $status = "success"; }
}
if (isset($_POST['update_statistik'])) {
    $total_asesi = $_POST['total_asesi']; $total_skema = $_POST['total_skema']; $total_tuk = $_POST['total_tuk']; $total_asesor = $_POST['total_asesor'];
    if(mysqli_query($koneksi, "UPDATE statistik SET total_asesi='$total_asesi', total_skema='$total_skema', total_tuk='$total_tuk', total_asesor='$total_asesor' WHERE id=1")) { $pesan = "Statistik sukses!"; $status = "success"; $tab_aktif = "stats"; }
}

// PROSES SKEMA
if (isset($_POST['add_skema'])) {
    $kode_skema  = mysqli_real_escape_string($koneksi, trim($_POST['kode_skema']));
    $nama_skema  = mysqli_real_escape_string($koneksi, $_POST['nama_skema']); 
    $jenis_skema = mysqli_real_escape_string($koneksi, $_POST['jenis_skema']); 
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $unit_input = trim($_POST['unit_kompetensi']);
    $lines = explode("\n", str_replace("\r", "", $unit_input));
    $unit_array = array();
    
    foreach ($lines as $line) {
        if (!empty(trim($line))) {
            $part = explode("|", $line);
            $kd_u  = isset($part[0]) ? trim($part[0]) : '';
            $jdl_u = isset($part[1]) ? trim($part[1]) : '';
            if (!empty($kd_u) && !empty($jdl_u)) {
                $unit_array[] = array("kode" => $kd_u, "judul" => $jdl_u);
            }
        }
    }
    $unit_kompetensi_json = mysqli_real_escape_string($koneksi, json_encode($unit_array));

    $query_add = "INSERT INTO skema (kode_skema, nama_skema, jenis_skema, deskripsi, unit_kompetensi) 
                  VALUES ('$kode_skema', '$nama_skema', '$jenis_skema', '$deskripsi', '$unit_kompetensi_json')";

    if(mysqli_query($koneksi, $query_add)) { 
        echo "<script>window.location.href='admin.php?status=success&tab=skema&pesan=Skema+baru+berhasil+diterbitkan';</script>"; 
        exit; 
    }
}

if (isset($_POST['update_skema'])) {
    $id         = $_POST['id']; 
    $kode_skema = mysqli_real_escape_string($koneksi, trim($_POST['kode_skema']));
    $nama_skema = mysqli_real_escape_string($koneksi, $_POST['nama_skema']); 
    $jenis_skema = mysqli_real_escape_string($koneksi, $_POST['jenis_skema']); 
    $deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $unit_input = trim($_POST['unit_kompetensi']);
    $lines = explode("\n", str_replace("\r", "", $unit_input));
    $unit_array = array();
    
    foreach ($lines as $line) {
        if (!empty(trim($line))) {
            $part = explode("|", $line);
            $kd_u  = isset($part[0]) ? trim($part[0]) : '';
            $jdl_u = isset($part[1]) ? trim($part[1]) : '';
            if (!empty($kd_u) && !empty($jdl_u)) {
                $unit_array[] = array("kode" => $kd_u, "judul" => $jdl_u);
            }
        }
    }
    $unit_kompetensi_json = mysqli_real_escape_string($koneksi, json_encode($unit_array));

    $query_update = "UPDATE skema SET 
                        kode_skema='$kode_skema', 
                        nama_skema='$nama_skema', 
                        jenis_skema='$jenis_skema', 
                        deskripsi='$deskripsi', 
                        unit_kompetensi='$unit_kompetensi_json' 
                     WHERE id=$id";

    if(mysqli_query($koneksi, $query_update)) { 
        echo "<script>window.location.href='admin.php?status=success&tab=skema&pesan=Skema+dan+unit+berhasil+diperbarui';</script>"; 
        exit; 
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if(mysqli_query($koneksi, "DELETE FROM skema WHERE id=" . $_GET['id'])) { echo "<script>window.location.href='admin.php?status=success&tab=skema&pesan=Skema+dihapus';</script>"; exit; }
}

// PROSES GALERI
if (isset($_POST['add_galeri'])) {
    $judul_galeri = mysqli_real_escape_string($koneksi, $_POST['judul_galeri']); $deskripsi_galeri = mysqli_real_escape_string($koneksi, $_POST['deskripsi_galeri']);
    $file_nama = $_FILES['foto_galeri']['name']; $file_tmp = $_FILES['foto_galeri']['tmp_name'];
    if ($_FILES['foto_galeri']['error'] === 0) {
        $ekstensi_file = strtolower(pathinfo($file_nama, PATHINFO_EXTENSION));
        if (in_array($ekstensi_file, ['jpg', 'jpeg', 'png', 'webp'])) {
            $file_nama_baru = uniqid() . '.' . $ekstensi_file;
            if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
            if (move_uploaded_file($file_tmp, 'uploads/' . $file_nama_baru)) {
                mysqli_query($koneksi, "INSERT INTO galeri (judul, deskripsi, foto) VALUES ('$judul_galeri', '$deskripsi_galeri', '$file_nama_baru')");
                $pesan = "Foto terunggah!"; $status = "success"; $tab_aktif = "galeri";
            }
        }
    }
}
if (isset($_POST['update_galeri'])) {
    $id = $_POST['id']; $judul_galeri = mysqli_real_escape_string($koneksi, $_POST['judul_galeri']); $deskripsi_galeri = mysqli_real_escape_string($koneksi, $_POST['deskripsi_galeri']);
    if (!empty($_FILES['foto_galeri']['name'])) {
        $file_nama = $_FILES['foto_galeri']['name']; $file_tmp = $_FILES['foto_galeri']['tmp_name'];
        $ekstensi_file = strtolower(pathinfo($file_nama, PATHINFO_EXTENSION));
        if (in_array($ekstensi_file, ['jpg', 'jpeg', 'png', 'webp'])) {
            $file_nama_baru = uniqid() . '.' . $ekstensi_file;
            $d_foto = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT foto FROM galeri WHERE id=$id"));
            if (file_exists('uploads/' . $d_foto['foto'])) { unlink('uploads/' . $d_foto['foto']); }
            if (move_uploaded_file($file_tmp, 'uploads/' . $file_nama_baru)) { 
                mysqli_query($koneksi, "UPDATE galeri SET judul='$judul_galeri', deskripsi='$deskripsi_galeri', foto='$file_nama_baru' WHERE id=$id"); 
            }
        }
    } else { mysqli_query($koneksi, "UPDATE galeri SET judul='$judul_galeri', deskripsi='$deskripsi_galeri' WHERE id=$id"); }
    echo "<script>window.location.href='admin.php?status=success&tab=galeri&pesan=Galeri+diperbarui';</script>"; exit;
}
if (isset($_GET['action']) && $_GET['action'] == 'delete_galeri' && isset($_GET['id'])) {
    $id_galeri = $_GET['id']; $d_f = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT foto FROM galeri WHERE id=$id_galeri"));
    if (file_exists('uploads/' . $d_f['foto'])) { unlink('uploads/' . $d_f['foto']); }
    mysqli_query($koneksi, "DELETE FROM galeri WHERE id=$id_galeri");
    echo "<script>window.location.href='admin.php?status=success&tab=galeri&pesan=Foto+dihapus';</script>"; exit;
}

// PROSES JADWAL KEGIATAN
if (isset($_POST['add_jadwal'])) {
    $nama_kegiatan = mysqli_real_escape_string($koneksi, $_POST['nama_kegiatan']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $waktu = mysqli_real_escape_string($koneksi, $_POST['waktu']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $insert = mysqli_query($koneksi, "INSERT INTO jadwal (nama_kegiatan, tanggal, waktu, lokasi, keterangan) VALUES ('$nama_kegiatan', '$tanggal', '$waktu', '$lokasi', '$keterangan')");
    if ($insert) { $pesan = "Jadwal kegiatan baru sukses ditambahkan!"; $status = "success"; $tab_aktif = "jadwal"; }
}
if (isset($_POST['update_jadwal'])) {
    $id = $_POST['id']; $nama_kegiatan = mysqli_real_escape_string($koneksi, $_POST['nama_kegiatan']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']); $waktu = mysqli_real_escape_string($koneksi, $_POST['waktu']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']); $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    if(mysqli_query($koneksi, "UPDATE jadwal SET nama_kegiatan='$nama_kegiatan', tanggal='$tanggal', waktu='$waktu', lokasi='$lokasi', keterangan='$keterangan' WHERE id=$id")) {
        echo "<script>window.location.href='admin.php?status=success&tab=jadwal&pesan=Jadwal+kegiatan+berhasil+diperbarui';</script>"; exit;
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete_jadwal' && isset($_GET['id'])) {
    mysqli_query($koneksi, "DELETE FROM jadwal WHERE id=" . $_GET['id']);
    echo "<script>window.location.href='admin.php?status=success&tab=jadwal&pesan=Jadwal+berhasil+dihapus';</script>"; exit;
}

// PROSES TUK
if (isset($_POST['add_tuk'])) {
    $nama_tuk = mysqli_real_escape_string($koneksi, $_POST['nama_tuk']); $jenis_tuk = mysqli_real_escape_string($koneksi, $_POST['jenis_tuk']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']); $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $insert_tuk = mysqli_query($koneksi, "INSERT INTO tuk (nama_tuk, jenis_tuk, alamat, kontak) VALUES ('$nama_tuk', '$jenis_tuk', '$alamat', '$kontak')");
    if ($insert_tuk) { $pesan = "Lokasi TUK berhasil ditambahkan!"; $status = "success"; $tab_aktif = "tuk"; }
}
if (isset($_POST['update_tuk'])) {
    $id = $_POST['id']; $nama_tuk = mysqli_real_escape_string($koneksi, $_POST['nama_tuk']); $jenis_tuk = mysqli_real_escape_string($koneksi, $_POST['jenis_tuk']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']); $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    if(mysqli_query($koneksi, "UPDATE tuk SET nama_tuk='$nama_tuk', jenis_tuk='$jenis_tuk', alamat='$alamat', kontak='$kontak' WHERE id=$id")) {
        echo "<script>window.location.href='admin.php?status=success&tab=tuk&pesan=Data+TUK+berhasil+diperbarui';</script>"; exit;
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete_tuk' && isset($_GET['id'])) {
    mysqli_query($koneksi, "DELETE FROM tuk WHERE id=" . $_GET['id']);
    echo "<script>window.location.href='admin.php?status=success&tab=tuk&pesan=Data+TUK+berhasil+dihapus';</script>"; exit;
}

// PROSES ASESOR
if (isset($_POST['add_asesor'])) {
    $met_asesor = mysqli_real_escape_string($koneksi, trim($_POST['met_asesor']));
    $nama_asesor = mysqli_real_escape_string($koneksi, trim($_POST['nama_asesor']));
    $bidang_keahlian = mysqli_real_escape_string($koneksi, trim($_POST['bidang_keahlian']));
    $status_asesor = mysqli_real_escape_string($koneksi, $_POST['status_asesor']);
    
    $cek_met = mysqli_query($koneksi, "SELECT id FROM asesor WHERE met_asesor = '$met_asesor'");
    if (mysqli_num_rows($cek_met) == 0) {
        $ins_asesor = mysqli_query($koneksi, "INSERT INTO asesor (met_asesor, nama_asesor, bidang_keahlian, status_asesor) VALUES ('$met_asesor', '$nama_asesor', '$bidang_keahlian', '$status_asesor')");
        if ($ins_asesor) { $pesan = "Asesor baru berhasil didaftarkan!"; $status = "success"; $tab_aktif = "asesor_tab"; }
    } else { $pesan = "Gagal! No. Reg MET Asesor sudah terdaftar."; $status = "danger"; $tab_aktif = "asesor_tab"; }
}
if (isset($_POST['update_asesor'])) {
    $id = $_POST['id']; $met_asesor = mysqli_real_escape_string($koneksi, trim($_POST['met_asesor']));
    $nama_asesor = mysqli_real_escape_string($koneksi, trim($_POST['nama_asesor'])); $bidang_keahlian = mysqli_real_escape_string($koneksi, trim($_POST['bidang_keahlian']));
    $status_asesor = mysqli_real_escape_string($koneksi, $_POST['status_asesor']);
    if(mysqli_query($koneksi, "UPDATE asesor SET met_asesor='$met_asesor', nama_asesor='$nama_asesor', bidang_keahlian='$bidang_keahlian', status_asesor='$status_asesor' WHERE id=$id")) {
        echo "<script>window.location.href='admin.php?status=success&tab=asesor_tab&pesan=Data+asesor+berhasil+diperbarui';</script>"; exit;
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete_asesor' && isset($_GET['id'])) {
    mysqli_query($koneksi, "DELETE FROM asesor WHERE id=" . $_GET['id']);
    echo "<script>window.location.href='admin.php?status=success&tab=asesor_tab&pesan=Data+asesor+berhasil+dihapus';</script>"; exit;
}

$edit_asesor_data = (isset($_GET['action']) && $_GET['action'] == 'edit_asesor') ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM asesor WHERE id=" . $_GET['id'])) : null;

// ========================================================================
// PROSES G: MANAJEMEN SERTIFIKAT ASESI (MODE INPUT LINK URL DOKUMEN)
// ========================================================================

// 1. INPUT MANUAL SERTIFIKAT
if (isset($_POST['add_sertifikat'])) {
    $nik = mysqli_real_escape_string($koneksi, trim($_POST['nik'])); // Disimpan sebagai Nomor Blanko
    $nama_asesi = mysqli_real_escape_string($koneksi, trim($_POST['nama_asesi']));
    $no_sertifikat = mysqli_real_escape_string($koneksi, trim($_POST['no_sertifikat']));
    $skema = mysqli_real_escape_string($koneksi, trim($_POST['skema']));
    $bidang = mysqli_real_escape_string($koneksi, trim($_POST['bidang']));
    $status_sertif = mysqli_real_escape_string($koneksi, trim($_POST['status_sertif']));
    $tanggal_expired = mysqli_real_escape_string($koneksi, trim($_POST['tanggal_expired']));
    $link_sertifikat = mysqli_real_escape_string($koneksi, trim($_POST['link_sertifikat']));
    
    $cek_dup = mysqli_query($koneksi, "SELECT id FROM sertifikat WHERE no_sertifikat = '$no_sertifikat'");
    if (mysqli_num_rows($cek_dup) == 0) {
        $ins = mysqli_query($koneksi, "INSERT INTO sertifikat (nik, nama_asesi, no_sertifikat, skema, bidang, status, tanggal_expired, file_sertifikat) VALUES ('$nik', '$nama_asesi', '$no_sertifikat', '$skema', '$bidang', '$status_sertif', '$tanggal_expired', '$link_sertifikat')");
        if ($ins) { $pesan = "Sertifikat baru berhasil diterbitkan!"; $status = "success"; $tab_aktif = "sertifikat"; }
    } else {
        $pesan = "Gagal! Nomor sertifikat tersebut sudah terdaftar di sistem."; $status = "danger"; $tab_aktif = "sertifikat";
    }
}

// 2. UPDATE DATA SERTIFIKAT
if (isset($_POST['update_sertifikat'])) {
    $id = $_POST['id']; 
    $nik = mysqli_real_escape_string($koneksi, trim($_POST['nik'])); // Disimpan sebagai Nomor Blanko
    $nama_asesi = mysqli_real_escape_string($koneksi, trim($_POST['nama_asesi'])); 
    $no_sertifikat = mysqli_real_escape_string($koneksi, trim($_POST['no_sertifikat']));
    $skema = mysqli_real_escape_string($koneksi, trim($_POST['skema'])); 
    $bidang = mysqli_real_escape_string($koneksi, trim($_POST['bidang'])); 
    $status_sertif = mysqli_real_escape_string($koneksi, trim($_POST['status_sertif']));
    $tanggal_expired = mysqli_real_escape_string($koneksi, trim($_POST['tanggal_expired']));
    $link_sertifikat = mysqli_real_escape_string($koneksi, trim($_POST['link_sertifikat']));
    
    if(mysqli_query($koneksi, "UPDATE sertifikat SET nik='$nik', nama_asesi='$nama_asesi', no_sertifikat='$no_sertifikat', skema='$skema', bidang='$bidang', status='$status_sertif', tanggal_expired='$tanggal_expired', file_sertifikat='$link_sertifikat' WHERE id=$id")) {
        echo "<script>window.location.href='admin.php?status=success&tab=sertifikat&pesan=Data+sertifikat+berhasil+diperbarui';</script>"; exit;
    }
}

// 3. HAPUS DATA SERTIFIKAT (SATUAN)
if (isset($_GET['action']) && $_GET['action'] == 'delete_sertifikat' && isset($_GET['id'])) {
    $id_del = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM sertifikat WHERE id=" . $id_del);
    echo "<script>window.location.href='admin.php?status=success&tab=sertifikat&pesan=Sertifikat+berhasil+dihapus';</script>"; exit;
}

// 3b. HAPUS SEMUA DATA SERTIFIKAT (BULK DELETE)
if (isset($_POST['delete_all_sertifikat'])) {
    if (mysqli_query($koneksi, "TRUNCATE TABLE sertifikat")) {
        echo "<script>window.location.href='admin.php?status=success&tab=sertifikat&pesan=Semua+data+sertifikat+berhasil+dihapus+secara+permanen';</script>";
        exit;
    } else {
        $pesan = "Gagal mengosongkan tabel sertifikat."; $status = "danger"; $tab_aktif = "sertifikat";
    }
}

// 4. BULK ENGINE DATA DARI EXCEL/CSV (MENDUKUNG KOLOM H UNTUK LINK TAUTAN DOKUMEN)
if (isset($_POST['import_sertifikat_bulk'])) {
    $file_nama = $_FILES['file_csv']['name']; 
    $file_tmp  = $_FILES['file_csv']['tmp_name'];
    $file_ext  = strtolower(pathinfo($file_nama, PATHINFO_EXTENSION));

    if ($file_ext === 'csv') {
        $delimiter = ",";
        $file_detect = fopen($file_tmp, "r");
        if ($file_detect) {
            $first_line = fgets($file_detect);
            fclose($file_detect);
            if (substr_count($first_line, ';') > substr_count($first_line, ',')) {
                $delimiter = ";";
            }
        }

        if (($handle = fopen($file_tmp, "r")) !== FALSE) {
            fgetcsv($handle, 1000, $delimiter); 
            $jumlah_sukses = 0; 
            $jumlah_gagal = 0;

            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                if (count($data) >= 4) {
                    $nik_raw         = trim($data[0]);
                    $nik             = mysqli_real_escape_string($koneksi, str_replace("\xEF\xBB\xBF", "", $nik_raw));
                    $nama_asesi      = mysqli_real_escape_string($koneksi, trim($data[1]));
                    $no_sertifikat   = mysqli_real_escape_string($koneksi, trim($data[2]));
                    $skema           = mysqli_real_escape_string($koneksi, trim($data[3]));
                    $bidang          = isset($data[4]) ? mysqli_real_escape_string($koneksi, trim($data[4])) : '';
                    $status_sertif   = isset($data[5]) && !empty(trim($data[5])) ? mysqli_real_escape_string($koneksi, trim($data[5])) : 'Aktif'; 
                    $tanggal_raw     = isset($data[6]) ? str_replace('/', '-', trim($data[6])) : '';
                    $tanggal_expired = !empty($tanggal_raw) ? date('Y-m-d', strtotime($tanggal_raw)) : date('Y-m-d'); 
                    $link_dokumen    = isset($data[7]) ? mysqli_real_escape_string($koneksi, trim($data[7])) : '';

                    if (!empty($nik) && !empty($no_sertifikat)) {
                        $cek_duplikat = mysqli_query($koneksi, "SELECT id FROM sertifikat WHERE no_sertifikat = '$no_sertifikat'");
                        
                        if (mysqli_num_rows($cek_duplikat) == 0) {
                            $insert = mysqli_query($koneksi, "INSERT INTO sertifikat (nik, nama_asesi, no_sertifikat, skema, bidang, status, tanggal_expired, file_sertifikat) VALUES ('$nik', '$nama_asesi', '$no_sertifikat', '$skema', '$bidang', '$status_sertif', '$tanggal_expired', '$link_dokumen')");
                            if ($insert) { $jumlah_sukses++; } else { $jumlah_gagal++; }
                        } else {
                            mysqli_query($koneksi, "UPDATE sertifikat SET nik='$nik', nama_asesi='$nama_asesi', skema='$skema', bidang='$bidang', status='$status_sertif', tanggal_expired='$tanggal_expired', file_sertifikat='$link_dokumen' WHERE no_sertifikat='$no_sertifikat'");
                            $jumlah_sukses++;
                        }
                    }
                }
            }
            fclose($handle);
            $pesan = "Bulk Import Selesai! <strong>$jumlah_sukses data berhasil</strong> diproses ke database.";
            if ($jumlah_gagal > 0) { $pesan .= " ($jumlah_gagal data error/gagal)."; }
            $status = "success"; $tab_aktif = "sertifikat";
        } else { $pesan = "Gagal membuka file dokumen."; $status = "danger"; $tab_aktif = "sertifikat"; }
    } else { $pesan = "Format file tidak valid! Wajib menggunakan file ekstensi .csv."; $status = "danger"; $tab_aktif = "sertifikat"; }
}

// PROSES ADMIN USER
if (isset($_POST['add_admin'])) {
    $username_baru = mysqli_real_escape_string($koneksi, $_POST['username_baru']); $password_baru = trim($_POST['password_baru']);
    if (!empty($username_baru) && !empty($password_baru)) {
        $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);
        $insert_admin = mysqli_query($koneksi, "INSERT INTO admin (username, password) VALUES ('$username_baru', '$password_hashed')");
        if($insert_admin) { $pesan = "Admin baru sukses didaftarkan dengan enkripsi Bcrypt!"; $status = "success"; $tab_aktif = "admin"; }
        else { $pesan = "Gagal mendaftarkan admin baru (Username sudah ada)."; $status = "danger"; $tab_aktif = "admin"; }
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete_admin' && isset($_GET['id'])) {
    $id_adm = $_GET['id'];
    $check_adm = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT username FROM admin WHERE id=$id_adm"));
    if($check_adm['username'] != $_SESSION['admin_username']){
        mysqli_query($koneksi, "DELETE FROM admin WHERE id=$id_adm");
        echo "<script>window.location.href='admin.php?status=success&tab=admin&pesan=Akun+admin+berhasil+dihapus';</script>"; exit;
    }
}

// SINKRONISASI DATA VIEW UNTUK FORM EDIT
$data_setting = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1"));
$data_stats = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM statistik WHERE id=1"));
$edit_data = (isset($_GET['action']) && $_GET['action'] == 'edit') ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM skema WHERE id=" . $_GET['id'])) : null;
$edit_galeri_data = (isset($_GET['action']) && $_GET['action'] == 'edit_galeri') ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM galeri WHERE id=" . $_GET['id'])) : null;
$edit_jadwal_data = (isset($_GET['action']) && $_GET['action'] == 'edit_jadwal') ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM jadwal WHERE id=" . $_GET['id'])) : null;
$edit_tuk_data = (isset($_GET['action']) && $_GET['action'] == 'edit_tuk') ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tuk WHERE id=" . $_GET['id'])) : null;
$edit_sertifikat_data = (isset($_GET['action']) && $_GET['action'] == 'edit_sertifikat') ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM sertifikat WHERE id=" . $_GET['id'])) : null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Panel Admin - <?php echo htmlspecialchars($data_setting['nama_web'] ?? 'LSP'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --mandiri-blue: #003D79; } body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .navbar-admin { background-color: var(--mandiri-blue) !important; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.04); margin-bottom: 25px; }
        .nav-pills .nav-link.active { background-color: var(--mandiri-blue) !important; } 
        .nav-pills .nav-link { color: var(--mandiri-blue); font-weight: 500; text-align: left; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin mb-4">
        <div class="container">
            <span class="navbar-brand fw-bold mb-0 h1">Panel Manajemen LSP Bank Mandiri</span>
            <div>
                <span class="text-light small me-3 d-none d-md-inline">User: <strong><?php echo $_SESSION['admin_username']; ?></strong></span>
                <a href="index.php" class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold me-2" target="_blank">Lihat Web Utama</a>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <?php if(!empty($pesan)) { ?>
            <div class="alert alert-<?php echo $status; ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <?php echo $pesan; ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-12 col-lg-3 mb-4">
                <div class="card card-custom p-3">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                        <button class="nav-link <?php echo ($tab_aktif == 'umum') ? 'active' : ''; ?>" id="tab-umum" data-bs-toggle="pill" data-bs-target="#content-umum" type="button" role="tab"><i class="fas fa-sliders-h me-2"></i> Pengaturan Umum</button>
                        <button class="nav-link <?php echo ($tab_aktif == 'stats') ? 'active' : ''; ?>" id="tab-stats" data-bs-toggle="pill" data-bs-target="#content-stats" type="button" role="tab"><i class="fas fa-chart-line me-2"></i> Statistik Capaian</button>
                        <button class="nav-link <?php echo ($tab_aktif == 'skema') ? 'active' : ''; ?>" id="tab-skema" data-bs-toggle="pill" data-bs-target="#content-skema" type="button" role="tab"><i class="fas fa-award me-2"></i> Skema Sertifikasi</button>
                        <button class="nav-link <?php echo ($tab_aktif == 'galeri') ? 'active' : ''; ?>" id="tab-galeri" data-bs-toggle="pill" data-bs-target="#content-galeri" type="button" role="tab"><i class="fas fa-images me-2"></i> Dokumentasi Kegiatan</button>
                        <button class="nav-link <?php echo ($tab_aktif == 'jadwal') ? 'active' : ''; ?>" id="tab-jadwal" data-bs-toggle="pill" data-bs-target="#content-jadwal" type="button" role="tab"><i class="fas fa-calendar-alt me-2"></i> Jadwal Kegiatan</button>
                        <button class="nav-link <?php echo ($tab_aktif == 'tuk') ? 'active' : ''; ?>" id="tab-tuk" data-bs-toggle="pill" data-bs-target="#content-tuk" type="button" role="tab"><i class="fas fa-map-marker-alt me-2"></i> Manajemen TUK</button>
                        <button class="nav-link <?php echo ($tab_aktif == 'sertifikat') ? 'active' : ''; ?>" id="tab-sertifikat" data-bs-toggle="pill" data-bs-target="#content-sertifikat" type="button" role="tab"><i class="fas fa-id-card-alt me-2"></i> Sertifikat Asesi</button>
                        <button class="nav-link <?php echo ($tab_aktif == 'asesor_tab') ? 'active' : ''; ?>" id="tab-asesor" data-bs-toggle="pill" data-bs-target="#content-asesor" type="button" role="tab"><i class="fas fa-user-shield me-2"></i> Manajemen Asesor</button>
                        <button class="nav-link <?php echo ($tab_aktif == 'admin') ? 'active' : ''; ?>" id="tab-admin" data-bs-toggle="pill" data-bs-target="#content-admin" type="button" role="tab"><i class="fas fa-user-cog me-2"></i> Manajemen Admin</button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-9">
                <div class="tab-content">
                    
                    <div class="tab-pane fade <?php echo ($tab_aktif == 'umum') ? 'show active' : ''; ?>" id="content-umum" role="tabpanel">
                        <div class="card card-custom"><div class="card-header bg-white py-3 fw-bold text-primary">Pengaturan Profile Website</div><div class="card-body"><form method="POST"><div class="mb-3"><label class="form-label">Nama Lembaga</label><input type="text" name="nama_web" class="form-control" value="<?php echo htmlspecialchars($data_setting['nama_web'] ?? ''); ?>" required></div><div class="mb-3"><label class="form-label">WhatsApp Admin</label><input type="number" name="no_wa_admin" class="form-control" value="<?php echo htmlspecialchars($data_setting['no_wa_admin'] ?? ''); ?>" required></div><div class="mb-3"><label class="form-label">Pesan Pembuka</label><textarea name="pesan_default" class="form-control" rows="3" required><?php echo htmlspecialchars($data_setting['pesan_default'] ?? ''); ?></textarea></div><button type="submit" name="update_pengaturan" class="btn btn-primary px-4">Simpan</button></form></div></div>
                    </div>
                    
                    <div class="tab-pane fade <?php echo ($tab_aktif == 'stats') ? 'show active' : ''; ?>" id="content-stats" role="tabpanel">
                        <div class="card card-custom"><div class="card-header bg-white py-3 fw-bold text-primary">Perbarui Angka Indikator Kinerja</div><div class="card-body"><form method="POST"><div class="row"><div class="col-md-6 mb-3"><label class="form-label">Sertifikasi</label><input type="text" name="total_asesi" class="form-control" value="<?php echo htmlspecialchars($data_stats['total_asesi'] ?? ''); ?>" required></div><div class="col-md-6 mb-3"><label class="form-label">Skema</label><input type="text" name="total_skema" class="form-control" value="<?php echo htmlspecialchars($data_stats['total_skema'] ?? ''); ?>" required></div><div class="col-md-6 mb-3"><label class="form-label">TUK</label><input type="text" name="total_tuk" class="form-control" value="<?php echo htmlspecialchars($data_stats['total_tuk'] ?? ''); ?>" required></div><div class="col-md-6 mb-3"><label class="form-label">Asesor</label><input type="text" name="total_asesor" class="form-control" value="<?php echo htmlspecialchars($data_stats['total_asesor'] ?? ''); ?>" required></div></div><button type="submit" name="update_statistik" class="btn btn-primary px-4">Perbarui</button></form></div></div>
                    </div>
                    
                    <div class="tab-pane fade <?php echo ($tab_aktif == 'skema') ? 'show active' : ''; ?>" id="content-skema" role="tabpanel">
                        <?php if ($edit_data != null) { 
                            $textarea_edit_value = "";
                            $existing_units = json_decode($edit_data['unit_kompetensi'], true);
                            if(!empty($existing_units) && is_array($existing_units)){
                                foreach($existing_units as $u){
                                    $textarea_edit_value .= $u['kode'] . " | " . $u['judul'] . "\n";
                                }
                            }
                        ?>
                            <div class="card card-custom border-warning">
                                <div class="card-header bg-warning py-3 fw-bold text-dark">Edit Skema Sertifikasi</div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Kode Skema</label>
                                            <input type="text" name="kode_skema" class="form-control" value="<?php echo htmlspecialchars($edit_data['kode_skema']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Nama Skema</label>
                                            <input type="text" name="nama_skema" class="form-control" value="<?php echo htmlspecialchars($edit_data['nama_skema']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Jenis Skema</label>
                                            <select name="jenis_skema" class="form-select" required>
                                                <option value="KKNI" <?php echo ($edit_data['jenis_skema'] == 'KKNI') ? 'selected' : ''; ?>>KKNI</option>
                                                <option value="Okupasi" <?php echo ($edit_data['jenis_skema'] == 'Okupasi') ? 'selected' : ''; ?>>Okupasi</option>
                                                <option value="Klaster" <?php echo ($edit_data['jenis_skema'] == 'Klaster') ? 'selected' : ''; ?>>Klaster</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Deskripsi</label>
                                            <textarea name="deskripsi" class="form-control" rows="3" required><?php echo htmlspecialchars($edit_data['deskripsi']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-primary">Daftar Unit Kompetensi</label>
                                            <div class="form-text text-muted small mb-2">Aturan tulis: <strong>Kode Unit | Judul Unit</strong> (Gunakan pemisah <code>|</code> satu unit per baris).</div>
                                            <textarea name="unit_kompetensi" class="form-control font-monospace" rows="6" placeholder="Contoh:&#10;M.642000.001.01 | Mengimplementasikan Manajemen Risiko"><?php echo htmlspecialchars($textarea_edit_value); ?></textarea>
                                        </div>
                                        <button type="submit" name="update_skema" class="btn btn-warning text-dark fw-bold">Simpan</button>
                                        <a href="admin.php?tab=skema" class="btn btn-secondary ms-2">Batal</a>
                                    </form>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="card card-custom mb-4">
                                <div class="card-header bg-white py-3 fw-bold text-primary">Tambah Skema Sertifikasi</div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Kode Skema</label>
                                            <input type="text" name="kode_skema" class="form-control" placeholder="SKM-00/LSP-BM/000/2026" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Skema</label>
                                            <input type="text" name="nama_skema" class="form-control" placeholder="Skema Kompetensi X" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jenis Skema</label>
                                            <select name="jenis_skema" class="form-select" required>
                                                <option value="" disabled selected>- Pilih Jenis Skema -</option>
                                                <option value="KKNI">KKNI</option>
                                                <option value="Okupasi">Okupasi</option>
                                                <option value="Klaster">Klaster</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi</label>
                                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="SKKNI Nomor 00 - 2026" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-primary fw-bold">Daftar Unit Kompetensi</label>
                                            <div class="form-text text-muted small mb-2">Aturan tulis: <strong>Kode Unit | Judul Unit</strong> (Gunakan pemisah <code>|</code> satu unit per baris).</div>
                                            <textarea name="unit_kompetensi" class="form-control font-monospace" rows="5" placeholder="M.642000.001.01 | Mengimplementasikan Prosedur Manajemen Risiko Portofolio Perbankan&#10;M.642000.002.01 | Melakukan Analisis Validasi Kelayakan Fasilitas Pembiayaan Retail"></textarea>
                                        </div>
                                        <button type="submit" name="add_skema" class="btn btn-success">Simpan</button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="card card-custom">
                                <div class="card-header bg-white py-3 fw-bold text-dark">Daftar Skema</div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-4" style="width: 15%;">Kode Skema</th>
                                                    <th style="width: 25%;">Nama Skema</th>
                                                    <th style="width: 15%;">Jenis</th>
                                                    <th style="width: 30%;">Deskripsi & Unit</th>
                                                    <th class="text-center" style="width: 15%;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $get_skema = mysqli_query($koneksi, "SELECT * FROM skema ORDER BY id DESC"); 
                                                while($row = mysqli_fetch_assoc($get_skema)) { 
                                                ?>
                                                <tr>
                                                    <td class="px-4 fw-bold text-dark" style="font-family: monospace; font-size: 0.9rem;"><?php echo htmlspecialchars($row['kode_skema']); ?></td>
                                                    <td class="fw-semibold text-primary"><?php echo htmlspecialchars($row['nama_skema']); ?></td>
                                                    <td>
                                                        <span class="badge bg-info text-dark fw-bold"><?php echo htmlspecialchars($row['jenis_skema'] ?? 'N/A'); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted small d-block mb-1"><?php echo htmlspecialchars($row['deskripsi']); ?></span>
                                                        <?php 
                                                        $u_total = json_decode($row['unit_kompetensi'], true); 
                                                        if(!empty($u_total)) { 
                                                            echo '<span class="badge bg-light text-secondary border small"><i class="fas fa-list-ol me-1"></i> '.count($u_total).' Unit Terpasang</span>';
                                                        } 
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="admin.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-outline-warning btn-sm me-1">Edit</a>
                                                        <a href="admin.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <div class="tab-pane fade <?php echo ($tab_aktif == 'galeri') ? 'show active' : ''; ?>" id="content-galeri" role="tabpanel">
                        <?php if ($edit_galeri_data != null) { ?><div class="card card-custom border-warning"><div class="card-header bg-warning text-dark py-3 fw-bold">Edit Dokumentasi Kegiatan</div><div class="card-body"><form method="POST" enctype="multipart/form-data"><input type="hidden" name="id" value="<?php echo $edit_galeri_data['id']; ?>"><div class="mb-3"><label class="form-label">Judul</label><input type="text" name="judul_galeri" class="form-control" value="<?php echo htmlspecialchars($edit_galeri_data['judul']); ?>" required></div><div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="deskripsi_galeri" class="form-control" value="<?php echo htmlspecialchars($edit_galeri_data['deskripsi']); ?>" required></div><div class="mb-3"><img src="uploads/<?php echo $edit_galeri_data['foto']; ?>" style="width:120px;height:70px;object-fit:cover;" class="rounded mb-2"><input type="file" name="foto_galeri" class="form-control"></div><button type="submit" name="update_galeri" class="btn btn-warning text-dark fw-bold">Simpan</button><a href="admin.php?tab=galeri" class="btn btn-secondary ms-2">Batal</a></form></div></div><?php } else { ?><div class="card card-custom mb-4"><div class="card-header bg-white py-3 fw-bold text-primary">Tambah Dokumentasi Kegiatan</div><div class="card-body"><form method="POST" enctype="multipart/form-data"><div class="mb-3"><label class="form-label">Judul Kegiatan</label><input type="text" name="judul_galeri" class="form-control" placeholder="Pelatihan / Uji Sertifikasi" required></div><div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="deskripsi_galeri" class="form-control" placeholder="Batch 1" required></div><div class="mb-3"><label class="form-label">File Foto</label><input type="file" name="foto_galeri" class="form-control" required></div><button type="submit" name="add_galeri" class="btn btn-success">Simpan</button></form></div></div><div class="card card-custom"><div class="card-header bg-white py-3 fw-bold text-dark">Daftar Dokumentasi</div><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th class="px-4">Foto</th><th>Judul</th><th>Keterangan</th><th class="text-center">Aksi</th></tr></thead><tbody><?php $get_g = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY id DESC"); while($g_row = mysqli_fetch_assoc($get_g)) { ?><tr><td class="px-4"><img src="uploads/<?php echo $g_row['foto']; ?>" class="rounded" style="width:70px;height:45px;object-fit:cover;"></td><td class="fw-semibold text-primary"><?php echo htmlspecialchars($g_row['judul']); ?></td><td><span class="text-muted small"><?php echo htmlspecialchars($g_row['deskripsi']); ?></span></td><td class="text-center"><a href="admin.php?action=edit_galeri&id=<?php echo $g_row['id']; ?>" class="btn btn-outline-warning btn-sm me-1">Edit</a><a href="admin.php?action=delete_galeri&id=<?php echo $g_row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</a></td></tr><?php } ?></tbody></table></div></div></div><?php } ?>
                    </div>

                    <div class="tab-pane fade <?php echo ($tab_aktif == 'jadwal') ? 'show active' : ''; ?>" id="content-jadwal" role="tabpanel">
                        <?php if ($edit_jadwal_data != null) { ?>
                        <div class="card card-custom border-warning"><div class="card-header bg-warning text-dark py-3 fw-bold">Edit Jadwal Kegiatan</div><div class="card-body"><form method="POST" action=""><input type="hidden" name="id" value="<?php echo $edit_jadwal_data['id']; ?>"><div class="mb-3"><label class="form-label fw-medium">Nama Agenda / Kegiatan</label><input type="text" name="nama_kegiatan" class="form-control" value="<?php echo htmlspecialchars($edit_jadwal_data['nama_kegiatan']); ?>" required></div><div class="row"><div class="col-md-6 mb-3"><label class="form-label fw-medium">Tanggal</label><input type="text" name="tanggal" class="form-control" value="<?php echo htmlspecialchars($edit_jadwal_data['tanggal']); ?>" required></div><div class="col-md-6 mb-3"><label class="form-label fw-medium">Waktu</label><input type="text" name="waktu" class="form-control" value="<?php echo htmlspecialchars($edit_jadwal_data['waktu']); ?>" required></div></div><div class="mb-3"><label class="form-label fw-medium">Lokasi / Ruangan</label><input type="text" name="lokasi" class="form-control" value="<?php echo htmlspecialchars($edit_jadwal_data['lokasi']); ?>" required></div><div class="mb-3"><label class="form-label fw-medium">Keterangan</label><input type="text" name="keterangan" class="form-control" value="<?php echo htmlspecialchars($edit_jadwal_data['keterangan']); ?>"></div><button type="submit" name="update_jadwal" class="btn btn-warning px-4 text-dark fw-bold">Simpan</button><a href="admin.php?tab=jadwal" class="btn btn-secondary px-3 ms-2">Batal</a></form></div></div>
                        <?php } else { ?>
                        <div class="card card-custom mb-4"><div class="card-header bg-white py-3 fw-bold text-primary">Tambah Jadwal Kegiatan</div><div class="card-body"><form method="POST" action=""><div class="mb-3"><label class="form-label">Nama Agenda / Kegiatan</label><input type="text" name="nama_kegiatan" class="form-control" placeholder="Uji Sertifikasi Skema X" required></div><div class="row"><div class="col-md-6 mb-3"><label class="form-label">Tanggal</label><input type="text" name="tanggal" class="form-control" placeholder="8-9 Mei 2026" required></div><div class="col-md-6 mb-3"><label class="form-label">Waktu</label><input type="text" name="waktu" class="form-control" placeholder="08:00 WIB - Selesai" required></div></div><div class="mb-3"><label class="form-label">Lokasi / Ruangan</label><input type="text" name="lokasi" class="form-control" placeholder="TUK Jakarta Mandiri University" required></div><div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control" placeholder="Online / Offline"></div><button type="submit" name="add_jadwal" class="btn btn-success px-4">Simpan</button></form></div></div><div class="card card-custom"><div class="card-header bg-white py-3 fw-bold text-dark">Daftar Jadwal Kegiatan</div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="px-4">Nama Agenda / Kegiatan</th><th>Waktu & Tanggal</th><th>Lokasi / Ruangan</th><th class="text-center" style="width: 160px;">Aksi</th></tr></thead><tbody><?php $get_j = mysqli_query($koneksi, "SELECT * FROM jadwal ORDER BY id DESC"); if($get_j && mysqli_num_rows($get_j) > 0) { while($j_row = mysqli_fetch_assoc($get_j)) { ?><tr><td class="fw-semibold text-primary px-4"><?php echo htmlspecialchars($j_row['nama_kegiatan']); ?><br><span class="text-muted small font-weight-normal"><?php echo htmlspecialchars($j_row['keterangan']); ?></span></td><td><small class="fw-medium text-dark"><?php echo htmlspecialchars($j_row['tanggal']); ?></small><br><span class="text-muted small"><?php echo htmlspecialchars($j_row['waktu']); ?></span></td><td><span class="badge bg-light text-dark border small"><?php echo htmlspecialchars($j_row['lokasi']); ?></span></td><td class="text-center"><a href="admin.php?action=edit_jadwal&id=<?php echo $j_row['id']; ?>" class="btn btn-outline-warning btn-sm me-1">Edit</a><a href="admin.php?action=delete_jadwal&id=<?php echo $j_row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</a></td></tr><?php } } else { echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Belum ada jadwal.</td></tr>"; } ?></tbody></table></div></div></div>
                        <?php } ?>
                    </div>

                    <div class="tab-pane fade <?php echo ($tab_aktif == 'tuk') ? 'show active' : ''; ?>" id="content-tuk" role="tabpanel">
                        <?php if ($edit_tuk_data != null) { ?>
                        <div class="card card-custom border-warning"><div class="card-header bg-warning text-dark py-3 fw-bold">Edit Tempat Uji Kompetensi (TUK)</div><div class="card-body"><form method="POST" action=""><input type="hidden" name="id" value="<?php echo $edit_tuk_data['id']; ?>"><div class="row"><div class="col-md-6 mb-3"><label class="form-label fw-medium">Nama TUK</label><input type="text" name="nama_tuk" class="form-control" value="<?php echo htmlspecialchars($edit_tuk_data['nama_tuk']); ?>" required></div><div class="col-md-6 mb-3"><label class="form-label fw-medium">Jenis TUK</label><select name="jenis_tuk" class="form-select"><option value="TUK Mandiri" <?php echo ($edit_tuk_data['jenis_tuk'] == 'TUK Mandiri') ? 'selected' : ''; ?>>TUK Mandiri</option><option value="TUK Sewaktu" <?php echo ($edit_tuk_data['jenis_tuk'] == 'TUK Sewaktu') ? 'selected' : ''; ?>>TUK Sewaktu</option><option value="TUK Jarak Jauh" <?php echo ($edit_tuk_data['jenis_tuk'] == 'TUK Jarak Jauh') ? 'selected' : ''; ?>>TUK Jarak Jauh</option></select></div></div><div class="mb-3"><label class="form-label fw-medium">Nomor Telepon / PIC</label><input type="text" name="kontak" class="form-control" value="<?php echo htmlspecialchars($edit_tuk_data['kontak']); ?>"  placeholder="021-12345678" required></div><div class="mb-3"><label class="form-label fw-medium">Alamat</label><textarea name="alamat" class="form-control" rows="3" required><?php echo htmlspecialchars($edit_tuk_data['alamat']); ?></textarea></div><button type="submit" name="update_tuk" class="btn btn-warning px-4 text-dark fw-bold">Simpan</button><a href="admin.php?tab=tuk" class="btn btn-secondary px-3 ms-2">Batal</a></form></div></div>
                        <?php } else { ?>
                        <div class="card card-custom mb-4"><div class="card-header bg-white py-3 fw-bold text-primary">Tambah Tempat Uji Kompetensi</div><div class="card-body"><form method="POST" action=""><div class="row"><div class="col-md-6 mb-3"><label class="form-label">Nama TUK</label><input type="text" name="nama_tuk" class="form-control"  placeholder="Mandiri University" required></div><div class="col-md-6 mb-3"><label class="form-label">Jenis TUK</label><select name="jenis_tuk" class="form-select"><option value="TUK Mandiri">TUK Mandiri</option><option value="TUK Sewaktu">TUK Sewaktu</option><option value="TUK Jarak Jauh">TUK Jarak Jauh</option></select></div></div><div class="mb-3"><label class="form-label">Nomor Telepon / PIC</label><input type="text" name="kontak" class="form-control"  placeholder="021-12345678" required></div><div class="mb-3"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"  placeholder="Jakarta" required></textarea></div><button type="submit" name="add_tuk" class="btn btn-success px-4">Simpan</button></form></div></div><div class="card card-custom"><div class="card-header bg-white py-3 fw-bold text-dark">Daftar Tempat Uji Kompetensi</div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="px-4">Nama TUK / Alamat</th><th>Jenis</th><th>Kontak / PIC</th><th class="text-center">Aksi</th></tr></thead><tbody><?php $get_tuk_list = mysqli_query($koneksi, "SELECT * FROM tuk ORDER BY id DESC"); if($get_tuk_list && mysqli_num_rows($get_tuk_list) > 0) { while($tuk_row = mysqli_fetch_assoc($get_tuk_list)) { ?><tr><td class="px-4"><span class="fw-bold text-primary d-block"><?php echo htmlspecialchars($tuk_row['nama_tuk']); ?></span><small class="text-muted"><?php echo htmlspecialchars($tuk_row['alamat']); ?></small></td><td><span class="badge bg-primary"><?php echo htmlspecialchars($tuk_row['jenis_tuk']); ?></span></td><td><small class="fw-medium text-dark"><?php echo htmlspecialchars($tuk_row['kontak']); ?></small></td><td class="text-center"><a href="admin.php?action=edit_tuk&id=<?php echo $tuk_row['id']; ?>" class="btn btn-outline-warning btn-sm me-1">Edit</a><a href="admin.php?action=delete_tuk&id=<?php echo $tuk_row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus TUK?')">Hapus</a></td></tr><?php } } else { echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Belum ada data TUK.</td></tr>"; } ?></tbody></table></div></div></div>
                        <?php } ?>
                    </div>

                    <!-- TAB MANAJEMEN SERTIFIKAT ASESI -->
                    <div class="tab-pane fade <?php echo ($tab_aktif == 'sertifikat') ? 'show active' : ''; ?>" id="content-sertifikat" role="tabpanel">
                        <?php if ($edit_sertifikat_data != null) { ?>
                        <div class="card card-custom border-warning">
                            <div class="card-header bg-warning text-dark py-3 fw-bold">Edit Validasi Sertifikat Karyawan Asesi</div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?php echo $edit_sertifikat_data['id']; ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label fw-medium">Nomor Blanko</label><input type="text" name="nik" class="form-control" value="<?php echo htmlspecialchars($edit_sertifikat_data['nik']); ?>" required></div>
                                        <div class="col-md-6 mb-3"><label class="form-label fw-medium">Nama Lengkap Pemilik</label><input type="text" name="nama_asesi" class="form-control" value="<?php echo htmlspecialchars($edit_sertifikat_data['nama_asesi']); ?>" required></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label fw-medium">Nomor Sertifikat</label><input type="text" name="no_sertifikat" class="form-control" value="<?php echo htmlspecialchars($edit_sertifikat_data['no_sertifikat']); ?>" required></div>
                                        <div class="col-md-6 mb-3"><label class="form-label fw-medium">Skema Sertifikasi</label><input type="text" name="skema" class="form-control" value="<?php echo htmlspecialchars($edit_sertifikat_data['skema']); ?>" required></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label fw-medium">Bidang / Level</label><input type="text" name="bidang" class="form-control" value="<?php echo htmlspecialchars($edit_sertifikat_data['bidang'] ?? ''); ?>" placeholder="Contoh: Perbankan / Level 3"></div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-medium">Status Sertifikat</label>
                                            <select name="status_sertif" class="form-select">
                                                <option value="Aktif" <?php echo ($edit_sertifikat_data['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                                <option value="Dalam Proses" <?php echo ($edit_sertifikat_data['status'] == 'Dalam Proses') ? 'selected' : ''; ?>>Dalam Proses</option>
                                                <option value="Kedaluwarsa" <?php echo ($edit_sertifikat_data['status'] == 'Kedaluwarsa') ? 'selected' : ''; ?>>Kedaluwarsa</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label fw-medium">Masa Berlaku Akhir (Tanggal Expired)</label><input type="date" name="tanggal_expired" class="form-control" value="<?php echo $edit_sertifikat_data['tanggal_expired']; ?>" required></div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-medium">Link Sertifikat Digital (URL / Cloud)</label>
                                            <input type="url" name="link_sertifikat" class="form-control" value="<?php echo htmlspecialchars($edit_sertifikat_data['file_sertifikat'] ?? ''); ?>" placeholder="https://drive.google.com/file/d/...">
                                        </div>
                                    </div>
                                    <button type="submit" name="update_sertifikat" class="btn btn-warning px-4 text-dark fw-bold">Simpan</button>
                                    <a href="admin.php?tab=sertifikat" class="btn btn-secondary px-3 ms-2">Batal</a>
                                </form>
                            </div>
                        </div>
                        <?php } else { ?>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <div class="card bg-light border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold text-primary mb-2"><i class="fas fa-file-excel me-2"></i>Bulk Import via Excel / CSV</h6>
                                        <p class="text-secondary small mb-3">Unggah seluruh baris data sertifikat asesi secara massal menggunakan dokumen excel/spreadsheet.</p>
                                        <form method="POST" action="" enctype="multipart/form-data">
                                            <div class="input-group">
                                                <input type="file" name="file_csv" class="form-control form-control-sm shadow-none" accept=".csv" required>
                                                <button type="submit" name="import_sertifikat_bulk" class="btn btn-primary btn-sm fw-bold px-3"><i class="fas fa-upload me-1"></i> Proses</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card bg-white border border-warning shadow-sm" style="border-radius: 12px; border-style: dashed !important;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-warning small mb-1"><i class="fas fa-info-circle me-2"></i>Aturan Jaminan Keberhasilan Bulk Import:</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.78rem; line-height: 1.5;">
                                            Urutan kolom Excel wajib: <strong>A: Nomor_Blanko</strong> | <strong>B: Nama_Pemilik</strong> | <strong>C: No_Sertifikat</strong> | <strong>D: Skema</strong> | <strong>E: Bidang</strong> | <strong>F: Status</strong> | <strong>G: Tanggal_Expired</strong> | <strong>H: Link_Dokumen</strong>.<br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-custom mb-4">
                            <div class="card-header bg-white py-3 fw-bold text-primary">Tambah Sertifikat Kompetensi (Manual)</div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label">Nomor Blanko</label><input type="text" name="nik" class="form-control" placeholder="12345678" required></div>
                                        <div class="col-md-6 mb-3"><label class="form-label">Nama Lengkap Pemilik</label><input type="text" name="nama_asesi" class="form-control" placeholder="Nama sesuai pemilik Sertifikat" required></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label">Nomor Sertifikat</label><input type="text" name="no_sertifikat" class="form-control" placeholder="00000 0000 0 0000000 2026" required></div>
                                        <div class="col-md-6 mb-3"><label class="form-label">Skema Sertifikasi</label><input type="text" name="skema" class="form-control" placeholder="Skema X" required></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label">Bidang / Level</label><input type="text" name="bidang" class="form-control" placeholder="Perbankan / Level 6"></div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status Sertifikat</label>
                                            <select name="status_sertif" class="form-select">
                                                <option value="Aktif">Aktif</option>
                                                <option value="Dalam Proses">Dalam Proses</option>
                                                <option value="Kedaluwarsa">Kedaluwarsa</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label">Masa Berlaku Akhir (Tanggal Expired)</label><input type="date" name="tanggal_expired" class="form-control" required></div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Link Sertifikat (URL / Google Drive / Cloud)</label>
                                            <input type="url" name="link_sertifikat" class="form-control" placeholder="https://drive.google.com/file/d/...">
                                        </div>
                                    </div>
                                    <button type="submit" name="add_sertifikat" class="btn btn-success px-4">Simpan</button>
                                </form>
                            </div>
                        </div>

                        <div class="card card-custom">
                            <!-- TOMBOL HAPUS SEMUA DATA DITAMBAHKAN DI HEADER CARD -->
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="fw-bold text-dark m-0">Datar Pemilik Sertifikat</span>
                                <form method="POST" action="" onsubmit="return confirm('PERINGATAN PERMANEN!\n\nApakah Anda benar-benar yakin ingin MENGHAPUS SEMUA DATA sertifikat?\nProses ini tidak dapat dibatalkan!');" class="m-0">
                                    <button type="submit" name="delete_all_sertifikat" class="btn btn-danger btn-sm fw-bold px-3">
                                        <i class="fas fa-trash-alt me-1"></i> Hapus Semua Data
                                    </button>
                                </form>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4">Nama Pemilik & Nomor Blanko</th>
                                                <th>No. Sertifikat, Skema, Bidang / Level</th>
                                                <th>Status</th>
                                                <th>Link Sertifikat</th>
                                                <th>Masa Berlaku</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $get_sertif_list = mysqli_query($koneksi, "SELECT * FROM sertifikat ORDER BY id DESC");
                                            if($get_sertif_list && mysqli_num_rows($get_sertif_list) > 0) {
                                                while($s_row = mysqli_fetch_assoc($get_sertif_list)) {
                                                    $bg_badge = "bg-success";
                                                    if($s_row['status'] == 'Kedaluwarsa') { $bg_badge = "bg-danger"; }
                                                    elseif($s_row['status'] == 'Dalam Proses') { $bg_badge = "bg-warning text-dark"; }
                                            ?>
                                            <tr>
                                                <td class="px-4">
                                                    <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($s_row['nama_asesi']); ?></span>
                                                    <small class="text-muted">No. Blanko: <?php echo htmlspecialchars($s_row['nik']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="text-primary fw-semibold small d-block" style="font-family: monospace;"><?php echo htmlspecialchars($s_row['no_sertifikat']); ?></span>
                                                    <small class="text-secondary"><?php echo htmlspecialchars($s_row['skema']); ?></small>
                                                    <?php if(!empty($s_row['bidang'])) { ?>
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;"><i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($s_row['bidang']); ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td><span class="badge <?php echo $bg_badge; ?>"><?php echo htmlspecialchars($s_row['status']); ?></span></td>
                                                <td>
                                                    <?php if(!empty($s_row['file_sertifikat'])) { ?>
                                                        <a href="<?php echo htmlspecialchars($s_row['file_sertifikat']); ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.8rem;">
                                                            <i class="fas fa-external-link-alt me-1"></i> Buka Link
                                                        </a>
                                                    <?php } else { ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php } ?>
                                                </td>
                                                <td><small class="fw-bold text-secondary"><i class="far fa-calendar-alt me-1"></i><?php echo date('d-M-Y', strtotime($s_row['tanggal_expired'])); ?></small></td>
                                                <td class="text-center">
                                                    <a href="admin.php?action=edit_sertifikat&id=<?php echo $s_row['id']; ?>" class="btn btn-outline-warning btn-sm me-1">Edit</a>
                                                    <a href="admin.php?action=delete_sertifikat&id=<?php echo $s_row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus data kelulusan ini?')">Hapus</a>
                                                </td>
                                            </tr>
                                            <?php } } else { echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Database sertifikat asesi masih kosong.</td></tr>"; } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="tab-pane fade <?php echo ($tab_aktif == 'asesor_tab') ? 'show active' : ''; ?>" id="content-asesor" role="tabpanel">
                        <?php if ($edit_asesor_data != null) { ?>
                        <div class="card card-custom border-warning">
                            <div class="card-header bg-warning text-dark py-3 fw-bold">Edit Registrasi</div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?php echo $edit_asesor_data['id']; ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label fw-medium">No. Registrasi MET (BNSP)</label><input type="text" name="met_asesor" class="form-control" value="<?php echo htmlspecialchars($edit_asesor_data['met_asesor']); ?>" required></div>
                                        <div class="col-md-6 mb-3"><label class="form-label fw-medium">Nama Lengkap Tanpa Gelar</label><input type="text" name="nama_asesor" class="form-control" value="<?php echo htmlspecialchars($edit_asesor_data['nama_asesor']); ?>" required></div>
                                    </div>
                                    <div class="mb-3"><label class="form-label fw-medium">Skema Kompetensi</label><input type="text" name="bidang_keahlian" class="form-control" value="<?php echo htmlspecialchars($edit_asesor_data['bidang_keahlian']); ?>" required></div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Status Sertifikat</label>
                                        <select name="status_asesor" class="form-select">
                                            <option value="Aktif" <?php echo ($edit_asesor_data['status_asesor'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                            <option value="Masa Perpanjangan" <?php echo ($edit_asesor_data['status_asesor'] == 'Masa Perpanjangan') ? 'selected' : ''; ?>>Masa Perpanjangan</option>
                                            <option value="Tidak Aktif" <?php echo ($edit_asesor_data['status_asesor'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_asesor" class="btn btn-warning px-4 text-dark fw-bold">Simpan</button>
                                    <a href="admin.php?tab=asesor_tab" class="btn btn-secondary px-3 ms-2">Batal</a>
                                </form>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="card card-custom mb-4">
                            <div class="card-header bg-white py-3 fw-bold text-primary">Registrasi Asesor</div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label">No. Reg MET (BNSP)</label><input type="text" name="met_asesor" class="form-control" placeholder="Contoh: MET.000.012345 2026" required></div>
                                        <div class="col-md-6 mb-3"><label class="form-label">Nama Lengkap Tanpa Gelar</label><input type="text" name="nama_asesor" class="form-control" placeholder="Nama Lengkap Asesor" required></div>
                                    </div>
                                    <div class="mb-3"><label class="form-label">Skema Kompetensi</label><input type="text" name="bidang_keahlian" class="form-control" placeholder="General Banking" required></div>
                                    <div class="mb-3">
                                        <label class="form-label">Status Sertifikat</label>
                                        <select name="status_asesor" class="form-select">
                                            <option value="Aktif">Aktif</option>
                                            <option value="Masa Perpanjangan">Masa Perpanjangan</option>
                                            <option value="Tidak Aktif">Tidak Aktif</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="add_asesor" class="btn btn-success px-4">Simpan</button>
                                </form>
                            </div>
                        </div>
                        <div class="card card-custom">
                            <div class="card-header bg-white py-3 fw-bold text-dark">Daftar Asesor Berlisensi BNSP</div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4">Nama Lengkap Asesor</th>
                                                <th>No. Registrasi MET</th>
                                                <th>Bidang Keahlian</th>
                                                <th>Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $get_asesor = mysqli_query($koneksi, "SELECT * FROM asesor ORDER BY id DESC"); 
                                            if($get_asesor && mysqli_num_rows($get_asesor) > 0) { 
                                                while($as_row = mysqli_fetch_assoc($get_asesor)) { 
                                                    $b_asesor = "bg-success"; 
                                                    if($as_row['status_asesor'] == 'Tidak Aktif') { $b_asesor = "bg-danger"; } 
                                                    elseif($as_row['status_asesor'] == 'Masa Perpanjangan') { $b_asesor = "bg-warning text-dark"; } 
                                            ?>
                                            <tr>
                                                <td class="px-4 fw-bold text-primary"><?php echo htmlspecialchars($as_row['nama_asesor']); ?></td>
                                                <td style="font-family:monospace; font-size:0.88rem;"><?php echo htmlspecialchars($as_row['met_asesor']); ?></td>
                                                <td><span class="small fw-medium"><?php echo htmlspecialchars($as_row['bidang_keahlian']); ?></span></td>
                                                <td><span class="badge <?php echo $b_asesor; ?>"><?php echo htmlspecialchars($as_row['status_asesor']); ?></span></td>
                                                <td class="text-center">
                                                    <a href="admin.php?action=edit_asesor&id=<?php echo $as_row['id']; ?>" class="btn btn-outline-warning btn-sm me-1">Edit</a>
                                                    <a href="admin.php?action=delete_asesor&id=<?php echo $as_row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus data penguji?')">Hapus</a>
                                                </td>
                                            </tr>
                                            <?php } } else { echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Belum ada data asesor terdaftar.</td></tr>"; } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="tab-pane fade <?php echo ($tab_aktif == 'admin') ? 'show active' : ''; ?>" id="content-admin" role="tabpanel">
                        <div class="card card-custom"><div class="card-header bg-white py-3 fw-bold text-primary">Registrasi Akun Admin Baru</div><div class="card-body"><form method="POST"><div class="row"><div class="col-md-6 mb-3"><label class="form-label">Username</label><input type="text" name="username_baru" class="form-control" required></div><div class="col-md-6 mb-3"><label class="form-label">Password</label><input type="password" name="password_baru" class="form-control" required></div></div><button type="submit" name="add_admin" class="btn btn-primary">Daftarkan</button></form></div></div><div class="card card-custom"><div class="card-header bg-white py-3 fw-bold text-dark">Daftar Admin</div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead class="table-light"><tr><th class="px-4">ID</th><th>Username</th><th>Aksi</th></tr></thead><tbody><?php $get_admin_users = mysqli_query($koneksi, "SELECT id, username FROM admin ORDER BY id ASC"); while($user_row = mysqli_fetch_assoc($get_admin_users)) { $is_current_user = ($user_row['username'] == $_SESSION['admin_username']); ?><tr><td class="px-4">#<?php echo $user_row['id']; ?></td><td class="fw-semibold"><?php echo htmlspecialchars($user_row['username']); ?></td><td><?php if(!$is_current_user) { ?><a href="admin.php?action=delete_admin&tab=admin&id=<?php echo $user_row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus akun admin ini?')">Hapus</a><?php } else { echo "Aktif"; } ?></td></tr><?php } ?></tbody></table></div></div></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>