<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>IF1A2 TaskMeet - Pengelolaan Rapat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f9f9f9;
    }

    nav {
      background-color: #311432;
      text-align: center;
      padding: 10px 0;
      position: sticky;
      top: 0;
      z-index: 1000;
      transition: all 0.3s ease;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin: 0 15px;
      font-weight: bold;
      position: relative;
      padding-bottom: 5px;
      transition: color 0.3s ease;
    }

    nav a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 50%;
      background-color: #D7BFDC;
      transition: all 0.3s ease;
    }

    nav a:hover {
      color: #D7BFDC;
    }

    nav a:hover::after {
      width: 100%;
      left: 0;
    }

    header {
      background: linear-gradient(180deg, #784B84, #D7BFDC);
      color: white;
      padding: 20px 0;
      text-align: center;
    }

    .hero {
      background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url(https://p2m.polibatam.ac.id/wp-content/uploads/2020/01/foto-poltek-kompress-Copy-1536x811.jpg) center/cover;
      color: white;
      text-align: center;
      padding: 200px 20px;
    }

    .hero h1 {
      font-size: 35px;
      margin-bottom: 15px;
    }

    .hero p {
      font-size: 18px;
      margin-bottom: 25px;
    }

    .hero a {
      background: #D7BFDC;
      color: black;
      padding: 12px 25px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      display: inline-block;
      transition: all 0.3s ease;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .hero a:hover {
      background: #784B84;
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    }

    .features {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      padding: 40px 20px;
      background: white;
    }

    .feature-box {
      background: #F3E5F5;
      border-radius: 10px;
      padding: 20px;
      margin: 15px;
      width: 280px;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      opacity: 0; 
      transform: translateY(20px);
    }

    .feature-box.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    .feature-box:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }

    .feature-box h3 {
      font-size: medium;
      font-weight: bold;
      color: #311432;
      text-align: center;
    }

    .about-section {
      background-color: #f8f8ff;
      padding: 60px 20px;
      text-align: center;
    }

    .about-section h2 {
      color: #311432;
      font-weight: 700;
      margin-bottom: 20px;
      opacity: 0; 
      transform: translateY(20px);
      transition: all 0.5s ease;
    }

    .about-section h2.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    .about-section p {
      color: #333;
      font-size: 16px;
      line-height: 1.7;
      margin: 10px auto;
      max-width: 800px;
    }

    .card {
      transition: all 0.3s ease;
      opacity: 0;
      transform: translateY(20px);
    }

    .card.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }

    .card:hover .card-img-top {
      transform: scale(1.05);
    }

    .card-img-top {
      transition: transform 0.5s ease;
    }

    .contact-section {
      padding: 60px 20px;
      text-align: center;
    }

    .contact-section h2 {
      color: #311432;
      font-weight: 700;
      margin-bottom: 20px;
      opacity: 0; 
      transform: translateY(20px); 
      transition: all 0.5s ease;
    }

    .contact-section h2.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    .contact-section p {
      color: #555;
      font-size: 16px;
      margin-bottom: 30px;
    }

    .contact-links {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 40px;
    }

    .contact-link-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-decoration: none;
      color: #311432;
      transition: all 0.3s ease;
      opacity: 0; 
      transform: translateY(20px); 
    }

    .contact-link-item.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    .contact-link-item:hover {
      transform: translateY(-10px) scale(1.05);
      color: #784B84;
    }

    .contact-link-item i {
      font-size: 48px;
      margin-bottom: 10px;
      transition: all 0.3s ease;
    }

    .contact-link-item:hover i {
      transform: rotate(10deg) scale(1.1);
    }

    .contact-link-item span {
      font-size: 16px;
      font-weight: 500;
    }

    footer {
      text-align: center;
      padding: 25px 15px;
      background: #784B84;
      color: white;
      font-size: 14px;
    }

    .footer-contact {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 20px;
      margin-bottom: 15px;
    }

    .footer-contact a {
      color: white;
      font-size: 24px;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .footer-contact a:hover {
      color: #D7BFDC;
      transform: scale(1.2) rotate(15deg);
    }
  </style>
</head>
<body>
  <header>
    <h1 class="fw-bold">TaskMeet</h1>
    <p>Platform Pengelolaan Rapat yang Efisien</p>
  </header>

  <nav>
    <a href="#beranda">Beranda</a>
    <a href="#fitur">Fitur</a>
    <a href="#tentang">Tentang</a>
    <a href="#kontak">Kontak</a>
  </nav>

  <section class="hero" id="beranda">
    <h1 class="fw-bold">Kelola Rapat Lebih Mudah Bersama TaskMeet</h1>
    <p>Atur jadwal dan koordinasikan tim Anda secara online.</p>
    <a href="../autentikasi/login.php">Mulai Sekarang</a>
  </section>

  <section class="features" id="fitur">
    <div class="feature-box animate-on-scroll">
      <h3>📅 Penjadwalan Otomatis</h3>
      <p>Atur jadwal rapat dengan sistem pengingat otomatis dan sinkronisasi kalender.</p>
    </div>
    <div class="feature-box animate-on-scroll">
      <h3>👥 Manajemen Anggota</h3>
      <p>Tambah, hapus, dan kelola anggota tim dengan akses yang terkontrol.</p>
    </div>
  </section>
  <div class="container my-5" id="tentang">
    <h2 class="text-center mb-4 fw-bold animate-on-scroll">Anggota Tim PBL</h2>
<div class="row">
    <div class="col-md-3 mb-3">
    <div class="card shadow-sm animate-on-scroll">
        <img src="format_gambar/qeypp.png" class="card-img-top" alt="Foto Ketua">
    <div class="card-body text-center">
        <h5 class="card-title fw-bold">Ketua Kelompok</h5>
        <p class="card-text">Kontribusi: Homepage dan Dashboard Pengguna.</p>
    <a href="#"></a>
</div></div> </div>
    <div class="col-md-3 mb-3">
    <div class="card shadow-sm animate-on-scroll">
        <img src="format_gambar/rizkapp.png" class="card-img-top" alt="Foto Anggota 1">
    <div class="card-body text-center">
        <h5 class="card-title fw-bold">Anggota 1</h5>
        <p class="card-text">Kontribusi: Wireframe dan Dashboard Admin.</p>
    <a href="#"></a>
</div></div> </div>
    <div class="col-md-3 mb-3">
    <div class="card shadow-sm animate-on-scroll">
        <img src="format_gambar/iqbalpp.png" class="card-img-top" alt="Foto Anggota 2">
    <div class="card-body text-center">
        <h5 class="card-title fw-bold">Anggota 2</h5>
        <p class="card-text">Kontribusi: Daftar User dan History Pengguna.</p>
    <a href="#"></a>
</div></div> </div>
<div class="col-md-3 mb-3">
    <div class="card shadow-sm animate-on-scroll">
        <img src="format_gambar/nabilapp.png" class="card-img-top" alt="Foto Anggota 3">
    <div class="card-body text-center">
        <h5 class="card-title fw-bold">Anggota 3</h5>
        <p class="card-text">Kontribusi: Login, sign in page, dan Daftar Agenda Rapat.</p>
    <a href="#"></a>
</div></div> </div></div></div>

  <section id="kontak" class="contact-section">
    <div class="container">
      <h2 class="fw-bold animate-on-scroll">Hubungi Kami</h2>
      <p>Ada pertanyaan atau butuh bantuan? Hubungi kami langsung melalui:</p>
      
      <div class="contact-links">
        <a href="https://wa.me/6281275838648" target="_blank" class="contact-link-item animate-on-scroll">
          <i class="fab fa-whatsapp" style="color: #25D366;"></i>
          <span>WhatsApp</span>
        </a>
        <a href="mailto:taskmeetif1a@gmail.com" class="contact-link-item animate-on-scroll">
          <i class="fas fa-envelope" style="color: #D44638;"></i>
          <span>Email</span>
        </a>
      </div>
    </div>
  </section>

  <footer>
    <div class="footer-contact">
        <a href="https://wa.me/6281275838648" target="_blank" title="Hubungi kami via WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="mailto:taskmeetif1a@gmail.com" title="Kirim email kepada kami">
            <i class="fas fa-envelope"></i>
        </a>
    </div>
    &copy; 2025 TaskMeet | Dibuat untuk Proyek Pengelolaan Rapat
  </footer>

  <script>

    const observerOptions = {
      root: null, 
      rootMargin: '0px',
      threshold: 0.1 
    };

    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    animatedElements.forEach(element => {
      observer.observe(element);
    });
  </script>
</body>
</html>