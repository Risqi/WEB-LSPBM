<?php
// PERINTAH UTAMA: Mendeteksi nama file halaman yang sedang aktif dibuka
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
    <div class="container">
        
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="logo/logo_lspbm.png" alt="Logo LSP Bank Mandiri" style="height: 85px; width: auto; object-fit: contain;">
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded fw-medium <?php echo ($current_page == 'index.php' || $current_page == '') ? 'fw-bold text-primary' : 'text-secondary'; ?>" href="index.php" style="<?php echo ($current_page == 'index.php' || $current_page == '') ? 'color: #003D79 !important;' : ''; ?>">
                        Beranda
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded fw-medium <?php echo ($current_page == 'profil.php') ? 'fw-bold text-primary' : 'text-secondary'; ?>" href="profil.php" style="<?php echo ($current_page == 'profil.php') ? 'color: #003D79 !important;' : ''; ?>">
                        Profil
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded fw-medium <?php echo ($current_page == 'skema.php') ? 'fw-bold text-primary' : 'text-secondary'; ?>" href="skema.php" style="<?php echo ($current_page == 'skema.php') ? 'color: #003D79 !important;' : ''; ?>">
                        Skema
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded fw-medium <?php echo ($current_page == 'tuk.php') ? 'fw-bold text-primary' : 'text-secondary'; ?>" href="tuk.php" style="<?php echo ($current_page == 'tuk.php') ? 'color: #003D79 !important;' : ''; ?>">
                        TUK
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded fw-medium <?php echo ($current_page == 'asesor.php') ? 'fw-bold text-primary' : 'text-secondary'; ?>" href="asesor.php" style="<?php echo ($current_page == 'asesor.php') ? 'color: #003D79 !important;' : ''; ?>">
                        Asesor
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded fw-medium <?php echo ($current_page == 'jadwal.php') ? 'fw-bold text-primary' : 'text-secondary'; ?>" href="jadwal.php" style="<?php echo ($current_page == 'jadwal.php') ? 'color: #003D79 !important;' : ''; ?>">
                        Jadwal
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded fw-medium <?php echo ($current_page == 'status.php') ? 'fw-bold text-primary' : 'text-secondary'; ?>" href="status.php" style="<?php echo ($current_page == 'status.php') ? 'color: #003D79 !important;' : ''; ?>">
                        Sertifikat
                    </a>
                </li>
                
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a href="https://wa.me/6281398384500" target="_blank" class="btn text-white px-4 py-2 rounded-pill d-flex align-items-center gap-2 shadow-sm" style="background-color: #003D79; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#F2A900'; this.style.color='#003D79';" onmouseout="this.style.backgroundColor='#003D79'; this.style.color='#ffffff';">
                        <i class="fab fa-whatsapp" style="font-size: 1.1rem;"></i> Hubungi Admin
                    </a>
                </li>
                
            </ul>
        </div>
        
    </div>
</nav>