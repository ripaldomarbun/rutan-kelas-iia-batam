<?php
require_once __DIR__ . '/../backend/includes/helpers.php';
requireLogin();

$db = getDB();
$activePage = 'faq';
$pesan = '';
$tipePesan = 'success';

$faqs = $db->query("SELECT * FROM faq ORDER BY urutan ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FAQ – Admin Rutan</title>
  <link rel="icon" type="image/png" href="../images/logo.png"/>
  <link rel="stylesheet" href="css/admin.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    .faq-item { background: var(--white); border: 1px solid var(--border-soft); border-radius: 10px; padding: 1rem; margin-bottom: 0.75rem; }
    .faq-item:hover { border-color: var(--gold); }
    .faq-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
    .faq-pertanyaan { font-weight: 600; color: var(--navy); margin: 0; flex: 1; }
    .faq-kategori { font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 4px; background: var(--gold-light); color: var(--navy); font-weight: 600; }
    .faq-kategori.kunjungan { background: #dbeafe; color: #1e40af; }
    .faq-kategori.umum { background: #dcfce7; color: #166534; }
    .faq-kategori.layanan { background: #fef3c7; color: #92400e; }
    .faq-jawaban { margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border-soft); color: var(--text-mid); font-size: 0.9rem; line-height: 1.5; }
    .faq-actions { display: flex; gap: 0.5rem; }
    .faq-aktif { width: 18px; height: 18px; cursor: pointer; }
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 1rem; }
    .modal-overlay.active { display: flex; }
    .modal { background: var(--white); border-radius: 12px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
    .modal-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; }
    .modal-title { font-size: 1.1rem; font-weight: 700; color: var(--navy); }
    .modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-light); }
    .modal-body { padding: 1.25rem; }
    .modal-footer { padding: 1rem 1.25rem; border-top: 1px solid var(--border-soft); display: flex; justify-content: flex-end; gap: 0.75rem; }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--navy); margin-bottom: 0.4rem; }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--border-soft); border-radius: 8px; font-size: 0.9rem; font-family: inherit; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--gold); }
    .form-textarea { min-height: 100px; resize: vertical; }
    .checkbox-label { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; }
  </style>
</head>
<body>
<script>sessionStorage.setItem('csrf_token', '<?= getCsrfToken() ?>');</script>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div class="admin-main">
  <header class="admin-topbar">
    <div class="topbar-left">
      <button class="sidebar-toggle" id="sidebarToggle">☰</button>
      <div>
        <div class="topbar-page-title">FAQ</div>
        <div class="topbar-breadcrumb">Dashboard / FAQ</div>
      </div>
    </div>
    <div class="topbar-right">
      <button class="btn btn-primary" onclick="bukaModal()">➕ Tambah FAQ</button>
    </div>
  </header>
  <div class="admin-content">
    <?php if ($pesan): ?>
      <div class="admin-alert alert-<?= $tipePesan ?>" data-auto-close>
        <?= $tipePesan === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($pesan) ?>
      </div>
    <?php endif; ?>
    
    <div class="admin-page-header">
      <div>
        <div class="admin-page-title">❓ Frequently Asked Questions</div>
        <div class="admin-page-desc">Kelola pertanyaan yang sering diajukan (FAQ) untuk chatbot</div>
      </div>
    </div>
    
    <div id="faqList">
      <?php foreach ($faqs as $faq): ?>
        <div class="faq-item" data-id="<?= $faq['id'] ?>">
          <div class="faq-header">
            <div>
              <span class="faq-kategori <?= $faq['kategori'] ?>"><?= strtoupper($faq['kategori']) ?></span>
              <h3 class="faq-pertanyaan"><?= htmlspecialchars($faq['pertanyaan']) ?></h3>
            </div>
            <div class="faq-actions">
              <button class="btn btn-outline btn-sm" onclick="editFaq(<?= $faq['id'] ?>, '<?= htmlspecialchars($faq['pertanyaan'], ENT_QUOTES) ?>', '<?= htmlspecialchars($faq['jawaban'], ENT_QUOTES) ?>', '<?= $faq['kategori'] ?>', <?= $faq['aktif'] ?>)">✏️ Edit</button>
              <button class="btn btn-outline btn-sm" onclick="hapusFaq(<?= $faq['id'] ?>)">🗑️ Hapus</button>
            </div>
          </div>
          <div class="faq-jawaban"><?= $faq['jawaban'] ?></div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($faqs)): ?>
        <div style="text-align: center; padding: 3rem; color: var(--text-light);">
          <div style="font-size: 3rem; margin-bottom: 0.5rem;">❓</div>
          <div>Belum ada FAQ. Klik "Tambah FAQ" untuk membuat.</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal Tambah/Edit FAQ -->
