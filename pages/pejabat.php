<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/config/database.php';

$db      = getDB();
$pejabat = $db->query("SELECT * FROM pejabat ORDER BY urutan ASC")->fetchAll();

$karutan = null; $lainnya = [];
foreach ($pejabat as $p) {
    if ($p['kode'] === 'karutan') $karutan = $p;
    else $lainnya[] = $p;
}

function fotoUrl($p) {
    if ($p['foto'] && file_exists(__DIR__ . '/../backend/uploads/pejabat/' . $p['foto']))
        return BASE_URL . '/backend/uploads/pejabat/' . htmlspecialchars($p['foto']);
    return BASE_URL . '/images/' . htmlspecialchars($p['kode']) . '.jpg';
}

$pageTitle    = 'Profil Pejabat – Rutan Kelas IIA Batam';
$halamanAktif = 'pejabat';

$extraCss     = ['sejarah.css', 'pejabat.css'];
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
      <span class="breadcrumb-active">Profil Pejabat</span>
    </div>
    <h1 class="page-title">Profil<br/><span>Pejabat</span></h1>
    <p class="page-subtitle">Pimpinan dan pejabat struktural Rutan Kelas IIA Batam</p>
  </div>
</div>

<!-- ── KONTEN UTAMA ── -->
<main class="page-main">
  <div class="page-container">

    <!-- Sidebar -->
    <aside class="page-sidebar">
      <div class="sidebar-title">Menu Tentang</div>
      <ul class="sidebar-menu">
        <li><a href="<?= BASE_URL ?>/sejarah">📜 Sejarah Rutan</a></li>
        <li><a href="<?= BASE_URL ?>/visi-misi">🎯 Visi, Misi &amp; Tujuan</a></li>
        <li><a href="<?= BASE_URL ?>/struktur">🏛️ Struktur Organisasi</a></li>
        <li><a href="<?= BASE_URL ?>/tupoksi">📋 Tugas Pokok &amp; Fungsi</a></li>
        <li><a href="<?= BASE_URL ?>/pejabat" class="active">👤 Profil Pejabat</a></li>
      </ul>
    </aside>

    <!-- Konten -->
    <article class="page-content">
      <div class="content-body">

        <?php if ($karutan): ?>
        <div class="pejabat-featured">
          <div class="featured-foto">
            <img src="<?= fotoUrl($karutan) ?>" alt="<?= htmlspecialchars($karutan['nama'] ?? 'Kepala Rutan') ?>" loading="lazy"
              onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
            <div class="featured-foto-fallback"><?= strtoupper(substr($karutan['nama'] ?? 'KR', 0, 2)) ?></div>
          </div>
          <div class="featured-info">
            <div class="featured-jabatan-label"><?= htmlspecialchars($karutan['jabatan']) ?></div>
            <h2 class="featured-nama"><?= htmlspecialchars($karutan['nama'] ?? '—') ?></h2>
            <?php if ($karutan['nip']): ?>
            <div class="featured-nip"><span class="info-label">NIP</span> <span class="info-val"><?= htmlspecialchars($karutan['nip']) ?></span></div>
            <?php endif; ?>
            <?php if ($karutan['pangkat']): ?>
            <div class="featured-pangkat"><span class="info-label">Pangkat</span> <span class="info-val"><?= htmlspecialchars($karutan['pangkat']) ?></span></div>
            <?php endif; ?>
            <?php if ($karutan['bio']): ?>
            <p class="featured-bio"><?= nl2br(htmlspecialchars($karutan['bio'])) ?></p>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($lainnya)): ?>
        <div class="pejabat-seksi-title">
          <h3>Pejabat Struktural</h3>
          <div class="seksi-line"></div>
        </div>
        <div class="pejabat-grid">
          <?php foreach ($lainnya as $p): ?>
          <div class="pejabat-card">
            <div class="pejabat-foto-wrap">
              <img src="<?= fotoUrl($p) ?>" alt="<?= htmlspecialchars($p['nama'] ?? $p['jabatan']) ?>"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
              <div class="pejabat-foto-fallback"><?= strtoupper(substr($p['nama'] ?? $p['jabatan'], 0, 2)) ?></div>
            </div>
            <div class="pejabat-card-body">
              <div class="pejabat-jabatan"><?= htmlspecialchars($p['jabatan']) ?></div>
              <div class="pejabat-nama"><?= htmlspecialchars($p['nama'] ?? '—') ?></div>
              <?php if ($p['nip']): ?>
              <div class="pejabat-detail"><div class="pejabat-detail-row"><span class="detail-label">NIP</span> <span class="detail-val"><?= htmlspecialchars($p['nip']) ?></span></div></div>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

      </div>
    </article>

  </div>
</main>


<?php include __DIR__ . '/../backend/includes/footer.php'; ?>