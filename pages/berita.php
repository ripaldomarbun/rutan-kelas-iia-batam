<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/config/database.php';

$db = getDB();

$kat    = $_GET['kategori'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 6;
$offset = ($page - 1) * $limit;

$sql    = "SELECT * FROM berita WHERE status = 'publish'";
$params = [];
if ($kat) { $sql .= " AND kategori = ?"; $params[] = $kat; }
$sql .= " ORDER BY tanggal DESC LIMIT ? OFFSET ?";
$params[] = $limit; $params[] = $offset;
$stmt = $db->prepare($sql); $stmt->execute($params);
$beritaList = $stmt->fetchAll();

$sqlT  = "SELECT COUNT(*) FROM berita WHERE status='publish'" . ($kat ? " AND kategori=?" : "");
$stmtT = $db->prepare($sqlT); $stmtT->execute($kat ? [$kat] : []);
$total     = (int)$stmtT->fetchColumn();
$totalPage = ceil($total / $limit);

$featured = $db->query("SELECT * FROM berita WHERE status='publish' ORDER BY tanggal DESC LIMIT 1")->fetch();

function gambarBerita($gambar, $fallback='berita-1.jpg') {
    if (!$gambar) return BASE_URL . '/images/' . $fallback;
    $path = __DIR__ . '/../backend/uploads/berita/' . $gambar;
    return file_exists($path) ? BASE_URL . '/backend/uploads/berita/' . htmlspecialchars($gambar) : BASE_URL . '/images/' . htmlspecialchars($gambar);
}
function tgl($d) { return $d ? date('d M Y', strtotime($d)) : '-'; }

$pageTitle    = 'Berita & Pengumuman – Rutan Kelas IIA Batam';
$halamanAktif = 'berita';

$extraCss     = ['berita.css','sejarah.css'];
$extraJs      = ['berita.js'];
include __DIR__ . '/../header.php';
?>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-overlay"></div>
  <div class="page-header-inner">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>/index.php">Beranda</a>
      <span>›</span>
      <span class="breadcrumb-active">Berita</span>
    </div>
    <h1 class="berita-header-title">Berita &amp; <span>Pengumuman</span></h1>
    <p class="berita-header-sub">Informasi terkini seputar kegiatan dan layanan Rutan Kelas IIA Batam</p>
  </div>
</div>

<main class="berita-main">
  <div class="berita-container">

    <!-- Featured -->
    <?php if ($featured && !$kat && $page == 1): ?>
    <a href="<?= BASE_URL ?>/detail-berita?slug=<?= urlencode($featured['slug']) ?>" class="berita-featured">
      <div class="featured-img-wrap">
        <img src="<?= gambarBerita($featured['gambar']) ?>" alt="<?= htmlspecialchars($featured['judul']) ?>"
          onerror="this.parentElement.classList.add('img-fallback')"/>
        <span class="featured-kat cat-<?= $featured['kategori'] ?>"><?= ucfirst($featured['kategori']) ?></span>
      </div>
      <div class="featured-body">
        <div class="featured-meta">
          <span>📅 <?= tgl($featured['tanggal']) ?></span>
          <span>✍️ <?= htmlspecialchars($featured['penulis'] ?? 'Humas Rutan Batam') ?></span>
        </div>
        <h2 class="featured-judul"><?= htmlspecialchars($featured['judul']) ?></h2>
        <p class="featured-ringkasan"><?= htmlspecialchars($featured['ringkasan'] ?? '') ?></p>
        <span class="featured-more">Baca Selengkapnya →</span>
      </div>
    </a>
    <?php endif; ?>

    <!-- Toolbar + Grid -->
    <div class="berita-right">

      <!-- Filter kategori -->
      <div class="berita-toolbar">
        <div class="kat-filter" id="katFilter">
          <a href="berita.php" class="kat-btn <?= !$kat ? 'active' : '' ?>">Semua</a>
          <a href="berita.php?kategori=kegiatan"   class="kat-btn <?= $kat==='kegiatan'   ? 'active' : '' ?>">Kegiatan</a>
          <a href="berita.php?kategori=pengumuman" class="kat-btn <?= $kat==='pengumuman' ? 'active' : '' ?>">Pengumuman</a>
          <a href="berita.php?kategori=prestasi"   class="kat-btn <?= $kat==='prestasi'   ? 'active' : '' ?>">Prestasi</a>
          <a href="berita.php?kategori=pembinaan"  class="kat-btn <?= $kat==='pembinaan'  ? 'active' : '' ?>">Pembinaan</a>
        </div>
      </div>

      <!-- Grid berita -->
      <?php if (empty($beritaList)): ?>
      <div class="berita-kosong" id="beritaKosong">
        <div>📭</div>
        <p>Belum ada berita untuk kategori ini.</p>
      </div>
      <?php else: ?>
      <div class="berita-grid" id="beritaGrid">
        <?php foreach ($beritaList as $b): ?>
        <article class="berita-card" data-kat="<?= $b['kategori'] ?>">
          <a href="<?= BASE_URL ?>/detail-berita?slug=<?= urlencode($b['slug']) ?>">
            <div class="card-img-wrap">
              <img src="<?= gambarBerita($b['gambar']) ?>" alt="<?= htmlspecialchars($b['judul']) ?>" loading="lazy"
                onerror="this.parentElement.classList.add('img-fallback')"/>
              <span class="card-kat cat-<?= $b['kategori'] ?>"><?= ucfirst($b['kategori']) ?></span>
            </div>
            <div class="card-body">
              <div class="card-meta">📅 <?= tgl($b['tanggal']) ?> · 👁 <?= $b['views'] ?? 0 ?></div>
              <h3 class="card-judul"><?= htmlspecialchars($b['judul']) ?></h3>
              <p class="card-ringkasan"><?= htmlspecialchars($b['ringkasan'] ?? '') ?></p>
            </div>
          </a>
        </article>
        <?php endforeach; ?>
      </div>

      <!-- Paginasi -->
      <?php if ($totalPage > 1): ?>
      <div class="berita-loadmore">
        <div style="display:flex; gap:0.5rem; justify-content:center; flex-wrap:wrap;">
          <?php if ($page > 1): ?>
          <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page-1])) ?>" class="btn-loadmore">← Sebelumnya</a>
          <?php endif; ?>
          <?php for ($i=1; $i<=$totalPage; $i++): ?>
          <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
             class="btn-loadmore <?= $i===$page ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if ($page < $totalPage): ?>
          <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page+1])) ?>" class="btn-loadmore">Selanjutnya →</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
      <?php endif; ?>

    </div><!-- /berita-right -->

  </div><!-- /berita-container -->
</main>

<script src="<?= BASE_URL ?>/js/berita.js"></script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>