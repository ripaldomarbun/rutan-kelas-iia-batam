<?php
require_once __DIR__ . '/../backend/includes/helpers.php';
requireLogin();

$db     = getDB();
$action = $_GET['action'] ?? (!empty($_GET['edit']) ? 'edit' : 'list');
$editId = (int)($_GET['edit'] ?? 0);
$pesan  = '';
$tipePesan = 'success';

// ── HAPUS ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $hapusId = (int)$_POST['hapus_id'];
    $row = $db->prepare("SELECT gambar FROM berita WHERE id=?");
    $row->execute([$hapusId]); $g = $row->fetchColumn();
    if ($g) { $p = UPLOAD_PATH . 'berita/' . $g; if (file_exists($p)) unlink($p); }
    $db->prepare("DELETE FROM berita WHERE id=?")->execute([$hapusId]);
    $pesan = 'Berita berhasil dihapus.';
    $action = 'list';
}

// ── HAPUS FOTO TAMBAHAN ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_foto_id'])) {
    $hapusFotoId = (int)$_POST['hapus_foto_id'];
    $r = $db->prepare("SELECT foto FROM berita_fotos WHERE id=?");
    $r->execute([$hapusFotoId]); $fotoFile = $r->fetchColumn();
    if ($fotoFile) {
        $p = UPLOAD_PATH . 'berita/' . $fotoFile;
        if (file_exists($p)) unlink($p);
    }
    $db->prepare("DELETE FROM berita_fotos WHERE id=?")->execute([$hapusFotoId]);
    $pesan = 'Foto berhasil dihapus.';
    $action = 'edit';
    $editId = (int)$_POST['berita_id'];
}

// ── SIMPAN ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id        = (int)($_POST['id'] ?? 0);
    $judul     = bersihkan($_POST['judul']    ?? '');
    $ringkasan = bersihkan($_POST['ringkasan']?? '');
    $isi       = $_POST['isi'] ?? '';
    $kategori  = bersihkan($_POST['kategori'] ?? '');
    $status    = bersihkan($_POST['status']   ?? 'draft');
    $penulis   = bersihkan($_POST['penulis']  ?? 'Humas Rutan Batam');
    $tanggal   = bersihkan($_POST['tanggal']  ?? date('Y-m-d'));

    if (!$judul || !$isi || !$kategori) {
        $pesan = 'Judul, isi, dan kategori wajib diisi.'; $tipePesan = 'danger';
    } else {
        $slug   = slugUnik($db, buatSlug($judul), $id ?: null);
        $gambar = null;
        if (!empty($_FILES['gambar']['name'])) {
            $gambarLama = null;
            if ($id) { $r=$db->prepare("SELECT gambar FROM berita WHERE id=?"); $r->execute([$id]); $gambarLama=$r->fetchColumn(); }
            $gambar = uploadGambar($_FILES['gambar'], 'berita', $gambarLama);
            if (!$gambar) { $pesan='Upload gambar gagal.'; $tipePesan='danger'; }
        }
        if (!$pesan) {
            if ($id) {
                $sql = "UPDATE berita SET judul=?,slug=?,ringkasan=?,isi=?,kategori=?,status=?,penulis=?,tanggal=?,updated_at=NOW()";
                $par = [$judul,$slug,$ringkasan,$isi,$kategori,$status,$penulis,$tanggal];
                if ($gambar) { $sql.=",gambar=?"; $par[]=$gambar; }
                $sql.=" WHERE id=?"; $par[]=$id;
                $db->prepare($sql)->execute($par);
            } else {
                $db->prepare("INSERT INTO berita (judul,slug,ringkasan,isi,gambar,kategori,status,penulis,tanggal) VALUES (?,?,?,?,?,?,?,?,?)")
                   ->execute([$judul,$slug,$ringkasan,$isi,$gambar,$kategori,$status,$penulis,$tanggal]);
                $id = $db->lastInsertId();
            }
            
            // Upload foto tambahan
            if (isset($_FILES['fotos']) && $id) {
                $captions = $_POST['foto_caption'] ?? [];
                $urutan = $db->query("SELECT COALESCE(MAX(urutan),0) FROM berita_fotos WHERE berita_id=$id")->fetchColumn();
                
                // Hitung jumlah file
                $fileCount = is_array($_FILES['fotos']['name']) ? count($_FILES['fotos']['name']) : 0;
                
                for ($i = 0; $i < $fileCount; $i++) {
                    // Skip jika tidak ada file atau ada error
                    if (empty($_FILES['fotos']['name'][$i]) || $_FILES['fotos']['error'][$i] !== UPLOAD_ERR_OK) {
                        continue;
                    }
                    
                    $foto = uploadGambar([
                        'name'      => $_FILES['fotos']['name'][$i],
                        'tmp_name'  => $_FILES['fotos']['tmp_name'][$i],
                        'size'      => $_FILES['fotos']['size'][$i],
                        'type'      => $_FILES['fotos']['type'][$i],
                        'error'     => $_FILES['fotos']['error'][$i]
                    ], 'berita');
                    
                    if ($foto) {
                        $urutan++;
                        $caption = isset($captions[$i]) ? bersihkan($captions[$i]) : '';
                        $db->prepare("INSERT INTO berita_fotos (berita_id, foto, caption, urutan) VALUES (?,?,?,?)")
                           ->execute([$id, $foto, $caption, $urutan]);
                    }
                }
            }
            
            $pesan = 'Berita berhasil disimpan!';
            $action = 'list';
        }
    }
}

