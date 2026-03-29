<?php
require_once __DIR__ . '/../backend/config/config.php';
$pageTitle    = 'Tupoksi – Rutan Kelas IIA Batam';
$halamanAktif = 'tupoksi';

$extraCss     = ['struktur.css','sejarah.css', 'visi-misi.css','tupoksi.css'];
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
        <span class="breadcrumb-active">Tugas Pokok &amp; Fungsi</span>
      </div>
      <h1 class="page-title">Tugas Pokok<br/><span>&amp; Fungsi</span></h1>
      <p class="page-subtitle">Uraian tugas dan fungsi setiap unit kerja Rutan Kelas IIA Batam</p>
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

          <p class="tupoksi-intro">
            Berdasarkan peraturan perundang-undangan yang berlaku, setiap unit kerja di Rutan Kelas IIA Batam
            memiliki tugas pokok dan fungsi masing-masing sebagai berikut:
          </p>

          <!-- Tab navigasi unit kerja -->
          <div class="tupoksi-tabs" id="tupoksiTabs">
            <button class="tab-btn active" data-target="karutan">Kepala Rutan</button>
            <button class="tab-btn" data-target="tata-usaha">Tata Usaha</button>
            <button class="tab-btn" data-target="pengelolaan">Kasubsi Pengelolaan</button>
            <button class="tab-btn" data-target="kpr">Kasubsi KPR</button>
            <button class="tab-btn" data-target="peltah">Kasubsi Peltah</button>
            <button class="tab-btn" data-target="bimgiat">Kasubsi Bimgiat</button>
          </div>

          <!-- ── Panel: Kepala Rutan ── -->
          <div class="tupoksi-panel active" id="panel-karutan">
            <div class="panel-header">
              <div class="panel-icon">🏛️</div>
              <div>
                <div class="panel-unit">Kepala Rumah Tahanan Negara</div>
                <div class="panel-singkatan">Karutan</div>
              </div>
            </div>

            <div class="panel-tugas">
              <div class="panel-section-title">Tugas Pokok</div>
              <p>Kepala Rutan mempunyai tugas pokok memimpin, membina, mengawasi, mengendalikan, dan mengkoordinasikan pelaksanaan tugas di lingkungan Rumah Tahanan Negara Kelas IIA Batam sesuai dengan peraturan perundang-undangan yang berlaku.</p>
            </div>

            <div class="panel-fungsi">
              <div class="panel-section-title">Fungsi</div>
              <ul class="fungsi-list">
                <li>
                  <div class="fungsi-num">1</div>
                  <span>Penetapan kebijakan teknis pelaksanaan tugas di bidang pemasyarakatan sesuai dengan peraturan perundang-undangan.</span>
                </li>
                <li>
                  <div class="fungsi-num">2</div>
                  <span>Pembinaan, pengawasan, dan pengendalian seluruh pelaksanaan tugas di lingkungan Rutan Kelas IIA Batam.</span>
                </li>
                <li>
                  <div class="fungsi-num">3</div>
                  <span>Pengelolaan dan pemanfaatan sumber daya manusia, sarana, dan prasarana secara efektif dan efisien.</span>
                </li>
                <li>
                  <div class="fungsi-num">4</div>
                  <span>Koordinasi dengan instansi terkait dalam rangka pelaksanaan tugas pemasyarakatan di wilayah Kota Batam.</span>
                </li>
                <li>
                  <div class="fungsi-num">5</div>
                  <span>Pelaksanaan evaluasi dan pelaporan kinerja secara berkala kepada Kantor Wilayah Kementerian Imigrasi dan Pemasyarakatan.</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- ── Panel: Tata Usaha ── -->
          <div class="tupoksi-panel" id="panel-tata-usaha">
            <div class="panel-header">
              <div class="panel-icon">📋</div>
              <div>
                <div class="panel-unit">Sub Bagian Tata Usaha</div>
                <div class="panel-singkatan">Tata Usaha</div>
              </div>
            </div>

            <div class="panel-tugas">
              <div class="panel-section-title">Tugas Pokok</div>
              <p>Sub Bagian Tata Usaha mempunyai tugas melakukan urusan tata usaha dan rumah tangga Rumah Tahanan Negara Kelas IIA Batam, meliputi urusan kepegawaian, keuangan, perlengkapan, dan administrasi umum.</p>
            </div>

            <div class="panel-fungsi">
              <div class="panel-section-title">Fungsi</div>
              <ul class="fungsi-list">
                <li>
                  <div class="fungsi-num">1</div>
                  <span>Pelaksanaan urusan surat menyurat, kearsipan, dokumentasi, dan tata persuratan dinas.</span>
                </li>
                <li>
                  <div class="fungsi-num">2</div>
                  <span>Pengelolaan administrasi kepegawaian meliputi kenaikan pangkat, mutasi, pensiun, dan kesejahteraan pegawai.</span>
                </li>
                <li>
                  <div class="fungsi-num">3</div>
                  <span>Pengelolaan keuangan, penyusunan anggaran, pelaksanaan pembayaran, dan pelaporan keuangan.</span>
                </li>
                <li>
                  <div class="fungsi-num">4</div>
                  <span>Pengelolaan barang milik negara, inventarisasi aset, dan pemeliharaan sarana prasarana.</span>
                </li>
                <li>
                  <div class="fungsi-num">5</div>
                  <span>Penyelenggaraan urusan rumah tangga, kebersihan, dan keprotokolan kegiatan dinas.</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- ── Panel: Kasubsi Pengelolaan ── -->
          <div class="tupoksi-panel" id="panel-pengelolaan">
            <div class="panel-header">
              <div class="panel-icon">🔧</div>
              <div>
                <div class="panel-unit">Sub Seksi Pengelolaan</div>
                <div class="panel-singkatan">Kasubsi Pengelolaan</div>
              </div>
            </div>

            <div class="panel-tugas">
              <div class="panel-section-title">Tugas Pokok</div>
              <p>Sub Seksi Pengelolaan mempunyai tugas melakukan pengelolaan dan perawatan bangunan, perlengkapan, dan fasilitas Rutan serta pengelolaan kebutuhan sehari-hari warga binaan pemasyarakatan.</p>
            </div>

            <div class="panel-fungsi">
              <div class="panel-section-title">Fungsi</div>
              <ul class="fungsi-list">
                <li>
                  <div class="fungsi-num">1</div>
                  <span>Pengelolaan dan pemeliharaan bangunan, instalasi listrik, air, dan fasilitas fisik Rutan.</span>
                </li>
                <li>
                  <div class="fungsi-num">2</div>
                  <span>Penyediaan, pendistribusian, dan pengelolaan kebutuhan logistik warga binaan.</span>
                </li>
                <li>
                  <div class="fungsi-num">3</div>
                  <span>Pengelolaan perlengkapan dan peralatan operasional Rutan.</span>
                </li>
                <li>
                  <div class="fungsi-num">4</div>
                  <span>Pelaksanaan inventarisasi dan pelaporan kondisi sarana dan prasarana secara berkala.</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- ── Panel: Kasubsi KPR ── -->
          <div class="tupoksi-panel" id="panel-kpr">
            <div class="panel-header">
              <div class="panel-icon">🔒</div>
              <div>
                <div class="panel-unit">Sub Seksi Keamanan dan Perawatan</div>
                <div class="panel-singkatan">Kasubsi KPR</div>
              </div>
            </div>

            <div class="panel-tugas">
              <div class="panel-section-title">Tugas Pokok</div>
              <p>Sub Seksi KPR mempunyai tugas melakukan pemeliharaan keamanan dan ketertiban, serta perawatan tahanan di lingkungan Rumah Tahanan Negara Kelas IIA Batam.</p>
            </div>

            <div class="panel-fungsi">
              <div class="panel-section-title">Fungsi</div>
              <ul class="fungsi-list">
                <li>
                  <div class="fungsi-num">1</div>
                  <span>Pelaksanaan pengamanan dan pemeliharaan ketertiban di lingkungan Rutan secara berkelanjutan.</span>
                </li>
                <li>
                  <div class="fungsi-num">2</div>
                  <span>Pengaturan jadwal jaga, patroli, dan pengawasan terhadap penghuni Rutan.</span>
                </li>
                <li>
                  <div class="fungsi-num">3</div>
                  <span>Pelaksanaan penggeledahan, razia, dan tindakan pencegahan gangguan keamanan.</span>
                </li>
                <li>
                  <div class="fungsi-num">4</div>
                  <span>Perawatan kesehatan dan pengawasan kondisi fisik tahanan/warga binaan.</span>
                </li>
                <li>
                  <div class="fungsi-num">5</div>
                  <span>Penanganan situasi darurat dan koordinasi dengan instansi terkait apabila terjadi gangguan keamanan.</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- ── Panel: Kasubsi Peltah ── -->
          <div class="tupoksi-panel" id="panel-peltah">
            <div class="panel-header">
              <div class="panel-icon">📁</div>
              <div>
                <div class="panel-unit">Sub Seksi Pelayanan Tahanan</div>
                <div class="panel-singkatan">Kasubsi Peltah</div>
              </div>
            </div>

            <div class="panel-tugas">
              <div class="panel-section-title">Tugas Pokok</div>
              <p>Sub Seksi Pelayanan Tahanan mempunyai tugas melakukan pelayanan administrasi dan hak-hak tahanan, mulai dari penerimaan, pendataan, penempatan, hingga pengeluaran tahanan.</p>
            </div>

            <div class="panel-fungsi">
              <div class="panel-section-title">Fungsi</div>
              <ul class="fungsi-list">
                <li>
                  <div class="fungsi-num">1</div>
                  <span>Penerimaan, pendaftaran, dan penempatan tahanan baru sesuai prosedur yang berlaku.</span>
                </li>
                <li>
                  <div class="fungsi-num">2</div>
                  <span>Pengelolaan data dan administrasi tahanan secara akurat dan mutakhir.</span>
                </li>
                <li>
                  <div class="fungsi-num">3</div>
                  <span>Pelayanan hak-hak tahanan meliputi kunjungan keluarga, bantuan hukum, dan kebutuhan rohani.</span>
                </li>
                <li>
                  <div class="fungsi-num">4</div>
                  <span>Pengurusan penangguhan penahanan, pembebasan, dan proses administrasi lainnya.</span>
                </li>
                <li>
                  <div class="fungsi-num">5</div>
                  <span>Koordinasi dengan instansi penegak hukum terkait status dan proses hukum tahanan.</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- ── Panel: Kasubsi Bimgiat ── -->
          <div class="tupoksi-panel" id="panel-bimgiat">
            <div class="panel-header">
              <div class="panel-icon">🌱</div>
              <div>
                <div class="panel-unit">Sub Seksi Bimbingan Kegiatan</div>
                <div class="panel-singkatan">Kasubsi Bimgiat</div>
              </div>
            </div>

            <div class="panel-tugas">
              <div class="panel-section-title">Tugas Pokok</div>
              <p>Sub Seksi Bimbingan Kegiatan mempunyai tugas melakukan bimbingan kegiatan kerja, pendidikan, dan latihan kerja bagi warga binaan pemasyarakatan dalam rangka reintegrasi sosial.</p>
            </div>

            <div class="panel-fungsi">
              <div class="panel-section-title">Fungsi</div>
              <ul class="fungsi-list">
                <li>
                  <div class="fungsi-num">1</div>
                  <span>Perencanaan dan pelaksanaan program pembinaan kepribadian dan kemandirian warga binaan.</span>
                </li>
                <li>
                  <div class="fungsi-num">2</div>
                  <span>Penyelenggaraan kegiatan kerja, keterampilan, dan pelatihan vokasional bagi warga binaan.</span>
                </li>
                <li>
                  <div class="fungsi-num">3</div>
                  <span>Pelaksanaan program pendidikan formal dan nonformal untuk warga binaan.</span>
                </li>
                <li>
                  <div class="fungsi-num">4</div>
                  <span>Pembinaan spiritual, kerohanian, dan kegiatan sosial kemasyarakatan.</span>
                </li>
                <li>
                  <div class="fungsi-num">5</div>
                  <span>Persiapan program reintegrasi sosial untuk warga binaan yang akan kembali ke masyarakat.</span>
                </li>
              </ul>
            </div>
          </div>

        </div>
      </article>

    </div>
  </main>


  <script src="<?= BASE_URL ?>/js/tupoksi.js"></script>
  


<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
