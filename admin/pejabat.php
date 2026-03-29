<?php
require_once __DIR__ . '/../backend/includes/helpers.php';
requireLogin();
$db = getDB();
$pesan = ''; $tipePesan = 'success';
$kodeEdit = bersihkan($_GET['kode'] ?? '');

// ── SIMPAN ──
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['simpan'])) {
    $kode       = bersihkan($_POST['kode']      ?? '');
    $nama       = bersihkan($_POST['nama']       ?? '');
    $nip        = bersihkan($_POST['nip']        ?? '');
    $pangkat    = bersihkan($_POST['pangkat']    ?? '');
    $pendidikan = bersihkan($_POST['pendidikan'] ?? '');
    $bio        = bersihkan($_POST['bio']        ?? '');

    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $r = $db->prepare("SELECT foto FROM pejabat WHERE kode=?"); $r->execute([$kode]);
        $fotoLama = $r->fetchColumn();
        $foto = uploadGambar($_FILES['foto'], 'pejabat', $fotoLama ?: null);
        if (!$foto) { $pesan='Upload foto gagal.'; $tipePesan='danger'; }
    }
    if (!$pesan) {
        $sql = "UPDATE pejabat SET nama=?,nip=?,pangkat=?,pendidikan=?,bio=?";
        $par = [$nama,$nip,$pangkat,$pendidikan,$bio];
        if ($foto) { $sql.=",foto=?"; $par[]=$foto; }
        $sql.=" WHERE kode=?"; $par[]=$kode;
        $db->prepare($sql)->execute($par);
        $pesan='Profil pejabat berhasil disimpan!';
        $kodeEdit='';
    }
}

$daftarPejabat = $db->query("SELECT * FROM pejabat ORDER BY urutan ASC")->fetchAll();

// Data untuk form edit
$editData = [];
if ($kodeEdit) {
    foreach ($daftarPejabat as $p) { if ($p['kode']===$kodeEdit) { $editData=$p; break; } }
}

function fotoAdminUrl($p) {
    if ($p['foto'] && file_exists(__DIR__.'/../backend/uploads/pejabat/'.$p['foto']))
        return '../backend/uploads/pejabat/'.htmlspecialchars($p['foto']);
    return '../images/'.htmlspecialchars($p['kode']).'.jpg';
}

$activePage = 'pejabat';
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Profil Pejabat – Admin Rutan</title>
<link rel="icon" type="image/png" href="../images/logo.png"/>
<link rel="stylesheet" href="css/admin.css"/>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/>
<style>
.pejabat-grid-admin{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;}
.pejabat-item-admin{border:1px solid var(--border-soft);border-radius:10px;padding:1rem;text-align:center;background:var(--white);}
.pej-foto{width:72px;height:80px;border-radius:8px;overflow:hidden;margin:0 auto .6rem;border:2px solid var(--gold);background:var(--navy-light);display:flex;align-items:center;justify-content:center;color:var(--gold);font-weight:900;}
.pej-foto img{width:100%;height:100%;object-fit:cover;object-position:top;}
</style></head>
<body>
<script>sessionStorage.setItem('csrf_token', '<?= getCsrfToken() ?>');</script>
<div class="admin-layout">
<?php include __DIR__.'/includes/sidebar.php'; ?>
<div class="admin-main">
<header class="admin-topbar">
  <div class="topbar-left"><button class="sidebar-toggle" id="sidebarToggle">☰</button>
  <div><div class="topbar-page-title">Profil Pejabat</div><div class="topbar-breadcrumb">Dashboard / Pejabat</div></div></div>
  <div class="topbar-right"><a href="../pages/pejabat.php" target="_blank" class="btn-view-site">🌐 Lihat Halaman</a>
  <div class="admin-user-chip"><div class="admin-user-avatar"><?=strtoupper(substr($_SESSION['admin_nama'],0,1))?></div>
  <div><div class="admin-user-name"><?=htmlspecialchars($_SESSION['admin_nama'])?></div></div></div></div>
</header>
<div class="admin-content">
<?php if($pesan):?><div class="admin-alert alert-<?=$tipePesan?>" data-auto-close><?=$tipePesan==='success'?'✅':'❌'?> <?=htmlspecialchars($pesan)?></div><?php endif;?>

<?php if(!$kodeEdit):?>
<!-- DAFTAR -->
<div class="admin-page-header">
  <div><div class="admin-page-title">👤 Profil Pejabat</div><div class="admin-page-desc">Klik Edit untuk mengubah data</div></div>
