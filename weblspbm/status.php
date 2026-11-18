<?php
// Memanggil koneksi database weblspbm
include 'koneksi.php';

// Ambil data Pengaturan Utama untuk identitas aplikasi
$query_setting = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$data_setting  = mysqli_fetch_assoc($query_setting);
$nama_web       = !empty($data_setting['nama_web']) ? $data_setting['nama_web'] : "LSP Bank Mandiri";

// Inisialisasi variabel state pencarian
$search_query = "";
$result_data = null;
$searched = false;

if (isset($_GET['q'])) {
    // SENIOR DEV SECURITY LAYER: Sanitasi input untuk mencegah SQL Injection & XSS
    $search_query = trim(mysqli_real_escape_string($koneksi, $_GET['q']));
    
    if (!empty($search_query)) {
        $searched = true;
        // Query fleksibel: asesi bisa mencari berdasarkan Nomor Blanko (kolom nik) atau Nomor Sertifikat
        $query_sertif = mysqli_query($koneksi, "SELECT * FROM sertifikat WHERE nik = '$search_query' OR no_sertifikat = '$search_query' LIMIT 1");
        if ($query_sertif && mysqli_num_rows($query_sertif) > 0) {
            $result_data = mysqli_fetch_assoc($query_sertif);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Sertifikat - <?php echo $nama_web; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --mandiri-blue: #003D79;
            --mandiri-gold: #F2A900;
            --bg-light: #F4F6F9;
            --text-dark: #212529;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        /* ==================== 1. RESPONSIVE HERO BANNER ==================== */
        .status-banner {
            background: linear-gradient(rgba(0, 61, 121, 0.9), rgba(0, 61, 121, 0.9)), 
                        url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=2070') center/cover no-repeat;
            padding: 50px 20px 65px;
            color: #ffffff;
            text-align: center;
        }
        .status-banner h2 {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 8px;
        }
        .status-banner p {
            color: #d1e1f1;
            font-size: 0.92rem;
        }

        @media (min-width: 768px) {
            .status-banner { padding: 75px 0 95px; }
            .status-banner h2 { font-size: 2.5rem; }
            .status-banner p { font-size: 1.1rem; }
        }

        /* ==================== 2. OVERLAPPING SEARCH COMPONENT ==================== */
        .search-box-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 61, 121, 0.08);
            margin-top: -35px;
            z-index: 10;
            position: relative;
        }
        @media (min-width: 768px) {
            .search-box-card { margin-top: -55px; }
        }

        /* ==================== 3. MODULAR RESULT PANEL COMPONENTS ==================== */
        .result-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
            background-color: #ffffff;
            overflow: hidden;
        }

        .result-header-valid { background: linear-gradient(135deg, #198754, #146c43); color: white; }
        .result-header-expired { background: linear-gradient(135deg, #dc3545, #b02a37); color: white; }
        .result-header-process { background: linear-gradient(135deg, #ffc107, #d39e00); color: #212529; }

        .info-label {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--mandiri-blue);
            line-height: 1.4;
        }
        @media (min-width: 768px) {
            .info-value { font-size: 1.05rem; }
        }

        .animate-fade-in {
            animation: cardFadeIn 0.4s ease-in-out forwards;
        }
        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="status-banner">
        <div class="container px-2">
            <h2>Status & Validasi Sertifikat</h2>
            <p class="text-white-50 small mb-0">Verifikasi data keabsahan sertifikat kompetensi</p>
        </div>
    </div>

    <div class="container px-3 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9 col-12">
                <div class="card search-box-card p-4 bg-white">
                    <form method="GET" action="">
                        <label class="form-label small fw-bold text-secondary mb-2">Masukkan Nomor Blanko atau Nomor Sertifikat:</label>
                        <div class="input-group">
                            <input type="text" name="q" class="form-control form-control-lg shadow-none" placeholder="12345678 atau 00000 0000 0 0000000 2026" value="<?php echo htmlspecialchars($search_query); ?>" style="font-size: 0.92rem; border-color: #cbd5e1;" required>
                            <button type="submit" class="btn text-white px-3 px-sm-4" style="background-color: var(--mandiri-blue); font-weight: 600; font-size: 0.9rem;">
                                <i class="fas fa-search me-1 me-sm-2"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-lg-7 col-md-9 col-12">
                
                <?php if ($searched) { ?>
                    <?php if ($result_data != null) { 
                        $status_sertif = $result_data['status'];
                        $header_class = "result-header-valid";
                        $icon_status = "fa-check-circle";
                        $text_desc = "Sertifikat ini sah, valid, dan terdaftar resmi di pangkalan data nasional LSP Bank Mandiri.";
                        
                        if ($status_sertif == 'Kedaluwarsa') {
                            $header_class = "result-header-expired";
                            $icon_status = "fa-times-circle";
                            $text_desc = "Masa berlaku sertifikat kompetensi ini telah habis. Silakan hubungi admin untuk proses sertifikasi ulang / perpanjangan kualifikasi.";
                        } elseif ($status_sertif == 'Dalam Proses') {
                            $header_class = "result-header-process";
                            $icon_status = "fa-hourglass-half";
                            $text_desc = "Proses pengujian berkas telah selesai. Saat ini berkas sedang dalam tahap pengajuan blangko sertifikat resmi ke BNSP.";
                        }
                    ?>
                        <div class="card result-card border-0 animate-fade-in">
                            <div class="card-header <?php echo $header_class; ?> py-4 px-3 text-center">
                                <i class="fas <?php echo $icon_status; ?> fa-3x mb-2"></i>
                                <h4 class="fw-bold m-0" style="font-size: 1.25rem;">Sertifikat <?php echo htmlspecialchars($status_sertif); ?></h4>
                                <small class="opacity-75 small">Terverifikasi di Sistem Informasi LSP Bank Mandiri</small>
                            </div>
                            <div class="card-body p-3 p-sm-4 bg-white">
                                <div class="row g-3">
                                    
                                    <!-- 1. NAMA PEMILIK -->
                                    <div class="col-12 col-sm-6 border-bottom pb-2">
                                        <div class="info-label">Nama Pemilik</div>
                                        <div class="info-value text-success"><?php echo htmlspecialchars($result_data['nama_asesi']); ?></div>
                                    </div>

                                    <!-- 2. MASA BERLAKU SERTIFIKAT -->
                                    <div class="col-12 col-sm-6 border-bottom pb-2">
                                        <div class="info-label">Masa Berlaku Sertifikat</div>
                                        <div class="info-value <?php echo ($status_sertif == 'Kedaluwarsa') ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo date('d M Y', strtotime($result_data['tanggal_expired'])); ?>
                                        </div>
                                    </div>

                                    <!-- 3. SKEMA SERTIFIKASI -->
                                    <div class="col-12 col-sm-6 border-bottom pb-2">
                                        <div class="info-label">Skema Sertifikasi</div>
                                        <div class="info-value text-dark"><?php echo htmlspecialchars($result_data['skema']); ?></div>
                                    </div>

                                    <!-- 4. BIDANG / LEVEL -->
                                    <div class="col-12 col-sm-6 border-bottom pb-2">
                                        <div class="info-label">Bidang / Level</div>
                                        <div class="info-value text-dark"><?php echo htmlspecialchars($result_data['bidang'] ?? $result_data['bidang_keahlian'] ?? '-'); ?></div>
                                    </div>

                                    <!-- 5. NOMOR BLANKO -->
                                    <div class="col-12 col-sm-6 border-bottom pb-2">
                                        <div class="info-label">Nomor Blanko</div>
                                        <div class="info-value text-dark"><?php echo htmlspecialchars($result_data['nik']); ?></div>
                                    </div>

                                    <!-- 6. NOMOR SERTIFIKAT -->
                                    <div class="col-12 col-sm-6 border-bottom pb-2">
                                        <div class="info-label">Nomor Sertifikat</div>
                                        <div class="info-value text-dark"><?php echo htmlspecialchars($result_data['no_sertifikat']); ?></div>
                                    </div>

                                    <!-- DOKUMEN SERTIFIKAT DIGITAL -->
                                    <div class="col-12 border-bottom pb-2">
                                        <div class="info-label">Dokumen Sertifikat Digital</div>
                                        <div class="info-value mt-1">
                                            <?php 
                                            // Deteksi fleksibel nama kolom database (link_dokumen / file_sertifikat)
                                            $link_dokumen = "";
                                            if (!empty($result_data['link_dokumen'])) {
                                                $link_dokumen = $result_data['link_dokumen'];
                                            } elseif (!empty($result_data['file_sertifikat'])) {
                                                $link_dokumen = (strpos($result_data['file_sertifikat'], 'http') === 0) 
                                                    ? $result_data['file_sertifikat'] 
                                                    : 'uploads/' . $result_data['file_sertifikat'];
                                            }
                                            ?>

                                            <?php if (!empty($link_dokumen)) { ?>
                                                <a href="<?php echo htmlspecialchars($link_dokumen); ?>" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-medium shadow-sm" style="font-size: 0.85rem;">
                                                    <i class="fas fa-file-pdf me-1"></i> Buka / Unduh Dokumen <i class="fas fa-external-link-alt ms-1 small"></i>
                                                </a>
                                            <?php } else { ?>
                                                <span class="text-muted small fw-normal"><i class="fas fa-info-circle me-1"></i>Dokumen digital belum diunggah</span>
                                            <?php } ?>
                                        </div>
                                    </div>

                                </div>
                                
                                <div class="mt-4 p-3 border rounded-3 text-center" style="background-color: #f8fafc; border-style: dashed !important;">
                                    <p class="m-0 text-secondary small" style="line-height: 1.6;"><?php echo $text_desc; ?></p>
                                </div>
                            </div>
                        </div>

                    <?php } else { ?>
                        <div class="card border-0 p-4 text-center bg-white shadow-sm animate-fade-in" style="border-radius: 16px;">
                            <div class="text-danger mb-3"><i class="fas fa-exclamation-triangle fa-3x"></i></div>
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">Data Tidak Ditemukan</h5>
                            <p class="text-secondary small mb-0 px-md-3" style="font-size: 0.85rem; line-height: 1.6;">Maaf, nomor registrasi atau Nomor Blanko "<strong><?php echo htmlspecialchars($search_query); ?></strong>" tidak terdaftar di pangkalan data kelulusan LSP Bank Mandiri. Mohon pastikan kembali penulisan karakter Anda sudah benar.</p>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="text-center py-3 text-secondary opacity-50">
                        <i class="fas fa-id-card-alt fa-2x mb-2"></i>
                        <p class="small mb-0" style="font-size: 0.82rem;">Sistem menunggu input pencarian kata kunci...</p>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>