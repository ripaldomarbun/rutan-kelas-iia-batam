<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Rutan Kelas IIA Batista</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Source Sans 3', sans-serif; background: #0a0f1c; min-height: 100vh; display: flex; align-items: center; justify-content: center; color: #fff; }
        .error-container { text-align: center; padding: 2rem; max-width: 600px; }
        .error-code { font-size: 8rem; font-weight: 900; color: #c8a951; line-height: 1; text-shadow: 0 0 40px rgba(200,169,81,0.4); }
        .error-title { font-size: 2.5rem; font-weight: 700; margin: 1rem 0 0.5rem; color: #fff; }
        .error-desc { font-size: 1.1rem; color: #8899aa; margin-bottom: 2rem; line-height: 1.6; }
        .error-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn { padding: 14px 28px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.2s; font-size: 1rem; }
        .btn-primary { background: linear-gradient(135deg, #c8a951, #a88941); color: #0a0f1c; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,169,81,0.4); }
        .btn-secondary { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); }
        .btn-secondary:hover { background: rgba(255,255,255,0.2); }
        .error-icon { font-size: 4rem; margin-bottom: 1rem; }
        @media (max-width: 600px) {
            .error-code { font-size: 5rem; }
            .error-title { font-size: 1.8rem; }
            .error-actions { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔍</div>
        <div class="error-code">404</div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">
            Maaf, halaman yang Anda cari tidak ditemukan.<br>
            Mungkin halaman telah dipindahkan atau URL yang Anda masukkan salah.
        </p>
        <div class="error-actions">
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">← Kembali ke Beranda</a>
            <a href="<?= BASE_URL ?>/berita" class="btn btn-secondary">Lihat Berita</a>
        </div>
    </div>
</body>
</html>