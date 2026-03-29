<?php
// header.php - Include file untuk navbar semua halaman (kecuali admin)
// Path base: gunakan '' untuk index.php, '../' untuk halaman di pages/

if (!isset($cssBase)) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    $cssBase = ($currentPage === 'index.php') ? '' : '../';
}

// Get BASE_URL for absolute paths
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/backend/config/config.php';
}

// Get kontak info if not already set
if (!isset($kontak)) {
    require_once __DIR__ . '/backend/includes/helpers.php';
    $kontak = getKontak();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $pageTitle ?? 'Rutan Kelas IIA Batam' ?></title>
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>/images/logo.png"/>
  <meta name="description" content="Rutan Kelas IIA Batam - Lembaga Pemasyarakat Menteri Imigrasi dan Pemasyarakatan Republik Indonesia. Informasi layanan kunjungan, berita terkini, dan perkembangan kegiatan Rutan Kelas IIA Batam."/>
  <meta name="keywords" content="rutan batam, rumah tahanan batam, lapas batam, pemasyarakatan,imigrasi dan pemasyarakatan,visitasi narapidana,kunjungan penjara,kementerian imigrasi kepulauan riau"/>
  <meta name="author" content="Rutan Kelas IIA Batam"/>
  <meta name="robots" content="index, follow"/>
  <meta name="googlebot" content="index, follow"/>
  <meta name="theme-color" content="#112240"/>
  <link rel="canonical" href="<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>"/>

  <!-- Open Graph -->
  <meta property="og:type" content="website"/>
  <meta property="og:title" content="<?= $pageTitle ?? 'Rutan Kelas IIA Batam' ?>"/>
  <meta property="og:description" content="Rutan Kelas IIA Batista - Lembaga Pemasyarakat Kementerian Imigrasi dan Pemasyarakatan RIPematang Tinggi"/>
  <meta property="og:url" content="<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>"/>
  <meta property="og:site_name" content="Rutan Kelas IIA Batista"/>
  <meta property="og:image" content="<?= $cssBase ?? '' ?>images/logo.png"/>
  <meta property="og:locale" content="id_ID"/>

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="<?= $pageTitle ?? 'Rutan Kelas IIA Batista' ?>"/>
  <meta name="twitter:description" content="Rutan Kelas IIA Batista - Lembaga Pemasyarakat Kementerian Imigrasi dan Pemasyarakatan RIPematang Tinggi"/>
  <meta name="twitter:image" content="<?= $cssBase ?? '' ?>images/logo.png"/>

  <!-- Content Security Policy -->
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com data:; img-src 'self' data: https: blob:; connect-src 'self' https:; frame-src https:;">
  <meta http-equiv="X-Content-Type-Options" content="nosniff"/>

  <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin"/>

  <!-- Canonical URL -->
  <link rel="canonical" href="<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>"/>

  <!-- Preconnect untuk performa fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link rel="dns-prefetch" href="https://fonts.googleapis.com"/>
  <link rel="dns-prefetch" href="https://fonts.gstatic.com"/>

  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css"/>
  <?php if (!empty($extraCss)): foreach ($extraCss as $css): ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/<?= $css ?>"/>
  <?php endforeach; endif; ?>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    /* ── TOMBOL DAFTAR KUNJUNGAN DI NAVBAR ── */
    .nav-kunjungan-btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      background: linear-gradient(135deg, #16a34a, #15803d);
      color: #fff !important;
      font-weight: 600;
      font-size: 13.5px;
      padding: 9px 18px;
      border-radius: 8px;
      text-decoration: none !important;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 2px 8px rgba(22, 163, 74, 0.35);
      white-space: nowrap;
      letter-spacing: 0.01em;
    }

    .nav-kunjungan-btn svg {
      flex-shrink: 0;
      transition: transform 0.2s ease;
    }

    .nav-kunjungan-btn:hover {
      background: linear-gradient(135deg, #15803d, #166534);
      transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(22, 163, 74, 0.45);
      color: #fff !important;
    }

    .nav-kunjungan-btn:active {
      transform: translateY(0);
      box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);
    }

    /* Pulse animation */
    @keyframes kunjungan-pulse {
      0%, 100% { box-shadow: 0 2px 8px rgba(22, 163, 74, 0.35); }
      50%       { box-shadow: 0 2px 18px rgba(22, 163, 74, 0.6); }
    }

    .nav-kunjungan-btn {
      animation: kunjungan-pulse 2.5s ease-in-out infinite;
    }

    .nav-kunjungan-btn:hover {
      animation: none;
    }

    /* Mobile: tombol full-width */
    @media (max-width: 768px) {
      .nav-kunjungan-btn {
        width: 100%;
        justify-content: center;
        padding: 11px 18px;
        margin-top: 4px;
      }
    }

    /* Search Button */
    .nav-search-btn {
      background: transparent;
      border: none;
      color: #fff;
      cursor: pointer;
      padding: 8px;
      border-radius: 6px;
      transition: background 0.2s;
      display: flex;
      align-items: center;
    }
    .nav-search-btn:hover { background: rgba(255,255,255,0.1); }

    /* Search Modal */
    .search-modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.8);
      z-index: 9999;
      padding: 2rem;
      overflow-y: auto;
    }
    .search-modal.active { display: flex; align-items: flex-start; justify-content: center; padding-top: 10vh; }
    .search-box {
      background: #fff;
      border-radius: 12px;
      width: 100%;
      max-width: 600px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .search-input-wrap {
      display: flex;
      align-items: center;
      padding: 1rem;
      border-bottom: 1px solid #eee;
    }
    .search-input-wrap svg { width: 24px; height: 24px; color: #666; flex-shrink: 0; }
    .search-input {
      flex: 1;
      border: none;
      font-size: 1.1rem;
      padding: 0.5rem 1rem;
      outline: none;
      font-family: inherit;
    }
    .search-close {
      background: transparent;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: #666;
      padding: 0.5rem;
    }
    .search-results { max-height: 60vh; overflow-y: auto; }
    .search-result-item {
      display: flex;
      gap: 1rem;
      padding: 1rem;
      text-decoration: none;
      color: inherit;
      border-bottom: 1px solid #f5f5f5;
      transition: background 0.2s;
    }
    .search-result-item:hover { background: #f9f9f9; }
    .search-result-img {
      width: 80px;
      height: 60px;
      object-fit: cover;
      border-radius: 6px;
      background: #eee;
    }
    .search-result-info { flex: 1; min-width: 0; }
    .search-result-judul { font-weight: 600; color: #1a1a2e; margin-bottom: 4px; }
    .search-result-date { font-size: 0.8rem; color: #888; }
    .search-no-result { padding: 2rem; text-align: center; color: #888; }

    /* ── CHATBOT WIDGET ── */
    .chatbot-widget {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 9998;
    }
    .chatbot-toggle {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1e3a5f, #112240);
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .chatbot-toggle:hover {
      transform: scale(1.08);
      box-shadow: 0 6px 28px rgba(0,0,0,0.4);
    }
    .chatbot-toggle svg { width: 28px; height: 28px; fill: #fff; }
    .chatbot-toggle .close-icon { display: none; }
    .chatbot-toggle.open .chat-icon { display: none; }
    .chatbot-toggle.open .close-icon { display: block; }
    
    .chatbot-modal {
      position: absolute;
      bottom: 76px;
      right: 0;
      width: 380px;
      max-width: calc(100vw - 48px);
      max-height: 520px;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 10px 50px rgba(0,0,0,0.25);
      display: none;
      flex-direction: column;
      overflow: hidden;
    }
    .chatbot-modal.open { display: flex; }
    .chatbot-header {
      background: linear-gradient(135deg, #1e3a5f, #112240);
      color: #fff;
      padding: 1rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .chatbot-header svg { width: 28px; height: 28px; fill: #fff; }
    .chatbot-header h3 { margin: 0; font-size: 1rem; font-weight: 600; }
    .chatbot-header p { margin: 0; font-size: 0.75rem; opacity: 0.85; }
    
    .chatbot-search {
      padding: 0.75rem;
      border-bottom: 1px solid #eee;
    }
    .chatbot-search input {
      width: 100%;
      padding: 0.6rem 0.9rem;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 0.9rem;
      outline: none;
      box-sizing: border-box;
    }
    .chatbot-search input:focus { border-color: #1e3a5f; }
    
    .chatbot-body {
      flex: 1;
      overflow-y: auto;
      padding: 0.5rem;
    }
    .chatbot-empty {
      padding: 2rem;
      text-align: center;
      color: #888;
      font-size: 0.9rem;
    }
    .chatbot-item {
      padding: 0.75rem;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.2s;
      margin-bottom: 0.25rem;
    }
    .chatbot-item:hover { background: #f5f7fa; }
    .chatbot-item.pertanyaan {
      font-weight: 600;
      color: #1e3a5f;
      font-size: 0.9rem;
    }
    .chatbot-item.jawaban {
      color: #555;
      font-size: 0.85rem;
      line-height: 1.5;
    }
    .chatbot-item.jawaban p { margin: 0; }
    
    @media (max-width: 480px) {
      .chatbot-widget { bottom: 16px; right: 16px; }
      .chatbot-toggle { width: 54px; height: 54px; }
      .chatbot-modal { width: calc(100vw - 32px); bottom: 70px; }
    }
  </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <div class="topbar-inner">
    <span>Kementerian Imigrasi dan Pemasyarakatan Republik Indonesia</span>
    <div class="topbar-links">
      <a href="mailto:<?= htmlspecialchars($kontak['email']['nilai'] ?? '') ?>"><?= htmlspecialchars($kontak['email']['nilai'] ?? '') ?></a>
      <a href="tel:<?= preg_replace('/[^0-9+]/', '', $kontak['telepon']['nilai'] ?? '') ?>"><?= htmlspecialchars($kontak['telepon']['nilai'] ?? '') ?></a>
    </div>
  </div>
</div>

<!-- NAVBAR -->
<nav>
  <div class="nav-inner">
    <a href="<?= BASE_URL ?>/index.php" class="nav-brand">
      <div class="nav-logo-circle">
        <img src="<?= BASE_URL ?>/images/logo.png" alt="Logo Rutan Kelas IIA Batista - Lembaga Pemasyarakatan Kementerian Imigrasi dan Pemasyarakatan"/>
      </div>
      <div class="nav-brand-text">
        <span class="main">Rutan Kelas IIA Batam</span>
        <span class="sub">Kementerian Imigrasi dan Pemasyarakatan</span>
      </div>
    </a>

    <div class="nav-links" id="navLinks">
      <div class="dropdown">
        <a href="#">Tentang <span class="nav-caret">▾</span></a>
        <div class="dropdown-menu">
          <a href="<?= BASE_URL ?>/sejarah">Sejarah Rutan</a>
          <a href="<?= BASE_URL ?>/visi-misi">Visi, Misi &amp; Tujuan</a>
          <a href="<?= BASE_URL ?>/struktur">Struktur Organisasi</a>
          <a href="<?= BASE_URL ?>/tupoksi">Tugas Pokok &amp; Fungsi</a>
          <a href="<?= BASE_URL ?>/pejabat">Profil Pejabat</a>
        </div>
      </div>

      <div class="dropdown">
        <a href="#">Informasi Publik <span class="nav-caret">▾</span></a>
        <div class="dropdown-menu">
          <a href="<?= BASE_URL ?>/kunjungan">Layanan Kunjungan</a>
          <a href="<?= BASE_URL ?>/survey">Survey Kepuasan Masyarakat</a>
          <a href="<?= BASE_URL ?>/komitmen">Komitmen</a>
        </div>
      </div>

      <a href="<?= BASE_URL ?>/berita">Berita</a>
      <a href="<?= BASE_URL ?>/index.php#kontak">Kontak</a>
      
      <!-- SEARCH ICON -->
      <button class="nav-search-btn" onclick="bukaSearchModal()" title="Cari">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </button>
      
      <!-- TOMBOL DAFTAR KUNJUNGAN ONLINE -->
      <a href="https://forms.gle/euo3Yx94T4jp5GYXA" 
         class="nav-kunjungan-btn" 
         target="_blank" 
         rel="noopener noreferrer">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16">
          <rect x="3" y="4" width="18" height="16" rx="2"/>
          <path d="M8 2v4M16 2v4M3 10h18"/>
        </svg>
        Daftar Kunjungan Online
      </a>
    </div>

    <button class="hamburger" id="hamburgerBtn" aria-label="Buka menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<script>
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
  var hamburgerBtn = document.getElementById('hamburgerBtn');
  var navLinks = document.getElementById('navLinks');
  var menuTerbuka = false;

  if (hamburgerBtn && navLinks) {
    hamburgerBtn.addEventListener('click', function() {
      if (menuTerbuka) {
        navLinks.style.display = 'none';
        hamburgerBtn.classList.remove('active');
        menuTerbuka = false;
      } else {
        navLinks.style.display = 'flex';
        navLinks.style.flexDirection = 'column';
        navLinks.style.position = 'absolute';
        navLinks.style.top = '72px';
        navLinks.style.left = '0';
        navLinks.style.right = '0';
        navLinks.style.background = '#112240';
        navLinks.style.padding = '1rem';
        navLinks.style.borderTop = '1px solid rgba(201,168,76,0.25)';
        navLinks.style.zIndex = '999';
        hamburgerBtn.classList.add('active');
        menuTerbuka = true;
      }
    });

    // Reset on resize
    window.addEventListener('resize', function() {
      if (window.innerWidth > 640) {
        navLinks.style.cssText = '';
        hamburgerBtn.classList.remove('active');
        menuTerbuka = false;
      }
    });

    // Mobile dropdown toggle
    var dropdowns = navLinks.querySelectorAll('.dropdown > a');
    dropdowns.forEach(function(link) {
      link.addEventListener('click', function(e) {
        if (window.innerWidth <= 640) {
          e.preventDefault();
          var parent = this.parentElement;
          var isOpen = parent.classList.contains('open');
          navLinks.querySelectorAll('.dropdown').forEach(function(dd) {
            dd.classList.remove('open');
          });
          if (!isOpen) {
            parent.classList.add('open');
          }
        }
      });
    });

    // Close menu on link click (mobile)
    navLinks.querySelectorAll('a').forEach(function(link) {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 640 && !this.parentElement.classList.contains('dropdown')) {
          navLinks.style.display = 'none';
          hamburgerBtn.classList.remove('active');
          menuTerbuka = false;
        }
      });
    });
  }
});

// Search Modal
function bukaSearchModal() {
  const modal = document.getElementById('searchModal');
  if (modal) modal.classList.add('active');
  const input = document.getElementById('searchInput');
  if (input) input.focus();
}

function tutupSearchModal() {
  const modal = document.getElementById('searchModal');
  if (modal) modal.classList.remove('active');
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const modal = document.getElementById('searchModal');
    if (modal && modal.classList.contains('active')) {
      modal.classList.remove('active');
    }
  }
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    const modal = document.getElementById('searchModal');
    if (modal) {
      modal.classList.add('active');
      const input = document.getElementById('searchInput');
      if (input) input.focus();
    }
  }
});

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
  let searchTimeout;
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      clearTimeout(searchTimeout);
      const q = e.target.value.trim();
      const resultsDiv = document.getElementById('searchResults');
      
      if (q.length < 2) {
        resultsDiv.innerHTML = '<div class="search-no-result">Ketik minimal 2 karakter untuk mencari...</div>';
        return;
      }
      
      resultsDiv.innerHTML = '<div class="search-no-result">Mencari...</div>';
      
      searchTimeout = setTimeout(function() {
        fetch('<?= BASE_URL ?>/backend/api/search.php?q=' + encodeURIComponent(q))
          .then(r => r.json())
          .then(data => {
            if (data.status === 'success' && data.data && data.data.results && data.data.results.length > 0) {
              resultsDiv.innerHTML = data.data.results.map(b => 
                '<a href="<?= BASE_URL ?>/detail-berita?slug=' + b.slug + '" class="search-result-item">' +
                  '<img src="' + b.gambar_url + '" alt="" class="search-result-img">' +
                  '<div class="search-result-info">' +
                    '<div class="search-result-judul">' + b.judul + '</div>' +
                    '<div class="search-result-date">' + (b.tanggal ? new Date(b.tanggal).toLocaleDateString('id-ID') : '') + '</div>' +
                  '</div>' +
                '</a>'
              ).join('');
            } else {
              resultsDiv.innerHTML = '<div class="search-no-result">Tidak ada hasil untuk "' + q + '"</div>';
            }
          })
          .catch(err => {
            resultsDiv.innerHTML = '<div class="search-no-result">Terjadi kesalahan. Coba lagi.</div>';
          });
      }, 300);
    });
  }
});
</script>

