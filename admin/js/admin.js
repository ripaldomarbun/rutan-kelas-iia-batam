/*
  FILE: admin/js/admin.js
  JavaScript untuk seluruh halaman Admin Panel
*/

document.addEventListener('DOMContentLoaded', function () {

  /* ══════════════════════════
     SIDEBAR TOGGLE (mobile)
     ══════════════════════════ */
  const sidebar = document.getElementById('adminSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('sidebarOverlay');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      if (overlay) overlay.classList.toggle('active');
    });
  }

  if (overlay) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    });
  }

  /* ══════════════════════════
     UPLOAD PREVIEW GAMBAR
     Cara pakai: tambahkan data-preview="idElPreview" pada input file
     ══════════════════════════ */
  document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      const previewId = input.getAttribute('data-preview');
      const previewEl = document.getElementById(previewId);
      if (!previewEl) return;

      const file = input.files[0];
      if (!file) return;

      // Gambar
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
          const img = previewEl.querySelector('img');
          if (img) img.src = e.target.result;
          previewEl.style.display = 'block';
          const nama = previewEl.querySelector('.upload-preview-nama');
          if (nama) nama.textContent = file.name;
        };
        reader.readAsDataURL(file);
      }

      // PDF
      if (file.type === 'application/pdf') {
        previewEl.style.display = 'block';
        const nama = previewEl.querySelector('.upload-preview-nama');
        if (nama) nama.textContent = '📄 ' + file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        const img = previewEl.querySelector('img');
        if (img) img.style.display = 'none';
      }
    });
  });

  /* ══════════════════════════
     DRAG & DROP UPLOAD AREA
     ══════════════════════════ */
  document.querySelectorAll('.upload-area').forEach(function (area) {
    area.addEventListener('dragover', function (e) {
      e.preventDefault();
      area.classList.add('drag-over');
    });
    area.addEventListener('dragleave', function () {
      area.classList.remove('drag-over');
    });
    area.addEventListener('drop', function (e) {
      e.preventDefault();
      area.classList.remove('drag-over');
      const input = area.querySelector('input[type="file"]');
      if (input && e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });
    // Klik area → trigger input file
    area.addEventListener('click', function () {
      const input = area.querySelector('input[type="file"]');
      if (input) input.click();
    });
  });

  /* ══════════════════════════
     MODAL KONFIRMASI HAPUS
     Cara pakai:
     <button class="btn-hapus" data-modal="modalHapus" data-id="5">Hapus</button>
     <div id="modalHapus" class="modal-backdrop">...</div>
     ══════════════════════════ */
  document.querySelectorAll('[data-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const modalId = btn.getAttribute('data-modal');
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('active');
        // Simpan id target ke tombol konfirmasi dalam modal
        const confirmBtn = modal.querySelector('[data-confirm]');
        if (confirmBtn && btn.getAttribute('data-id')) {
          confirmBtn.setAttribute('data-target-id', btn.getAttribute('data-id'));
        }
      }
    });
  });

  // Tutup modal
  document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const modal = btn.closest('.modal-backdrop');
      if (modal) modal.classList.remove('active');
    });
  });

  // Klik backdrop → tutup
  document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
    backdrop.addEventListener('click', function (e) {
      if (e.target === backdrop) backdrop.classList.remove('active');
    });
  });

  /* ══════════════════════════
     TOGGLE PASSWORD (login)
     ══════════════════════════ */
  document.querySelectorAll('.toggle-pw').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const input = btn.closest('.input-icon-wrap').querySelector('input');
      if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
      } else {
        input.type = 'password';
        btn.textContent = '👁️';
      }
    });
  });

  /* ══════════════════════════
     AUTO DISMISS ALERT
     ══════════════════════════ */
  document.querySelectorAll('.admin-alert[data-auto-close]').forEach(function (alert) {
    setTimeout(function () {
      alert.style.opacity = '0';
      alert.style.transition = 'opacity 0.4s';
      setTimeout(function () { alert.remove(); }, 400);
    }, 3000);
  });

});

/* ══════════════════════════════════════
   FUNGSI GLOBAL (bisa dipanggil inline)
   ══════════════════════════════════════ */

// Konfirmasi hapus sederhana
function konfirmasiHapus(pesan) {
  return confirm(pesan || 'Apakah Anda yakin ingin menghapus data ini?');
}

// Tampilkan toast notifikasi
function tampilkanToast(pesan, tipe) {
  tipe = tipe || 'success';
  const toast = document.createElement('div');
  toast.style.cssText = [
    'position:fixed', 'bottom:1.5rem', 'right:1.5rem',
    'background:' + (tipe === 'success' ? '#16a34a' : '#dc2626'),
    'color:#fff', 'padding:0.75rem 1.25rem',
    'border-radius:10px', 'font-size:0.83rem', 'font-weight:700',
    'z-index:9999', 'box-shadow:0 8px 24px rgba(0,0,0,0.2)',
    'animation:fadeIn 0.3s ease'
  ].join(';');
  toast.textContent = (tipe === 'success' ? '✅ ' : '❌ ') + pesan;
  document.body.appendChild(toast);
  setTimeout(function () {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.4s';
    setTimeout(function () { toast.remove(); }, 400);
  }, 3000);
}
