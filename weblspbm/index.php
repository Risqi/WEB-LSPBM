<?php
// Memanggil koneksi database weblspbm
include 'koneksi.php';

// Ambil data Pengaturan Utama
$query_setting = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$data_setting  = mysqli_fetch_assoc($query_setting);
$nama_web       = !empty($data_setting['nama_web']) ? $data_setting['nama_web'] : "LSP Bank Mandiri";

// Ambil data Statistik
$query_stats = mysqli_query($koneksi, "SELECT * FROM statistik LIMIT 1");
$data_stats  = mysqli_fetch_assoc($query_stats);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nama_web; ?> - Portal Resmi</title>
    
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
            scroll-behavior: smooth;
        }

        /* ==================== 1. HERO SECTION (RESPONSIVE FLUID) ==================== */
        .hero-section {
            background: linear-gradient(rgba(0, 61, 121, 0.45), rgba(0, 61, 121, 0.45)), 
                        url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat;
            min-height: 350px;
            display: flex;
            align-items: center;
            padding: 30px 20px; /* Memberikan ruang napas di HP */
        }
        .hero-card {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 24px; /* Padding ramah mobile (Mobile-First) */
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            max-width: 600px;
            width: 100%;
        }
        .hero-card h1 {
            color: var(--mandiri-blue);
            font-weight: 700;
            font-size: 1.65rem; /* Ukuran teks proporsional di HP */
            line-height: 1.3;
        }
        .hero-card p {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* BREAKPOINT: Optimasi Khusus Layar Tablet & Laptop (Desktop Viewport) */
        @media (min-width: 768px) {
            .hero-section {
                height: 60vh;
                min-height: 520px;
                padding: 0;
            }
            .hero-card {
                padding: 45px;
            }
            .hero-card h1 {
                font-size: 2.3rem;
            }
            .hero-card p {
                font-size: 1rem;
            }
        }

        /* ==================== 2. STATISTIK UTAMA ==================== */
        .section-stats {
            padding: 50px 0; /* Padding vertikal dinamis */
        }
        .stat-card {
            background-color: var(--mandiri-blue);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 61, 121, 0.15);
        }
        .stat-card-title {
            font-weight: 500;
            font-size: 0.95rem;
            color: #d1e1f1;
            margin-bottom: 8px;
        }
        .stat-card-number {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.1;
        }
        
        @media (min-width: 768px) {
            .section-stats { padding: 80px 0; }
            .stat-card-number { font-size: 2.6rem; }
        }
        
        .stat-card-subtitle {
            color: var(--mandiri-gold);
            font-weight: 500;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        .stat-icon {
            font-size: 4.5rem;
            color: rgba(255, 255, 255, 0.08);
            position: absolute;
            bottom: -5px;
            right: 15px;
        }

        /* ==================== 3. SEKSI GALERI FOTO (FIXED RATIO 16:9) ==================== */
        .section-galeri {
            padding: 50px 0;
            background-color: #ffffff;
        }
        @media (min-width: 768px) {
            .section-galeri { padding: 85px 0; }
        }
        .galeri-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            height: 100%;
        }
        .galeri-card:hover {
            transform: scale(1.03);
            box-shadow: 0 12px 25px rgba(0, 61, 121, 0.08);
            border-color: var(--mandiri-blue);
        }
        
        /* REFAKTOR UTAMA: Kunci aspek rasio gambar di semua resolusi screen */
        .galeri-img-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9; /* Menjaga presisi bentuk gambar sinematik */
            overflow: hidden;
            background-color: #eaeaea;
        }
        .galeri-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Mencegah gambar menjadi gepeng atau pecah layout */
            transition: transform 0.5s ease;
        }
        .galeri-card:hover .galeri-img-wrapper img {
            transform: scale(1.1);
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <section class="hero-section">
        <div class="container d-flex justify-content-center justify-content-md-start">
            <div class="hero-card">
                <h2 class="mb-3" style="color: #003D79; font-weight: bold;">Lembaga Sertifikasi Profesi Bank Mandiri</h2>
                <p class="mb-4">Mewujudkan SDM perbankan di lingkungan Bank Mandiri yang kompeten, profesional, dan terstandarisasi BNSP.</p>
                <a href="profil.php" class="btn-hero btn-sm btn-md-lg">
                    Profil Selengkapnya <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="section-stats bg-light">
        <div class="container">
            <div class="text-center mb-5 px-3">
                <h3 class="fw-bold text-dark fs-4 fs-md-2">Statistik Pencapaian</h3>
                <p class="text-muted small">Data performa akumulatif</p>
            </div>
            <div class="row g-3 g-md-4 px-2">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card stat-card p-4">
                        <i class="fas fa-file-signature stat-icon"></i>
                        <div class="stat-card-title">Jumlah Asesi</div>
                        <div class="stat-card-number"><?php echo !empty($data_stats['total_asesi']) ? $data_stats['total_asesi'] : '228.095'; ?></div>
                        <div class="stat-card-subtitle">Asesi Uji Sertifikasi</div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card stat-card p-4">
                        <i class="fas fa-file-contract stat-icon"></i>
                        <div class="stat-card-title">Jumlah Skema</div>
                        <div class="stat-card-number"><?php echo !empty($data_stats['total_skema']) ? $data_stats['total_skema'] : '85'; ?></div>
                        <div class="stat-card-subtitle">Skema Kompetensi</div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card stat-card p-4">
                        <i class="fas fa-landmark stat-icon"></i>
                        <div class="stat-card-title">Jumlah TUK</div>
                        <div class="stat-card-number"><?php echo !empty($data_stats['total_tuk']) ? $data_stats['total_tuk'] : '1.529'; ?></div>
                        <div class="stat-card-subtitle">Tempat Uji Kompetensi</div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card stat-card p-4">
                        <i class="fas fa-user-check stat-icon"></i>
                        <div class="stat-card-title">Jumlah Asesor</div>
                        <div class="stat-card-number"><?php echo !empty($data_stats['total_asesor']) ? $data_stats['total_asesor'] : '10.230'; ?></div>
                        <div class="stat-card-subtitle">Asesor Berlisensi </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="galeri" class="section-galeri">
        <div class="container">
            <div class="text-center mb-5 px-3">
                <h3 class="fw-bold text-dark fs-4 fs-md-2">Galeri Dokumentasi Kegiatan</h3>
                <p class="text-muted small">Dokumentasi pelaksanaan kegiatan pelatihan dan uji sertifikasi LSP Bank Mandiri</p>
            </div>

            <div class="row g-3 g-md-4 px-2">
                <?php
                $get_galeri_front = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY id DESC LIMIT 3");
                if ($get_galeri_front && mysqli_num_rows($get_galeri_front) > 0) {
                    while ($g_row = mysqli_fetch_assoc($get_galeri_front)) {
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card galeri-card shadow-sm">
                        <div class="galeri-img-wrapper">
                            <img src="uploads/<?php echo $g_row['foto']; ?>" alt="Dokumentasi LSP">
                        </div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-primary mb-2" style="font-size: 1.05rem;"><?php echo htmlspecialchars($g_row['judul']); ?></h5>
                            <p class="text-muted small mb-0" style="text-align: justify;"><i class="far fa-calendar-alt me-2"></i><?php echo htmlspecialchars($g_row['deskripsi']); ?></p>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card galeri-card shadow-sm">
                        <div class="galeri-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070" alt="Placeholder">
                        </div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-primary mb-2" style="font-size: 1.05rem;">Sosialisasi Skema Baru</h5>
                            <p class="text-muted small mb-0">Pelaksanaan pengenalan skema secara hybrid di Menara Mandiri.</p>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>