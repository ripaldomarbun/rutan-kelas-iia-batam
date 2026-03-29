<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/includes/helpers.php';

$db       = getDB();
$maklumat = $db->query("SELECT * FROM komitmen ORDER BY id ASC")->fetchAll();
$kontak   = getKontak();

function gambarMaklumat($row) {
    if ($row['gambar'] && file_exists(__DIR__ . '/../backend/uploads/maklumat/' . $row['gambar']))
        return BASE_URL . '/backend/uploads/maklumat/' . htmlspecialchars($row['gambar']);
    return BASE_URL . '/images/' . htmlspecialchars($row['gambar'] ?? 'maklumat-1.jpg');
}

$pageTitle    = 'Komitmen – Rutan Kelas IIA Batam';
$halamanAktif = 'komitmen';

$extraCss     = ['sejarah.css', 'komitmen.css'];
include __DIR__ . '/../header.php';
?>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-overlay"></div>
  <div class="page-header-inner">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>/index.php">Beranda</a>
      <span>›</span>
      <a href="#">Informasi Publik</a>
      <span>›</span>
      <span class="breadcrumb-active">Komitmen</span>
    </div>
    <h1 class="page-title">Komitmen<br/><span>Rutan Kelas IIA Batam</span></h1>
    <p class="page-subtitle">Maklumat dan standar pelayanan pemasyarakatan yang kami janjikan</p>
  </div>
</div>

