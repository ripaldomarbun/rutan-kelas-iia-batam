<?php
// ============================================================
// FILE: backend/config/mail.php
// Konfigurasi email untuk Rumahweb Hosting
// ============================================================

// Rumahweb SMTP Settings
// smtp.domain.com atau mail.domain.com
// Port: 465 (SSL) atau 587 (TLS)

return [
    // SMTP Host Rumahweb
    // Ganti 'rutanbatam.id' dengan domain Anda
    'host'       => 'mail.rutanbatam.id',
    
    // Port SMTP
    // 465 = SSL (recommended untuk Rumahweb)
    // 587 = TLS
    'port'       => 465,
    
    // Enkripsi
    // 'ssl' untuk port 465
    // 'tls' untuk port 587
    'encryption' => 'ssl',
    
    // Username email dari cPanel Rumahweb
    // Format: email@domain.com
    'username'   => 'admin@rutanbatam.id',
    
    // Password email (password yang dibuat di cPanel)
    'password'   => 'password-email-anda',
    
    // Email pengirim
    'from_email' => 'admin@rutanbatam.id',
    
    // Nama pengirim
    'from_name'  => 'Admin Rutan Batam',
];