// ── DATA UNTUK FORM EDIT ───────────────────────────────
$editData = [];
$editFotos = [];
if (($action === 'edit' || $action === 'tambah') && $editId) {
    $stmt = $db->prepare("SELECT * FROM berita WHERE id=?");
    $stmt->execute([$editId]); $editData = $stmt->fetch() ?: [];
    
    // Load foto tambahan
    $stmtFotos = $db->prepare("SELECT * FROM berita_fotos WHERE berita_id=? ORDER BY urutan ASC");
    $stmtFotos->execute([$editId]);
    $editFotos = $stmtFotos->fetchAll();
    
    $action = 'edit';
}
if ($_GET['action'] ?? '' === 'tambah') $action = 'tambah';

// ── DAFTAR ─────────────────────────────────────────────
$cari = bersihkan($_GET['cari'] ?? '');
$kat  = bersihkan($_GET['kategori'] ?? '');
$sql  = "SELECT id,judul,kategori,status,tanggal,views FROM berita WHERE 1=1";
$par  = [];
if ($cari) { $sql.=" AND judul LIKE ?"; $par[]="%$cari%"; }
if ($kat)  { $sql.=" AND kategori=?";  $par[]=$kat; }
$sql .= " ORDER BY created_at DESC";
$stmt = $db->prepare($sql); $stmt->execute($par);
$daftarBerita = $stmt->fetchAll();

$activePage = 'berita';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Berita – Admin Rutan Kelas IIA Batam</title>
  <link rel="stylesheet" href="css/admin.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/>
