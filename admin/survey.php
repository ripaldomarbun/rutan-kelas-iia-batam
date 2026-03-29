<?php
require_once __DIR__ . '/../backend/includes/helpers.php';
requireLogin();
$db = getDB();
$action = $_GET['action'] ?? 'list';
$pesan = ''; $tipePesan = 'success';

// ── HAPUS ──
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['hapus_id'])) {
    $hid = (int)$_POST['hapus_id'];
    $r = $db->prepare("SELECT file_pdf FROM survey_skm WHERE id=?"); $r->execute([$hid]);
    $f = $r->fetchColumn();
    if ($f && file_exists(PDF_PATH.$f)) unlink(PDF_PATH.$f);
    $db->prepare("DELETE FROM survey_skm WHERE id=?")->execute([$hid]);
    $pesan='Laporan berhasil dihapus.'; $action='list';
}

// ── SIMPAN ──
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['simpan'])) {
    $id = (int)($_POST['id']??0);
    $bulan=(int)($_POST['bulan']??0); $tahun=(int)($_POST['tahun']??date('Y'));
    $responden=(int)($_POST['responden']??0); $nilai=(float)($_POST['nilai']??0);
    if (!$bulan||!$nilai) { $pesan='Bulan dan nilai wajib diisi.'; $tipePesan='danger'; }
    else {
        $mutu = mutuSKM($nilai);
        $filePdf = null;
        if (!empty($_FILES['pdf']['name'])) {
            $nb = strtolower(namaBulan($bulan));
            $filePdf = uploadPDF($_FILES['pdf'], "skm-{$nb}-{$tahun}.pdf");
            if (!$filePdf) { $pesan='Upload PDF gagal.'; $tipePesan='danger'; }
        }
        if (!$pesan) {
            if ($id) {
                $sql="UPDATE survey_skm SET bulan=?,tahun=?,responden=?,nilai_skm=?,mutu=?,kinerja=?,updated_at=NOW()";
                $par=[$bulan,$tahun,$responden,$nilai,$mutu['mutu'],$mutu['kinerja']];
                if ($filePdf){$sql.=",file_pdf=?";$par[]=$filePdf;} $sql.=" WHERE id=?";$par[]=$id;
                $db->prepare($sql)->execute($par);
            } else {
                $db->prepare("INSERT INTO survey_skm (bulan,tahun,responden,nilai_skm,mutu,kinerja,file_pdf) VALUES(?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE responden=VALUES(responden),nilai_skm=VALUES(nilai_skm),mutu=VALUES(mutu),kinerja=VALUES(kinerja),file_pdf=COALESCE(VALUES(file_pdf),file_pdf),updated_at=NOW()")
                   ->execute([$bulan,$tahun,$responden,$nilai,$mutu['mutu'],$mutu['kinerja'],$filePdf]);
            }
            $pesan='Laporan SKM berhasil disimpan!'; $action='list';
        }
    }
}

$editData=[];
if ($action==='edit') { $stmt=$db->prepare("SELECT * FROM survey_skm WHERE id=?"); $stmt->execute([(int)($_GET['id']??0)]); $editData=$stmt->fetch()?:[];}

