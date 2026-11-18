<?php
// Memanggil koneksi database weblspbm
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
    <title>Skema Sertifikasi - <?php echo $nama_web; ?></title>
    
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
        .skema-banner {
            background: linear-gradient(rgba(0, 61, 121, 0.88), rgba(0, 61, 121, 0.88)), 
                        url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=2070') center/cover no-repeat;
            padding: 45px 20px;
            color: #ffffff;
            text-align: center;
        }
        .skema-banner h2 { 
            font-weight: 700; 
            font-size: 1.8rem; 
            margin-bottom: 8px; 
        }
        .skema-banner p { 
            color: #d1e1f1; 
            font-size: 0.95rem; 
            margin-bottom: 0;
        }

        @media (min-width: 768px) {
            .skema-banner { padding: 80px 0; }
            .skema-banner h2 { font-size: 2.5rem; }
            .skema-banner p { font-size: 1.1rem; }
        }

        /* ==================== 2. CONTROLLABLE SECTION SPACING ==================== */
        .section-padding {
            padding: 40px 0;
        }
        @media (min-width: 768px) {
            .section-padding { padding: 60px 0; }
        }

        /* ==================== 3. MODULAR SKEMA CARD COMPONENTS ==================== */
        .skema-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background-color: #ffffff;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .skema-card:hover {
            border-color: var(--mandiri-blue);
            box-shadow: 0 10px 25px rgba(0, 61, 121, 0.08);
            transform: translateY(-5px);
        }
        .skema-icon-box {
            width: 52px;
            height: 52px;
            background-color: rgba(0, 61, 121, 0.06);
            color: var(--mandiri-blue);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }
        .skema-card:hover .skema-icon-box {
            background-color: var(--mandiri-blue);
            color: var(--mandiri-gold);
        }
        
        .skema-code {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--mandiri-gold);
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        
        .skema-title {
            color: var(--mandiri-blue);
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 10px;
        }
        .skema-desc {
            color: #6c757d;
            font-size: 0.88rem;
            line-height: 1.6;
            text-align: left;
        }
        @media (min-width: 768px) {
            .skema-desc { text-align: justify; }
        }
        
        /* TOMBOL INSIDE CARD BOUNDARIES */
        .btn-skema-action {
            margin-top: auto;
            border: 1px solid var(--mandiri-blue);
            color: var(--mandiri-blue);
            background: transparent;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 8px;
            padding: 10px 16px;
            transition: all 0.2s ease;
            text-align: center;
        }
        .skema-card:hover .btn-skema-action {
            background-color: var(--mandiri-blue);
            color: #ffffff;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="skema-banner">
        <div class="container px-2">
            <h2>Skema Sertifikasi Kompetensi</h2>
            <p>Daftar standardisasi skema kompetensi berlisensi BNSP</p>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-end px-2">
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" id="inputSearchSkema" class="form-control border-start-0 shadow-none" onkeyup="liveSearchSkema()" placeholder="Cari nama, jenis atau kode skema..." style="border-radius: 0 6px 6px 0;">
                </div>
            </div>
        </div>
        <div id="noResultFeedback" class="alert alert-light text-center border mt-3 d-none" style="border-radius: 10px;">
            <i class="fas fa-search text-muted me-2"></i> Skema atau kode yang Anda cari tidak dapat ditemukan.
        </div>
    </div>

    <section class="container section-padding">
        <div class="row g-3 g-md-4 px-2" id="skemaGridWrapper">
            <?php
            // Ambil semua data skema dari database
            $get_skema_page = mysqli_query($koneksi, "SELECT * FROM skema ORDER BY id DESC");
            
            if ($get_skema_page && mysqli_num_rows($get_skema_page) > 0) {
                while ($skema_row = mysqli_fetch_assoc($get_skema_page)) {
                    $icon_tampil = !empty($skema_row['icon']) ? $skema_row['icon'] : "fas fa-award";
                    $kode_skema = !empty($skema_row['kode_skema']) ? $skema_row['kode_skema'] : "N/A";
                    $jenis_skema = !empty($skema_row['jenis_skema']) ? $skema_row['jenis_skema'] : "KKNI";
                    $id_skema = $skema_row['id'];
            ?>
            <div class="col-12 col-md-6 col-lg-4 search-target-item">
                <div class="card skema-card p-4 shadow-sm">
                    <div class="skema-icon-box"><i class="<?php echo $icon_tampil; ?>"></i></div>
                    <div class="skema-code">SKEMA <?php echo htmlspecialchars($jenis_skema); ?></div>
                    <h4 class="skema-title"><?php echo htmlspecialchars($skema_row['nama_skema']); ?></h4>
                    <p class="skema-desc mb-4"><?php echo htmlspecialchars($skema_row['deskripsi']); ?></p>
                    
                    <button type="button" class="btn btn-skema-action" data-bs-toggle="modal" data-bs-target="#modalUnit<?php echo $id_skema; ?>">
                        <i class="fas fa-folder-open me-2"></i>Lihat Unit Kompetensi
                    </button>
                </div>
            </div>

            <div class="modal fade" id="modalUnit<?php echo $id_skema; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="border-radius: 14px; overflow: hidden; border: none;">
                        <div class="modal-header text-white p-3" style="background-color: var(--mandiri-blue);">
                            <h5 class="modal-title fw-bold" style="font-size: 1.05rem;">
                                <i class="fas fa-database me-2 text-warning"></i>Skema: <?php echo htmlspecialchars($skema_row['nama_skema']); ?>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <span class="badge bg-primary text-white px-3 py-2 text-uppercase me-2" style="letter-spacing: 0.5px;">
                                    Jenis: <?php echo htmlspecialchars($jenis_skema); ?>
                                </span>
                                <span class="badge bg-light text-dark border px-3 py-2 text-uppercase" style="letter-spacing: 0.5px;">
                                    Kode: <?php echo htmlspecialchars($kode_skema); ?>
                                </span>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light fw-bold text-secondary">
                                        <tr class="text-center">
                                            <th style="width: 8%;">No</th>
                                            <th style="width: 32%;">Kode Unit</th>
                                            <th style="width: 60%;">Judul Unit Kompetensi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $data_unit_array = json_decode($skema_row['unit_kompetensi'], true);

                                        if (!empty($data_unit_array) && is_array($data_unit_array)) {
                                            $no_unit = 1;
                                            foreach ($data_unit_array as $unit) {
                                        ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?php echo $no_unit++; ?></td>
                                            <td class="text-center fw-semibold text-primary" style="font-family: monospace;"><?php echo htmlspecialchars($unit['kode']); ?></td>
                                            <td class="text-dark"><?php echo htmlspecialchars($unit['judul']); ?></td>
                                        </tr>
                                        <?php
                                            }
                                        } else {
                                        ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3 small">
                                                <i class="fas fa-info-circle me-1"></i> Struktur rincian unit kompetensi belum diinput oleh administrator.
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn text-white fw-semibold px-4 btn-sm" data-bs-dismiss="modal" style="background-color: var(--mandiri-blue); border-radius: 6px;">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                // DATA TAMPILAN CONTOH (FALLBACK) JIKA DATABASE TOTAL MASIH KOSONG
            ?>
            <div class="col-12 col-md-6 col-lg-4 search-target-item">
                <div class="card skema-card p-4 shadow-sm">
                    <div class="skema-icon-box"><i class="fas fa-credit-card"></i></div>
                    <div class="skema-code">SKEMA OKUPASI</div>
                    <h4 class="skema-title">Credit Risk Analyst Level 1</h4>
                    <p class="skema-desc mb-4">Menguji kompetensi teknis dalam melakukan analisis risiko pembiayaan, kepatuhan portofolio, serta mitigasi kredit komersial perbankan retail.</p>
                    <button type="button" class="btn btn-skema-action" data-bs-toggle="modal" data-bs-target="#modalFallback1">
                        <i class="fas fa-folder-open me-2"></i>Lihat Unit Kompetensi
                    </button>
                </div>
            </div>

            <div class="modal fade" id="modalFallback1" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="border-radius: 14px; overflow: hidden; border: none;">
                        <div class="modal-header text-white p-3" style="background-color: var(--mandiri-blue);">
                            <h5 class="modal-title fw-bold" style="font-size: 1.05rem;"><i class="fas fa-database me-2 text-warning"></i>Skema: Credit Risk Analyst Level 1</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <span class="badge bg-primary text-white px-3 py-2 text-uppercase me-2">Jenis: Okupasi</span>
                                <span class="badge bg-light text-dark border px-3 py-2 text-uppercase">Kode: SKM-MAN-01</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light fw-bold text-secondary">
                                        <tr class="text-center">
                                            <th style="width: 8%;">No</th>
                                            <th style="width: 32%;">Kode Unit</th>
                                            <th style="width: 60%;">Judul Unit Kompetensi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="text-center fw-bold text-muted">1</td><td class="text-center fw-semibold text-primary" style="font-family: monospace;">M.642000.001.01</td><td>Mengimplementasikan Prosedur Manajemen Risiko Portofolio Perbankan</td></tr>
                                        <tr><td class="text-center fw-bold text-muted">2</td><td class="text-center fw-semibold text-primary" style="font-family: monospace;">M.642000.002.01</td><td>Melakukan Analisis Validasi Kelayakan Fasilitas Pembiayaan Retail</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2"><button type="button" class="btn text-white btn-sm px-4" data-bs-dismiss="modal" style="background-color: var(--mandiri-blue);">Tutup</button></div>
                    </div>
                </div>
            </div>
           
            <?php } ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
    function liveSearchSkema() {
        let input = document.getElementById("inputSearchSkema").value.toLowerCase();
        let items = document.getElementsByClassName("search-target-item");
        let noResult = document.getElementById("noResultFeedback");
        let matchCount = 0;

        for (let i = 0; i < items.length; i++) {
            let cardContent = items[i].innerText.toLowerCase();
            if (cardContent.includes(input)) {
                items[i].classList.remove("d-none");
                matchCount++;
            } else {
                items[i].classList.add("d-none");
            }
        }

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