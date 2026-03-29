<?php
require_once __DIR__ . '/../backend/includes/helpers.php';
requireLogin();
$db = getDB(); $pesan=''; $tipePesan='success';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['hapus_id'])) {
    $hid=(int)$_POST['hapus_id'];
    $r=$db->prepare("SELECT gambar FROM slider WHERE id=?"); $r->execute([$hid]);
    $g=$r->fetchColumn();
    if($g){$p=UPLOAD_PATH.'slider/'.$g; if(file_exists($p))unlink($p);}
    $db->prepare("DELETE FROM slider WHERE id=?")->execute([$hid]);
    $pesan='Slide berhasil dihapus.';
}

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['tambah'])) {
    if (empty($_FILES['gambar']['name'])) { $pesan='Pilih gambar terlebih dahulu.'; $tipePesan='danger'; }
    else {
        $namaFile = uploadGambar($_FILES['gambar'], 'slider');
        if (!$namaFile) { $pesan='Upload gagal.'; $tipePesan='danger'; }
        else {
            $max=(int)$db->query("SELECT COALESCE(MAX(urutan),0) FROM slider")->fetchColumn();
            $db->prepare("INSERT INTO slider (gambar,urutan) VALUES (?,?)")->execute([$namaFile,$max+1]);
            $pesan='Slide berhasil ditambahkan!';
        }
    }
}

$slides = $db->query("SELECT * FROM slider WHERE aktif=1 ORDER BY urutan ASC")->fetchAll();
$activePage='slider';
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Slider – Admin Rutan</title><link rel="stylesheet" href="css/admin.css"/>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/></head>
<body>
<script>sessionStorage.setItem('csrf_token', '<?= getCsrfToken() ?>');</script>
<div class="admin-layout">
<?php include __DIR__.'/includes/sidebar.php';?>
<div class="admin-main">
<header class="admin-topbar">
  <div class="topbar-left"><button class="sidebar-toggle" id="sidebarToggle">☰</button>
  <div><div class="topbar-page-title">Slider Beranda</div><div class="topbar-breadcrumb">Dashboard / Slider</div></div></div>
  <div class="topbar-right"><a href="../index.php" target="_blank" class="btn-view-site">🌐 Lihat Website</a>
  <div class="admin-user-chip"><div class="admin-user-avatar"><?=strtoupper(substr($_SESSION['admin_nama'],0,1))?></div>
  <div><div class="admin-user-name"><?=htmlspecialchars($_SESSION['admin_nama'])?></div></div></div></div>
</header>
<div class="admin-content">
<?php if($pesan):?><div class="admin-alert alert-<?=$tipePesan?>" data-auto-close><?=$tipePesan==='success'?'✅':'❌'?> <?=htmlspecialchars($pesan)?></div><?php endif;?>
<div class="admin-page-header">
  <div><div class="admin-page-title">🖼️ Slider Beranda</div><div class="admin-page-desc">Kelola gambar slider halaman utama (maks. 5 slide)</div></div>
</div>
<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:1.25rem;align-items:start;">
  <div class="admin-card"><div class="admin-card-header"><div class="admin-card-title">Slide Aktif</div><span style="font-size:.75rem;color:var(--text-light);"><?=count($slides)?> / 5</span></div>
  <div class="admin-card-body" style="display:flex;flex-direction:column;gap:.75rem;">
    <?php foreach($slides as $i=>$s):
      $imgUrl = file_exists(__DIR__.'/../backend/uploads/slider/'.$s['gambar'])
        ? '../backend/uploads/slider/'.htmlspecialchars($s['gambar'])
        : '../images/'.htmlspecialchars($s['gambar']);
    ?>
    <div style="display:grid;grid-template-columns:180px 1fr auto;gap:1rem;align-items:center;padding:1rem;border:1px solid var(--border-soft);border-radius:10px;">
      <div style="position:relative;width:180px;height:100px;border-radius:8px;overflow:hidden;background:var(--navy-light);">
        <img src="<?=$imgUrl?>" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'"/>
        <span style="position:absolute;top:6px;left:6px;background:rgba(0,0,0,.6);color:#fff;font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:999px;">Slide <?=$i+1?></span>
      </div>
      <div>
        <div style="font-weight:700;font-size:.85rem;color:var(--navy);"><?=htmlspecialchars($s['gambar'])?></div>
        <span class="badge badge-publish" style="margin-top:4px;">Aktif</span>
      </div>
      <form method="POST" onsubmit="return confirm('Hapus slide ini?')">
        <input type="hidden" name="hapus_id" value="<?=$s['id']?>"/>
        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
      </form>
    </div>
    <?php endforeach; if(empty($slides)):?>
    <div class="empty-state"><div class="empty-state-icon">🖼️</div><div class="empty-state-title">Belum ada slide</div></div>
    <?php endif;?>
  </div></div>

  <div class="admin-card"><div class="admin-card-header"><div class="admin-card-title">Tambah Slide Baru</div></div>
  <div class="admin-card-body">
    <?php if(count($slides)>=5):?>
    <div class="admin-alert alert-warning">⚠️ Maksimal 5 slide. Hapus slide lama sebelum menambah.</div>
    <?php else:?>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="tambah" value="1"/>
      <div class="upload-area"><input type="file" name="gambar" accept="image/*" data-preview="prevSlide"/>
      <div class="upload-icon">🖼️</div><div class="upload-text"><strong>Klik pilih gambar</strong> atau drag & drop</div>
      <div class="upload-hint">JPG, PNG, WEBP · Maks. 3 MB<br/>Rekomendasi: 1920×700px</div></div>
      <div class="upload-preview" id="prevSlide" style="margin-top:.75rem;">
        <img src="" style="width:100%;height:120px;object-fit:cover;border-radius:8px;"/>
        <div class="upload-preview-nama"></div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;margin-top:1rem;">💾 Simpan Slide</button>
    </form>
    <?php endif;?>
  </div></div>
</div>
</div></div></div>
<script src="js/admin.js"></script></body></html>
