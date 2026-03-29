<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/includes/helpers.php';

$baseUrl = defined('BASE_URL') ? BASE_URL : 'http://localhost/web-rutan';
$slug = $_GET['slug'] ?? '';

if (!$slug) { 
    header('Location: ' . $baseUrl . '/berita'); 
    exit; 
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM berita WHERE slug = ? AND status = 'publish'");
    $stmt->execute([$slug]);
    $berita = $stmt->fetch();
    
    if (!$berita) {
        header('Location: ' . $baseUrl . '/berita');
        exit;
    }
} catch (Exception $e) {
    header('Location: ' . $baseUrl . '/berita');
    exit;
}

// Load foto tambahan
$stmtFotos = $db->prepare("SELECT * FROM berita_fotos WHERE berita_id = ? ORDER BY urutan ASC");
$stmtFotos->execute([$berita['id']]);
$fotos = $stmtFotos->fetchAll();

$db->prepare("UPDATE berita SET views = views + 1 WHERE id = ?")->execute([$berita['id']]);

$prev = $db->prepare("SELECT judul, slug FROM berita WHERE status='publish' AND id < ? ORDER BY id DESC LIMIT 1");
$prev->execute([$berita['id']]); $prevBerita = $prev->fetch();

$next = $db->prepare("SELECT judul, slug FROM berita WHERE status='publish' AND id > ? ORDER BY id ASC LIMIT 1");
$next->execute([$berita['id']]); $nextBerita = $next->fetch();

$gambarUrl = $berita['gambar']
    ? (file_exists(__DIR__ . '/../backend/uploads/berita/' . $berita['gambar'])
        ? BASE_URL . '/backend/uploads/berita/' . htmlspecialchars($berita['gambar'])
        : BASE_URL . '/images/' . htmlspecialchars($berita['gambar']))
    : BASE_URL . '/images/berita-1.jpg';

function imgBerita($g) {
    if (!$g) return BASE_URL . '/images/berita-1.jpg';
    $p = __DIR__ . '/../backend/uploads/berita/' . $g;
    return file_exists($p) ? BASE_URL . '/backend/uploads/berita/' . htmlspecialchars($g) : BASE_URL . '/images/' . htmlspecialchars($g);
}

$pageTitle    = htmlspecialchars($berita['judul']) . ' – Rutan Kelas IIA Batam';
$halamanAktif = 'berita';

$extraCss     = ['sejarah.css', 'berita.css'];
include __DIR__ . '/../header.php';

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => htmlspecialchars($berita['judul']),
    'image' => [htmlspecialchars($gambarUrl)],
    'datePublished' => $berita['tanggal'] ? date('c', strtotime($berita['tanggal'])) : '',
    'dateModified' => $berita['updated_at'] && $berita['updated_at'] !== '0000-00-00 00:00:00' ? date('c', strtotime($berita['updated_at'])) : '',
    'author' => [
        '@type' => 'Person',
        'name' => htmlspecialchars($berita['penulis'] ?? 'Humas Rutan Kelas IIA Batista')
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'Rutan Kelas IIA Batista',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => BASE_URL . '/images/logo.png'
        ]
    ]
];
?>

<script type="application/ld+json">
<?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
</script>

<style>
  /* ── DETAIL SIDEBAR ── */
.detail-container {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1.5rem;
  align-items: start;
}

.detail-sidebar {
  position: sticky;
  top: 100px;
}

.sidebar-widget {
  background: #fff;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 12px rgba(0,0,0,0.07);
  border: 1px solid #f0f0f0;
}

.widget-title {
  font-family: 'Playfair Display', serif;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 1.2rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #c8a951;
}

.widget-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.widget-item {
  display: flex;
  gap: 0.75rem;
  text-decoration: none;
  color: inherit;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f0f0f0;
  transition: opacity 0.2s;
}
.widget-item:last-child { border-bottom: none; padding-bottom: 0; }
.widget-item:hover { opacity: 0.75; }

.widget-item-img {
  width: 75px;
  height: 60px;
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  background: #e8e8f0;
}
.widget-item-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.widget-item-info {
  flex: 1;
  min-width: 0;
}

