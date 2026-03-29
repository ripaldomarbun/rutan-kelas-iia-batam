/*
  FILE: admin/js/api.js
  Penghubung antara halaman admin HTML ke backend PHP API
  Semua fetch/AJAX ke backend ada di sini
*/

const API = {
    base: '../backend/api',

    // ── AUTH ────────────────────────────────────────────
    async login(username, password) {
        const fd = new FormData();
        fd.append('username', username);
        fd.append('password', password);
        return await this._post('auth.php?action=login', fd);
    },

    async logout() {
        const res = await this._get('auth.php?action=logout');
        if (res.status === 'success') window.location.href = 'login.html';
    },

    // ── BERITA ──────────────────────────────────────────
    async daftarBerita(params = {}) {
        const q = new URLSearchParams({ action: 'list', ...params });
        return await this._get(`berita.php?${q}`);
    },

    async detailBerita(id) {
        return await this._get(`berita.php?action=detail&id=${id}`);
    },

    async simpanBerita(formData) {
        return await this._post('berita.php?action=simpan', formData);
    },

    async hapusBerita(id) {
        const fd = new FormData(); fd.append('id', id);
        return await this._post('berita.php?action=hapus', fd);
    },

    // ── SURVEY SKM ──────────────────────────────────────
    async daftarSKM(tahun) {
        return await this._get(`survey.php?action=list&tahun=${tahun}`);
    },

    async simpanSKM(formData) {
        return await this._post('survey.php?action=simpan', formData);
    },

    async hapusSKM(id) {
        const fd = new FormData(); fd.append('id', id);
        return await this._post('survey.php?action=hapus', fd);
    },

    // ── SLIDER ──────────────────────────────────────────
    async daftarSlider() {
        return await this._get('slider.php?action=list');
    },

    async tambahSlider(formData) {
        return await this._post('slider.php?action=tambah', formData);
    },

    async hapusSlider(id) {
        const fd = new FormData(); fd.append('id', id);
        return await this._post('slider.php?action=hapus', fd);
    },

    async simpanUrutanSlider(data) {
        return await this._postJson('slider.php?action=urutan', data);
    },

    // ── PEJABAT ─────────────────────────────────────────
    async daftarPejabat() {
        return await this._get('pejabat.php?action=list');
    },

    async detailPejabat(kode) {
        return await this._get(`pejabat.php?action=detail&kode=${kode}`);
    },

    async simpanPejabat(formData) {
        return await this._post('pejabat.php?action=simpan', formData);
    },

    // ── KOMITMEN ────────────────────────────────────────
    async daftarKomitmen() {
        return await this._get('komitmen.php?action=list');
    },

    async simpanKomitmen(formData) {
        return await this._post('komitmen.php?action=simpan', formData);
    },

    // ── KUNJUNGAN ───────────────────────────────────────
    async getKunjungan() {
        return await this._get('kunjungan.php?action=get');
    },

    async simpanJadwal(data) {
        return await this._postJson('kunjungan.php?action=simpan_jadwal', data);
    },

    async simpanInfoKunjungan(kode, konten) {
        const fd = new FormData();
        fd.append('kode', kode); fd.append('konten', konten);
        return await this._post('kunjungan.php?action=simpan_info', fd);
    },

    // ── INTERNAL ────────────────────────────────────────
    async _get(endpoint) {
        try {
            const res = await fetch(`${this.base}/${endpoint}`);
            return await res.json();
        } catch (e) {
            return { status: 'error', message: 'Gagal terhubung ke server.' };
        }
    },

    async _post(endpoint, formData) {
        try {
            const csrfToken = sessionStorage.getItem('csrf_token') || '';
            formData.append('csrf_token', csrfToken);
            const res = await fetch(`${this.base}/${endpoint}`, {
                method: 'POST', body: formData
            });
            return await res.json();
        } catch (e) {
            return { status: 'error', message: 'Gagal terhubung ke server.' };
        }
    },

    async _postJson(endpoint, data) {
        try {
            const csrfToken = sessionStorage.getItem('csrf_token') || '';
            data.csrf_token = csrfToken;
            const res = await fetch(`${this.base}/${endpoint}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            return await res.json();
        } catch (e) {
            return { status: 'error', message: 'Gagal terhubung ke server.' };
        }
    },

    async getCsrfToken() {
        return await this._get('auth.php?action=csrf');
    },

    setCsrfToken(token) {
        sessionStorage.setItem('csrf_token', token);
    }
};