</div>
<div class="admin-card"><div class="admin-card-body">
<div class="pejabat-grid-admin">
<?php foreach($daftarPejabat as $p):?>
<div class="pejabat-item-admin">
  <div class="pej-foto">
    <img src="<?=fotoAdminUrl($p)?>" alt="" onerror="this.style.display='none';this.parentElement.textContent='<?=strtoupper(substr($p['jabatan'],0,2))?>'"/>
  </div>
  <div style="font-size:.65rem;font-weight:700;color:var(--gold);text-transform:uppercase;"><?=htmlspecialchars($p['jabatan'])?></div>
  <div style="font-size:.82rem;font-weight:800;color:var(--navy);margin-top:2px;"><?=$p['nama']?htmlspecialchars($p['nama']):'<em style="color:#ccc">Belum diisi</em>'?></div>
  <a href="pejabat.php?kode=<?=urlencode($p['kode'])?>" class="btn btn-outline btn-sm" style="margin-top:.6rem;width:100%;">✏️ Edit</a>
</div>
<?php endforeach;?>
</div>
</div></div>

<?php else:?>
<!-- FORM EDIT -->
<div class="admin-page-header">
  <div><div class="admin-page-title">✏️ Edit: <?=htmlspecialchars($editData['jabatan']??$kodeEdit)?></div></div>
  <a href="pejabat.php" class="btn btn-outline">← Kembali</a>
</div>
<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="simpan" value="1"/>
  <input type="hidden" name="kode" value="<?=htmlspecialchars($kodeEdit)?>"/>
  <div style="display:grid;grid-template-columns:1fr 280px;gap:1.25rem;align-items:start;">
    <div class="admin-card"><div class="admin-card-header"><div class="admin-card-title">Data Pejabat</div></div>
    <div class="admin-card-body">
      <div class="form-group"><label class="form-label">Jabatan</label>
      <input type="text" class="form-control" value="<?=htmlspecialchars($editData['jabatan']??'')?>" readonly style="background:#f8fafc;"/></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Nama Lengkap <span>*</span></label>
        <input type="text" name="nama" class="form-control" value="<?=htmlspecialchars($editData['nama']??'')?>" placeholder="Nama beserta gelar"/></div>
        <div class="form-group"><label class="form-label">NIP</label>
        <input type="text" name="nip" class="form-control" value="<?=htmlspecialchars($editData['nip']??'')?>" placeholder="19XXXXXXXXXXXXXXXX"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Pangkat/Golongan</label>
        <input type="text" name="pangkat" class="form-control" value="<?=htmlspecialchars($editData['pangkat']??'')?>" placeholder="Penata, III/c"/></div>
        <div class="form-group"><label class="form-label">Pendidikan Terakhir</label>
        <input type="text" name="pendidikan" class="form-control" value="<?=htmlspecialchars($editData['pendidikan']??'')?>" placeholder="S1 Hukum"/></div>
      </div>
      <div class="form-group"><label class="form-label">Biografi Singkat</label>
      <textarea name="bio" class="form-control" style="min-height:100px;"><?=htmlspecialchars($editData['bio']??'')?></textarea></div>
    </div></div>
    <div style="display:flex;flex-direction:column;gap:1rem;">
      <div class="admin-card"><div class="admin-card-header"><div class="admin-card-title">Foto</div></div>
      <div class="admin-card-body">
        <div style="text-align:center;margin-bottom:.75rem;">
          <img src="<?=fotoAdminUrl($editData)?>" style="width:90px;height:110px;object-fit:cover;object-position:top;border-radius:8px;border:2px solid var(--gold);" onerror="this.style.display='none'"/>
        </div>
        <div class="upload-area"><input type="file" name="foto" accept="image/*" data-preview="prevFoto"/>
        <div class="upload-icon">📷</div><div class="upload-text"><strong>Ganti foto</strong></div>
        <div class="upload-hint">JPG, PNG · Maks. 2 MB</div></div>
        <div class="upload-preview" id="prevFoto" style="margin-top:.5rem;text-align:center;">
          <img src="" style="width:90px;height:110px;object-fit:cover;border-radius:8px;border:2px solid var(--gold);"/>
          <div class="upload-preview-nama" style="font-size:.7rem;margin-top:4px;"></div>
        </div>
      </div></div>
      <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">💾 Simpan Profil</button>
      <a href="pejabat.php" class="btn btn-outline" style="width:100%;text-align:center;">Batal</a>
    </div>
  </div>
</form>
<?php endif;?>
</div></div></div>
<script src="js/admin.js"></script></body></html>