<main class="page-main">
  <div class="komitmen-container">

    <!-- Intro -->
    <div class="komitmen-intro">
      <p>Seluruh layanan yang kami berikan di Rutan Kelas IIA Batam mengacu pada <strong>Standar Operasional Prosedur (SOP)</strong> yang telah ditetapkan. Maklumat ini menjadi dasar bagi masyarakat untuk mendapatkan hak pelayanan yang adil, akuntabel, dan humanis.</p>
    </div>

    <!-- Maklumat Pelayanan -->
    <section class="komitmen-section">
      <div class="komitmen-section-header">
        <div class="komitmen-section-icon">📜</div>
        <h2 class="komitmen-section-title">Maklumat Pelayanan</h2>
      </div>
      <div class="maklumat-gallery">
        <?php if (!empty($maklumat)): foreach ($maklumat as $m):
          $src = gambarMaklumat($m);
        ?>
        <div class="maklumat-item" onclick="bukaLightbox(this)">
          <div class="zoom-hint">🔍 Klik untuk perbesar</div>
          <img src="<?= $src ?>" alt="<?= htmlspecialchars($m['kode']) ?>" loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
          <div class="maklumat-placeholder">
            <div class="placeholder-icon">📋</div>
            <div class="placeholder-title"><?= htmlspecialchars($m['kode']) ?></div>
            <p>Gambar belum tersedia</p>
          </div>
        </div>
        <?php endforeach; else: ?>
        <!-- Fallback statis jika DB kosong -->
        <div class="maklumat-item" onclick="bukaLightbox(this)">
          <div class="zoom-hint">🔍 Klik untuk perbesar</div>
          <img src="<?= BASE_URL ?>/images/maklumat.png" alt="Maklumat Pelayanan"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
          <div class="maklumat-placeholder">
            <div class="placeholder-icon">📋</div>
            <div class="placeholder-title">Maklumat Pelayanan</div>
            <p>Simpan file di: <code>images/maklumat.png</code></p>
          </div>
        </div>
        <div class="maklumat-item" onclick="bukaLightbox(this)">
          <div class="zoom-hint">🔍 Klik untuk perbesar</div>
          <img src="<?= BASE_URL ?>/images/standar.png" alt="Standar Pelayanan"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
          <div class="maklumat-placeholder">
            <div class="placeholder-icon">📋</div>
            <div class="placeholder-title">Standar Pelayanan</div>
            <p>Simpan file di: <code>images/standar.png</code></p>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- 16 Standar Pelayanan -->
    <section class="komitmen-section">
      <div class="komitmen-section-header">
        <div class="komitmen-section-icon">⭐</div>
        <h2 class="komitmen-section-title">16 Standar Pelayanan Pemasyarakatan</h2>
      </div>
      <p class="standar-desc">Rutan Kelas IIA Batam berkomitmen menyelenggarakan <strong>16 Standar Pelayanan Pemasyarakatan</strong> secara profesional bagi seluruh warga binaan dan masyarakat.</p>
      <div class="standar-grid">
        <?php
        $standar = [
          ['01','👥','Layanan Kunjungan Keluarga'],
          ['02','⚕️','Layanan Kesehatan'],
          ['03','🙏','Layanan Kebutuhan Rohani'],
          ['04','📚','Layanan Pendidikan'],
          ['05','🔨','Layanan Kegiatan Kerja'],
          ['06','⚖️','Layanan Bantuan Hukum'],
          ['07','🏠','Layanan Asimilasi'],
          ['08','🎓','Layanan Remisi'],
          ['09','🔓','Layanan Pembebasan Bersyarat'],
          ['10','🌿','Layanan Cuti Bersyarat'],
          ['11','🏡','Layanan Cuti Menjelang Bebas'],
          ['12','🤝','Layanan Integrasi'],
          ['13','📦','Layanan Penerimaan Titipan'],
          ['14','📝','Layanan Administrasi'],
          ['15','💬','Layanan Pengaduan'],
          ['16','🔄','Layanan Reintegrasi Sosial'],
        ];
        foreach ($standar as $s): ?>
        <div class="standar-card">
          <div class="standar-num"><?= $s[0] ?></div>
          <div class="standar-icon"><?= $s[1] ?></div>
          <div class="standar-nama"><?= $s[2] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Zona Integritas -->
    <section class="komitmen-section zona-section">
      <div class="zona-content">
        <div class="zona-text">
          <div class="zona-label">Komitmen Kami</div>
          <h2 class="zona-title">Menuju Zona Integritas</h2>
          <p>Rutan Kelas IIA Batam berkomitmen untuk mewujudkan <strong>Wilayah Bebas dari Korupsi (WBK)</strong> dan <strong>Wilayah Birokrasi Bersih Melayani (WBBM)</strong> sebagai bagian dari Reformasi Birokrasi Kementerian Imigrasi dan Pemasyarakatan RI.</p>
          <div class="zona-badges">
            <div class="zona-badge">
              <div class="zona-badge-icon">🏆</div>
              <div><div class="zona-badge-title">WBK</div><div class="zona-badge-sub">Wilayah Bebas dari Korupsi</div></div>
            </div>
            <div class="zona-badge">
              <div class="zona-badge-icon">🌟</div>
              <div><div class="zona-badge-title">WBBM</div><div class="zona-badge-sub">Wilayah Birokrasi Bersih Melayani</div></div>
            </div>
          </div>
        </div>
        <div class="zona-cta">
          <div class="zona-cta-title">Ada Pengaduan?</div>
          <p>Laporkan jika ada pelanggaran atau ketidakpuasan terhadap layanan kami</p>
          <a href="https://wa.me/6282216262626" target="_blank" class="btn-pengaduan">
            <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Hubungi WhatsApp
          </a>
          <a href="mailto:<?= htmlspecialchars($kontak['email']['nilai'] ?? '') ?>" class="btn-email-pengaduan">Kirim Email Pengaduan</a>
        </div>
      </div>
    </section>

  </div>
</main>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" onclick="tutupLightbox()">
  <button class="lightbox-close" onclick="tutupLightbox()">✕</button>
  <div class="lightbox-inner" onclick="event.stopPropagation()">
    <img src="" alt="" id="lightboxImg"/>
    <div class="lightbox-caption" id="lightboxCaption"></div>
  </div>
</div>

<script>
function bukaLightbox(el) {
  const img = el.querySelector('img');
  if (!img || img.style.display === 'none') return;
  document.getElementById('lightboxImg').src = img.src;
  document.getElementById('lightboxImg').alt = img.alt;
  document.getElementById('lightboxCaption').textContent = img.alt;
  document.getElementById('lightbox').classList.add('active');
  document.body.style.overflow = 'hidden';
}
function tutupLightbox() {
  document.getElementById('lightbox').classList.remove('active');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') tutupLightbox();
});
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>