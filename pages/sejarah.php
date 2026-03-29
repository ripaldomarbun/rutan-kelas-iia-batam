<?php
require_once __DIR__ . '/../backend/config/config.php';
$pageTitle    = 'Sejarah – Rutan Kelas IIA Batam';
$halamanAktif = 'sejarah';
$extraCss     = ['sejarah.css'];
include __DIR__ . '/../header.php';
?>

  <!-- ── PAGE HEADER: Banner judul halaman ── -->
  <div class="page-header">
    <div class="page-header-overlay"></div>
    <div class="page-header-inner">
      <!-- Breadcrumb: navigasi posisi halaman -->
      <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/index.php">Beranda</a>
        <span>›</span>
        <a href="#">Tentang</a>
        <span>›</span>
        <span class="breadcrumb-active">Sejarah Rutan</span>
      </div>
      <h1 class="page-title">Sejarah Rutan<br/><span>Kelas IIA Batam</span></h1>
      <p class="page-subtitle">Perjalanan panjang menuju pemasyarakatan yang berkualitas</p>
    </div>
  </div>

  <!-- ── KONTEN UTAMA ── -->
  <main class="page-main">
    <div class="page-container">

      <!-- Sidebar kiri: navigasi menu tentang -->
      <aside class="page-sidebar">
        <div class="sidebar-title">Menu Tentang</div>
        <ul class="sidebar-menu">
          <li><a href="<?= BASE_URL ?>/sejarah" class="active">📜 Sejarah Rutan</a></li>
          <li><a href="<?= BASE_URL ?>/visi-misi">🎯 Visi, Misi &amp; Tujuan</a></li>
          <li><a href="<?= BASE_URL ?>/struktur">🏛️ Struktur Organisasi</a></li>
          <li><a href="<?= BASE_URL ?>/tupoksi">📋 Tugas Pokok &amp; Fungsi</a></li>
          <li><a href="<?= BASE_URL ?>/pejabat">👤 Profil Pejabat</a></li>
        </ul>
      </aside>

      <!-- Konten sejarah -->
      <article class="page-content">

        <!-- Gambar utama -->
        <div class="content-img-box">
          <img src="<?= BASE_URL ?>/images/gedung.png" alt="Gedung Rutan Kelas IIA Batam" class="content-img"/>
          <div class="content-img-caption">Gedung Rutan Kelas IIA Batam, Jl. Raya Trans Barelang</div>
        </div>

        <!-- Isi teks sejarah -->
        <div class="content-body">

          <h2 class="content-heading">Latar Belakang Berdirinya Rutan Kelas IIA Batam</h2>
          <p>
            Rumah Tahanan Negara (Rutan) Kelas IIA Batam merupakan unit pelaksana teknis di bawah
            Kementerian Imigrasi dan Pemasyarakatan Republik Indonesia. Rutan ini didirikan untuk
            memenuhi kebutuhan penampungan tahanan di wilayah Kota Batam yang terus berkembang
            sebagai kota industri dan perdagangan internasional.
          </p>

          <h2 class="content-heading">Perkembangan dari Masa ke Masa</h2>
          <p>
            Seiring dengan pertumbuhan pesat Kota Batam sebagai kawasan ekonomi khusus, kebutuhan
            akan fasilitas pemasyarakatan yang memadai semakin meningkat. Rutan Kelas IIA Batam
            hadir sebagai jawaban atas kebutuhan tersebut, dengan fasilitas yang terus diperbaharui
            untuk memberikan pelayanan terbaik.
          </p>

          <!-- Garis waktu / timeline -->
          <h2 class="content-heading">Tonggak Sejarah</h2>

          <div class="timeline">

            <div class="timeline-item">
              <div class="timeline-dot"></div>
              <div class="timeline-content">
                <div class="timeline-year">Pendirian</div>
                <div class="timeline-title">Berdirinya Rutan Batam</div>
                <p class="timeline-desc">
                  Rutan Kelas IIA Batam resmi berdiri sebagai unit pelaksana teknis
                  di bawah Departemen Kehakiman Republik Indonesia untuk melayani
                  wilayah Batam dan sekitarnya.
                </p>
              </div>
            </div>

            <div class="timeline-item">
              <div class="timeline-dot"></div>
              <div class="timeline-content">
                <div class="timeline-year">Pengembangan</div>
                <div class="timeline-title">Peningkatan Kapasitas dan Fasilitas</div>
                <p class="timeline-desc">
                  Dilakukan berbagai pengembangan infrastruktur untuk meningkatkan
                  kapasitas dan kualitas pelayanan kepada warga binaan pemasyarakatan
                  di wilayah Kota Batam.
                </p>
              </div>
            </div>

            <div class="timeline-item">
              <div class="timeline-dot"></div>
              <div class="timeline-content">
                <div class="timeline-year">Reformasi</div>
                <div class="timeline-title">Era Pemasyarakatan Modern</div>
                <p class="timeline-desc">
                  Rutan Kelas IIA Batam bertransformasi mengikuti paradigma baru
                  pemasyarakatan yang lebih humanis, dengan fokus pada pembinaan
                  dan reintegrasi sosial warga binaan.
                </p>
              </div>
            </div>

            <div class="timeline-item">
              <div class="timeline-dot timeline-dot-active"></div>
              <div class="timeline-content">
                <div class="timeline-year">Saat Ini</div>
                <div class="timeline-title">Menuju Zona Integritas</div>
                <p class="timeline-desc">
                  Rutan Kelas IIA Batam berkomitmen menuju Wilayah Bebas Korupsi (WBK)
                  dan Wilayah Birokrasi Bersih Melayani (WBBM) di bawah naungan
                  Kementerian Imigrasi dan Pemasyarakatan.
                </p>
              </div>
            </div>

          </div><!-- /timeline -->

          <h2 class="content-heading">Komitmen Kami</h2>
          <p>
            Hingga saat ini, Rutan Kelas IIA Batam terus berbenah dan berinovasi dalam
            memberikan pelayanan pemasyarakatan yang terbaik. Dengan semangat <em>"Semakin Bestari"</em>,
            seluruh jajaran Rutan Kelas IIA Batam berkomitmen untuk menjadi institusi
            pemasyarakatan yang transparan, akuntabel, dan profesional.
          </p>

        </div><!-- /content-body -->
      </article>

    </div><!-- /page-container -->
  </main>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
