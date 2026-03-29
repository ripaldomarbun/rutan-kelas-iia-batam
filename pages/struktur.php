<?php
require_once __DIR__ . '/../backend/config/config.php';
require_once __DIR__ . '/../backend/config/database.php';

// Ambil data pejabat dari database
$db = getDB();
$pejabat = $db->query("SELECT * FROM pejabat ORDER BY urutan ASC")->fetchAll();

// Helper function untuk foto (sama seperti pejabat.php)
function strukturFotoUrl($kode) {
    global $pejabat;
    foreach ($pejabat as $p) {
        if ($p['kode'] === $kode) {
            if ($p['foto'] && file_exists(__DIR__ . '/../backend/uploads/pejabat/' . $p['foto'])) {
                return BASE_URL . '/backend/uploads/pejabat/' . htmlspecialchars($p['foto']);
            }
            return BASE_URL . '/images/' . htmlspecialchars($kode) . '.jpg';
        }
    }
    return BASE_URL . '/images/' . htmlspecialchars($kode) . '.jpg';
}

// Helper function untuk nama pejabat
function strukturNama($kode) {
    global $pejabat;
    foreach ($pejabat as $p) {
        if ($p['kode'] === $kode) {
            return htmlspecialchars($p['nama'] ?? '—');
        }
    }
    return 'Nama Pejabat';
}

// Helper function untuk inisial
function strukturInitial($kode) {
    global $pejabat;
    foreach ($pejabat as $p) {
        if ($p['kode'] === $kode && $p['nama']) {
            return strtoupper(substr($p['nama'], 0, 2));
        }
    }
    return strtoupper(substr($kode, 0, 2));
}

