<?php
require_once __DIR__ . '/../backend/includes/helpers.php';
requireLogin();
$db = getDB(); $pesan=''; $tipePesan='success';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['simpan'])) {
    $kode = in_array($_POST['kode']??'', ['maklumat-1','maklumat-2']) ? $_POST['kode'] : '';
    if (!$kode || empty($_FILES['gambar']['name'])) { $pesan='Pilih gambar terlebih dahulu.'; $tipePesan='danger'; }
    else {
        $r=$db->prepare("SELECT gambar FROM komitmen WHERE kode=?"); $r->execute([$kode]);
        $lama=$r->fetchColumn();
        $namaFile=uploadGambar($_FILES['gambar'],'maklumat',$lama?:null);
        if(!$namaFile){$pesan='Upload gagal.';$tipePesan='danger';}
        else{$db->prepare("UPDATE komitmen SET gambar=? WHERE kode=?")->execute([$namaFile,$kode]);$pesan='Gambar '.$kode.' berhasil diperbarui!';}
    }
}
$maklumat=$db->query("SELECT * FROM komitmen ORDER BY id ASC")->fetchAll();
function imgMaklumat($m){
    if($m['gambar']&&file_exists(__DIR__.'/../backend/uploads/maklumat/'.$m['gambar']))
        return '../backend/uploads/maklumat/'.htmlspecialchars($m['gambar']);
    return '../images/'.htmlspecialchars($m['gambar']??'maklumat-1.jpg');
}
$activePage='komitmen';
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Komitmen – Admin Rutan</title><link rel="icon" type="image/png" href="../images/logo.png"/><link rel="stylesheet" href="css/admin.css"/>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/></head>
<body>
<script>sessionStorage.setItem('csrf_token', '<?= getCsrfToken() ?>');</script>
<div class="admin-layout">
<?php include __DIR__.'/includes/sidebar.php';?>
<div class="admin-main">
<header class="admin-topbar">
  <div class="topbar-left"><button class="sidebar-toggle" id="sidebarToggle">☰</button>
  <div><div class="topbar-page-title">Komitmen</div><div class="topbar-breadcrumb">Dashboard / Komitmen</div></div></div>
  <div class="topbar-right"><a href="../pages/komitmen.php" target="_blank" class="btn-view-site">🌐 Lihat Halaman</a>
  <div class="admin-user-chip"><div class="admin-user-avatar"><?=strtoupper(substr($_SESSION['admin_nama'],0,1))?></div>
  <div><div class="admin-user-name"><?=htmlspecialchars($_SESSION['admin_nama'])?></div></div></div></div>
</header>
<div class="admin-content">
<?php if($pesan):?><div class="admin-alert alert-<?=$tipePesan?>" data-auto-close><?=$tipePesan==='success'?'✅':'❌'?> <?=htmlspecialchars($pesan)?></div><?php endif;?>
<div class="admin-page-header"><div><div class="admin-page-title">📜 Gambar Maklumat</div><div class="admin-page-desc">Ganti poster maklumat pelayanan</div></div></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
<?php foreach($maklumat as $m):?>
<div class="admin-card"><div class="admin-card-header">
  <div class="admin-card-title">Gambar <?=htmlspecialchars($m['kode'])?></div>
  <a href="../pages/komitmen.php" target="_blank" class="btn btn-outline btn-sm">👁️ Preview</a>
</div>
<div class="admin-card-body">
  <div style="margin-bottom:1rem;">
    <div style="font-size:.75rem;font-weight:700;color:var(--text-mid);margin-bottom:.4rem;">Gambar saat ini:</div>
    <div style="border-radius:8px;overflow:hidden;border:1px solid var(--border-soft);background:var(--cream);">
      <img src="<?=imgMaklumat($m)?>" style="width:100%;height:200px;object-fit:contain;" onerror="this.parentElement.innerHTML='<div style=\'height:200px;display:flex;align-items:center;justify-content:center;color:var(--text-light);font-size:.82rem;\'>📋 Belum ada gambar</div>'"/>
    </div>
  </div>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="simpan" value="1"/>
    <input type="hidden" name="kode" value="<?=htmlspecialchars($m['kode'])?>"/>
    <div class="upload-area"><input type="file" name="gambar" accept="image/*" data-preview="prev<?=$m['id']?>"/>
    <div class="upload-icon">🖼️</div><div class="upload-text"><strong>Ganti dengan gambar baru</strong></div>
    <div class="upload-hint">JPG, PNG · Maks. 5 MB</div></div>
    <div class="upload-preview" id="prev<?=$m['id']?>" style="margin-top:.75rem;">
      <img src="" style="width:100%;border-radius:8px;object-fit:contain;max-height:180px;"/>
      <div class="upload-preview-nama"></div>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:.75rem;">💾 Simpan Gambar</button>
  </form>
</div></div>
<?php endforeach;?>
</div>
</div></div></div>
<script src="js/admin.js"></script></body></html>
