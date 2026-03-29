<?php
$halamanAktif = basename($_SERVER['PHP_SELF'], '.php');
if (empty($halamanAktif)) {
    $halamanAktif = basename($_SERVER['PHP_SELF'], '.php');
}

// Determine base path for CSS/JS
// If $cssBase is not set or is empty, use '../' as default (for pages in subfolder)
if (!isset($cssBase) || $cssBase === '') {
    $cssBase = '../';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $pageTitle ?? 'Rutan Kelas IIA Batam' ?></title>
  <link rel="icon" type="image/png" href="<?= $cssBase ?? '../' ?>../images/logo.png"/>
  <link rel="stylesheet" href="<?= $cssBase ?? '../' ?>css/style.css"/>
  <?php if (!empty($extraCss)): foreach ($extraCss as $css): ?>
  <link rel="stylesheet" href="<?= $cssBase ?? '../' ?>css/<?= $css ?>"/>
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

    /* Pulse animation biar menarik perhatian */
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

    /* Mobile: tombol full-width di dalam menu */
    @media (max-width: 768px) {
      .nav-kunjungan-btn {
        width: 100%;
        justify-content: center;
        padding: 11px 18px;
        border-radius: 8px;
        margin-top: 4px;
      }
    }
  </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <div class="topbar-inner">
    <span>Kementerian Imigrasi dan Pemasyarakatan Republik Indonesia</span>
    <div class="topbar-links">
      <a href="mailto:humasrutanbatam@gmail.com">humasrutanbatam@gmail.com</a>
      <a href="tel:+62778393497">+62 778 393 497</a>
    </div>
  </div>
</div>

<!-- NAVBAR DIHAPUS -->