</head>
<body>
<script>sessionStorage.setItem('csrf_token', '<?= getCsrfToken() ?>');</script>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div class="admin-main">
  <header class="admin-topbar">
    <div class="topbar-left">
      <button class="sidebar-toggle" id="sidebarToggle">☰</button>
      <div><div class="topbar-page-title">Manajemen Berita</div>
      <div class="topbar-breadcrumb">Dashboard / Berita</div></div>
    </div>
    <div class="topbar-right">
      <a href="../index.php" target="_blank" class="btn-view-site">🌐 Lihat Website</a>
      <div class="admin-user-chip">
        <div class="admin-user-avatar"><?= strtoupper(substr($_SESSION['admin_nama'],0,1)) ?></div>
        <div><div class="admin-user-name"><?= htmlspecialchars($_SESSION['admin_nama']) ?></div>
        <div class="admin-user-role"><?= $_SESSION['admin_role'] ?></div></div>
      </div>
    </div>
  </header>

  <div class="admin-content">
  <?php if ($pesan): ?>
  <div class="admin-alert alert-<?= $tipePesan ?>" data-auto-close>
    <?= $tipePesan==='success'?'✅':'❌' ?> <?= htmlspecialchars($pesan) ?>
  </div>
  <?php endif; ?>

  <?php if ($action === 'list'): ?>
  <!-- ═══ DAFTAR BERITA ═══ -->
  <div class="admin-page-header">
    <div><div class="admin-page-title">📰 Berita</div>
    <div class="admin-page-desc">Kelola seluruh berita dan pengumuman</div></div>
    <a href="berita.php?action=tambah" class="btn btn-primary btn-lg">✏️ Tulis Berita Baru</a>
  </div>

  <!-- Filter -->
  <div class="admin-card" style="margin-bottom:1.25rem;">
    <div class="admin-card-body" style="display:flex; gap:0.75rem; flex-wrap:wrap;">
      <form method="GET" style="display:flex; gap:0.75rem; flex-wrap:wrap; width:100%;">
        <input type="hidden" name="action" value="list"/>
        <input type="text" name="cari" class="form-control" placeholder="🔍 Cari judul..." style="max-width:240px;" value="<?= htmlspecialchars($cari) ?>"/>
        <select name="kategori" class="form-control" style="max-width:160px;">
          <option value="">Semua Kategori</option>
          <?php foreach(['kegiatan','pengumuman','prestasi','pembinaan'] as $k): ?>
          <option value="<?=$k?>" <?=$kat===$k?'selected':''?>><?=ucfirst($k)?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
        <a href="berita.php" class="btn btn-outline">Reset</a>
      </form>
    </div>
  </div>

  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Daftar Berita</div>
      <span style="font-size:0.75rem; color:var(--text-light);"><?= count($daftarBerita) ?> artikel</span>
    </div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>No</th><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Views</th><th style="text-align:right">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($daftarBerita as $i => $b): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:600; color:var(--navy);">
            <?= htmlspecialchars($b['judul']) ?>
          </td>
          <td><span class="badge badge-<?= $b['kategori'] ?>"><?= ucfirst($b['kategori']) ?></span></td>
          <td><?= $b['tanggal'] ? date('d M Y', strtotime($b['tanggal'])) : '—' ?></td>
          <td><span class="badge badge-<?= $b['status']==='publish'?'publish':'draft' ?>"><?= $b['status'] ?></span></td>
          <td><?= $b['views'] ?></td>
          <td>
            <div class="td-actions">
              <a href="berita.php?edit=<?= $b['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
              <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus berita ini?')">
                <input type="hidden" name="hapus_id" value="<?= $b['id'] ?>"/>
                <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($daftarBerita)): ?>
        <tr><td colspan="7" style="text-align:center; padding:3rem; color:#ccc;">Belum ada berita</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php else: ?>
  <!-- ═══ FORM TAMBAH / EDIT ═══ -->
  <?php $isEdit = !empty($editData); ?>
  <div class="admin-page-header">
    <div><div class="admin-page-title"><?= $isEdit ? '✏️ Edit Berita' : '✏️ Tulis Berita Baru' ?></div>
    <div class="admin-page-desc">Isi semua kolom yang diperlukan</div></div>
    <a href="berita.php" class="btn btn-outline">← Kembali ke Daftar</a>
  </div>

  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="simpan" value="1"/>
    <input type="hidden" name="id" value="<?= $editData['id'] ?? 0 ?>"/>
    <div style="display:grid; grid-template-columns:1fr 300px; gap:1.25rem; align-items:start;">

      <div style="display:flex; flex-direction:column; gap:1.25rem;">
        <div class="admin-card">
          <div class="admin-card-header"><div class="admin-card-title">Konten Berita</div></div>
          <div class="admin-card-body">
            <div class="form-group">
              <label class="form-label">Judul Berita <span>*</span></label>
              <input type="text" name="judul" class="form-control" required
                value="<?= htmlspecialchars($editData['judul'] ?? '') ?>" placeholder="Masukkan judul berita..."/>
            </div>
            <div class="form-group">
              <label class="form-label">Isi Berita <span>*</span></label>
              <textarea name="isi" class="form-control" style="min-height:280px;" required
                placeholder="Tulis isi berita..."><?= htmlspecialchars($editData['isi'] ?? '') ?></textarea>
              <div class="form-hint">💡 Untuk teks HTML (tebal, miring, dll) gunakan tag HTML langsung di sini.</div>
            </div>
            <div class="form-group">
              <label class="form-label">Ringkasan</label>
              <textarea name="ringkasan" class="form-control" style="min-height:80px;"
                placeholder="Ringkasan singkat (tampil di kartu berita)..."><?= htmlspecialchars($editData['ringkasan'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <div class="admin-card">
          <div class="admin-card-header"><div class="admin-card-title">Gambar Utama</div></div>
          <div class="admin-card-body">
            <?php if (!empty($editData['gambar'])): ?>
            <div style="margin-bottom:0.75rem;">
              <div style="font-size:0.75rem; color:var(--text-light); margin-bottom:4px;">Gambar saat ini:</div>
              <?php
              $imgSrc = file_exists(__DIR__ . '/../backend/uploads/berita/' . $editData['gambar'])
                ? '../backend/uploads/berita/' . $editData['gambar']
                : '../images/' . $editData['gambar'];
              ?>
              <img src="<?= $imgSrc ?>" style="height:120px; border-radius:8px; border:1px solid var(--border-soft);"/>
            </div>
            <?php endif; ?>
            <div class="upload-area">
              <input type="file" name="gambar" accept="image/*" data-preview="prevGambar"/>
              <div class="upload-icon">🖼️</div>
              <div class="upload-text"><strong>Klik untuk pilih gambar</strong> atau drag & drop</div>
              <div class="upload-hint">JPG, PNG, WEBP · Maks. 5 MB · Rekomendasi 1200×630px</div>
            </div>
            <div class="upload-preview" id="prevGambar" style="margin-top:0.75rem;">
              <img src="" alt="Preview" style="max-height:150px; border-radius:8px;"/>
              <div class="upload-preview-nama"></div>
            </div>
          </div>
        </div>

        <!-- Foto Tambahan (Gallery) -->
        <div class="admin-card">
          <div class="admin-card-header"><div class="admin-card-title">📷 Foto Tambahan (Gallery)</div></div>
          <div class="admin-card-body">
            
            <!-- Foto yang sudah ada (saat edit) -->
            <?php if (!empty($editFotos)): ?>
            <div style="margin-bottom:1rem;">
              <div style="font-size:0.75rem; color:var(--text-light); margin-bottom:8px;">Foto yang sudah diupload:</div>
              <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(140px, 1fr)); gap:0.75rem;">
                <?php foreach ($editFotos as $f): ?>
                <div style="position:relative; background:#f5f5f5; border-radius:8px; overflow:hidden; border:1px solid var(--border-soft);">
                  <img src="../backend/uploads/berita/<?= htmlspecialchars($f['foto']) ?>" 
                       style="width:100%; height:100px; object-fit:cover;"
                       onerror="this.src='../images/berita-1.jpg'"/>
                  <?php if ($f['caption']): ?>
                  <div style="padding:4px 8px; font-size:0.7rem; color:var(--text-mid); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($f['caption']) ?></div>
                  <?php endif; ?>
                  <form method="POST" style="position:absolute; top:4px; right:4px;">
                    <input type="hidden" name="hapus_foto_id" value="<?= $f['id'] ?>"/>
                    <input type="hidden" name="berita_id" value="<?= $editId ?>"/>
                    <button type="submit" onclick="return confirm('Hapus foto ini?')" 
                            style="background:rgba(220,38,38,0.9); color:#fff; border:none; border-radius:4px; padding:2px 6px; cursor:pointer; font-size:0.7rem;">✕</button>
                  </form>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
            
            <!-- Upload foto baru -->
            <div class="upload-area">
              <input type="file" name="fotos[]" accept="image/*" multiple id="inputFotos"/>
              <div class="upload-icon">📷</div>
              <div class="upload-text"><strong>Klik untuk pilih multiple foto</strong> atau drag & drop</div>
              <div class="upload-hint">Bisa pilih banyak foto sekaligus · JPG, PNG, WEBP · Maks. 5 MB per foto</div>
            </div>
            
            <!-- Preview foto yang dipilih -->
            <div id="previewFotos" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(120px, 1fr)); gap:0.75rem; margin-top:1rem;"></div>
            
          </div>
        </div>
      </div>

      <!-- Kolom kanan -->
      <div style="display:flex; flex-direction:column; gap:1rem;">
        <div class="admin-card">
          <div class="admin-card-header"><div class="admin-card-title">Pengaturan</div></div>
          <div class="admin-card-body">
            <div class="form-group">
              <label class="form-label">Status <span>*</span></label>
              <select name="status" class="form-control">
                <option value="publish" <?= ($editData['status']??'')=='publish'?'selected':'' ?>>🟢 Publish</option>
                <option value="draft"   <?= ($editData['status']??'draft')=='draft'?'selected':'' ?>>⚪ Draft</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Kategori <span>*</span></label>
              <select name="kategori" class="form-control" required>
                <option value="">— Pilih —</option>
                <?php foreach(['kegiatan','pengumuman','prestasi','pembinaan'] as $k): ?>
                <option value="<?=$k?>" <?=($editData['kategori']??'')===$k?'selected':''?>><?=ucfirst($k)?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Penulis</label>
              <input type="text" name="penulis" class="form-control"
                value="<?= htmlspecialchars($editData['penulis'] ?? 'Humas Rutan Batam') ?>"/>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label">Tanggal Terbit</label>
              <input type="date" name="tanggal" class="form-control"
                value="<?= $editData['tanggal'] ?? date('Y-m-d') ?>"/>
            </div>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">💾 Simpan Berita</button>
        <a href="berita.php" class="btn btn-outline" style="width:100%; text-align:center;">Batal</a>
      </div>

    </div>
  </form>
  <?php endif; ?>

  </div>
</div>
</div>
<script src="js/admin.js"></script>
<script>
// Preview multiple foto
document.getElementById('inputFotos')?.addEventListener('change', function(e) {
  const preview = document.getElementById('previewFotos');
  preview.innerHTML = '';
  
  const files = Array.from(e.target.files);
  
  if (files.length === 0) return;
  
  files.forEach(function(file, i) {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(ev) {
        const div = document.createElement('div');
        div.style.cssText = 'position:relative; background:#f5f5f5; border-radius:8px; overflow:hidden; border:1px solid var(--border-soft);';
        div.innerHTML = `
          <img src="${ev.target.result}" style="width:100%; height:90px; object-fit:cover;"/>
          <input type="text" name="foto_caption[]" placeholder="Caption foto ${i+1}..." 
                 style="width:100%; padding:4px; font-size:0.7rem; border:none; border-top:1px solid #eee;"/>
        `;
        preview.appendChild(div);
      };
      reader.readAsDataURL(file);
    }
  });
});
</script>
</body>
</html>
