<?php
require_once __DIR__ . '/callinglibs.php';

$db = new DBconnection();

// Ambil data ruangan
$ruangClass = new Ruang($db);
$listRuang = $ruangClass->getAll()->data ?? [];

// Ambil data unit
$unitClass = new Unit($db);
$listUnit = $unitClass->getAll()->data ?? [];

$db->close_connection();

// Kontak
$waNumber   = '6285732931681';
$igUsername = 'ikhsann_fadhil';
$waLink     = 'https://wa.me/' . $waNumber;
$igLink     = 'https://instagram.com/' . $igUsername;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AKU ADMIN PE ES - Booking Dashboard</title>
    <!-- Font Google Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { 
            font-family: 'Poppins', sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #f8fafc; 
            color: #1e293b; 
        }

        /* Navbar Menyatu dengan Hero Gelap */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #111827; 
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            font-size: 1.1em;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: 0.5px;
            flex: 1;
        }

        .nav-menu {
            display: flex;
            gap: 32px;
            align-items: center;
            justify-content: center;
            flex: 2;
        }

        .nav-link {
            color: #9ca3af;
            text-decoration: none;
            font-size: 0.95em;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #ff4757;
        }

        .nav-spacer {
            flex: 1;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 15px 20px;
            }
            .nav-spacer { display: none; }
            .nav-brand { text-align: center; }
            .nav-menu { gap: 20px; }
        }

        /* Hero Section Gelap */
        .hero { 
            background-color: #111827;
            padding: 60px 20px 80px; 
            text-align: center; 
            position: relative;
            color: #ffffff;
        }
        .hero h1 { 
            margin: 0 0 15px 0; 
            font-size: 3em; 
            font-weight: 700;
            color: #ffffff;
        }
        .hero p { 
            font-size: 1.05em; 
            margin-bottom: 35px; 
            color: #9ca3af;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-booking { 
            background: #ff4757; 
            color: white; 
            padding: 14px 36px; 
            text-decoration: none; 
            font-size: 1em; 
            border-radius: 50px; 
            font-weight: 600; 
            transition: all 0.3s ease; 
            display: inline-block; 
        }
        .btn-booking:hover { 
            background: #ff6b81;
            transform: translateY(-2px); 
        }
        
        .container { max-width: 1200px; margin: 50px auto; padding: 0 20px; }
        .section-title { 
            text-align: center; 
            margin-bottom: 40px; 
            font-size: 2.2em; 
            font-weight: 700;
            color: #1f2937;
        }

        /* Grid System */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }

        /* Card Putih / Light Style */
        .card { 
            background: #ffffff; 
            border-radius: 16px; 
            padding: 24px; 
            transition: all 0.3s ease; 
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        .card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
        }

        /* Image Styling */
        .card-img-wrapper {
            width: 100%;
            height: 180px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 15px;
            background: #f1f5f9;
        }
        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card h3 { 
            margin: 12px 0 8px 0; 
            color: #111827; 
            font-size: 1.25em; 
            font-weight: 700;
        }
        .card p {
            color: #6b7280;
            font-size: 0.9em;
            line-height: 1.5;
            margin-top: 12px;
            margin-bottom: 15px;
        }

        .badge { 
            display: inline-block; 
            padding: 4px 14px; 
            background: #fef3c7; 
            color: #d97706; 
            border-radius: 20px; 
            font-size: 20px; 
            font-weight: 600; 
            width: fit-content;
        }
        .price { 
            font-size: 1.25em; 
            font-weight: 700; 
            color: #10b981; 
            margin-top: auto;
            padding-top: 10px;
        }

        /* Scroll Horizontal Unit */
        .unit-scroll-container {
            display: flex;
            gap: 25px;
            overflow-x: auto;
            padding: 10px 5px 25px 5px;
            scroll-behavior: smooth;
        }
        .unit-scroll-container .card {
            min-width: 300px;
            max-width: 320px;
            flex-shrink: 0;
        }

        /* Features Section */
        .features-section { margin-top: 10px; margin-bottom: 80px; }
        .feature-card { 
            background: #ffffff; 
            border-radius: 16px; 
            padding: 24px; 
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .feature-header { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .feature-box { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
        .feature-card h3 { margin: 0; color: #111827; font-size: 0.95em; font-weight: 700; }
        .feature-card p { font-size: 0.88em; color: #6b7280; line-height: 1.5; margin: 0; }
        
        .icon-red    { background-color: #ef4444; }
        .icon-blue   { background-color: #3b82f6; }
        .icon-orange { background-color: #f97316; }
        .icon-purple { background-color: #8b5cf6; }
        .icon-yellow { background-color: #eab308; }
        .icon-green  { background-color: #10b981; }
        .icon-teal   { background-color: #14b8a6; }

        /* Stock Tag */
        .stock-tag {
            font-size: 0.85em;
            color: #4b5563;
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 10px;
        }

        /* Contact Section Gelap */
        .contact-section { 
            background: #111827; 
            color: white; 
            text-align: center; 
            padding: 60px 20px; 
            margin-top: 80px; 
        }
        .contact-section h2 { margin: 0 0 10px 0; font-size: 2em; font-weight: 700; }
        .contact-section p { color: #9ca3af; margin: 0 0 30px 0; }
        .contact-buttons { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; }
        .btn-contact { display: inline-flex; align-items: center; gap: 10px; padding: 12px 30px; border-radius: 50px; color: white; text-decoration: none; font-weight: 600; }
        .btn-wa { background-color: #25d366; }
        .btn-ig { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
    </style>
</head>
<body>

    <!-- Header Navigasi -->
    <header class="navbar">
        <a href="#" class="nav-brand">AKU ADMIN PE ES</a>
        
        <nav class="nav-menu">
            <a href="#" class="nav-link">Home</a>
            <a href="#katalog-ruangan" class="nav-link">Katalog Ruangan</a>
            <a href="#unit-tersedia" class="nav-link">Unit</a>
        </nav>

        <div class="nav-spacer"></div>
    </header>

    <!-- Hero Section Gelap -->
    <div class="hero">
        <h1>AKU ADMIN PE ES Facilities</h1>
        <p>Lokasi: Pusat Kota Surabaya. Menyediakan Ruangan & Fasilitas Terbaik untuk Kebutuhan Anda.</p>
        <a href="bookingpage.php" class="btn-booking">Booking Jadwal Sekarang</a>
    </div>

    <!-- Konten Utama -->
    <div class="container">
        
        <!-- 1. Section Informasi Fasilitas / Fitur -->
        <div class="features-section">
            <div class="grid">
                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-box icon-red"></div>
                        <h3>CONSOLE LENGKAP & BERSAHABAT</h3>
                    </div>
                    <p>Main puas tanpa mikir budget. Console favorit lengkap dengan harga ramah di kantong plus pilihan private room.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-box icon-blue"></div>
                        <h3>SMART BOOKING & LOYALTY</h3>
                    </div>
                    <p>Cek slot kosong dan booking langsung via web. Setiap sesi main kumpulin poin yang bisa dipakai lagi nanti.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-box icon-orange"></div>
                        <h3>COZY, ESTETIK & NYAMAN</h3>
                    </div>
                    <p>Full AC, desain modern, non smoking area terpisah, nyaman buat mabar lama tanpa gerah.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-box icon-purple"></div>
                        <h3>PRIVATE ROOM SESUAI STYLE</h3>
                    </div>
                    <p>Mau movie date, mabar rame, atau serius ranked? Ada pilihan VIP sampai Suite Room.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-box icon-yellow"></div>
                        <h3>AKSESORIS LENGKAP & GRATIS</h3>
                    </div>
                    <p>Butuh stik tambahan atau aksesoris lain? Santai, pinjam gratis dan nggak ribet.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-box icon-green"></div>
                        <h3>FOOD & DRINKS READY</h3>
                    </div>
                    <p>Snack, minuman, sampai menu kenyang siap nemenin sesi gaming kamu.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-header">
                        <div class="feature-box icon-teal"></div>
                        <h3>GAMING & MOVIE DATE</h3>
                    </div>
                    <p>Nggak cuma main game. Bisa Netflix, HBO, atau quality time bareng pasangan dan keluarga.</p>
                </div>
            </div>
        </div>

        <!-- 2. Katalog Ruangan -->
        <h2 id="katalog-ruangan" class="section-title">Katalog Ruangan</h2>
        <div class="grid">
            <?php if(empty($listRuang)): ?>
                <p style="text-align:center; grid-column: 1/-1; color: #9ca3af;">Belum ada data ruangan yang aktif.</p>
            <?php else: ?>
                <?php foreach($listRuang as $r): ?>
                    <div class="card">
                        <div>
                            <div class="card-img-wrapper">
                                <?php if(!empty($r['foto'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($r['foto']) ?>" alt="<?= htmlspecialchars($r['nama'] ?? 'Foto Ruangan') ?>">
                                <?php else: ?>
                                    <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#9ca3af; font-size:0.85em;">No Image Available</div>
                                <?php endif; ?>
                            </div>
                            <!-- Hanya Menampilkan Badge Kategori Kuning -->
                            <span class="badge"><?= htmlspecialchars($r['nama_kategori'] ?? 'Umum') ?></span>
                            <p><?= htmlspecialchars($r['deskripsi']) ?></p>
                        </div>
                        <div class="price">Rp <?= number_format((float)($r['tarif_per_jam'] ?? $r['tarif'] ?? 0), 0, ',', '.') ?> <span style="font-size: 0.7em; font-weight: normal; color: #6b7280;">/ jam</span></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 3. Unit Tersedia -->
        <h2 id="unit-tersedia" class="section-title" style="margin-top: 80px;">Unit Tersedia</h2>
        <div class="unit-scroll-container">
            <?php if(empty($listUnit)): ?>
                <p style="text-align:center; width: 100%; color: #9ca3af;">Belum ada data unit yang aktif.</p>
            <?php else: ?>
                <?php foreach($listUnit as $u): ?>
                    <div class="card">
                        <div>
                            <div class="card-img-wrapper">
                                <?php if(!empty($u['foto'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($u['foto']) ?>" alt="<?= htmlspecialchars($u['nama'] ?? 'Foto Unit') ?>">
                                <?php else: ?>
                                    <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#9ca3af; font-size:0.85em;">No Image Available</div>
                                <?php endif; ?>
                            </div>
                            <span class="badge" style="background: #dcfce7; color: #16a34a;"><?= htmlspecialchars($u['nama_kategori'] ?? 'Umum') ?></span>
                            <h3><?= htmlspecialchars($u['nama']) ?></h3>
                            <p><?= htmlspecialchars($u['deskripsi']) ?></p>
                        </div>
                        <div class="stock-tag">
                            Stock Tersedia: <strong style="color: #16a34a;"><?= $u['jumlah_unit'] ?> unit</strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <!-- Contact Section Gelap -->
    <div class="contact-section">
        <h2>Hubungi Kami</h2>
        <p>Ada pertanyaan? Chat kami atau ikuti update terbaru di Instagram.</p>
        <div class="contact-buttons">
            <a href="<?= htmlspecialchars($waLink) ?>" target="_blank" rel="noopener" class="btn-contact btn-wa">WhatsApp</a>
            <a href="<?= htmlspecialchars($igLink) ?>" target="_blank" rel="noopener" class="btn-contact btn-ig">@<?= htmlspecialchars($igUsername) ?></a>
        </div>
    </div>

</body>
</html>