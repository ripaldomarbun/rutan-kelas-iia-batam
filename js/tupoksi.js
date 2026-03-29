/*
  FILE: js/tupoksi.js
  Logika tab switching untuk halaman Tupoksi

  CARA KERJA:
  - Setiap tombol tab punya atribut data-target="nama-panel"
  - Klik tombol → sembunyikan semua panel → tampilkan panel yang sesuai
  - Tambahkan class "active" ke tombol yang diklik
*/

document.addEventListener('DOMContentLoaded', function () {

  // Ambil semua tombol tab
  const tabBtns = document.querySelectorAll('.tab-btn');

  // Ambil semua panel konten
  const panels = document.querySelectorAll('.tupoksi-panel');

  // Pasang event listener ke setiap tombol
  tabBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {

      // 1. Hapus class "active" dari semua tombol
      tabBtns.forEach(function (b) { b.classList.remove('active'); });

      // 2. Hapus class "active" dari semua panel
      panels.forEach(function (p) { p.classList.remove('active'); });

      // 3. Tambahkan "active" ke tombol yang diklik
      btn.classList.add('active');

      // 4. Tampilkan panel yang sesuai
      //    data-target="karutan" → cari id="panel-karutan"
      const targetId = 'panel-' + btn.getAttribute('data-target');
      const targetPanel = document.getElementById(targetId);
      if (targetPanel) {
        targetPanel.classList.add('active');
      }

    });
  });

});
