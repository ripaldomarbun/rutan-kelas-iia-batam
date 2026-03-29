<?php
require_once __DIR__ . '/../backend/includes/helpers.php';
requireLogin();
$db = getDB(); $pesan=''; $tipePesan='success';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['simpan_kontak'])) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!validateCsrf($csrf)) {
        $pesan = 'Token CSRF tidak valid.';
        $tipePesan = 'danger';
    } else {
        $ids = $_POST['kontak_id'] ?? [];
        $labels = $_POST['label'] ?? [];
        $nilais = $_POST['nilai'] ?? [];
        $icons = $_POST['icon'] ?? [];
        
        $stmt = $db->prepare("UPDATE kontak_info SET label = ?, nilai = ?, icon = ? WHERE id = ?");
        foreach ($ids as $i => $id) {
            $stmt->execute([
                trim($labels[$i] ?? ''),
                trim($nilais[$i] ?? ''),
                trim($icons[$i] ?? ''),
                (int)$id
            ]);
        }
        $pesan = 'Informasi kontak berhasil disimpan!';
    }
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $db->prepare("DELETE FROM kontak_info WHERE id = ?")->execute([$id]);
    header('Location: kontak.php?pesan=Data berhasil dihapus');
    exit;
}

$kontak = $db->query("SELECT * FROM kontak_info ORDER BY urutan ASC")->fetchAll();
$activePage = 'kontak';
$pesan = $_GET['pesan'] ?? $pesan;
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Informasi Kontak – Admin Rutan</title><link rel="icon" type="image/png" href="../images/logo.png"/><link rel="stylesheet" href="css/admin.css"/>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/></head>
<body>
<script>sessionStorage.setItem('csrf_token', '<?= getCsrfToken() ?>');</script>
<div class="admin-layout">
<?php include __DIR__.'/includes/sidebar.php';?>
<div class="admin-main">
<header class="admin-topbar">
  <div class="topbar-left"><button class="sidebar-toggle" id="sidebarToggle">☰</button>
  <div><div class="topbar-page-title">Informasi Kontak</div><div class="topbar-breadcrumb">Dashboard / Kontak</div></div></div>
  <div class="topbar-right"><a href="../index.php#kontak" target="_blank" class="btn-view-site">🌐 Lihat Halaman</a>
  <div class="admin-user-chip"><div class="admin-user-avatar"><?=strtoupper(substr($_SESSION['admin_nama'],0,1))?></div>
  <div><div class="admin-user-name"><?=htmlspecialchars($_SESSION['admin_nama'])?></div></div></div></div>
</header>
<div class="admin-content">
<?php if($pesan):?><div class="admin-alert alert-<?=$tipePesan?>" data-auto-close>✅ <?=htmlspecialchars($pesan)?></div><?php endif;?>
<div class="admin-page-header"><div><div class="admin-page-title">📞 Informasi Kontak</div><div class="admin-page-desc">Kelola informasi kontak yang tampil di website (footer, halaman kontak, dll)</div></div></div>

<form method="POST">
  <input type="hidden" name="simpan_kontak" value="1"/>
  <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>"/>
  
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Daftar Kontak</div>
      <button type="submit" class="btn btn-primary">💾 Simpan Semua</button>
    </div>
    <div class="admin-card-body">
      <div style="display:flex;flex-direction:column;gap:1rem;">
      <?php foreach($kontak as $k):?>
      <div style="padding:1rem;border:1px solid var(--border-soft);border-radius:8px;background:var(--cream-light);">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
          <input type="text" name="icon[]" class="form-control" style="width:70px;text-align:center;font-size:1.25rem;" value="<?=htmlspecialchars($k['icon'])?>" placeholder="📍"/>
          <input type="hidden" name="kontak_id[]" value="<?=$k['id']?>"/>
          <input type="text" name="label[]" class="form-control" style="flex:1;font-weight:600;" value="<?=htmlspecialchars($k['label'])?>" placeholder="Label"/>
          <div style="font-size:.7rem;color:var(--text-light);font-family:monospace;"><?=htmlspecialchars($k['kode'])?></div>
        </div>
        <?php if($k['kode'] === 'jam_operasional'):?>
        <textarea name="nilai[]" class="form-control" style="min-height:70px;" placeholder="Jam operasional..."><?=htmlspecialchars($k['nilai'])?></textarea>
        <div class="form-hint">Gunakan &lt;br&gt; untuk baris baru</div>
        <?php elseif($k['kode'] === 'alamat'):?>
        <textarea name="nilai[]" class="form-control" style="min-height:70px;" placeholder="Alamat..."><?=htmlspecialchars(strip_tags($k['nilai']))?></textarea>
        <div class="form-hint">Gunakan &lt;br&gt; untuk baris baru</div>
        <?php elseif(in_array($k['kode'], ['maps'])):?>
        <input type="url" name="nilai[]" class="form-control" value="<?=htmlspecialchars($k['nilai'])?>" placeholder="https://..."/>
        <?php else:?>
        <input type="text" name="nilai[]" class="form-control" value="<?=htmlspecialchars($k['nilai'])?>" placeholder="Nilai..."/>
        <?php endif;?>
      </div>
      <?php endforeach;?>
      </div>
    </div>
  </div>
</form>

<div style="margin-top:1.5rem;padding:1rem;background:var(--navy);border-radius:8px;color:#fff;">
  <div style="font-weight:600;margin-bottom:.5rem;">📌 Catatan</div>
  <ul style="margin:0;padding-left:1.25rem;font-size:.85rem;opacity:.9;">
    <li>Email & Telepon akan muncul di topbar website</li>
    <li>Alamat, Telepon, Email, WhatsApp & Jam Operasional muncul di section kontak</li>
    <li>Link Google Maps digunakan untuk embed peta</li>
    <li>WhatsApp akan otomatis menjadi link wa.me</li>
  </ul>
</div>

</div></div></div>
<script src="js/admin.js"></script></body></html>
