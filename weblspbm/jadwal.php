<?php
// Memanggil koneksi database weblspbm
include 'koneksi.php';

// Ambil data Pengaturan Utama untuk sinkronisasi identitas halaman
$query_setting = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$data_setting  = mysqli_fetch_assoc($query_setting);
$nama_web       = !empty($data_setting['nama_web']) ? $data_setting['nama_web'] : "LSP Bank Mandiri";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kegiatan - <?php echo $nama_web; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --mandiri-blue: #003D79;
            --mandiri-gold: #F2A900;
            --bg-light: #F8F9FA;
            --text-dark: #212529;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
            color: var(--text-dark);
        }

        /* ==================== 1. RESPONSIVE HERO BANNER ==================== */
        .sub-banner { 
            background: linear-gradient(rgba(0, 61, 121, 0.85), rgba(0, 61, 121, 0.85)), 
                        url('https://images.unsplash.com/photo-1506784983877-45594efa4cbe?q=80&w=2068') center/cover no-repeat; 
            padding: 45px 20px; /* Padding ideal mobile-first */
            color: #ffffff; 
            text-align: center; 
        }
        .sub-banner h2 { 
            font-weight: 700; 
            font-size: 1.75rem; /* Font compact khusus layar HP */
            margin-bottom: 8px;
        }
        .sub-banner p {
            color: #d1e1f1;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        /* RESPONSIVE SCALING BREAKPOINT (TABLET TO DESKTOP SCREEN) */
        @media (min-width: 768px) {
            .sub-banner { padding: 80px 0; }
            .sub-banner h2 { font-size: 2.4rem; }
            .sub-banner p { font-size: 1.1rem; }
        }

        /* ==================== 2. CONTROLLABLE SECTION SPACING ==================== */
        .section-padding {
            padding: 40px 0; /* Mengurangi kelelahan scrolling vertikal di HP */
        }
        @media (min-width: 768px) {
            .section-padding { padding: 60px 0; }
        }

        /* ==================== 3. PERFECT MODULAR TABLE STYLE ==================== */
        .table-custom { 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.03); 
            border: none; 
        }
        .table-custom thead { 
            background-color: var(--mandiri-blue); 
            color: #ffffff; 
        }
        
        /* Mengunci keterbacaan teks baris tabel di layar HP */
        .table-custom td {
            font-size: 0.9rem;
            white-space: nowrap; /* Mencegah kolom hancur berhimpitan ke bawah */
        }
        
        /* Khusus kolom agenda diperbolehkan melakukan text-wrap agar judul tidak terpotong */
        .table-custom td.agenda-cell {
            white-space: normal;
            min-width: 260px;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="sub-banner">
        <div class="container px-2">
            <h2>Kegiatan & Agenda</h2>
            <p>Jadwal Pelaksanaan Pelatihan dan Sertifikasi</p>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-end px-2">
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="inputSearchJadwal" class="form-control border-start-0 shadow-none" onkeyup="liveSearchJadwal()" placeholder="Cari nama agenda atau lokasi..." style="border-radius: 0 6px 6px 0;">
                </div>
            </div>
        </div>
    </div>

    <section class="container section-padding">
        <div class="row px-2">
            <div class="col-12">
                <div class="card border-0 p-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-uppercase small fw-bold" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                    <th class="py-3 px-4" style="width: 40%;">Agenda / Nama Kegiatan</th>
                                    <th class="py-3">Tanggal Pelaksanaan</th>
                                    <th class="py-3">Waktu / Jam</th>
                                    <th class="py-3 px-4">Tempat / Lokasi</th>
                                </tr>
                            </thead>
                            <tbody id="jadwalTableBody">
                                <?php
                                // Ambil semua data agenda kegiatan ter-update dari database
                                $query_front_j = mysqli_query($koneksi, "SELECT * FROM jadwal ORDER BY id DESC");
                                if ($query_front_j && mysqli_num_rows($query_front_j) > 0) {
                                    while($j_row = mysqli_fetch_assoc($query_front_j)) {
                                ?>
                                <tr class="search-jadwal-row">
                                    <td class="py-4 px-4 agenda-cell">
                                        <span class="fw-bold text-primary d-block" style="font-size: 1.05rem; line-height: 1.4;"><?php echo htmlspecialchars($j_row['nama_kegiatan']); ?></span>
                                        <small class="text-muted d-block mt-1"><?php echo htmlspecialchars($j_row['keterangan']); ?></small>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <i class="far fa-calendar-alt text-warning me-2"></i><?php echo htmlspecialchars($j_row['tanggal']); ?>
                                    </td>
                                    <td class="text-secondary small">
                                        <i class="far fa-clock me-2"></i><?php echo htmlspecialchars($j_row['waktu']); ?>
                                    </td>
                                    <td class="px-4">
                                        <span class="badge bg-light text-dark border p-2 rounded fw-medium" style="font-size: 0.85rem;">
                                            <i class="fas fa-map-marker-alt text-danger me-2"></i><?php echo htmlspecialchars($j_row['lokasi']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr id="emptyFallbackRow">
                                    <td colspan="4" class="text-center py-5 text-muted small">
                                        <i class="fas fa-calendar-times fa-3x d-block text-black-50 mb-3"></i>
                                        Belum ada jadwal kegiatan atau sosialisasi yang diterbitkan oleh administrator.
                                    </td>
                                </tr>
                                <?php } ?>
                                
                                <tr id="noJadwalResultRow" class="d-none">
                                    <td colspan="4" class="text-center py-5 text-muted small">
                                        <i class="fas fa-search-minus fa-3x d-block text-black-50 mb-3"></i>
                                        Agenda kegiatan dengan kata kunci tersebut tidak ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
    function liveSearchJadwal() {
        let input = document.getElementById("inputSearchJadwal").value.toLowerCase();
        let rows = document.getElementsByClassName("search-jadwal-row");
        let noResultRow = document.getElementById("noJadwalResultRow");
        let fallbackRow = document.getElementById("emptyFallbackRow");
        let matchCount = 0;

        // Jika database dari awal memang kosong, skip pencarian
        if (rows.length === 0) return;

        for (let i = 0; i < rows.length; i++) {
            // Membaca seluruh teks dalam satu baris (Nama kegiatan, tanggal, waktu, maupun tempat)
            let rowText = rows[i].innerText.toLowerCase();
            if (rowText.includes(input)) {
                rows[i].classList.remove("d-none");
                matchCount++;
            } else {
                rows[i].classList.add("d-none");
            }
        }

        // Kontrol menampilkan feedback baris kosong
        if (matchCount === 0 && input !== "") {
            noResultRow.classList.remove("d-none");
        } else {
            noResultRow.classList.add("d-none");
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>