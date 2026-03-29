<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/config/database.php';

$db    = getDB();
$tahun = (int)($_GET['tahun'] ?? date('Y'));

$stmt = $db->prepare("SELECT * FROM survey_skm WHERE tahun = ? ORDER BY bulan ASC");
$stmt->execute([$tahun]);
$laporan = $stmt->fetchAll();

$tahunList = $db->query("SELECT DISTINCT tahun FROM survey_skm ORDER BY tahun DESC")->fetchAll(PDO::FETCH_COLUMN);
if (empty($tahunList)) $tahunList = [date('Y')];

$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];

// Hitung ringkasan
$rataRata  = !empty($laporan) ? round(array_sum(array_column($laporan, 'nilai_skm')) / count($laporan), 1) : 0;
$mutuRata  = $rataRata >= 88.31 ? 'A' : ($rataRata >= 76.61 ? 'B' : ($rataRata >= 65 ? 'C' : 'D'));
$jumlahLap = count($laporan);

$pageTitle    = 'Survey Kepuasan Masyarakat – Rutan Kelas IIA Batam';
$halamanAktif = 'survey';

$extraCss     = ['sejarah.css', 'survey.css'];
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
      <span class="breadcrumb-active">Survey Kepuasan Masyarakat</span>
    </div>
    <h1 class="page-title">Survey Kepuasan<br/><span>Masyarakat</span></h1>
    <p class="page-subtitle">Laporan hasil survey kepuasan layanan Rutan Kelas IIA Batam</p>
  </div>
</div>

<main class="page-main">
  <div class="survey-container">

    <!-- Intro -->
    <div class="survey-intro">
      <div class="survey-intro-icon">📊</div>
      <div>
        <div class="survey-intro-title">Tentang Survey Kepuasan Masyarakat</div>
        <p>Survey Kepuasan Masyarakat (SKM) dilaksanakan secara rutin setiap bulan sebagai bentuk komitmen Rutan Kelas IIA Batam dalam meningkatkan kualitas pelayanan publik. Hasil survey dipublikasikan secara terbuka dan dapat diunduh oleh masyarakat.</p>
      </div>
    </div>

    <!-- Filter tahun -->
    <div class="survey-filter">
      <span class="filter-label">Tampilkan tahun:</span>
      <div class="filter-tabs" id="filterTabs">
        <?php foreach ($tahunList as $t): ?>
        <a href="survey.php?tahun=<?= $t ?>" class="filter-btn <?= $t == $tahun ? 'active' : '' ?>"><?= $t ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Panel tahun aktif -->
    <div class="survey-year-panel active" id="year-<?= $tahun ?>">

      <!-- Ringkasan -->
      <?php if (!empty($laporan)): ?>
      <div class="survey-summary">
        <div class="summary-item">
          <div class="summary-val"><?= $rataRata ?></div>
          <div class="summary-label">Nilai Rata-rata</div>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-item">
          <div class="summary-val summary-val-baik"><?= $mutuRata ?></div>
          <div class="summary-label">Mutu Pelayanan</div>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-item">
          <div class="summary-val"><?= $jumlahLap ?></div>
          <div class="summary-label">Laporan Tersedia</div>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-item">
          <div class="summary-val"><?= $tahun ?></div>
          <div class="summary-label">Tahun</div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Tabel -->
      <?php if (empty($laporan)): ?>
      <div style="text-align:center; padding:4rem; color:#8888aa;">
        <div style="font-size:3rem">📭</div>
        <p>Belum ada laporan SKM untuk tahun <?= $tahun ?>.</p>
      </div>
      <?php else: ?>
      <div class="survey-table-wrap">
        <table class="survey-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Periode</th>
              <th>Jumlah Responden</th>
              <th>Nilai SKM</th>
              <th>Mutu</th>
              <th>Kinerja</th>
              <th>Laporan PDF</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($laporan as $i => $row): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><div class="td-periode"><?= $namaBulan[$row['bulan']] ?> <?= $row['tahun'] ?></div></td>
              <td class="td-center"><?= number_format($row['responden']) ?></td>
              <td>
                <div class="td-nilai">
                  <div class="nilai-bar">
                    <div class="nilai-fill" style="width:<?= round($row['nilai_skm']) ?>%"></div>
                  </div>
                  <span><?= number_format($row['nilai_skm'], 2) ?></span>
                </div>
              </td>
              <td class="td-center">
                <span class="mutu-badge mutu-<?= strtolower($row['mutu']) ?>"><?= $row['mutu'] ?></span>
              </td>
              <td class="td-center">
                <span class="kinerja-badge kinerja-baik"><?= htmlspecialchars($row['kinerja']) ?></span>
              </td>
              <td class="td-center">
                <?php if ($row['file_pdf']): ?>
                <a href="../pdfs/<?= htmlspecialchars($row['file_pdf']) ?>" target="_blank" class="btn-unduh" download>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  Unduh PDF
                </a>
                <?php else: ?>
                <span class="btn-belum">Belum ada</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <!-- Keterangan nilai -->
    <div class="survey-keterangan">
      <div class="ket-title">Keterangan Nilai SKM</div>
      <div class="ket-grid">
        <div class="ket-item"><div class="ket-badge mutu-a">A</div><div><div class="ket-range">88.31 – 100.00</div><div class="ket-kinerja">Sangat Baik</div></div></div>
        <div class="ket-item"><div class="ket-badge mutu-b">B</div><div><div class="ket-range">76.61 – 88.30</div><div class="ket-kinerja">Baik</div></div></div>
        <div class="ket-item"><div class="ket-badge mutu-c">C</div><div><div class="ket-range">65.00 – 76.60</div><div class="ket-kinerja">Kurang Baik</div></div></div>
        <div class="ket-item"><div class="ket-badge mutu-d">D</div><div><div class="ket-range">25.00 – 64.99</div><div class="ket-kinerja">Tidak Baik</div></div></div>
      </div>
      <p class="ket-sumber">Berdasarkan Permenpan RB Nomor 14 Tahun 2017 tentang Pedoman Penyusunan Survei Kepuasan Masyarakat.</p>
    </div>

  </div>
</main>

<script src="<?= BASE_URL ?>/js/survey.js"></script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>