<div class="modal-overlay" id="faqModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">Tambah FAQ</div>
      <button class="modal-close" onclick="tutupModal()">×</button>
    </div>
    <form id="faqForm">
      <div class="modal-body">
        <input type="hidden" name="id" id="faqId"/>
        <div class="form-group">
          <label class="form-label">Pertanyaan</label>
          <input type="text" name="pertanyaan" id="faqPertanyaan" class="form-input" placeholder="Contoh: Apa jadwal kunjungan?" required/>
        </div>
        <div class="form-group">
          <label class="form-label">Jawaban</label>
          <textarea name="jawaban" id="faqJawaban" class="form-textarea" placeholder="Jawabannya..." required></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select name="kategori" id="faqKategori" class="form-select">
            <option value="kunjungan">Kunjungan</option>
            <option value="umum">Umum</option>
            <option value="layanan">Layanan</option>
          </select>
        </div>
        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" name="aktif" id="faqAktif" checked/>
            Aktif / Tampilkan di chatbot
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="tutupModal()">Batal</button>
        <button type="submit" class="btn btn-primary">💾 Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="js/admin.js"></script>
<script>
const modal = document.getElementById('faqModal');
const form = document.getElementById('faqForm');

function bukaModal() {
  document.getElementById('modalTitle').textContent = 'Tambah FAQ';
  form.reset();
  document.getElementById('faqId').value = '';
  document.getElementById('faqAktif').checked = true;
  modal.classList.add('active');
}

function tutupModal() {
  modal.classList.remove('active');
}

function editFaq(id, pertanyaan, jawaban, kategori, aktif) {
  document.getElementById('modalTitle').textContent = 'Edit FAQ';
  document.getElementById('faqId').value = id;
  document.getElementById('faqPertanyaan').value = pertanyaan;
  document.getElementById('faqJawaban').value = jawaban;
  document.getElementById('faqKategori').value = kategori;
  document.getElementById('faqAktif').checked = aktif == 1;
  modal.classList.add('active');
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(form);
  const id = fd.get('id');
  const action = id ? 'edit' : 'tambah';
  
  const csrf = sessionStorage.getItem('csrf_token');
  fd.append('csrf_token', csrf);
  
  try {
    const res = await fetch(`../backend/api/faq.php?action=${action}`, {
      method: 'POST',
      body: fd
    });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      location.reload();
    } else {
      alert(data.message || 'Gagal menyimpan FAQ');
    }
  } catch (err) {
    alert('Terjadi kesalahan: ' + err.message);
  }
});

async function hapusFaq(id) {
  if (!confirm('Yakin ingin menghapus FAQ ini?')) return;
  
  const fd = new FormData();
  fd.append('id', id);
  fd.append('csrf_token', sessionStorage.getItem('csrf_token'));
  
  try {
    const res = await fetch('../backend/api/faq.php?action=hapus', {
      method: 'POST',
      body: fd
    });
    const data = await res.json();
    if (data.status === 'success') {
      location.reload();
    } else {
      alert(data.message || 'Gagal menghapus FAQ');
    }
  } catch (err) {
    alert('Terjadi kesalahan: ' + err.message);
  }
}

modal.addEventListener('click', (e) => {
  if (e.target === modal) tutupModal();
});
</script>
</body>
</html>
