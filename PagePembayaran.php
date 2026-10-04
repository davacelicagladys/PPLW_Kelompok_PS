<?php
session_start();
require_once __DIR__ . '/callinglibs.php';

// 1. CEK SESSION: Lempar ke dashboard jika tidak ada request booking yang valid
if (!isset($_SESSION['pending_booking'])) {
    header("Location: dashboard.php");
    exit();
}

$pending = $_SESSION['pending_booking'];$db = new DBconnection();

// 2. HANDLE BATALKAN BOOKING
if (isset($_POST['cancel_booking'])) {
    unset($_SESSION['pending_booking']); // Hapus data dari memori
    header("Location: dashboard.php");
    exit();
}

// 3. AMBIL DATA RUANGAN DARI DATABASE UNTUK HITUNG HARGA
$ruangData = null;
$res =$db->send_query("SELECT nama, tarif_per_jam FROM ruang WHERE id = $1", [$pending['ruang_id']]);
if ($res->status && !empty($res->data)) {
    $ruangData =$res->data[0];
}

$totalHarga = 0;
$deposit = 0;
$durasiJam = (int)$pending['durasi'];

if ($ruangData) {$totalHarga = (float)$ruangData['tarif_per_jam'] *$durasiJam;
    $deposit =$totalHarga * 0.5;
}

