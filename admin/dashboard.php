<?php
require_once __DIR__ . '/../backend/includes/helpers.php';
requireLogin();

$db = getDB();

$totalBerita  = (int)$db->query("SELECT COUNT(*) FROM berita WHERE status='publish'")->fetchColumn();
$totalSlider  = (int)$db->query("SELECT COUNT(*) FROM slider WHERE aktif=1")->fetchColumn();
$totalPejabat = (int)$db->query("SELECT COUNT(*) FROM pejabat WHERE nama IS NOT NULL AND nama != ''")->fetchColumn();
$totalSKM     = (int)$db->query("SELECT COUNT(*) FROM survey_skm WHERE tahun=YEAR(NOW())")->fetchColumn();

$beritaTerbaru = $db->query(
    "SELECT id, judul, kategori, status, tanggal, views FROM berita ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard – Admin Rutan Kelas IIA Batam</title>
  <link rel="stylesheet" href="css/admin.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/>
</head>
<body>
<div class="admin-layout">
<script>sessionStorage.setItem('csrf_token', '<?= getCsrfToken() ?>');</script>
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div class="admin-main">
  <header class="admin-topbar">
    <div class="topbar-left">
      <button class="sidebar-toggle" id="sidebarToggle">☰</button>
      <div><div class="topbar-page-title">Dashboard</div>
      <div class="topbar-breadcrumb">Selamat datang, <?= htmlspecialchars($_SESSION['admin_nama']) ?> 👋</div></div>
    </div>
    <div class="topbar-right">
      <a href="../index.php" target="_blank" class="btn-view-site">🌐 Lihat Website</a>
      <div class="admin-user-chip">
        <div class="admin-user-avatar"><?= strtoupper(substr($_SESSION['admin_nama'], 0, 1)) ?></div>
        <div><div class="admin-user-name"><?= htmlspecialchars($_SESSION['admin_nama']) ?></div>
        <div class="admin-user-role"><?= htmlspecialchars($_SESSION['admin_role']) ?></div></div>
      </div>
    </div>
  </header>

  <div class="admin-content">

    <!-- Stat cards -->
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-icon stat-icon-navy">📰</div>
        <div><div class="stat-val"><?= $totalBerita ?></div><div class="stat-label">Berita Publish</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon stat-icon-gold">📊</div>
        <div><div class="stat-val"><?= $totalSKM ?></div><div class="stat-label">Laporan SKM <?= date('Y') ?></div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon stat-icon-green">👤</div>
        <div><div class="stat-val"><?= $totalPejabat ?></div><div class="stat-label">Data Pejabat Lengkap</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon stat-icon-blue">🖼️</div>
        <div><div class="stat-val"><?= $totalSlider ?></div><div class="stat-label">Slide Aktif</div></div>
      </div>
    </div>

    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:1.25rem;">

      <!-- Berita terbaru -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">📰 Berita Terbaru</div>
          <a href="berita.php" class="btn btn-outline btn-sm">Kelola Berita</a>
        </div>
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead><tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Views</th></tr></thead>
            <tbody>
            <?php foreach ($beritaTerbaru as $b): ?>
            <tr>
              <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                <a href="berita.php?edit=<?= $b['id'] ?>" style="color:var(--navy); font-weight:600;">
                  <?= htmlspecialchars($b['judul']) ?>
                </a>
              </td>
              <td><span class="badge badge-<?= $b['kategori'] ?>"><?= ucfirst($b['kategori']) ?></span></td>
              <td><span class="badge badge-<?= $b['status'] === 'publish' ? 'publish' : 'draft' ?>"><?= $b['status'] ?></span></td>
              <td><?= $b['views'] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($beritaTerbaru)): ?>
            <tr><td colspan="4" style="text-align:center; color:#ccc; padding:2rem;">Belum ada berita</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Akses cepat -->
      <div class="admin-card">
        <div class="admin-card-header"><div class="admin-card-title">⚡ Akses Cepat</div></div>
        <div class="admin-card-body" style="display:flex; flex-direction:column; gap:0.6rem;">
          <a href="berita.php?action=tambah" class="btn btn-primary">✏️ Tulis Berita Baru</a>
          <a href="survey.php?action=tambah" class="btn btn-outline">📤 Upload Laporan SKM</a>
          <a href="slider.php"     class="btn btn-outline">🖼️ Ganti Slider</a>
          <a href="kunjungan.php"  class="btn btn-outline">📅 Update Jadwal Kunjungan</a>
          <a href="komitmen.php"   class="btn btn-outline">📜 Ganti Gambar Maklumat</a>
          <a href="pejabat.php"    class="btn btn-outline">👤 Edit Profil Pejabat</a>
        </div>
      </div>

    </div>
  </div>
</div>
</div>
<script src="js/admin.js"></script>
</body>
</html>