$pageTitle    = 'Struktur – Rutan Kelas IIA Batam';
$halamanAktif = 'struktur';
$extraCss     = ['struktur.css','sejarah.css', 'visi-misi.css'];
include __DIR__ . '/../header.php';
?>
  <!-- ── PAGE HEADER ── -->
  <div class="page-header">
    <div class="page-header-overlay"></div>
    <div class="page-header-inner">
      <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/index.php">Beranda</a>
        <span>›</span>
        <a href="#">Tentang</a>
        <span>›</span>
        <span class="breadcrumb-active">Struktur Organisasi</span>
      </div>
      <h1 class="page-title">Struktur<br/><span>Organisasi</span></h1>
      <p class="page-subtitle">Susunan pejabat dan unit kerja Rutan Kelas IIA Batam</p>
    </div>
  </div>

  <!-- ── KONTEN UTAMA ── -->
  <main class="page-main">
    <div class="page-container">

      <!-- Sidebar -->
      <aside class="page-sidebar">
        <div class="sidebar-title">Menu Tentang</div>
        <ul class="sidebar-menu">
          <li><a href="<?= BASE_URL ?>/sejarah" class="active">📜 Sejarah Rutan</a></li>
          <li><a href="<?= BASE_URL ?>/visi-misi">🎯 Visi, Misi &amp; Tujuan</a></li>
          <li><a href="<?= BASE_URL ?>/struktur">🏛️ Struktur Organisasi</a></li>
          <li><a href="<?= BASE_URL ?>/tupoksi">📋 Tugas Pokok &amp; Fungsi</a></li>
          <li><a href="<?= BASE_URL ?>/pejabat">👤 Profil Pejabat</a></li>
        </ul>
      </aside>

      <!-- Konten -->
      <article class="page-content">
        <div class="content-body">

          <h2 class="content-heading">Bagan Struktur Organisasi</h2>
          <p>Rutan Kelas IIA Batam dipimpin oleh seorang Kepala Rutan yang membawahi beberapa sub bagian dan seksi sesuai dengan Peraturan Menteri yang berlaku.</p>

          <!-- ── BAGAN STRUKTUR ── -->
          <div class="org-chart">

            <!-- Level 1: Kepala Rutan + Tata Usaha (samping) -->
            <div class="org-level-top">

              <!-- Spacer kiri agar kepala tetap di tengah -->
              <div class="org-spacer"></div>

              <!-- Kotak Kepala Rutan (tengah) -->
              <div class="org-box org-box-top">
                <div class="org-avatar">
                  <img src="<?= strukturFotoUrl('karutan') ?>" alt="Kepala Rutan"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
                  <div class="org-avatar-fallback"><?= strukturInitial('karutan') ?></div>
                </div>
                <div class="org-info">
                  <div class="org-name"><?= strukturNama('karutan') ?></div>
                  <div class="org-jabatan">Kepala Rumah Tahanan Negara</div>
                </div>
              </div>

              <!-- Panah → Tata Usaha di kanan -->
              <div class="org-arrow-right">
                <div class="org-arrow-line"></div>
                <div class="org-arrow-head">▶</div>
                <!-- Simpan foto di images/tata-usaha.jpg, ganti nama pejabat -->
                <div class="org-box org-box-side">
                  <div class="org-foto-circle">
                    <img src="<?= strukturFotoUrl('tata-usaha') ?>" alt="Tata Usaha"
                      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
                    <div class="org-foto-fallback"><?= strukturInitial('tata-usaha') ?></div>
                  </div>
                  <div class="org-box-label">TATA USAHA</div>
                  <div class="org-box-nama"><?= strukturNama('tata-usaha') ?></div>
                </div>
              </div>

            </div>

            <!-- Garis vertikal dari Kepala ke bawah -->
            <div class="org-connector-center">
              <div class="org-v-line"></div>
            </div>

            <!-- Garis horizontal penghubung 4 kasubsi -->
            <div class="org-connector-center">
              <div class="org-h-line"></div>
            </div>

            <!-- Level 2: 4 Kasubsi -->
            <div class="org-level-bottom">

              <!-- Kasubsi Pengelolaan -->
              <div class="org-col-bottom">
                <div class="org-v-line-short"></div>
                <div class="org-arrow-down">↓</div>
                <div class="org-box org-box-mid">
                  <div class="org-foto-circle">
                    <img src="<?= strukturFotoUrl('kasubsi-pengelolaan') ?>" alt="Kasubsi Pengelolaan"
                      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
                    <div class="org-foto-fallback"><?= strukturInitial('kasubsi-pengelolaan') ?></div>
                  </div>
                  <div class="org-box-label">KASUBSI PENGELOLAAN</div>
                  <div class="org-box-nama"><?= strukturNama('kasubsi-pengelolaan') ?></div>
                </div>
              </div>

              <!-- Kasubsi KPR -->
              <div class="org-col-bottom">
                <div class="org-v-line-short"></div>
                <div class="org-arrow-down">↓</div>
                <div class="org-box org-box-mid">
                  <div class="org-foto-circle">
                    <img src="<?= strukturFotoUrl('kasubsi-kpr') ?>" alt="Kasubsi KPR"
                      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
                    <div class="org-foto-fallback"><?= strukturInitial('kasubsi-kpr') ?></div>
                  </div>
                  <div class="org-box-label">KASUBSI KPR</div>
                  <div class="org-box-nama"><?= strukturNama('kasubsi-kpr') ?></div>
                </div>
              </div>

              <!-- Kasubsi Peltah -->
              <div class="org-col-bottom">
                <div class="org-v-line-short"></div>
                <div class="org-arrow-down">↓</div>
                <div class="org-box org-box-mid">
                  <div class="org-foto-circle">
                    <img src="<?= strukturFotoUrl('kasubsi-peltah') ?>" alt="Kasubsi Peltah"
                      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
                    <div class="org-foto-fallback"><?= strukturInitial('kasubsi-peltah') ?></div>
                  </div>
                  <div class="org-box-label">KASUBSI PELTAH</div>
                  <div class="org-box-nama"><?= strukturNama('kasubsi-peltah') ?></div>
                </div>
              </div>

              <!-- Kasubsi Bimgiat -->
              <div class="org-col-bottom">
                <div class="org-v-line-short"></div>
                <div class="org-arrow-down">↓</div>
                <div class="org-box org-box-mid">
                  <div class="org-foto-circle">
                    <img src="<?= strukturFotoUrl('kasubsi-bimgiat') ?>" alt="Kasubsi Bimgiat"
                      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
                    <div class="org-foto-fallback"><?= strukturInitial('kasubsi-bimgiat') ?></div>
                  </div>
                  <div class="org-box-label">KASUBSI BIMGIAT</div>
                  <div class="org-box-nama"><?= strukturNama('kasubsi-bimgiat') ?></div>
                </div>
              </div>

            </div>

          </div><!-- /org-chart -->

          <!-- Keterangan -->
          <div class="org-keterangan">
            <div class="org-ket-title">Keterangan Warna</div>
            <div class="org-ket-list">
              <div class="org-ket-item">
                <div class="org-ket-dot org-ket-dot-top"></div>
                <span>Pimpinan</span>
              </div>
              <div class="org-ket-item">
                <div class="org-ket-dot org-ket-dot-mid"></div>
                <span>Sub Bagian / Seksi</span>
              </div>
              <div class="org-ket-item">
                <div class="org-ket-dot org-ket-dot-bottom"></div>
                <span>Unit Pelaksana</span>
              </div>
            </div>
          </div>

          <!-- Catatan -->
          <div class="org-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Struktur organisasi ini berdasarkan Peraturan Menteri Hukum dan HAM / Kementerian Imigrasi dan Pemasyarakatan yang berlaku. Untuk informasi lebih lanjut silakan hubungi Sub Bagian Tata Usaha.
          </div>

        </div>
      </article>

    </div>
  </main>



<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
