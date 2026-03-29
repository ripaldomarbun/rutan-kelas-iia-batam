<?php
require_once __DIR__ . '/../backend/includes/helpers.php';
requireLogin();
$db = getDB(); $pesan=''; $tipePesan='success';

// ── SIMPAN JADWAL ──
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['simpan_jadwal'])) {
    $ids    = $_POST['jadwal_id']     ?? [];
    $buka   = $_POST['jam_buka']      ?? [];
    $tutup  = $_POST['jam_tutup']     ?? [];
    $status = $_POST['jadwal_status'] ?? [];
    $stmt   = $db->prepare("UPDATE kunjungan_jadwal SET jam_buka=?,jam_tutup=?,status=? WHERE id=?");
    foreach ($ids as $i => $id) {
        $st = ($status[$i]??'tutup')==='buka' ? 'buka' : 'tutup';
        $stmt->execute([$buka[$i]?:null, $tutup[$i]?:null, $st, (int)$id]);
    }
    $pesan = 'Jadwal berhasil disimpan!';
}

// ── SIMPAN INFO TEKS ──
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['simpan_info'])) {
    $kodeValid = ['syarat','prosedur','pengumuman','boleh','dilarang'];
    $stmt = $db->prepare("UPDATE kunjungan_info SET konten=? WHERE kode=?");
    foreach ($kodeValid as $k) {
        if (isset($_POST[$k])) $stmt->execute([trim($_POST[$k]), $k]);
    }
    $pesan = 'Informasi kunjungan berhasil disimpan!';
}

$jadwal  = $db->query("SELECT * FROM kunjungan_jadwal ORDER BY urutan ASC")->fetchAll();
$infoRaw = $db->query("SELECT kode,konten FROM kunjungan_info")->fetchAll(PDO::FETCH_KEY_PAIR);
$activePage = 'kunjungan';
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Jadwal Kunjungan – Admin Rutan</title><link rel="stylesheet" href="css/admin.css"/>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/></head>
<body>
<script>sessionStorage.setItem('csrf_token', '<?= getCsrfToken() ?>');</script>
<div class="admin-layout">
<?php include __DIR__.'/includes/sidebar.php';?>
<div class="admin-main">
<header class="admin-topbar">
  <div class="topbar-left"><button class="sidebar-toggle" id="sidebarToggle">☰</button>
  <div><div class="topbar-page-title">Jadwal Kunjungan</div><div class="topbar-breadcrumb">Dashboard / Kunjungan</div></div></div>
  <div class="topbar-right"><a href="../pages/kunjungan.php" target="_blank" class="btn-view-site">🌐 Lihat Halaman</a>
  <div class="admin-user-chip"><div class="admin-user-avatar"><?=strtoupper(substr($_SESSION['admin_nama'],0,1))?></div>
  <div><div class="admin-user-name"><?=htmlspecialchars($_SESSION['admin_nama'])?></div></div></div></div>
</header>
<div class="admin-content">
<?php if($pesan):?><div class="admin-alert alert-<?=$tipePesan?>" data-auto-close>✅ <?=htmlspecialchars($pesan)?></div><?php endif;?>
<div class="admin-page-header"><div><div class="admin-page-title">📅 Jadwal &amp; Informasi Kunjungan</div></div></div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

  <!-- Jadwal -->
  <form method="POST">
    <input type="hidden" name="simpan_jadwal" value="1"/>
    <div class="admin-card"><div class="admin-card-header">
      <div class="admin-card-title">⏰ Jadwal Kunjungan</div>
      <button type="submit" class="btn btn-primary btn-sm">💾 Simpan Jadwal</button>
    </div>
    <div class="admin-card-body" style="display:flex;flex-direction:column;gap:.75rem;">
    <?php foreach($jadwal as $i=>$j):?>
    <input type="hidden" name="jadwal_id[]" value="<?=$j['id']?>"/>
    <div style="padding:.75rem;border:1px solid var(--border-soft);border-radius:8px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem;">
        <strong style="font-size:.85rem;color:var(--navy);"><?=htmlspecialchars($j['hari'])?></strong>
        <label style="display:flex;align-items:center;gap:.4rem;font-size:.78rem;cursor:pointer;">
          <input type="checkbox" name="jadwal_status[]" value="buka" <?=$j['status']==='buka'?'checked':''?>/>
          Buka
        </label>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;">
        <div><label class="form-label" style="font-size:.7rem;">Jam Mulai</label>
        <input type="time" name="jam_buka[]" class="form-control" value="<?=$j['jam_buka']?substr($j['jam_buka'],0,5):''?>"/></div>
        <div><label class="form-label" style="font-size:.7rem;">Jam Selesai</label>
        <input type="time" name="jam_tutup[]" class="form-control" value="<?=$j['jam_tutup']?substr($j['jam_tutup'],0,5):''?>"/></div>
      </div>
    </div>
    <?php endforeach;?>
    </div></div>
  </form>

  <!-- Info teks -->
  <form method="POST">
    <input type="hidden" name="simpan_info" value="1"/>
    <div style="display:flex;flex-direction:column;gap:1rem;">
    <?php
    $infoFields = [
      'syarat'      => '📋 Syarat Kunjungan',
      'prosedur'    => '🔄 Prosedur',
      'pengumuman'  => '⚠️ Pengumuman Penting',
      'boleh'       => '✅ Barang Diperbolehkan',
      'dilarang'    => '🚫 Barang Dilarang',
    ];
    foreach ($infoFields as $kode => $label):?>
    <div class="admin-card"><div class="admin-card-header"><div class="admin-card-title"><?=$label?></div></div>
    <div class="admin-card-body">
      <textarea name="<?=$kode?>" class="form-control" style="min-height:90px;"><?=htmlspecialchars($infoRaw[$kode]??'')?></textarea>
      <div class="form-hint">Tulis setiap poin di baris baru</div>
    </div></div>
    <?php endforeach;?>
    <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">💾 Simpan Semua Informasi</button>
    </div>
  </form>

</div>
</div></div></div>
<script src="js/admin.js"></script></body></html>
