/*
  FILE: js/berita.js
  Logika filter kategori untuk halaman Berita

  CARA KERJA:
  - Klik tombol kategori → sembunyikan kartu yang tidak sesuai
  - data-kat="kegiatan" → tampilkan hanya <article data-kat="kegiatan">
  - data-kat="semua"    → tampilkan semua
  - Jika tidak ada hasil → tampilkan pesan kosong
*/

document.addEventListener('DOMContentLoaded', function () {

  const katBtns  = document.querySelectorAll('.kat-btn');
  const cards    = document.querySelectorAll('.berita-card');
  const kosong   = document.getElementById('beritaKosong');

  katBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {

      // Update tombol aktif
      katBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');

      const pilihan = btn.getAttribute('data-kat');
      let ada = false;

      cards.forEach(function (card) {
        if (pilihan === 'semua' || card.getAttribute('data-kat') === pilihan) {
          card.classList.remove('hidden');
          ada = true;
        } else {
          card.classList.add('hidden');
        }
      });

      // Tampilkan pesan jika tidak ada berita
      if (kosong) {
        kosong.style.display = ada ? 'none' : 'flex';
      }
    });
  });

  // Tombol "Muat Lebih Banyak" — placeholder untuk nanti dikoneksikan ke backend PHP
  const btnLoadMore = document.getElementById('btnLoadMore');
  if (btnLoadMore) {
    btnLoadMore.addEventListener('click', function () {
      // Nanti diganti dengan fetch/AJAX ke backend PHP
      btnLoadMore.textContent = 'Memuat...';
      setTimeout(function () {
        btnLoadMore.textContent = 'Tidak ada berita lagi';
        btnLoadMore.disabled = true;
        btnLoadMore.style.opacity = '0.4';
        btnLoadMore.style.cursor = 'default';
      }, 800);
    });
  }

});