<!-- Search Modal -->
<div class="search-modal" id="searchModal" onclick="if(event.target===this)tutupSearchModal()">
  <div class="search-box">
    <div class="search-input-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
      <input type="text" id="searchInput" class="search-input" placeholder="Cari berita..." autocomplete="off">
      <button class="search-close" onclick="tutupSearchModal()">✕</button>
    </div>
    <div class="search-results" id="searchResults">
      <div class="search-no-result">Ketik minimal 2 karakter untuk mencari...</div>
    </div>
  </div>
</div>

<!-- Chatbot Widget -->
<div class="chatbot-widget">
  <div class="chatbot-modal" id="chatbotModal">
    <div class="chatbot-header">
      <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/></svg>
      <div>
        <h3>FAQ Rutan</h3>
        <p>Pertanyaan yang sering diajukan</p>
      </div>
    </div>
    <div class="chatbot-search">
      <input type="text" id="chatbotSearch" placeholder="Cari pertanyaan..." autocomplete="off">
    </div>
    <div class="chatbot-body" id="chatbotBody">
      <div class="chatbot-empty">Memuat FAQ...</div>
    </div>
  </div>
  <button class="chatbot-toggle" id="chatbotToggle" aria-label="Buka FAQ">
    <svg class="chat-icon" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/></svg>
    <svg class="close-icon" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
  </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const chatbotToggle = document.getElementById('chatbotToggle');
  const chatbotModal = document.getElementById('chatbotModal');
  const chatbotSearch = document.getElementById('chatbotSearch');
  const chatbotBody = document.getElementById('chatbotBody');
  
  let faqData = [];
  
  // Toggle chatbot
  chatbotToggle.addEventListener('click', function() {
    chatbotModal.classList.toggle('open');
    chatbotToggle.classList.toggle('open');
    if (chatbotModal.classList.contains('open') && faqData.length === 0) {
      loadFaq();
    }
  });
  
  // Load FAQ
  function loadFaq() {
    chatbotBody.innerHTML = '<div class="chatbot-empty">Memuat FAQ...</div>';
    fetch('<?= BASE_URL ?>/backend/api/faq.php?action=all')
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success' && res.data) {
          faqData = res.data;
          renderFaq(faqData);
        } else {
          chatbotBody.innerHTML = '<div class="chatbot-empty">Gagal memuat FAQ</div>';
        }
      })
      .catch(() => {
        chatbotBody.innerHTML = '<div class="chatbot-empty">Gagal memuat FAQ</div>';
      });
  }
  
  // Render FAQ
  function renderFaq(data) {
    if (data.length === 0) {
      chatbotBody.innerHTML = '<div class="chatbot-empty">Belum ada FAQ</div>';
      return;
    }
    chatbotBody.innerHTML = data.map(faq => 
      '<div class="chatbot-item pertanyaan">' + faq.pertanyaan + '</div>' +
      '<div class="chatbot-item jawaban"><p>' + faq.jawaban + '</p></div>'
    ).join('');
  }
  
  // Search FAQ
  let searchTimeout;
  chatbotSearch.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const q = e.target.value.toLowerCase().trim();
    searchTimeout = setTimeout(function() {
      if (q === '') {
        renderFaq(faqData);
      } else {
        const filtered = faqData.filter(f => 
          f.pertanyaan.toLowerCase().includes(q) || 
          f.jawaban.toLowerCase().includes(q)
        );
        renderFaq(filtered);
      }
    }, 200);
  });
  
  // Close on escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && chatbotModal.classList.contains('open')) {
      chatbotModal.classList.remove('open');
      chatbotToggle.classList.remove('open');
    }
  });
});
</script>
