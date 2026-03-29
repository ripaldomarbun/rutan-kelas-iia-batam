/*
  ============================================================
  FILE: js/main.js
  FUNGSI: Menangani interaktivitas website Rutan IIA Batam
  
  JavaScript = "otak" website yang membuat halaman jadi hidup
  - Klik tombol → sesuatu terjadi
  - Scroll halaman → animasi muncul
  - dll.
  ============================================================
*/

document.addEventListener('DOMContentLoaded', function() {

/* ============================================================
   FITUR SLIDER: Image Slider Otomatis
   
   LOGIKA DASAR:
   - Kita simpan nomor slide aktif di variabel: slideAktif
   - Fungsi pindahSlide(n) → geser track ke slide ke-n
   - Tombol prev/next → kurangi/tambah slideAktif
   - setInterval → panggil pindahSlide otomatis tiap 5 detik
   ============================================================ */

// ── 1. Ambil semua elemen yang dibutuhkan ──
const sliderTrack = document.getElementById('sliderTrack');
const sliderPrev  = document.getElementById('sliderPrev');
const sliderNext  = document.getElementById('sliderNext');
const semuaDot    = document.querySelectorAll('.dot');
const semuaSlide  = document.querySelectorAll('.slide');

// Only initialize slider if ALL required elements exist
if (sliderTrack && sliderPrev && sliderNext && semuaSlide.length > 0 && semuaDot.length > 0) {

  // ── 2. Variabel state slider ──
  let slideAktif   = 0;
  const totalSlide = semuaSlide.length;
  let timerOtomatis;

  /*
    ── 3. FUNGSI UTAMA: pindahSlide(nomor) ──
    Dipanggil setiap kali slide harus berganti.
  */
  function pindahSlide(nomor) {
    if (!sliderTrack) return;
    slideAktif = nomor;
    sliderTrack.style.transform = 'translateX(-' + (slideAktif * 100) + '%)';

    semuaSlide.forEach(function(slide) {
      if (slide) slide.classList.toggle('active', slide === semuaSlide[slideAktif]);
    });

    semuaDot.forEach(function(dot, index) {
      if (dot) dot.classList.toggle('active', index === slideAktif);
    });
  }

  // ── 4. Pindah ke slide berikutnya (loop) ──
  function slideBerikutnya() {
    pindahSlide((slideAktif + 1) % totalSlide);
  }

  // ── 5. Pindah ke slide sebelumnya (loop) ──
  function slideSebelumnya() {
    pindahSlide((slideAktif - 1 + totalSlide) % totalSlide);
  }

  // ── 6. Timer otomatis ──
  function mulaiTimer() {
    timerOtomatis = setInterval(slideBerikutnya, 5000);
  }

  function resetTimer() {
    clearInterval(timerOtomatis);
    mulaiTimer();
  }

  // ── 7. Event listener tombol navigasi ──
  if (sliderNext) sliderNext.addEventListener('click', function() { slideBerikutnya(); resetTimer(); });
  if (sliderPrev) sliderPrev.addEventListener('click', function() { slideSebelumnya(); resetTimer(); });

  // ── 8. Event listener dots ──
  if (semuaDot) {
    semuaDot.forEach(function(dot) {
      if (dot) {
        dot.addEventListener('click', function() {
          pindahSlide(parseInt(dot.dataset.index));
          resetTimer();
        });
      }
    });
  }

  // ── 9. Swipe mobile ──
  if (sliderTrack) {
    let posisiAwalSentuh = 0;

    sliderTrack.addEventListener('touchstart', function(e) {
      posisiAwalSentuh = e.touches[0].clientX;
    });

    sliderTrack.addEventListener('touchend', function(e) {
      const selisih = posisiAwalSentuh - e.changedTouches[0].clientX;
      if (selisih > 50)  { slideBerikutnya(); resetTimer(); }
      if (selisih < -50) { slideSebelumnya(); resetTimer(); }
    });
  }

  // ── 10. Jalankan slider saat halaman dimuat ──
  pindahSlide(0);
  mulaiTimer();
}


/* ============================================================
   FITUR 2: SCROLL REVEAL ANIMATION
   Elemen muncul dengan animasi saat di-scroll ke elemen tersebut
   
   Caranya menggunakan IntersectionObserver:
   - Browser mengamati elemen tertentu
   - Saat elemen masuk ke area layar → jalankan animasi
   ============================================================ */

// Buat observer (pengamat)
const scrollObserver = new IntersectionObserver(function(entries) {
  
  // entries = daftar elemen yang sedang diamati
  entries.forEach(function(entry) {
    
    // Jika elemen sudah masuk ke area layar...
    if (entry.isIntersecting) {
      // ...tampilkan dengan animasi
      entry.target.style.opacity   = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });

}, {
  threshold: 0.1  // Aktif saat 10% elemen terlihat di layar
});

// Daftar elemen yang akan diamati
const elemAnimasi = document.querySelectorAll(
  '.berita-card, .info-card, .tentang-feature, .kontak-item'
);

// Set state awal: sembunyikan semua elemen (nanti dimunculkan saat scroll)
elemAnimasi.forEach(function(el) {
  el.style.opacity    = '0';
  el.style.transform  = 'translateY(20px)';
  el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
  
  // Mulai amati elemen ini
  scrollObserver.observe(el);
});


/* ============================================================
   FITUR 3: AKTIFKAN LINK NAVBAR BERDASARKAN POSISI SCROLL
   Link menu akan menjadi "aktif" (berubah warna)
   sesuai section yang sedang terlihat di layar
   ============================================================ */

window.addEventListener('scroll', function() {
  
  // Ambil semua section yang punya id
  const semuaSection = document.querySelectorAll('section[id]');
  const semuaNavLink = document.querySelectorAll('.nav-links a');
  
  let sectionAktif = '';
  
  // Cek setiap section: apakah posisi scroll sudah melewatinya?
  semuaSection.forEach(function(section) {
    const posisiSection = section.offsetTop;  // Jarak dari atas halaman
    const scrollSaatIni = window.scrollY;     // Posisi scroll saat ini
    
    if (scrollSaatIni >= posisiSection - 100) {
      sectionAktif = section.id;
      // -100 = agar link aktif sedikit sebelum section benar-benar terlihat
    }
  });
  
  // Update class 'active' di semua link navbar
  semuaNavLink.forEach(function(link) {
    link.classList.remove('active'); // Hapus dari semua
    
    // Tambahkan ke link yang sesuai dengan section aktif
    if (link.getAttribute('href') === '#' + sectionAktif) {
      link.classList.add('active');
    }
  });
});


/* ============================================================
   FITUR 4: TAHUN OTOMATIS DI FOOTER
   Supaya copyright tahunnya selalu up-to-date otomatis
   ============================================================ */

// Cari elemen dengan class footer-copy
const elCopyright = document.querySelector('.footer-copy');

if (elCopyright) {
  const tahunSekarang = new Date().getFullYear(); // Ambil tahun saat ini
  elCopyright.textContent = '© ' + tahunSekarang + ' Rutan Kelas IIA Batam. Hak Cipta Dilindungi.';
}

}); // End of DOMContentLoaded
