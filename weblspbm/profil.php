<?php
// Memanggil koneksi database weblspbm
include 'koneksi.php';

// Ambil data Pengaturan Utama untuk sinkronisasi header & footer
$query_setting = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$data_setting  = mysqli_fetch_assoc($query_setting);
$nama_web       = !empty($data_setting['nama_web']) ? $data_setting['nama_web'] : "LSP Bank Mandiri";
$link_wa        = "https://wa.me/" . ($data_setting['no_wa_admin'] ?? '6281234567890') . "?text=" . urlencode($data_setting['pesan_default'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Lembaga - <?php echo $nama_web; ?></title>
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
        .profile-banner {
            background: linear-gradient(rgba(0, 61, 121, 0.85), rgba(0, 61, 121, 0.85)), 
                        url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070') center/cover no-repeat;
            padding: 45px 20px; /* Padding minimalis di HP */
            color: #ffffff;
            text-align: center;
        }
        .profile-banner h2 {
            font-weight: 700;
            font-size: 1.8rem; /* Font size ramah mobile */
            margin-bottom: 8px;
        }
        .profile-banner p {
            color: #d1e1f1;
            font-size: 0.95rem;
        }

        /* RESPONSIVE SCALING BREAKPOINT (TABLET TO DESKTOP) */
        @media (min-width: 768px) {
            .profile-banner { padding: 80px 0; }
            .profile-banner h2 { font-size: 2.5rem; }
            .profile-banner p { font-size: 1.1rem; }
        }

        /* ==================== 2. FLUID CONTENT BLOCKS ==================== */
        .section-padding {
            padding: 45px 0; /* Jarak section rapat di HP */
        }
        @media (min-width: 768px) {
            .section-padding { padding: 70px 0; }
        }

        .title-under {
            position: relative;
            padding-bottom: 12px;
            margin-bottom: 20px;
            color: var(--mandiri-blue);
            font-weight: 700;
            font-size: 1.5rem;
        }
        @media (min-width: 768px) {
            .title-under { font-size: 1.75rem; }
        }
        .title-under::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 4px;
            background-color: var(--mandiri-gold);
            border-radius: 2px;
        }

        .lead-text {
            line-height: 1.7;
            color: #4a5568;
            text-align: left; /* Aturan Senior Dev: Teks rata kiri di HP jauh lebih rapi */
            font-size: 0.9rem;
        }
        @media (min-width: 768px) {
            .lead-text { text-align: justify; font-size: 1rem; }
        }

        /* REFAKTOR EMBED LOGO CORPORATE: Aspek Rasio Bersih & Aman Tanpa Stretch */
        .about-img-wrapper {
            width: 98%;
            aspect-ratio: 18 / 8;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 61, 121, 0.05);
            background-color: #ffffff;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        .about-img-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* ==================== 3. VISI MISI RESPONSIVE CARD ==================== */
        .bg-visi-misi {
            background-color: var(--bg-light);
        }
        .card-vision {
            border: none;
            border-top: 5px solid var(--mandiri-gold);
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            height: 100%;
        }
        .card-mission {
            border: none;
            border-top: 5px solid var(--mandiri-blue);
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            height: 100%;
        }
        .mission-list {
            padding-left: 0;
            list-style: none;
            margin-bottom: 0;
        }
        .mission-list li {
            position: relative;
            padding-left: 26px;
            margin-bottom: 12px;
            line-height: 1.6;
            color: #4a5568;
            font-size: 0.9rem;
            text-align: left;
        }
        @media (min-width: 768px) {
            .mission-list li { font-size: 0.95rem; }
        }
        .mission-list li i {
            position: absolute;
            left: 0;
            top: 4px;
            color: var(--mandiri-blue);
        }

        /* ==================== 4. CORE VALUES BOX ==================== */
        .value-box {
            text-align: center;
            padding: 25px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            height: 100%;
            background: #ffffff;
        }
        .value-box:hover {
            border-color: var(--mandiri-blue);
            box-shadow: 0 8px 25px rgba(0, 61, 121, 0.06);
            transform: translateY(-5px);
        }
        .value-icon {
            font-size: 2rem;
            color: var(--mandiri-blue);
            margin-bottom: 12px;
        }
        .value-box h5 {
            font-weight: 700;
            color: var(--mandiri-blue);
            margin-bottom: 8px;
            font-size: 1.1rem;
        }
        .value-box p {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="profile-banner">
        <div class="container px-2">
            <h2>Profil Lembaga</h2>
            <p class="mb-0">Mengenal lebih dekat Lembaga Sertifikasi Profesi Bank Mandiri</p>
        </div>
    </div>

    <div class="container section-padding">
        <div class="row align-items-center g-4 g-lg-5 px-2">
            <div class="col-12 col-lg-6">
                <h3 class="title-under">Filosofi Logo</h3>
                <p class="lead-text mb-3">
                    Lambang identitas dirancang identik dengan logo instansi induk yang diwujudkan dalam gelombang emas cair yang bermakna pencapaian prestasi terbaik dan memiliki kompetensi unggul dan pemilihan huruf kecil pada lambing tulisan yang mencerminkan lembaga yang ramah, sederhana dan menjunjung tinggi profesionalisme.
                </p>
            </div>
            <div class="col-12 col-lg-6">
                <div class="about-img-wrapper">
                    <img src="logo/logo_lspbm.png" alt="Logo Resmi LSP Bank Mandiri">
                </div>
            </div>
        </div>
    </div>

    <section class="section-padding bg-visi-misi">
        <div class="container">
            <div class="row g-4 px-2">
                <div class="col-12 col-md-5">
                    <div class="card card-vision p-3 p-md-4 bg-white">
                        <div class="card-body p-2">
                            <h4 class="fw-bold text-primary mb-3 fs-5";><i class="fas fa-eye me-2"></i> Visi</h4>
                            <p class="lead-text mb-0" style="font-style: italic; color: #4a5568;">
                                "Menjadi Lembaga Sertifikasi Profesi yang profesional, independen dan terpercaya dalam rangka standardisasi kompetensi dibidang jasa keuangan pegawai Mandiri Group."
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7">
                    <div class="card card-mission p-3 p-md-4 bg-white">
                        <div class="card-body p-2">
                            <h4 class="fw-bold text-primary mb-3 fs-5"><i class="fas fa-bullseye me-2"></i> Misi</h4>
                            <ul class="mission-list">
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    Melaksanakan program sertifikasi kompetensi secara profesional, independen dan terpercaya.
                                </li>
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    Memastikan Sumber Daya Manusia yang memiliki standar kompetensi yang diakui secara nasional.
                                </li>
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    Meningkatkan rekognisi dan daya saing Sumber Daya Manusia didalam maupun diluar negeri.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container section-padding">
        <div class="text-center mb-4 mb-md-5 px-3">
            <h3 class="fw-bold text-dark fs-4 fs-md-3 mb-2">Sasaran Mutu</h3>
            <p class="text-muted small mb-0">Pegawai yang telah tersertifikasi, memiliki standar kompetensi yang diakui secara nasional sehingga mampu bersaing didalam maupun diluar negeri dan siap menjadi pemimpin yang memiliki kompetensi terbaik untuk Mandiri Group.</p>
        </div>
        <div class="row g-3 g-md-4 px-2">
            <div class="col-12 col-md-4">
                <div class="value-box">
                    <div class="value-icon"><i class="fas fa-shield-alt"></i></div>
                    <h5>Integritas</h5>
                    <p>Menjaga objektivitas penuh dan kejujuran tanpa kompromi dalam setiap keputusan pengakuan kualifikasi.</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="value-box">
                    <div class="value-icon"><i class="fas fa-chart-line"></i></div>
                    <h5>Aturan Profesional</h5>
                    <p>Melaksanakan standardisasi pengujian mutu kualifikasi berdasarkan keahlian mendalam sesuai praktik terbaik perbankan.</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="value-box">
                    <div class="value-icon"><i class="fas fa-sync"></i></div>
                    <h5>Adaptif</h5>
                    <p>Selalu responsif terhadap pembaharuan regulasi industri keuangan serta akselerasi transformasi digital.</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>