$tahun=(int)($_GET['tahun']??date('Y'));
$laporan=$db->prepare("SELECT * FROM survey_skm WHERE tahun=? ORDER BY bulan ASC"); $laporan->execute([$tahun]); $laporan=$laporan->fetchAll();
$tahunList=$db->query("SELECT DISTINCT tahun FROM survey_skm ORDER BY tahun DESC")->fetchAll(PDO::FETCH_COLUMN);
if(empty($tahunList))$tahunList=[date('Y')];
$nb=['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$activePage='survey';
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Survey SKM – Admin Rutan</title>
<link rel="icon" type="image/png" href="../images/logo.png"/>
<link rel="stylesheet" href="css/admin.css"/>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/></head>
<body>
<script>sessionStorage.setItem('csrf_token', '<?= getCsrfToken() ?>');</script>
<div class="admin-layout">
<?php include __DIR__.'/includes/sidebar.php'; ?>
<div class="admin-main">
<header class="admin-topbar">
  <div class="topbar-left"><button class="sidebar-toggle" id="sidebarToggle">☰</button>
  <div><div class="topbar-page-title">Survey SKM</div><div class="topbar-breadcrumb">Dashboard / Survey SKM</div></div></div>
  <div class="topbar-right"><a href="../index.php" target="_blank" class="btn-view-site">🌐 Lihat Website</a>
  <div class="admin-user-chip"><div class="admin-user-avatar"><?=strtoupper(substr($_SESSION['admin_nama'],0,1))?></div>
  <div><div class="admin-user-name"><?=htmlspecialchars($_SESSION['admin_nama'])?></div><div class="admin-user-role"><?=$_SESSION['admin_role']?></div></div></div></div>
</header>
<div class="admin-content">
<?php if($pesan):?><div class="admin-alert alert-<?=$tipePesan?>" data-auto-close><?=$tipePesan==='success'?'✅':'❌'?> <?=htmlspecialchars($pesan)?></div><?php endif;?>

<?php if($action==='list'):?>
<div class="admin-page-header">
  <div><div class="admin-page-title">📊 Survey SKM</div><div class="admin-page-desc">Kelola laporan SKM bulanan</div></div>
  <a href="survey.php?action=tambah" class="btn btn-primary btn-lg">📤 Tambah Laporan</a>
</div>
<div class="admin-card" style="margin-bottom:1.25rem;"><div class="admin-card-body" style="display:flex;gap:.75rem;align-items:center;">
  <form method="GET" style="display:flex;gap:.75rem;"><input type="hidden" name="action" value="list"/>
  <select name="tahun" class="form-control" style="max-width:120px;" onchange="this.form.submit()">
    <?php foreach($tahunList as $t):?><option value="<?=$t?>" <?=$t==$tahun?'selected':''?>><?=$t?></option><?php endforeach;?>
  </select></form>
</div></div>
<div class="admin-card"><div class="admin-card-header"><div class="admin-card-title">Laporan SKM <?=$tahun?></div></div>
<div class="admin-table-wrap"><table class="admin-table">
<thead><tr><th>Periode</th><th>Responden</th><th>Nilai SKM</th><th>Mutu</th><th>Kinerja</th><th>PDF</th><th style="text-align:right">Aksi</th></tr></thead>
<tbody>
<?php foreach($laporan as $row):?>
<tr><td><strong><?=$nb[$row['bulan']]?> <?=$row['tahun']?></strong></td>
<td><?=number_format($row['responden'])?></td>
<td><strong><?=number_format($row['nilai_skm'],2)?></strong></td>
<td><span class="badge badge-publish"><?=$row['mutu']?></span></td>
<td><?=htmlspecialchars($row['kinerja'])?></td>
<td><?=$row['file_pdf']?'<a href="../pdfs/'.htmlspecialchars($row['file_pdf']).'" target="_blank" class="btn btn-outline btn-sm">📄 Lihat</a>':'—'?></td>
<td><div class="td-actions">
  <a href="survey.php?action=edit&id=<?=$row['id']?>" class="btn btn-outline btn-sm">✏️</a>
  <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus laporan ini?')">
    <input type="hidden" name="hapus_id" value="<?=$row['id']?>"/>
    <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
  </form>
</div></td></tr>
<?php endforeach; if(empty($laporan)):?>
<tr><td colspan="7" style="text-align:center;padding:3rem;color:#ccc;">Belum ada laporan SKM <?=$tahun?></td></tr>
<?php endif;?>
</tbody></table></div></div>

<?php else:?>
<div class="admin-page-header">
  <div><div class="admin-page-title"><?=$action==='edit'?'✏️ Edit Laporan SKM':'📤 Tambah Laporan SKM'?></div></div>
  <a href="survey.php" class="btn btn-outline">← Kembali</a>
</div>
<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="simpan" value="1"/>
  <input type="hidden" name="id" value="<?=$editData['id']??0?>"/>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
    <div class="admin-card"><div class="admin-card-header"><div class="admin-card-title">Data Nilai SKM</div></div>
    <div class="admin-card-body">
      <div class="form-row">
        <div class="form-group"><label class="form-label">Bulan <span>*</span></label>
        <select name="bulan" class="form-control" required>
          <?php for($i=1;$i<=12;$i++):?>
          <option value="<?=$i?>" <?=($editData['bulan']??0)==$i?'selected':''?>><?=$nb[$i]?></option>
          <?php endfor;?>
        </select></div>
        <div class="form-group"><label class="form-label">Tahun <span>*</span></label>
        <select name="tahun" class="form-control">
          <?php foreach([2026,2025,2024] as $t):?>
          <option value="<?=$t?>" <?=($editData['tahun']??date('Y'))==$t?'selected':''?>><?=$t?></option>
          <?php endforeach;?>
        </select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Jumlah Responden</label>
        <input type="number" name="responden" class="form-control" value="<?=$editData['responden']??0?>" min="0"/></div>
        <div class="form-group"><label class="form-label">Nilai SKM <span>*</span></label>
        <input type="number" name="nilai" class="form-control" value="<?=$editData['nilai_skm']??''?>" step="0.01" min="0" max="100" required/></div>
      </div>
      <?php if(!empty($editData['mutu'])):?>
      <div class="admin-alert alert-info">Mutu saat ini: <strong><?=$editData['mutu']?> – <?=$editData['kinerja']?></strong> (otomatis dihitung ulang saat simpan)</div>
      <?php endif;?>
    </div></div>

    <div style="display:flex;flex-direction:column;gap:1rem;">
      <div class="admin-card"><div class="admin-card-header"><div class="admin-card-title">Upload PDF</div></div>
      <div class="admin-card-body">
        <?php if(!empty($editData['file_pdf'])):?>
        <div class="admin-alert alert-info" style="margin-bottom:.75rem;">📄 PDF saat ini: <a href="../pdfs/<?=htmlspecialchars($editData['file_pdf'])?>" target="_blank"><?=htmlspecialchars($editData['file_pdf'])?></a></div>
        <?php endif;?>
        <div class="upload-area"><input type="file" name="pdf" accept=".pdf" data-preview="prevPdf"/>
        <div class="upload-icon">📄</div><div class="upload-text"><strong>Pilih file PDF</strong></div>
        <div class="upload-hint">PDF · Maks. 10 MB</div></div>
        <div class="upload-preview" id="prevPdf"><div class="upload-preview-nama"></div></div>
      </div></div>
      <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">💾 Simpan Laporan</button>
      <a href="survey.php" class="btn btn-outline" style="width:100%;text-align:center;">Batal</a>
    </div>
  </div>
</form>
<?php endif;?>
</div></div></div>
<script src="js/admin.js"></script></body></html>
