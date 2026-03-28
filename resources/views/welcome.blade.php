<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $namaSekolah = \App\Models\Pengaturan::get('nama_sekolah', 'SIAKAD');
        $logoSekolah = \App\Models\Pengaturan::get('logo_sekolah');
        $alamatSekolah = \App\Models\Pengaturan::get('alamat_sekolah', 'Indonesia');
        $kepalaSekolah = \App\Models\Pengaturan::get('nama_kepala_sekolah', '-');
        $emailSekolah = \App\Models\Pengaturan::get('email_sekolah', '-');
        $teleponSekolah = \App\Models\Pengaturan::get('telepon_sekolah', '-');
        $description = "Sistem Informasi Akademik Terpadu (SIAKAD) " . $namaSekolah . ". Platform modern untuk manajemen nilai, raport digital, dan monitoring siswa secara real-time.";
        $currentUrl = url()->current();
    @endphp

    <title>{{ $namaSekolah }} - Sistem Informasi Akademik</title>

    <!-- Meta Tags -->
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="siakad, sistem informasi akademik, raport digital, nilai siswa, sekolah digital, {{ strtolower($namaSekolah) }}">
    <meta name="author" content="{{ $namaSekolah }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $currentUrl }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:title" content="{{ $namaSekolah }} - Sistem Informasi Akademik">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ $logoSekolah ? asset('storage/' . $logoSekolah) : asset('img/logophi.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $currentUrl }}">
    <meta property="twitter:title" content="{{ $namaSekolah }} - Sistem Informasi Akademik">
    <meta property="twitter:description" content="{{ $description }}">
    <meta property="twitter:image" content="{{ $logoSekolah ? asset('storage/' . $logoSekolah) : asset('img/logophi.png') }}">

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    @php
    echo json_encode([
        "@context" => "https://schema.org",
        "@type" => "School",
        "name" => $namaSekolah,
        "url" => url('/'),
        "logo" => asset('img/logophi.png'),
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => $alamatSekolah,
            "addressLocality" => "Bekasi",
            "addressRegion" => "Jawa Barat",
            "addressCountry" => "ID"
        ],
        "contactPoint" => [
            "@type" => "ContactPoint",
            "telephone" => $teleponSekolah,
            "contactType" => "customer service",
            "email" => $emailSekolah
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    @endphp
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #0f172a;
            --accent: #f59e0b;
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary);
            color: white;
            overflow-x: hidden;
        }

        h1, h2, h3, .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('/bg/school-img.jpeg') center/cover no-repeat;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.9));
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 900px;
            padding: 2rem;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 1.5rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            backdrop-filter: blur(10px);
            background: rgba(15, 23, 42, 0.3);
            border-bottom: 1px solid var(--glass-border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: white;
            text-transform: uppercase;
        }

        .logo img {
            height: 50px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .logo:hover img {
            transform: scale(1.1) rotate(5deg);
        }

        .logo span {
            color: var(--accent);
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            transition: 0.3s;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
        }

        nav a:hover {
            color: var(--accent);
        }

        .btn-outline {
            border: 1px solid var(--glass-border);
            background: var(--glass);
        }

        .btn-accent {
            background: var(--accent);
            color: var(--primary);
        }

        .btn-accent:hover {
            background: #d97706;
            color: white;
            transform: scale(1.05);
        }

        .title {
            font-size: clamp(2rem, 6vw, 3.5rem);
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            font-size: 1.15rem;
            color: #cbd5e1;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        /* Features Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        .stat-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            padding: 1.5rem;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            transition: 0.4s;
            cursor: default;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent);
        }

        .stat-icon {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* About Section */
        .section {
            padding: 6rem 5%;
            background: #0f172a;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .flex-container {
            display: flex;
            align-items: center;
            gap: 4rem;
            flex-wrap: wrap;
        }

        .flex-item {
            flex: 1;
            min-width: 300px;
        }

        .about-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            position: relative;
        }

        .about-image::after {
            content: '';
            position: absolute;
            inset: -15px;
            border: 2px solid var(--accent);
            border-radius: 35px;
            z-index: -1;
            opacity: 0.3;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--accent);
        }

        .section-text {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #94a3b8;
            margin-bottom: 1.5rem;
        }

        .info-list {
            list-style: none;
            margin-top: 2rem;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
            color: #cbd5e1;
        }

        .info-item i {
            color: var(--accent);
            font-size: 1.1rem;
            margin-top: 4px;
        }

        /* CTA Section */
        .cta-section {
            padding: 5rem 5%;
            text-align: center;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .cta-btn {
            display: inline-block;
            padding: 1.1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2);
        }

        /* Footer */
        footer {
            padding: 3rem 5%;
            background: #020617;
            text-align: center;
            border-top: 1px solid var(--glass-border);
        }

        .footer-text {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .flex-container { gap: 3rem; }
            .about-image { height: 350px; }
        }

        @media (max-width: 768px) {
            header { 
                padding: 1rem 4%; 
                backdrop-filter: blur(15px);
                background: rgba(15, 23, 42, 0.8);
            }
            .logo { font-size: 1rem; gap: 8px; }
            .logo img { height: 40px; }
            nav a { margin-left: 0; font-size: 0.8rem; padding: 0.5rem 0.8rem; }
            
            .hero { height: auto; min-height: 100vh; padding: 120px 0 60px; }
            .hero-content { padding: 1rem; width: 100%; max-width: 100%; }
            .title { font-size: clamp(1.8rem, 8vw, 2.8rem); margin-bottom: 1.2rem; line-height: 1.3; }
            .subtitle { font-size: 1rem; margin-bottom: 2rem; padding: 0 10px; }
            
            .stats-grid { 
                grid-template-columns: repeat(2, 1fr); 
                gap: 1rem; 
            }
            .stat-card { padding: 1.2rem 1rem; border-radius: 16px; }
            .stat-icon { font-size: 1.75rem; margin-bottom: 0.5rem; }
            .stat-number { font-size: 1.2rem; }
            .stat-label { font-size: 0.65rem; }
            
            .section { padding: 4rem 5%; }
            .flex-item { min-width: 100%; }
            .about-image { height: 280px; border-radius: 24px; }
            .section-title { font-size: 1.8rem; }
            .cta-section { padding: 4rem 5%; }
            .cta-section .section-title { font-size: 2rem !important; }
        }

        @media (max-width: 480px) {
            .logo span { font-size: 0.9rem; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .title { font-size: 1.8rem; }
            .title br { display: none; }
            .stats-grid { grid-template-columns: 1fr; }
            .stat-card { display: flex; align-items: center; text-align: left; gap: 15px; }
            .stat-icon { margin-bottom: 0; font-size: 1.5rem; width: 40px; text-align: center; }
            .stat-number { font-size: 1.1rem; }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <img src="{{ asset('img/logophi.png') }}" alt="Logo Sekolah">
            <span>{{ $namaSekolah }}</span>
        </div>
        <nav>
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="btn-accent">Masuk</a>
            @endif
        </nav>
    </header>

    <div class="hero">
        <div class="hero-content">
            <h1 class="title">Selamat Datang di Portal Akademik Digital <br> {{ $namaSekolah }}</h1>
            <p class="subtitle">Platform manajemen pendidikan modern untuk meningkatkan efisiensi, akurasi, dan transparansi dalam pengelolaan nilai serta perkembangan siswa.</p>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-graduation-cap stat-icon"></i>
                    <span class="stat-number">Digital</span>
                    <span class="stat-label">Raport Online</span>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chart-line stat-icon"></i>
                    <span class="stat-number">Real-time</span>
                    <span class="stat-label">Monitoring Nilai</span>
                </div>
                <div class="stat-card">
                    <i class="fas fa-file-invoice stat-icon"></i>
                    <span class="stat-number">Legger</span>
                    <span class="stat-label">Rekapitasi Cepat</span>
                </div>
                <div class="stat-card">
                    <i class="fas fa-shield-halved stat-icon"></i>
                    <span class="stat-number">Aman</span>
                    <span class="stat-label">Keamanan Data</span>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="flex-container">
                <div class="flex-item">
                    <img src="{{ asset('bg/school-img2.jpeg') }}" alt="Gedung {{ $namaSekolah }}" class="about-image">
                </div>
                <div class="flex-item">
                    <h2 class="section-title">Profil &amp; Informasi Sekolah</h2>
                    <p class="section-text">
                        {{ $namaSekolah }} berkomitmen untuk menghadirkan layanan pendidikan terbaik dengan dukungan teknologi informasi terkini. Melalui SIAKAD, kami mempermudah kolaborasi antara guru, siswa, dan orang tua.
                    </p>

                    <div class="info-list">
                        <div class="info-item">
                            <i class="fas fa-user-tie"></i>
                            <div>
                                <strong>Kepala Sekolah</strong><br>
                                {{ $kepalaSekolah }}
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-location-dot"></i>
                            <div>
                                <strong>Alamat</strong><br>
                                {{ $alamatSekolah }}
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong><br>
                                {{ $emailSekolah }}
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Telepon</strong><br>
                                {{ $teleponSekolah }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container" style="max-width: 800px;">
            <h2 class="section-title" style="color: white; font-size: 2.5rem;">Ciptakan Masa Depan Digital Bersama Kami</h2>
            <p class="subtitle">Akses data akademik Anda sekarang dengan masuk ke akun SIAKAD.</p>
            @guest
                <a href="{{ route('login') }}" class="btn-accent cta-btn">Pantau Nilai Sekarang</a>
            @else
                <a href="{{ url('/dashboard') }}" class="btn-accent cta-btn">Buka Dashboard Saya</a>
            @endguest
        </div>
    </section>

    <footer>
        <p class="footer-text">&copy; {{ date('Y') }} {{ $namaSekolah }}. All rights reserved.</p>
        <p class="footer-text" style="margin-top: 10px;">Dikembangkan untuk keunggulan manajemen pendidikan digital.</p>
    </footer>

</body>
</html>
