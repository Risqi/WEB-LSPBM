<?php
// Pengaman: Jika variabel nama web belum dipanggil dari database halaman utama, set default
if (!isset($nama_web)) {
    $nama_web = "LSP Bank Mandiri";
}
?>

<footer class="footer-section" style="background-color: #002d59; color: #ffffff; padding: 60px 0 20px; font-size: 0.9rem; border-top: 4px solid #F2A900;">
    <div class="container">
        <div class="row g-4">
            

            
            <div class="col-md-4 col-sm-12" style="text-align: center;">
                <h4 style="font-size: 1.05rem; font-weight: 600; text-transform: uppercase; margin-bottom: 22px; color: #F2A900; letter-spacing: 0.5px;">
                    <i class="fas fa-headset me-2"></i>Kontak & Jam Kerja
                </h4>
                <p style="color: #cbd5e1; line-height: 1.7; margin-bottom: 10px;">
                    <i class=style="width: 25px; color: #F2A900;"></i> lsp.bankmandiri@gmail.com
                </p>
                <p style="color: #cbd5e1; line-height: 1.7; margin-bottom: 10px;">
                    <i class=style="width: 25px; color: #F2A900;"></i> 0813-9838-4500 (WhatsApp)
                </p>
                <p style="color: #cbd5e1; line-height: 1.7; margin-bottom: 0;">
                    <i class=style="width: 25px; color: #F2A900;"></i> Senin - Jumat:<br>
                    <span style=display: inline-block; font-size: 0.85rem; color: #a4b5cb;">08.00 - 17.00 WIB</span>
                </p>
            </div>


            <div class="col-md-4 col-sm-12" style="text-align: center;">
                <h4 style="font-size: 1.05rem; font-weight: 600; text-transform: uppercase; margin-bottom: 22px; color: #F2A900; letter-spacing: 0.5px;">
                    <i class="fas fa-building me-2"></i>Alamat Utama
                </h4>
                <p style="color: #cbd5e1; line-height: 1.7; margin-bottom: 0; text-align: justify;">
                    Menara Mandiri Lantai 5, Jl. Daan Mogot No. 3, Wijayakusuma, Grogol Petamburan, Jakarta Barat, 11460, Indonesia
                </p>
            </div>


            
            <div class="col-md-4 col-sm-12" style="text-align: center;">
                <h4 style="font-size: 1.05rem; font-weight: 600; text-transform: uppercase; margin-bottom: 10px; color: #F2A900; letter-spacing: 0.5px;">
                    <i class="fas fa-shield-alt me-2"></i>Lisensi<br></br>
                
                <div style="margin-bottom: 20px; display: block;">
                    <img src="logo/logo_bnsp.png" alt="Badan Nasional Sertifikasi Profesi" style="width: auto; max-width: 240px; height: 60px; object-fit: contain;">
                </div>

                <p style="padding-left: 8px; color: #cbd5e1; line-height: 1; margin-bottom: 10px;">
                    BNSP-LSP-2446-ID
                </p>
            </div>    
        </div>
        
        <div class="copyright" style="text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.15); padding-top: 20px; margin-top: 40px; font-size: 0.8rem; color: #94a3b8;">
            &copy; <?php echo date("Y"); ?> Lembaga Sertifikasi Profesi (LSP) Bank Mandiri.
            <br>M. Achsanul Muchlis Al-Risqi
        </div>
        
    </div>
</footer>