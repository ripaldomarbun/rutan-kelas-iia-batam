<?php
require_once __DIR__ . '/backend/config/config.php';
require_once __DIR__ . '/backend/config/database.php';
require_once __DIR__ . '/backend/includes/helpers.php';

$db = getDB();

// ── Slider dari DB ──────────────────────────────────────
$sliders = $db->query("SELECT * FROM slider WHERE aktif = 1 ORDER BY urutan ASC")->fetchAll();

// ── Kontak info ─────────────────────────────────────────
$kontak = getKontak();

// ── 3 Berita terbaru untuk section beranda ──────────────
$beritaTerbaru = $db->query(
    "SELECT id, judul, slug, ringkasan, gambar, kategori, tanggal
     FROM berita WHERE status = 'publish'
     ORDER BY tanggal DESC LIMIT 3"
)->fetchAll();

$pageTitle = 'Beranda – Rutan Kelas IIA Batam';
$cssBase   = '';

// Include header (navbar)
include __DIR__ . '/header.php';

$bcSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => BASE_URL . '/index.php']
    ]
];
?>

<script type="application/ld+json">
<?= json_encode($bcSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
</script>

<!-- SLIDER DARI DATABASE -->
<div class="slider-wrapper" id="sliderWrapper">
  <div class="slider-track" id="sliderTrack">

    <?php if (empty($sliders)): ?>
<div class="slide active">
  <div class="slide-bg" style="background-image: url('images/slide1.png')"></div>
  <div class="slide-overlay"></div>
</div>

<?php else: foreach ($sliders as $i => $slide): ?>
<?php
  $imgPath = file_exists(__DIR__ . '/backend/uploads/slider/' . $slide['gambar'])
      ? 'backend/uploads/slider/' . htmlspecialchars($slide['gambar'])
      : 'images/' . htmlspecialchars($slide['gambar']);
?>
<div class="slide <?= $i === 0 ? 'active' : '' ?>">
  <div class="slide-bg" style="background-image: url('<?= $imgPath ?>')"></div>
  <div class="slide-overlay"></div>
</div>
<?php endforeach; endif; ?>

  </div>

  <button class="slider-btn slider-prev" id="sliderPrev" aria-label="Slide sebelumnya">&#8592;</button>
  <button class="slider-btn slider-next" id="sliderNext" aria-label="Slide berikutnya">&#8594;</button>

  <div class="slider-dots" id="sliderDots">
    <?php $jumlahSlide = max(1, count($sliders)); for ($i = 0; $i < $jumlahSlide; $i++): ?>
    <button class="dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>" aria-label="Slide <?= $i+1 ?>"></button>
    <?php endfor; ?>
  </div>
</div>
<!-- ── HERO: Bagian utama / banner ── -->
  <section class="hero" id="beranda">
    <!-- Efek latar belakang -->
    <div class="hero-bg-pattern"></div>
    <div class="hero-glow"></div>
    <div class="hero-glow2"></div>

    <div class="hero-inner">

      <!-- Teks sebelah kiri -->
      <div class="hero-left">
        <div class="hero-badge">Website Resmi</div>
        <div class="hero-subtitle">Rumah Tahanan Negara</div>
        <h1>Rutan <span>Kelas IIA</span><br />Batam</h1>
        <p class="hero-desc">
          Berkomitmen memberikan pelayanan yang transparan, akuntabel, dan profesional dalam sistem pemasyarakatan
          Indonesia. Melayani masyarakat dengan integritas dan dedikasi.
        </p>
        <div class="hero-btns">
          <a href="pages/kunjungan.php" class="btn-gold">Informasi Publik</a>
          <a href="pages/berita.php" class="btn-outline">Baca Berita Terkini</a>
        </div>
      </div>

      <!-- Kartu sambutan sebelah kanan -->
      <div class="hero-right">
        <div class="hero-card">
          <div class="hero-card-header">
            <div class="kepala-avatar">
              <img src="images/karutan.png" alt="Kepala Rutan" />
            </div>
            <div class="kepala-info">
              <div class="name">Fajar Teguh Wibowo</div>
              <div class="title">Kepala Rutan Kelas IIA Batam</div>
            </div>
          </div>
          <p class="sambutan-text">
            Website ini kami hadirkan sebagai sarana informasi dan komunikasi kepada masyarakat mengenai pelayanan,
            kegiatan pembinaan, serta berbagai program di Rutan Kelas IIA Batam.
          </p>
          <div class="hero-stats">
            <div class="stat-item">
              <div class="num">100+</div>
              <div class="lbl">Petugas</div>
            </div>
            <div class="stat-item">
              <div class="num">24/7</div>
              <div class="lbl">Pelayanan</div>
            </div>
            <div class="stat-item">
              <div class="num">ZI</div>
              <div class="lbl">Zona Integritas</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

<!-- SECTION BERITA TERBARU -->
<section id="berita" class="section-berita">
  <div class="section-inner">
    <div class="berita-header">
      <div>
        <div class="section-label">Warta Pemasyarakatan</div>
        <h2 class="section-title">Berita &amp; Kegiatan<br/>Terkini</h2>
      </div>
      <a href="pages/berita.php" class="btn-semua-berita">Semua Berita →</a>
    </div>

    <div class="berita-grid">
      <?php if (empty($beritaTerbaru)): ?>
      <div style="grid-column:1/-1; text-align:center; padding:3rem; color:#8888aa;">
        Belum ada berita yang dipublikasikan.
      </div>
      <?php else: foreach ($beritaTerbaru as $b):
        $gambarUrl = $b['gambar']
            ? (file_exists(__DIR__ . '/backend/uploads/berita/' . $b['gambar'])
                ? 'backend/uploads/berita/' . htmlspecialchars($b['gambar'])
                : 'images/' . htmlspecialchars($b['gambar']))
            : null;
        $tanggal = $b['tanggal'] ? date('d M Y', strtotime($b['tanggal'])) : '';
      ?>
      <div class="berita-card">
        <div class="berita-card-img">
          <?php if ($gambarUrl): ?>
            <img src="<?= $gambarUrl ?>" alt="<?= htmlspecialchars($b['judul']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;"/>
          <?php endif; ?>
          <span class="berita-cat"><?= ucfirst($b['kategori']) ?></span>
        </div>
        <div class="berita-card-body">
          <div class="berita-date"><?= $tanggal ?></div>
          <div class="berita-title"><?= htmlspecialchars($b['judul']) ?></div>
          <div class="berita-excerpt"><?= htmlspecialchars($b['ringkasan'] ?? '') ?></div>
          <a href="<?= BASE_URL ?>/detail-berita?slug=<?= urlencode($b['slug']) ?>" class="berita-readmore">Baca Selengkapnya →</a>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>

<!-- ── INFORMASI PUBLIK ── -->
  <section id="informasi" class="section-informasi">
    <div class="section-inner">
      <div class="section-label label-gold">Pelayanan Masyarakat</div>
      <h2 class="section-title title-white">Informasi Publik</h2>
      <p class="section-desc desc-light">Kami berkomitmen untuk memberikan informasi yang terbuka dan mudah diakses oleh
        seluruh lapisan masyarakat.</p>

      <div class="info-grid">
        <a href="pages/kunjungan.php" class="info-card">
          <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg></div>
          <div class="info-card-title">Layanan Kunjungan</div>
          <div class="info-card-desc">Informasi jadwal, syarat, dan prosedur kunjungan kepada warga binaan di Rutan
            Kelas IIA Batam.</div>
          <div class="info-card-link">Lihat Detail →</div>
        </a>
        <a href="pages/survey.php" class="info-card">
          <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
              <polyline points="14 2 14 8 20 8" />
              <line x1="16" y1="13" x2="8" y2="13" />
              <line x1="16" y1="17" x2="8" y2="17" />
            </svg></div>
          <div class="info-card-title">Survey Kepuasan Masyarakat</div>
          <div class="info-card-desc">Berikan penilaian dan masukan Anda untuk meningkatkan kualitas pelayanan Rutan
            Kelas IIA Batam.</div>
          <div class="info-card-link">Isi Survey →</div>
        </a>
        <a href="pages/komitmen.php" class="info-card">
          <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg></div>
          <div class="info-card-title">Komitmen Integritas</div>
          <div class="info-card-desc">Kami berkomitmen menuju Zona Integritas bebas korupsi dan bersih dari pungutan
            liar dalam setiap pelayanan.</div>
          <div class="info-card-link">Pelajari →</div>
        </a>
        <a href="pages/berita.php" class="info-card">
          <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg></div>
          <div class="info-card-title">Pengumuman</div>
          <div class="info-card-desc">Informasi terbaru tentang kebijakan, prosedur, dan pengumuman resmi dari Rutan
            Kelas IIA Batam.</div>
          <div class="info-card-link">Lihat Semua →</div>
        </a>
        <a href="#kontak" class="info-card">
          <div class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
            </svg></div>
          <div class="info-card-title">Pengaduan</div>
          <div class="info-card-desc">Sampaikan keluhan atau laporan terkait pelayanan Rutan melalui saluran resmi yang
            tersedia.</div>
          <div class="info-card-link">Laporkan →</div>
        </a>
      </div>
    </div>
  </section>

<!-- ── BANNER ZONA INTEGRITAS ── -->
  <div class="zi-banner">
    <div class="zi-inner">
      <div class="zi-left">
        <div class="zi-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
        </div>
        <div class="zi-text">
          <h3>Rutan Kelas IIA Batam Menuju Zona Integritas</h3>
          <p>Laporkan jika ada pungutan liar! Kami berkomitmen bebas korupsi dan melayani dengan integritas penuh.</p>
        </div>
      </div>
      <a href="https://wa.me/6282216262626" class="zi-btn">
        <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
          <path
            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
        </svg>
        WhatsApp Pengaduan
      </a>
    </div>
  </div>

  <!-- ── KONTAK ── -->
  <section id="kontak" class="section-kontak">
    <div class="section-inner">
      <div class="kontak-header">
        <div class="section-label">Hubungi Kami</div>
        <h2 class="section-title">Kontak &amp; Lokasi</h2>
        <p class="section-desc">Kami siap melayani pertanyaan dan keperluan Anda terkait Rutan Kelas IIA Batam.</p>
      </div>

      <div class="kontak-grid">

        <!-- Info kontak -->
        <div class="kontak-info">
          <div class="kontak-item">
            <div class="kontak-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
            </div>
            <div>
              <div class="kontak-label"><?= htmlspecialchars($kontak['alamat']['label'] ?? 'Alamat') ?></div>
              <div class="kontak-value"><?= $kontak['alamat']['nilai'] ?? 'Jl. Raya Trans Barelang, Batam' ?></div>
            </div>
          </div>
          <div class="kontak-item">
            <div class="kontak-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2">
                <path
                  d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.16 6.16l1.88-1.88a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2.02z" />
              </svg>
            </div>
            <div>
              <div class="kontak-label"><?= htmlspecialchars($kontak['telepon']['label'] ?? 'Telepon') ?></div>
              <div class="kontak-value"><?= htmlspecialchars($kontak['telepon']['nilai'] ?? '') ?></div>
            </div>
          </div>
          <div class="kontak-item">
            <div class="kontak-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                <polyline points="22,6 12,13 2,6" />
              </svg>
            </div>
            <div>
              <div class="kontak-label"><?= htmlspecialchars($kontak['email']['label'] ?? 'Email') ?></div>
              <div class="kontak-value"><?= htmlspecialchars($kontak['email']['nilai'] ?? '') ?></div>
            </div>
          </div>
          <div class="kontak-item">
            <div class="kontak-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
            </div>
            <div>
              <div class="kontak-label"><?= htmlspecialchars($kontak['jam_operasional']['label'] ?? 'Jam Operasional') ?></div>
              <div class="kontak-value"><?= $kontak['jam_operasional']['nilai'] ?? '' ?></div>
            </div>
          </div>
        </div>

        <!-- Peta -->
        <div class="kontak-map">

          <!-- iframe = jendela ke website lain (Google Maps) yang tampil di halaman kita -->
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4820.249336185669!2d103.9978019!3d1.031422!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d98df9bdbc4dd3%3A0x925bcc68e38607ca!2sRUTAN%20BATAM!5e1!3m2!1sen!2sid!4v1772998733907!5m2!1sen!2sid"
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>

          <!-- Tombol tetap ada di bawah peta -->
          <a href="https://maps.google.com/?q=Rutan+Kelas+IIA+Batam" target="_blank" class="btn-gold btn-maps">
            Buka di Google Maps
          </a>

        </div>

      </div>
    </div>
  </section>

<?php
// Pakai footer partial
$cssBase = '';
include __DIR__ . '/backend/includes/footer.php';
?>
