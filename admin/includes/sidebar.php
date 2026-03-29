<?php
// FILE: admin/includes/sidebar.php
// Sidebar admin - include di semua halaman admin
// Set $activePage sebelum include ini
?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <div class="sidebar-logo">
      <img src="../images/logo.png" alt="Logo" onerror="this.parentElement.textContent='⚖️'"/>
    </div>
    <div class="sidebar-brand-text">
      <span class="sidebar-brand-main">Admin Panel</span>
      <span class="sidebar-brand-sub">Rutan Kelas IIA Batam</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <span class="sidebar-nav-label">Menu Utama</span>
    <a href="dashboard.php" class="sidebar-nav-item <?= ($activePage??'')==='dashboard' ? 'active':'' ?>">
      <span class="nav-icon">📊</span> Dashboard</a>
    <a href="berita.php" class="sidebar-nav-item <?= ($activePage??'')==='berita' ? 'active':'' ?>">
      <span class="nav-icon">📰</span> Berita</a>
    <a href="slider.php" class="sidebar-nav-item <?= ($activePage??'')==='slider' ? 'active':'' ?>">
      <span class="nav-icon">🖼️</span> Slider Beranda</a>
    <span class="sidebar-nav-label">Informasi Publik</span>
    <a href="survey.php" class="sidebar-nav-item <?= ($activePage??'')==='survey' ? 'active':'' ?>">
      <span class="nav-icon">📊</span> Survey SKM</a>
    <a href="kunjungan.php" class="sidebar-nav-item <?= ($activePage??'')==='kunjungan' ? 'active':'' ?>">
      <span class="nav-icon">📅</span> Jadwal Kunjungan</a>
    <a href="komitmen.php" class="sidebar-nav-item <?= ($activePage??'')==='komitmen' ? 'active':'' ?>">
      <span class="nav-icon">📜</span> Komitmen</a>
    <a href="faq.php" class="sidebar-nav-item <?= ($activePage??'')==='faq' ? 'active':'' ?>">
      <span class="nav-icon">❓</span> FAQ Chatbot</a>
    <span class="sidebar-nav-label">Data Instansi</span>
    <a href="pejabat.php" class="sidebar-nav-item <?= ($activePage??'')==='pejabat' ? 'active':'' ?>">
      <span class="nav-icon">👤</span> Profil Pejabat</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="sidebar-logout">🚪 Keluar</a>
  </div>
</aside>
<div id="sidebarOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:99;"></div>
