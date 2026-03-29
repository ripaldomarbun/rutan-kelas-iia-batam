<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/includes/helpers.php';

$db     = getDB();
$jadwal = $db->query("SELECT * FROM kunjungan_jadwal ORDER BY urutan ASC")->fetchAll();
$infoRaw = $db->query("SELECT kode, konten FROM kunjungan_info")->fetchAll(PDO::FETCH_KEY_PAIR);
$kontak = getKontak();

function infoLines($teks) {
    return array_filter(array_map('trim', explode("\n", $teks ?? '')));
}

$pageTitle    = 'Layanan Kunjungan – Rutan Kelas IIA Batam';
$halamanAktif = 'kunjungan';

$extraCss     = ['sejarah.css', 'kunjungan.css'];
include __DIR__ . '/../header.php';
?>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-overlay"></div>
  <div class="page-header-inner">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>/index.php">Beranda</a>
      <span>›</span>
      <a href="#">Informasi Publik</a>
      <span>›</span>
      <span class="breadcrumb-active">Layanan Kunjungan</span>
    </div>
    <h1 class="page-title">Layanan<br/><span>Kunjungan</span></h1>
    <p class="page-subtitle">Informasi jadwal, syarat, dan tata cara kunjungan ke Rutan Kelas IIA Batam</p>
  </div>
</div>

<main class="page-main">
  <div class="kunj-container">

    <!-- Alert pengumuman dari DB -->
    <?php if (!empty($infoRaw['pengumuman'])): ?>
    <div class="kunj-alert">
      <div class="kunj-alert-icon">📢</div>
      <div class="kunj-alert-text">
        <strong>Informasi Penting:</strong> <?= htmlspecialchars($infoRaw['pengumuman']) ?>
      </div>
    </div>
    <?php else: ?>
    <div class="kunj-alert">
      <div class="kunj-alert-icon">📢</div>
      <div class="kunj-alert-text">
        <strong>Informasi Penting:</strong> Kunjungan hanya dapat dilakukan pada hari dan jam yang telah ditentukan. Pastikan membawa dokumen identitas yang sah.
      </div>
    </div>
    <?php endif; ?>

    <!-- Jadwal -->
    <section class="kunj-section">
      <div class="kunj-section-header">
        <div class="kunj-section-icon">🗓️</div>
        <h2 class="kunj-section-title">Jadwal Kunjungan</h2>
      </div>
      <div class="jadwal-grid">
        <?php if (!empty($jadwal)): foreach ($jadwal as $j): ?>
        <div class="jadwal-card <?= $j['status'] === 'tutup' ? 'jadwal-tutup' : 'jadwal-buka' ?>">
          <div class="jadwal-hari"><?= htmlspecialchars($j['hari']) ?></div>
          <?php if ($j['status'] === 'buka'): ?>
            <div class="jadwal-jam"><?= date('H.i', strtotime($j['jam_buka'])) ?> – <?= date('H.i', strtotime($j['jam_tutup'])) ?> WIB</div>
            <div class="jadwal-status jadwal-status-buka">● Buka</div>
          <?php else: ?>
            <div class="jadwal-jam">— Tutup —</div>
            <div class="jadwal-status jadwal-status-tutup">● Tutup</div>
          <?php endif; ?>
        </div>
        <?php endforeach; else: ?>
        <!-- Fallback statis jika DB kosong -->
        <div class="jadwal-card jadwal-buka">
          <div class="jadwal-hari">Senin – Kamis</div>
          <div class="jadwal-jam">08.00 – 12.00 WIB</div>
          <div class="jadwal-status jadwal-status-buka">● Buka</div>
        </div>
        <div class="jadwal-card jadwal-buka">
          <div class="jadwal-hari">Jumat</div>
          <div class="jadwal-jam">08.00 – 11.00 WIB</div>
          <div class="jadwal-status jadwal-status-buka">● Buka</div>
        </div>
        <div class="jadwal-card jadwal-buka">
          <div class="jadwal-hari">Sabtu</div>
          <div class="jadwal-jam">08.00 – 12.00 WIB</div>
          <div class="jadwal-status jadwal-status-buka">● Buka</div>
        </div>
        <div class="jadwal-card jadwal-tutup">
          <div class="jadwal-hari">Minggu &amp; Hari Libur</div>
          <div class="jadwal-jam">— Tutup —</div>
          <div class="jadwal-status jadwal-status-tutup">● Tutup</div>
        </div>
        <?php endif; ?>
      </div>
      <p class="jadwal-catatan">⚠️ Jadwal dapat berubah sewaktu-waktu pada hari libur nasional. Konfirmasi melalui WhatsApp atau telepon.</p>
    </section>

    <!-- ── BANNER LAYANAN KUNJUNGAN ONLINE ── -->