// 4. PROSES UPLOAD DAN INSERT KE DATABASE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti_bayar'])) {$uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);$fileName = time() . '_' . basename($_FILES['bukti_bayar']['name']);$targetPath = $uploadDir .$fileName;
    
    if (move_uploaded_file($_FILES['bukti_bayar']['tmp_name'],$targetPath)) {
        
        // CUSTOMER SUDAH UPLOAD, SEKARANG BARU KITA INSERT KE DATABASE
        $booking = new Booking($db);$responInsert = $booking->buatRequestBooking($pending['nama_depan'], $pending['nama_belakang'],$pending['no_wa'], 
            $pending['ruang_id'],$pending['tanggal'], $pending['jam_mulai'],$durasiJam
        );
        
        if ($responInsert->status) {
            // Pasang foto bukti pembayaran ke booking yang barusan di-insert
            $updateQuery = "UPDATE request_booking SET bukti_pembayaran = $1 WHERE id = (SELECT id FROM request_booking ORDER BY created_at DESC LIMIT 1)";
            $db->send_query($updateQuery, [$fileName]);
            
            // Bersihkan session
            unset($_SESSION['pending_booking']);$db->close_connection();
            
            // Tampilkan pop-up sukses dan alihkan ke dashboard
            echo "<script>
                alert('Terima kasih, request anda akan segera dikonfirmasi oleh admin by whatsapp.');
                window.location.href = 'dashboard.php';
            </script>";
            exit();
        }
    }
}
$db->close_connection();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Booking - Station Game</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #f1f5f9; min-height: 100vh; padding: 30px 15px; color: #1e293b; }
        .wrapper { max-width: 920px; margin: 0 auto; }
        
        .stepper { display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 25px; }
        .step { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; color: #94a3b8; }
        .step.active { color: #2563eb; }
        .step.done { color: #16a34a; }
        .step-number { width: 26px; height: 26px; border-radius: 50%; background: #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #fff; }
        .step.active .step-number { background: #2563eb; }
        .step.done .step-number { background: #16a34a; }
        .step-line { width: 40px; height: 2px; background: #cbd5e1; }
        
        .timer-box { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 20px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .timer-text { font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 8px; }
        .timer-badge { font-weight: 700; background: #dc2626; color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; }
        
        .container-card { background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); overflow: hidden; }
        .card-header { background: #0f172a; color: #ffffff; padding: 20px 28px; display: flex; justify-content: space-between; align-items: center; }
        .card-header h2 { font-size: 1.15rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        
        .grid-content { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 28px; padding: 28px; }
        @media (max-width: 768px) { .grid-content { grid-template-columns: 1fr; } }
        
        .order-section { display: flex; flex-direction: column; gap: 16px; }
        .section-title { font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; border-bottom: 2px solid #f1f5f9; padding-bottom: 6px; }
        .detail-item { display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 8px; }
        .detail-label { color: #64748b; }
        .detail-value { font-weight: 600; color: #0f172a; }
        .price-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 16px; text-align: center; margin-top: 10px; }
        .price-label { font-size: 0.85rem; color: #1d4ed8; font-weight: 600; }
        .price-amount { font-size: 1.4rem; font-weight: 700; color: #dc2626; margin-top: 4px; }
        .help-box { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-size: 0.85rem; color: #166534; margin-top: auto; }
        
        .payment-section { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 16px; }
        .qris-card { background: #ffffff; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 16px; width: 100%; max-width: 240px; display: flex; flex-direction: column; align-items: center; position: relative; }
        .qris-badge { position: absolute; top: -10px; background: #2563eb; color: #fff; font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 10px; text-transform: uppercase; }
        .qris-img { width: 100%; height: auto; border-radius: 8px; margin: 8px 0; object-fit: cover; }
        .upload-area { width: 100%; }
        .dropzone { border: 2px dashed #94a3b8; border-radius: 10px; padding: 16px; cursor: pointer; transition: 0.2s ease; background: #f8fafc; display: flex; flex-direction: column; align-items: center; gap: 6px; }
        .dropzone:hover { border-color: #16a34a; background: #f0fdf4; }
        .dropzone i { font-size: 1.5rem; color: #64748b; }
        .dropzone p { font-size: 0.85rem; color: #475569; }
        .file-preview { font-size: 0.8rem; color: #16a34a; font-weight: 600; margin-top: 4px; }
        input[type="file"] { display: none; }
        .btn-submit { width: 100%; background: #28a745; color: #ffffff; border: none; padding: 12px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-submit:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="stepper">
            <div class="step done">
                <div class="step-number"><i class="fa-solid fa-check"></i></div>
                <span>Pilih Ruang</span>
            </div>
            <div class="step-line"></div>
            <div class="step active">
                <div class="step-number">2</div>
                <span>Pembayaran</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-number">3</div>
                <span>Selesai</span>
            </div>
        </div>

        <div class="timer-box">
            <div class="timer-text">
                <i class="fa-regular fa-clock"></i>
                <span>Selesaikan Pembayaran Dalam:</span>
            </div>
            <div class="timer-badge" id="countdown">15:00</div>
        </div>

        <div class="container-card">
            <div class="card-header">
                <h2><i class="fa-solid fa-receipt"></i> Detail Pembayaran</h2>
                <span style="font-size: 0.85rem; opacity: 0.8;">ID Booking: #PENDING</span>
            </div>

            <div class="grid-content">
                <div class="order-section">
                    <div class="section-title">Rincian Ruangan</div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Ruangan</span>
                        <span class="detail-value"><?= htmlspecialchars($ruangData['nama'] ?? 'VIP Gaming Room') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Durasi Sesi</span>
                        <span class="detail-value"><?= htmlspecialchars($durasiJam) ?> Jam</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Biaya Sesi</span>
                        <span class="detail-value">Rp <?= number_format($totalHarga, 0, ',', '.') ?></span>
                    </div>

                    <div class="price-box">
                        <div class="price-label">Nominal DP Harus Dibayar (50%)</div>
                        <div class="price-amount">Rp <?= number_format($deposit, 0, ',', '.') ?></div>
                    </div>

                    <div class="help-box">
                        <i class="fa-solid fa-circle-info"></i>
                        <div>Upload bukti pembayaran agar pesanan kamu langsung diverifikasi oleh admin.</div>
                    </div>
                </div>

                <div class="payment-section">
                    <div class="qris-card">
                        <div class="qris-badge">Scan QRIS</div>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=DUMMY_PAYMENT_NAMA_STUDIO" alt="QRIS Code" class="qris-img">
                        <p style="font-size: 0.75rem; color: #64748b; font-weight: 500;">BCA / Mandiri / GoPay / OVO</p>
                    </div>

                    <form action="PagePembayaran.php" method="POST" enctype="multipart/form-data" class="upload-area">
                        <label for="bukti_bayar" class="dropzone">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p><strong>Klik untuk memilih foto</strong> bukti transfer</p>
                            <span class="file-preview" id="fileNameDisplay">Format: JPG, PNG (Max 2MB)</span>
                        </label>
                        <input type="file" name="bukti_bayar" id="bukti_bayar" accept="image/*" required onchange="updateFileName(this)">
                        
                        <button type="submit" class="btn-submit" style="margin-top: 14px;">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Bukti Pembayaran
                        </button>
                    </form>

                    <!-- TOMBOL BATALKAN BOOKING -->
                    <form action="PagePembayaran.php" method="POST" class="upload-area" style="margin-top: -5px;">
                        <button type="submit" name="cancel_booking" class="btn-submit" style="background: #ef4444; color: white;">
                            <i class="fa-solid fa-xmark"></i> Batalkan Booking Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            if (input.files && input.files[0]) {
                display.textContent = 'Terpilih: ' + input.files[0].name;
                display.style.color = '#15803d';
            }
        }

        let time = 15 * 60;
        const countdownEl = document.getElementById('countdown');
        setInterval(() => {
            if (time <= 0) {
                countdownEl.textContent = "Waktu Habis";
                return;
            }
            const minutes = Math.floor(time / 60);
            let seconds = time % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            countdownEl.textContent = `${minutes}:${seconds}`;
            time--;
        }, 1000);
    </script>
</body>
</html>