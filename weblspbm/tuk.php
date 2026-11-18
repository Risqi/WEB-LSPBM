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
    <title>Tempat Uji Kompetensi - <?php echo $nama_web; ?></title>
    
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

        /* ==================== 1. RESPONSIVE SUB-HERO BANNER ==================== */
        .tuk-banner {
            background: linear-gradient(rgba(0, 61, 121, 0.85), rgba(0, 61, 121, 0.85)), 
                        url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070') center/cover no-repeat;
            padding: 45px 20px; /* Padding minimalis mobile-first */
            color: #ffffff;
            text-align: center;
        }
        .tuk-banner h2 { 
            font-weight: 700; 
            font-size: 1.8rem; /* Font compact ideal layar HP */
            margin-bottom: 8px; 
        }
        .tuk-banner p { 
            color: #d1e1f1; 
            font-size: 0.95rem; 
            margin-bottom: 0;
        }

        /* RESPONSIVE BREAKPOINT (TABLET TO MONITOR DESKTOP) */
        @media (min-width: 768px) {
            .tuk-banner { padding: 80px 0; }
            .tuk-banner h2 { font-size: 2.5rem; }
            .tuk-banner p { font-size: 1.1rem; }
        }

        /* ==================== 2. CONTROLLABLE SECTION SPACING ==================== */
        .section-padding {
            padding: 40px 0; /* Menghemat vertikal viewport HP */
        }
        @media (min-width: 768px) {
            .section-padding { padding: 60px 0; }
        }

        /* ==================== 3. MODULAR TUK CARD COMPONENTS ==================== */
        .tuk-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background-color: #ffffff;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .tuk-card:hover {
            border-color: var(--mandiri-blue);
            box-shadow: 0 10px 25px rgba(0, 61, 121, 0.08);
            transform: translateY(-5px);
        }
        .tuk-icon-box {
            width: 52px;
            height: 52px;
            background-color: rgba(0, 61, 121, 0.06);
            color: var(--mandiri-blue);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            transition: all 0.3s ease;
        }
        .tuk-card:hover .tuk-icon-box {
            background-color: var(--mandiri-blue);
            color: var(--mandiri-gold);
        }
        
        .tuk-address {
            color: #6c757d;
            font-size: 0.88rem;
            line-height: 1.6;
            text-align: left; /* Rata kiri mutlak untuk estetika teks alamat di mobile */
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="tuk-banner">
        <div class="container px-2">
            <h2>Tempat Uji Kompetensi (TUK)</h2>
            <p>Daftar lokasi sarana uji sertifikasi</p>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-end px-2">
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="inputSearchTuk" class="form-control border-start-0 shadow-none" onkeyup="liveSearchTuk()" placeholder="Cari nama TUK atau kota..." style="border-radius: 0 6px 6px 0;">
                </div>
            </div>
        </div>
    </div>

    <section class="container section-padding">
        <div class="row g-3 g-md-4 px-2" id="tukGridWrapper">
            <?php
            // Query mengambil data TUK dari database secara real-time
            $get_tuk = mysqli_query($koneksi, "SELECT * FROM tuk ORDER BY id DESC");
            if ($get_tuk && mysqli_num_rows($get_tuk) > 0) {
                while ($row = mysqli_fetch_assoc($get_tuk)) {
            ?>
            <div class="col-12 col-md-6 col-lg-4 search-tuk-item">
                <div class="card tuk-card p-4 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="tuk-icon-box"><i class="fas fa-map-marked-alt"></i></div>
                        <span class="badge bg-primary px-3 py-2 rounded-pill small"><?php echo htmlspecialchars($row['jenis_tuk']); ?></span>
                    </div>
                    <h4 class="fw-bold mb-2 text-dark" style="color: var(--mandiri-blue); font-size: 1.2rem;"><?php echo htmlspecialchars($row['nama_tuk']); ?></h4>
                    <p class="tuk-address mb-3 flex-grow-1"><i class="fas fa-map-marker-alt text-danger me-2"></i><?php echo htmlspecialchars($row['alamat']); ?></p>
                    <hr class="text-black-50 my-2">
                    <div class="small text-secondary fw-medium"><i class="fas fa-phone-alt me-2 text-success"></i>Kontak: <?php echo htmlspecialchars($row['kontak']); ?></div>
                </div>
            </div>
            <?php
                }
            } else {
                // DATA CONTOH (FALLBACK) JIKA DATABASE ADMIN BELUM DI-INPUT
            ?>
            <div class="col-12 col-md-6 col-lg-4 search-tuk-item">
                <div class="card tuk-card p-4 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="tuk-icon-box"><i class="fas fa-map-marked-alt"></i></div>
                        <span class="badge bg-primary px-3 py-2 rounded-pill small">TUK Mandiri</span>
                    </div>
                    <h4 class="fw-bold mb-2 text-dark" style="color: var(--mandiri-blue); font-size: 1.2rem;">TUK Mandiri University Jakarta</h4>
                    <p class="tuk-address mb-3 flex-grow-1"><i class="fas fa-map-marker-alt text-danger me-2"></i>Jl. Wijaya IX No.1, Melawai, Kebayoran Baru, Jakarta Selatan</p>
                    <hr class="text-black-50 my-2">
                    <div class="small text-secondary fw-medium"><i class="fas fa-phone-alt me-2 text-success"></i>Kontak: (021) 7261123</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 search-tuk-item">
                <div class="card tuk-card p-4 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="tuk-icon-box"><i class="fas fa-map-marked-alt"></i></div>
                        <span class="badge bg-success px-3 py-2 rounded-pill small">TUK Sewaktu</span>
                    </div>
                    <h4 class="fw-bold mb-2 text-dark" style="color: var(--mandiri-blue); font-size: 1.2rem;">TUK Mandiri Regional III Sumatera</h4>
                    <p class="tuk-address mb-3 flex-grow-1"><i class="fas fa-map-marker-alt text-danger me-2"></i>Gedung Plaza Mandiri, Jl. Jend. Gatot Subroto, Kota Medan</p>
                    <hr class="text-black-50 my-2">
                    <div class="small text-secondary fw-medium"><i class="fas fa-phone-alt me-2 text-success"></i>Kontak: (061) 456789</div>
                </div>
            </div>
            <?php } ?>
            
            <div id="noTukResult" class="col-12 text-center py-5 text-muted d-none">
                <i class="fas fa-map-signs fa-2x mb-3"></i>
                <p class="small">Lokasi Tempat Uji Kompetensi (TUK) tidak ditemukan.</p>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
    function liveSearchTuk() {
        let input = document.getElementById("inputSearchTuk").value.toLowerCase();
        let items = document.getElementsByClassName("search-tuk-item");
        let noResult = document.getElementById("noTukResult");
        let matchCount = 0;

        for (let i = 0; i < items.length; i++) {
            // Membaca teks di dalam card (Nama TUK, Alamat, Klasifikasi, maupun Kontak)
            let cardText = items[i].innerText.toLowerCase();
            if (cardText.includes(input)) {
                items[i].classList.remove("d-none");
                matchCount++;
            } else {
                items[i].classList.add("d-none");
            }
        }

        // Tampilkan feedback jika pencarian kosong
        if (matchCount === 0 && input !== "") {
            noResult.classList.remove("d-none");
        } else {
            noResult.classList.add("d-none");
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>