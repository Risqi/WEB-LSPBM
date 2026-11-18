<?php
// 1. MEMANGGIL KONEKSI DATABASE
include 'koneksi.php';

// Ambil data Pengaturan Utama untuk judul halaman
$query_setting = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$data_setting  = mysqli_fetch_assoc($query_setting);
$nama_web       = !empty($data_setting['nama_web']) ? $data_setting['nama_web'] : "LSP Bank Mandiri";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Asesor - <?php echo $nama_web; ?></title>
    
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

        /* ==================== 1. RESPONSIVE BANNER SUB-HERO ==================== */
        .asesor-banner {
            background: linear-gradient(rgba(0, 61, 121, 0.88), rgba(0, 61, 121, 0.88)), 
                        url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=2070') center/cover no-repeat;
            padding: 45px 20px;
            color: #ffffff;
            text-align: center;
        }
        .asesor-banner h2 { 
            font-weight: 700; 
            font-size: 1.8rem; 
            margin-bottom: 8px; 
        }
        .asesor-banner p { 
            color: #d1e1f1; 
            font-size: 0.95rem; 
            margin-bottom: 0;
        }

        @media (min-width: 768px) {
            .asesor-banner { padding: 80px 0; }
            .asesor-banner h2 { font-size: 2.5rem; }
            .asesor-banner p { font-size: 1.1rem; }
        }

        /* ==================== 2. CONTROLLABLE SECTION SPACING ==================== */
        .section-padding {
            padding: 40px 0;
        }
        @media (min-width: 768px) {
            .section-padding { padding: 60px 0; }
        }

        /* ==================== 3. MODULAR ASESOR CARD COMPONENTS ==================== */
        .asesor-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background-color: #ffffff;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .asesor-card:hover {
            border-color: var(--mandiri-blue);
            box-shadow: 0 10px 25px rgba(0, 61, 121, 0.08);
            transform: translateY(-5px);
        }
        .asesor-icon-box {
            width: 52px;
            height: 52px;
            background-color: rgba(0, 61, 121, 0.06);
            color: var(--mandiri-blue);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 18px;
            transition: all 0.3s ease;
        }
        .asesor-card:hover .asesor-icon-box {
            background-color: var(--mandiri-blue);
            color: var(--mandiri-gold);
        }
        .asesor-title {
            color: var(--mandiri-blue);
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 4px;
        }
        .asesor-met {
            font-family: monospace;
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .asesor-desc {
            color: #4a5568;
            font-size: 0.88rem;
            line-height: 1.6;
            text-align: left;
        }
        @media (min-width: 768px) {
            .asesor-desc { text-align: justify; }
        }
        
        .status-area {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px dashed #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="asesor-banner">
        <div class="container px-2">
            <h2>Daftar Asesor Lisensi</h2>
            <p>Daftar penguji kualifikasi kompetensi resmi terlisensi</p>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-end px-2">
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="inputSearchAsesor" class="form-control border-start-0 shadow-none" onkeyup="liveSearchAsesor()" placeholder="Cari nama / No. Reg MET..." style="border-radius: 0 6px 6px 0;">
                </div>
            </div>
        </div>
    </div>

    <section class="container section-padding">
        <div class="row g-3 g-md-4 px-2" id="asesorGridWrapper">
            <?php
            // Ambil semua data asesor dari database secara langsung untuk live-filter client-side
            $get_asesor_page = mysqli_query($koneksi, "SELECT * FROM asesor ORDER BY id DESC");
            
            if ($get_asesor_page && mysqli_num_rows($get_asesor_page) > 0) {
                while ($asesor_row = mysqli_fetch_assoc($get_asesor_page)) {
                    
                    $badge_class = "bg-success text-white";
                    if ($asesor_row['status_asesor'] == 'Tidak Aktif') {
                        $badge_class = "bg-danger text-white";
                    } elseif ($asesor_row['status_asesor'] == 'Masa Perpanjangan') {
                        $badge_class = "bg-warning text-dark";
                    }
            ?>
            <div class="col-12 col-md-6 col-lg-4 search-asesor-item">
                <div class="card asesor-card p-4 shadow-sm">
                    <div class="asesor-icon-box"><i class="fas fa-user-shield"></i></div>
                    <h4 class="asesor-title"><?php echo htmlspecialchars($asesor_row['nama_asesor']); ?></h4>
                    <div class="asesor-met">Reg. MET: <?php echo htmlspecialchars($asesor_row['met_asesor']); ?></div>
                    <p class="asesor-desc mb-4">
                        <span class="text-secondary small d-block mb-1 fw-bold" style="letter-spacing:0.3px; text-transform:uppercase; font-size:0.72rem;">Skema Kompetensi:</span>
                        <?php echo htmlspecialchars($asesor_row['bidang_keahlian']); ?>
                    </p>
                    
                    <div class="status-area">
                        <span class="text-muted small" style="font-size:0.78rem;">Status Lisensi:</span>
                        <span class="badge <?php echo $badge_class; ?> rounded-pill font-weight-semibold px-2.5 py-1.5" style="font-size: 0.72rem;"><?php echo htmlspecialchars($asesor_row['status_asesor']); ?></span>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                // FALLBACK DATA CONTOH JIKA DATABASE MASIH KOSONG
            ?>
            <div class="col-12 col-md-6 col-lg-4 search-asesor-item">
                <div class="card asesor-card p-4 shadow-sm">
                    <div class="asesor-icon-box"><i class="fas fa-user-shield"></i></div>
                    <h4 class="asesor-title">Budi Santoso, S.E., M.M.</h4>
                    <div class="asesor-met">Reg. MET: MET.000.001234 2026</div>
                    <p class="asesor-desc mb-4">
                        <span class="text-secondary small d-block mb-1 fw-bold" style="letter-spacing:0.3px; text-transform:uppercase; font-size:0.72rem;">Skema Kompetensi:</span>
                        Menguji kompetensi teknis dalam bidang Credit Risk Management, Retail Banking, dan Kepatuhan Operasional.
                    </p>
                    <div class="status-area">
                        <span class="text-muted small" style="font-size:0.78rem;">Status Lisensi:</span>
                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1.5" style="font-size: 0.72rem;">Aktif</span>
                    </div>
                </div>
            </div>
            <?php } ?>
            
            <div id="noAsesorResult" class="col-12 text-center py-5 text-muted d-none">
                <i class="fas fa-search-minus fa-2x mb-3"></i>
                <p class="small">Asesor dengan kata kunci tersebut tidak ditemukan dalam pangkalan lisensi.</p>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
    function liveSearchAsesor() {
        let input = document.getElementById("inputSearchAsesor").value.toLowerCase();
        let items = document.getElementsByClassName("search-asesor-item");
        let noResult = document.getElementById("noAsesorResult");
        let matchCount = 0;

        for (let i = 0; i < items.length; i++) {
            let cardText = items[i].innerText.toLowerCase();
            if (cardText.includes(input)) {
                items[i].classList.remove("d-none");
                matchCount++;
            } else {
                items[i].classList.add("d-none");
            }
        }

        // Tampilkan feedback jika data kosong
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