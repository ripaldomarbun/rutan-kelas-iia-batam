/*
  FILE: js/survey.js
  Logika filter tahun untuk halaman Survey Kepuasan

  CARA KERJA:
  - Klik tombol tahun (2026 / 2025 / 2024)
  - Sembunyikan semua panel tahun
  - Tampilkan panel yang sesuai dengan data-year
*/

document.addEventListener('DOMContentLoaded', function () {

  const filterBtns = document.querySelectorAll('.filter-btn');
  const yearPanels = document.querySelectorAll('.survey-year-panel');

  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {

      // Hapus active dari semua tombol & panel
      filterBtns.forEach(function (b) { b.classList.remove('active'); });
      yearPanels.forEach(function (p) { p.classList.remove('active'); });

      // Aktifkan tombol yang diklik
      btn.classList.add('active');

      // Tampilkan panel tahun yang sesuai
      // data-year="2026" → id="year-2026"
      const targetPanel = document.getElementById('year-' + btn.getAttribute('data-year'));
      if (targetPanel) targetPanel.classList.add('active');

    });
  });

});
