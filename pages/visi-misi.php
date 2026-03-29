<?php
require_once __DIR__ . '/../backend/config/config.php';
$pageTitle    = 'Visi & Misi – Rutan Kelas IIA Batam';
$halamanAktif = 'visi-misi';
$cssBase      = '../';
$extraCss = ['sejarah.css', 'visi-misi.css']; // ← tambah sejarah.css di sini
include __DIR__ . '/../header.php';
?>
  <!-- ── PAGE HEADER ── -->
<div class="page-header">
  <div class="page-header-overlay"></div>
  <div class="page-header-inner">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>/index.php">Beranda</a>
      <span>›</span>
      <a href="#">Tentang</a>
      <span>›</span>
      <span class="breadcrumb-active">Visi, Misi &amp; Tujuan</span>
    </div>
    <h1 class="page-title">Visi, Misi<br/><span>&amp; Tujuan</span></h1>
    <p class="page-subtitle">Landasan arah dan cita-cita Rutan Kelas IIA Batam</p>
  </div>
</div>

  <!-- ── KONTEN UTAMA ── -->
  <main class="page-main">
    <div class="page-container">

      <!-- Sidebar -->
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

      <!-- Konten -->
      <article class="page-content">
        <div class="content-body">

          <!-- ── VISI ── -->
          <div class="vm-section">
            <div class="vm-icon-wrap vm-icon-visi">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </div>
            <div class="vm-label">VISI</div>
            <h2 class="vm-title">Visi Rutan Kelas IIA Batam</h2>
            <div class="vm-quote">
              "Terwujudnya Rutan Kelas IIA Batam yang Profesional, Bersih, dan Humanis dalam Mendukung Sistem Pemasyarakatan Nasional"
            </div>
          </div>

          <div class="vm-divider"></div>

          <!-- ── MISI ── -->
          <div class="vm-section">
            <div class="vm-icon-wrap vm-icon-misi">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <polyline points="9 11 12 14 22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
              </svg>
            </div>
            <div class="vm-label">MISI</div>
            <h2 class="vm-title">Misi Rutan Kelas IIA Batam</h2>

            <div class="misi-list">

              <div class="misi-item">
                <div class="misi-number">01</div>
                <div class="misi-text">
                  <div class="misi-title">Pelayanan Prima</div>
                  <p>Memberikan pelayanan yang prima, transparan, dan akuntabel kepada seluruh masyarakat yang membutuhkan layanan Rutan Kelas IIA Batam.</p>
                </div>
              </div>

              <div class="misi-item">
                <div class="misi-number">02</div>
                <div class="misi-text">
                  <div class="misi-title">Pembinaan Berkualitas</div>
                  <p>Menyelenggarakan pembinaan kepribadian dan kemandirian bagi warga binaan pemasyarakatan secara humanis dan berkeadilan.</p>
                </div>
              </div>

              <div class="misi-item">
                <div class="misi-number">03</div>
                <div class="misi-text">
                  <div class="misi-title">Keamanan dan Ketertiban</div>
                  <p>Menjaga keamanan dan ketertiban lingkungan Rutan secara profesional demi terciptanya kondisi yang kondusif bagi seluruh penghuni.</p>
                </div>
              </div>

              <div class="misi-item">
                <div class="misi-number">04</div>
                <div class="misi-text">
                  <div class="misi-title">Integritas Aparatur</div>
                  <p>Meningkatkan profesionalisme dan integritas aparatur Rutan Kelas IIA Batam menuju Zona Integritas Wilayah Bebas Korupsi.</p>
                </div>
              </div>

              <div class="misi-item">
                <div class="misi-number">05</div>
                <div class="misi-text">
                  <div class="misi-title">Reintegrasi Sosial</div>
                  <p>Mempersiapkan warga binaan untuk kembali ke masyarakat melalui program reintegrasi sosial yang terencana dan terukur.</p>
                </div>
              </div>

            </div>
          </div>

          <div class="vm-divider"></div>

          <!-- ── TUJUAN ── -->
          <div class="vm-section">
            <div class="vm-icon-wrap vm-icon-tujuan">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"/>
                <circle cx="12" cy="12" r="6"/>
                <circle cx="12" cy="12" r="2"/>
              </svg>
            </div>
            <div class="vm-label">TUJUAN</div>
            <h2 class="vm-title">Tujuan Rutan Kelas IIA Batam</h2>

            <div class="tujuan-grid">
              <div class="tujuan-card">
                <div class="tujuan-icon">⚖️</div>
                <div class="tujuan-title">Keadilan</div>
                <p>Mewujudkan keadilan dalam pelaksanaan penahanan dan pembinaan sesuai peraturan perundang-undangan yang berlaku.</p>
              </div>
              <div class="tujuan-card">
                <div class="tujuan-icon">🛡️</div>
                <div class="tujuan-title">Keamanan</div>
                <p>Menjamin keamanan dan keselamatan warga binaan, petugas, dan masyarakat sekitar lingkungan Rutan.</p>
              </div>
              <div class="tujuan-card">
                <div class="tujuan-icon">🌱</div>
                <div class="tujuan-title">Pembinaan</div>
                <p>Membina warga binaan agar menjadi manusia seutuhnya yang menyadari kesalahannya dan siap kembali ke masyarakat.</p>
              </div>
              <div class="tujuan-card">
                <div class="tujuan-icon">🤝</div>
                <div class="tujuan-title">Pelayanan</div>
                <p>Memberikan pelayanan terbaik kepada masyarakat, keluarga warga binaan, dan semua pihak yang berkepentingan.</p>
              </div>
            </div>

          </div>

        </div>
      </article>

    </div>
  </main>



<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