<div class="kunjungan-banner">
  <div class="kunjungan-inner">
    <div class="kunjungan-left">
      <div class="kunjungan-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
        </svg>
      </div>
      <div class="kunjungan-text">
        <h3>Layanan Kunjungan Online Rutan Kelas IIA Batam</h3>
        <p>Kunjungi Warga Binaan secara virtual dari rumah. Daftarkan jadwal kunjungan online Anda melalui formulir resmi kami.</p>
      </div>
    </div>
    <a href="https://forms.gle/euo3Yx94T4jp5GYXA" class="kunjungan-btn" target="_blank" rel="noopener noreferrer">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18">
        <rect x="3" y="4" width="18" height="16" rx="2" />
        <path d="M8 2v4M16 2v4M3 10h18" />
      </svg>
      Daftar Kunjungan
    </a>
  </div>
</div>

<style>
.kunjungan-banner {
  background: linear-gradient(135deg, #166534 0%, #15803d 50%, #16a34a 100%);
  border-left: 5px solid #bbf7d0;
  border-radius: 12px;
  padding: 20px 24px;
  margin: 16px 0;
  box-shadow: 0 4px 16px rgba(22, 101, 52, 0.25);
}

.kunjungan-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

.kunjungan-left {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
  min-width: 0;
}

.kunjungan-icon {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  background: rgba(187, 247, 208, 0.2);
  border: 2px solid rgba(187, 247, 208, 0.4);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #bbf7d0;
}

.kunjungan-icon svg {
  width: 26px;
  height: 26px;
}

.kunjungan-text h3 {
  margin: 0 0 4px 0;
  font-size: 15px;
  font-weight: 700;
  color: #f0fdf4;
  line-height: 1.3;
}

.kunjungan-text p {
  margin: 0;
  font-size: 13px;
  color: #bbf7d0;
  line-height: 1.5;
}

.kunjungan-btn {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #f0fdf4;
  color: #166534;
  font-weight: 700;
  font-size: 14px;
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
  transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  white-space: nowrap;
}

.kunjungan-btn:hover {
  background: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
}

.kunjungan-btn:active {
  transform: translateY(0);
}

@media (max-width: 600px) {
  .kunjungan-inner {
    flex-direction: column;
    align-items: flex-start;
  }
  .kunjungan-btn {
    width: 100%;
    justify-content: center;
  }
}
</style>

    <!-- Syarat + Prosedur -->
    <div class="kunj-two-col">
      <section class="kunj-section">
        <div class="kunj-section-header">
          <div class="kunj-section-icon">📄</div>
          <h2 class="kunj-section-title">Syarat Kunjungan</h2>
        </div>
        <?php if (!empty($infoRaw['syarat'])): ?>
        <ul class="syarat-list">
          <?php foreach (infoLines($infoRaw['syarat']) as $item): ?>
          <li><div class="syarat-icon">✔️</div><div><?= htmlspecialchars($item) ?></div></li>
          <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <!-- Konten statis fallback -->
        <ul class="syarat-list">
          <li><div class="syarat-icon">🪪</div><div><div class="syarat-title">Kartu Identitas</div><p>Membawa KTP / SIM / Paspor yang masih berlaku.</p></div></li>
          <li><div class="syarat-icon">👔</div><div><div class="syarat-title">Berpakaian Sopan</div><p>Tidak memakai pakaian berwarna abu-abu.</p></div></li>
          <li><div class="syarat-icon">📵</div><div><div class="syarat-title">Barang Bawaan</div><p>Semua barang akan diperiksa petugas.</p></div></li>
        </ul>
        <?php endif; ?>
      </section>

      <section class="kunj-section">
        <div class="kunj-section-header">
          <div class="kunj-section-icon">📋</div>
          <h2 class="kunj-section-title">Prosedur Kunjungan</h2>
        </div>
        <?php if (!empty($infoRaw['prosedur'])): ?>
        <ol class="prosedur-list">
          <?php foreach (infoLines($infoRaw['prosedur']) as $i => $item): ?>
          <li><div class="prosedur-num"><?= $i+1 ?></div><div><?= htmlspecialchars($item) ?></div></li>
          <?php endforeach; ?>
        </ol>
        <?php else: ?>
        <ol class="prosedur-list">
          <li><div class="prosedur-num">1</div><div><div class="prosedur-title">Datang ke Rutan</div><p>Datang sesuai jadwal dan antri di loket pendaftaran.</p></div></li>
          <li><div class="prosedur-num">2</div><div><div class="prosedur-title">Daftar di Loket</div><p>Tunjukkan kartu identitas dan sebutkan nama tahanan.</p></div></li>
          <li><div class="prosedur-num">3</div><div><div class="prosedur-title">Pemeriksaan Barang</div><p>Semua barang bawaan akan diperiksa petugas.</p></div></li>
          <li><div class="prosedur-num">4</div><div><div class="prosedur-title">Masuk Ruang Kunjungan</div><p>Waktu kunjungan maksimal 30 menit.</p></div></li>
          <li><div class="prosedur-num">5</div><div><div class="prosedur-title">Selesai</div><p>Ambil kembali barang yang dititipkan.</p></div></li>
        </ol>
        <?php endif; ?>
      </section>
    </div>

    <!-- Barang boleh & dilarang -->
    <section class="kunj-section">
      <div class="kunj-section-header">
        <div class="kunj-section-icon">🎁</div>
        <h2 class="kunj-section-title">Ketentuan Barang Titipan</h2>
      </div>
      <div class="barang-grid">
        <div class="barang-col barang-boleh">
          <div class="barang-col-header"><span class="barang-col-icon">✅</span><span>Barang yang Diizinkan</span></div>
          <ul class="barang-list">
            <?php if (!empty($infoRaw['boleh'])): foreach (infoLines($infoRaw['boleh']) as $item): ?>
            <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; else: ?>
            <li>Pakaian bersih (tidak berwarna abu-abu)</li>
            <li>Makanan dalam kemasan tersegel</li>
            <li>Perlengkapan mandi</li>
            <li>Kitab suci</li>
            <li>Obat-obatan dengan resep dokter</li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="barang-col barang-dilarang">
          <div class="barang-col-header"><span class="barang-col-icon">🚫</span><span>Barang yang Dilarang</span></div>
          <ul class="barang-list">
            <?php if (!empty($infoRaw['dilarang'])): foreach (infoLines($infoRaw['dilarang']) as $item): ?>
            <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; else: ?>
            <li>Telepon genggam dan elektronik</li>
            <li>Senjata tajam dan benda berbahaya</li>
            <li>Narkotika dan zat terlarang</li>
            <li>Makanan tanpa kemasan / buatan sendiri</li>
            <li>Pakaian berwarna abu-abu</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </section>

    <!-- Kontak -->
    <section class="kunj-section">
      <div class="kunj-section-header">
        <div class="kunj-section-icon">📞</div>
        <h2 class="kunj-section-title">Informasi &amp; Konfirmasi</h2>
      </div>
      <div class="kontak-kunj-grid">
        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $kontak['telepon']['nilai'] ?? '') ?>" class="kontak-kunj-card">
          <div class="kontak-kunj-icon">📞</div>
          <div class="kontak-kunj-label"><?= htmlspecialchars($kontak['telepon']['label'] ?? 'Telepon') ?></div>
          <div class="kontak-kunj-val"><?= htmlspecialchars($kontak['telepon']['nilai'] ?? '') ?></div>
          <div class="kontak-kunj-hint">Senin – Jumat, 08.00–16.00</div>
        </a>
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $kontak['whatsapp']['nilai'] ?? '') ?>" target="_blank" class="kontak-kunj-card kontak-wa">
          <div class="kontak-kunj-icon">💬</div>
          <div class="kontak-kunj-label"><?= htmlspecialchars($kontak['whatsapp']['label'] ?? 'WhatsApp') ?></div>
          <div class="kontak-kunj-val"><?= htmlspecialchars($kontak['whatsapp']['nilai'] ?? '') ?></div>
          <div class="kontak-kunj-hint">Pengaduan &amp; Konfirmasi</div>
        </a>
        <a href="mailto:<?= htmlspecialchars($kontak['email']['nilai'] ?? '') ?>" class="kontak-kunj-card">
          <div class="kontak-kunj-icon">✉️</div>
          <div class="kontak-kunj-label"><?= htmlspecialchars($kontak['email']['label'] ?? 'Email') ?></div>
          <div class="kontak-kunj-val"><?= htmlspecialchars($kontak['email']['nilai'] ?? '') ?></div>
          <div class="kontak-kunj-hint">Balasan dalam 1×24 jam</div>
        </a>
        <a href="<?= htmlspecialchars($kontak['maps']['nilai'] ?? 'https://maps.google.com/?q=Rutan+Kelas+IIA+Batam') ?>" target="_blank" class="kontak-kunj-card">
          <div class="kontak-kunj-icon">📍</div>
          <div class="kontak-kunj-label">Lokasi</div>
          <div class="kontak-kunj-val"><?= strip_tags($kontak['alamat']['nilai'] ?? 'Jl. Raya Trans Barelang') ?></div>
          <div class="kontak-kunj-hint">Buka di Google Maps →</div>
        </a>
      </div>
    </section>

  </div>
</main>


<?php include __DIR__ . '/../backend/includes/footer.php'; ?>