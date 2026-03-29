<?php
// FILE: backend/includes/footer.php
// Footer HTML untuk halaman publik

// Default cssBase if not set (for pages in subfolder)
if (!isset($cssBase)) {
    $cssBase = '../';
}

// Get BASE_URL from config if available
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

// Get kontak info if not already set
if (!isset($kontak)) {
    require_once __DIR__ . '/../includes/helpers.php';
    $kontak = getKontak();
}
?>
<footer>
  <div class="footer-main">
    <div class="footer-col footer-col-brand">
      <div class="footer-brand-logo">
        <div class="footer-logo-circle">
          <img src="<?= $baseUrl ?>/images/logo.png" alt="Logo Rutan Kelas IIA Batam"/>
        </div>
        <div class="footer-brand-name">
          Rutan Kelas IIA Batam
          <span>Kementerian Imigrasi dan Pemasyarakatan</span>
        </div>
      </div>
      <p class="footer-about">Rumah Tahanan Negara Kelas IIA Batam berkomitmen untuk memberikan pelayanan pemasyarakatan yang transparan, akuntabel, dan profesional.</p>
      <div class="footer-socials">
        <a href="https://www.instagram.com/rutan.batam/" class="social-btn" title="Instagram">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
        </a>
        <a href="https://www.facebook.com/rutanbatam" class="social-btn" title="Facebook">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </a>
        <a href="https://www.youtube.com/@RutanBatam" class="social-btn" title="YouTube">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>
        </a>
      </div>
    </div>
    <div class="footer-col">
      <div class="footer-col-title">Tentang</div>
      <div class="footer-links">
        <a href="<?= $baseUrl ?>/sejarah">Sejarah Rutan</a>
        <a href="<?= $baseUrl ?>/visi-misi">Visi, Misi &amp; Tujuan</a>
        <a href="<?= $baseUrl ?>/struktur">Struktur Organisasi</a>
        <a href="<?= $baseUrl ?>/tupoksi">Tugas Pokok &amp; Fungsi</a>
        <a href="<?= $baseUrl ?>/pejabat">Profil Pejabat</a>
      </div>
    </div>
    <div class="footer-col">
      <div class="footer-col-title">Informasi</div>
      <div class="footer-links">
        <a href="<?= $baseUrl ?>/kunjungan">Layanan Kunjungan</a>
        <a href="<?= $baseUrl ?>/survey">Survey Kepuasan</a>
        <a href="<?= $baseUrl ?>/komitmen">Komitmen Integritas</a>
        <a href="<?= $baseUrl ?>/berita">Berita</a>
        <a href="<?= $baseUrl ?>/admin/login.php">Login</a>
      </div>
    </div>
    <div class="footer-col">
      <div class="footer-col-title">Kontak</div>
      <div class="footer-contact-items">
        <div class="footer-contact-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span><?= strip_tags($kontak['alamat']['nilai'] ?? 'Jl. Raya Trans Barelang, Batam') ?></span>
        </div>
        <div class="footer-contact-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.16 6.16l1.88-1.88a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2.02z"/></svg>
          <span><?= htmlspecialchars($kontak['telepon']['nilai'] ?? '') ?></span>
        </div>
        <div class="footer-contact-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <span><?= htmlspecialchars($kontak['email']['nilai'] ?? '') ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="footer-bottom-inner">
      <div class="footer-copy">© <?= date('Y') ?> Rutan Kelas IIA Batam. Hak Cipta Dilindungi.</div>
      <div class="footer-kemenimipas">Di bawah naungan <span>Kementerian Imigrasi dan Pemasyarakatan RI</span></div>
    </div>
  </div>
</footer>
<script src="<?= $baseUrl ?>/js/main.js"></script>
<?php if (!empty($extraJs)): foreach ($extraJs as $js): ?>
<script src="<?= $baseUrl ?>/js/<?= $js ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