.widget-item-kat {
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 2px 8px;
  border-radius: 4px;
  display: inline-block;
  margin-bottom: 4px;
}
.widget-item-kat.kat-kegiatan   { background: #e8f4fd; color: #1a6fa8; }
.widget-item-kat.kat-pengumuman { background: #fff3e0; color: #e65100; }
.widget-item-kat.kat-prestasi   { background: #f3e5f5; color: #7b1fa2; }
.widget-item-kat.kat-pembinaan  { background: #e8f5e9; color: #2e7d32; }

.widget-item-judul {
  font-size: 0.82rem;
  font-weight: 600;
  color: #1a1a2e;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.widget-item-tgl {
  font-size: 0.72rem;
  color: #8888aa;
  margin-top: 3px;
}

.widget-lihat-semua {
  display: block;
  text-align: center;
  margin-top: 1rem;
  padding: 0.6rem;
  background: #f5f5fa;
  border-radius: 8px;
  font-size: 0.82rem;
  font-weight: 600;
  color: #1a6fa8;
  text-decoration: none;
  transition: background 0.2s;
}
.widget-lihat-semua:hover { background: #e8f4fd; }

/* Widget Kategori */
.widget-kategori {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.wkat-item {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  text-decoration: none;
  color: #333;
  font-size: 0.88rem;
  font-weight: 500;
  transition: background 0.2s;
}
.wkat-item:hover { background: #f5f5fa; }

.wkat-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}
.wkat-dot.kat-kegiatan   { background: #1a6fa8; }
.wkat-dot.kat-pengumuman { background: #e65100; }
.wkat-dot.kat-prestasi   { background: #7b1fa2; }
.wkat-dot.kat-pembinaan  { background: #2e7d32; }

.wkat-jumlah {
  margin-left: auto;
  background: #f0f0f5;
  color: #666;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 20px;
}

/* Responsive */
@media (max-width: 900px) {
  .detail-container {
    grid-template-columns: 1fr;
  }
  .detail-sidebar {
    position: static;
  }
}
</style>

<main class="page-main">
  <div class="detail-container">

    <!-- Kolom kiri: artikel -->
    <div class="detail-main">

      <!-- Breadcrumb -->
      <div class="breadcrumb detail-breadcrumb">
        <a href="<?= BASE_URL ?>/index.php">Beranda</a>
        <span>›</span>
        <a href="berita">Berita</a>
        <span>›</span>
        <span class="breadcrumb-active">Detail Berita</span>
      </div>

      <article class="detail-artikel">

        <!-- Badge kategori -->
        <span class="berita-kat-badge kat-<?= $berita['kategori'] ?>"><?= ucfirst($berita['kategori']) ?></span>

        <!-- Judul -->
        <h1 class="detail-judul"><?= htmlspecialchars($berita['judul']) ?></h1>

        <!-- Meta info -->
        <div class="detail-meta">
          <div class="detail-meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <?= $berita['tanggal'] ? date('d F Y', strtotime($berita['tanggal'])) : '-' ?>
          </div>
          <div class="detail-meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <?= htmlspecialchars($berita['penulis'] ?? 'Humas Rutan Batam') ?>
          </div>
          <div class="detail-meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <?= $berita['views'] ?> dilihat
          </div>
        </div>

        <!-- Foto utama -->
        <div class="detail-foto-utama">
          <img src="<?= $gambarUrl ?>" alt="<?= htmlspecialchars($berita['judul']) ?>"
            style="width:100%; max-height:450px; object-fit:cover; border-radius:10px; display:block;"
            onerror="this.style.display='none'"/>
          <div class="detail-foto-caption">Foto: <?= htmlspecialchars($berita['judul']) ?></div>
        </div>

        <!-- Gallery Foto Tambahan -->
        <?php if (!empty($fotos)): ?>
        <div class="detail-gallery" style="margin:1.5rem 0;">
          <div style="font-size:0.85rem; font-weight:700; color:var(--navy); margin-bottom:0.75rem;">📷 Foto-foto Kegiatan</div>
          <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:0.75rem;">
            <?php foreach ($fotos as $f): ?>
            <div class="gallery-item" style="border-radius:8px; overflow:hidden; border:1px solid #eee; cursor:pointer;" 
                 onclick="openLightbox('<?= imgBerita($f['foto']) ?>', '<?= htmlspecialchars($f['caption'] ?? $berita['judul']) ?>')">
              <img src="<?= imgBerita($f['foto']) ?>" alt="<?= htmlspecialchars($f['caption'] ?? '') ?>" loading="lazy"
                   style="width:100%; height:130px; object-fit:cover; display:block;"
                   onerror="this.src='<?= BASE_URL ?>/images/berita-1.jpg'"/>
              <?php if ($f['caption']): ?>
              <div style="padding:6px 8px; font-size:0.72rem; color:#666; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($f['caption']) ?></div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Isi artikel -->
        <div class="detail-isi">
          <?= sanitizeHtml($berita['isi']) ?>
        </div>

        <!-- Tombol bagikan -->
        <?php $url = BASE_URL . '/detail-berita?slug=' . urlencode($slug); ?>
        <div class="detail-share">
          <span class="share-label">Bagikan:</span>
          <a href="https://wa.me/?text=<?= urlencode($berita['judul'] . ' ' . $url) ?>"
             target="_blank" class="share-btn share-wa" title="WhatsApp">
            <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            WhatsApp
          </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($url) ?>"
             target="_blank" class="share-btn share-fb" title="Facebook">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            Facebook
          </a>
          <button class="share-btn share-copy" onclick="salinLink()" title="Salin link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            Salin Link
          </button>
        </div>

      </article>
    </div>
    <!-- Sidebar kanan -->
    <aside class="detail-sidebar">

      <!-- Widget Berita Terbaru -->
      <div class="sidebar-widget">
        <div class="widget-title">Berita Terbaru</div>
        <div class="widget-list">
          <?php
          $terkini = $db->query("SELECT judul, slug, gambar, tanggal, kategori FROM berita WHERE status='publish' ORDER BY tanggal DESC LIMIT 4")->fetchAll();
          foreach ($terkini as $t): ?>
          <a href="<?= BASE_URL ?>/detail-berita?slug=<?= urlencode($t['slug']) ?>" class="widget-item">
            <div class="widget-item-img">
              <img src="<?= imgBerita($t['gambar']) ?>" alt="<?= htmlspecialchars($t['judul']) ?>"
                onerror="this.style.display='none'"/>
            </div>
            <div class="widget-item-info">
              <div class="widget-item-kat kat-<?= $t['kategori'] ?>"><?= ucfirst($t['kategori']) ?></div>
              <div class="widget-item-judul"><?= htmlspecialchars($t['judul']) ?></div>
              <div class="widget-item-tgl"><?= $t['tanggal'] ? date('d M Y', strtotime($t['tanggal'])) : '' ?></div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <a href="berita" class="widget-lihat-semua">Lihat Semua Berita →</a>
      </div>

      <!-- Widget Kategori -->
      <div class="sidebar-widget">
        <div class="widget-title">Kategori</div>
        <div class="widget-kategori">
          <?php
          $katList = $db->query("SELECT kategori, COUNT(*) as jml FROM berita WHERE status='publish' GROUP BY kategori ORDER BY jml DESC")->fetchAll();
          foreach ($katList as $k): ?>
          <a href="berita?kategori=<?= $k['kategori'] ?>" class="wkat-item">
            <span class="wkat-dot kat-<?= $k['kategori'] ?>"></span>
            <?= ucfirst($k['kategori']) ?>
            <span class="wkat-jumlah"><?= $k['jml'] ?></span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

    </aside>

  </div>
</main>
  </div>
  
</main>

<script>
function salinLink() {
  navigator.clipboard.writeText(window.location.href).then(function() {
    alert('Link berhasil disalin!');
  });
}

// Lightbox untuk gallery foto
function openLightbox(src, caption) {
  const lightbox = document.createElement('div');
  lightbox.id = 'lightbox';
  lightbox.style.cssText = 'position:fixed; inset:0; background:rgba(0,0,0,0.9); z-index:9999; display:flex; align-items:center; justify-content:center; flex-direction:column; padding:2rem; cursor:pointer;';
  lightbox.innerHTML = `
    <img src="${src}" style="max-width:90%; max-height:80vh; object-fit:contain; border-radius:8px;"/>
    ${caption ? `<p style="color:#fff; margin-top:1rem; font-size:0.9rem; text-align:center;">${caption}</p>` : ''}
    <button onclick="closeLightbox()" style="position:absolute; top:1rem; right:1rem; background:rgba(255,255,255,0.2); border:none; color:#fff; font-size:1.5rem; width:40px; height:40px; border-radius:50%; cursor:pointer;">✕</button>
  `;
  lightbox.addEventListener('click', function(e) {
    if (e.target === lightbox) closeLightbox();
  });
  document.body.appendChild(lightbox);
}

function closeLightbox() {
  const lb = document.getElementById('lightbox');
  if (lb) lb.remove();
}

// Close lightbox with Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeLightbox();
});
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>