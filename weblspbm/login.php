<?php
// 1. MEMULAI SESSION & KONEKSI
session_start();
include 'koneksi.php';

// Jika sudah login admin, langsung lempar ke dashboard admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

// AUTO-CONSTRUCT: Memastikan tabel admin ada di database
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    username VARCHAR(50) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL
)");

// SEEDING DEFAULT ACCOUNT: Jika tabel admin kosong, otomatis buat akun default (admin / admin123)
$cek_admin = mysqli_query($koneksi, "SELECT * FROM admin");
if ($cek_admin && mysqli_num_rows($cek_admin) == 0) {
    $default_user = 'admin';
    // Menggunakan Bcrypt untuk pembuatan akun pertama kali
    $default_pass = password_hash('admin123', PASSWORD_DEFAULT);
    mysqli_query($koneksi, "INSERT INTO admin (username, password) VALUES ('$default_user', '$default_pass')");
}

$error_message = "";

if (isset($_POST['login'])) {
    $username = trim(mysqli_real_escape_string($koneksi, $_POST['username']));
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Query berdasarkan username saja terlebih dahulu
        $query_login = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$username' LIMIT 1");
        
        if ($query_login && mysqli_num_rows($query_login) > 0) {
            $row_admin = mysqli_fetch_assoc($query_login);
            
            // SENIOR DEV SECURITY APPLIED: Memverifikasi password Bcrypt
            if (password_verify($password, $row_admin['password'])) {
                // Set data session login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $row_admin['username'];
                
                header("Location: admin.php");
                exit;
            } else {
                $error_message = "Kombinasi Username & Password tidak sesuai.";
            }
        } else {
            $error_message = "Kombinasi Username & Password tidak sesuai.";
        }
    } else {
        $error_message = "Form login tidak boleh kosong.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login CMS Panel - LSP Bank Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --mandiri-blue: #003D79;
            --mandiri-gold: #F2A900;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 61, 121, 0.08);
            width: 100%;
            max-width: 420px;
            background-color: #ffffff;
        }
        .brand-header {
            background-color: var(--mandiri-blue);
            color: #ffffff;
            text-align: center;
            padding: 35px 20px;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        .btn-login {
            background-color: var(--mandiri-blue);
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background-color: var(--mandiri-gold);
            color: var(--mandiri-blue);
        }
    </style>
</head>
<body>

    <div class="container p-3">
        <div class="row justify-content-center">
            <div class="col-12 d-flex justify-content-center">
                <div class="card login-card">
                    <div class="brand-header">
                        <i class="fas fa-user-shield fa-3x mb-3 text-warning"></i>
                        <h4 class="fw-bold m-0" style="font-size: 1.25rem; letter-spacing: 0.5px;">PANEL MANAGEMENT SYSTEM</h4>
                        <small class="opacity-75">Lembaga Sertifikasi Profesi Bank Mandiri</small>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if(!empty($error_message)) { ?>
                            <div class="alert alert-danger border-0 small py-2 mb-3 shadow-sm" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                            </div>
                        <?php } ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                                    <input type="text" name="username" class="form-control bg-light border-start-0 shadow-none small" placeholder="Masukkan username admin" style="font-size: 0.9rem;" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control bg-light border-start-0 shadow-none" placeholder="Masukkan password" style="font-size: 0.9rem;" required>
                                </div>
                            </div>
                            <button type="submit" name="login" class="btn btn-login w-100 py-2.5 rounded-3 mb-2">
                                Masuk ke Dashboard<i class="fas fa-sign-in-alt ms-2"></i>
                            </button>
                            <div class="text-center mt-3">
                                <a href="index.php" class="text-decoration-none small text-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali ke Portal Utama</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>