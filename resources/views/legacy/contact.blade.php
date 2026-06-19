@include('layouts.header')


<main class="pt-36 pb-20 min-h-screen bg-transparent">
  <div class="container mx-auto px-4 relative z-10">
    
    <div class="max-w-4xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-xl overflow-hidden border border-slate-100">
      
      <?php if ($_SESSION['role'] === 'donatur'): ?>
          <div class="bg-teal-500 p-8 text-center text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full bg-teal-600 opacity-20 transform -skew-y-3 origin-top-left z-0"></div>
            <div class="relative z-10">
                <h2 class="text-3xl font-bold mb-2">Form Penawaran Bantuan</h2>
                <p class="text-teal-100">Lengkapi detail donasi yang ingin disalurkan untuk membantu desa.</p>
            </div>
          </div>
      <?php elseif ($_SESSION['role'] === 'desa'): ?>
          <div class="bg-amber-500 p-8 text-center text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full bg-amber-600 opacity-20 transform -skew-y-3 origin-top-left z-0"></div>
            <div class="relative z-10">
                <h2 class="text-3xl font-bold mb-2">Form Pengajuan Bantuan</h2>
                <p class="text-amber-100">Ajukan kebutuhan bantuan sosial (Warga/Fasilitas) untuk desa Anda.</p>
            </div>
          </div>
      <?php endif; ?>

      <div class="p-8 md:p-12">
        <?php if($success): ?>
            <div class="mb-8 bg-green-50 text-green-700 p-4 rounded-lg font-medium border border-green-200"><?= $success ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="mb-8 bg-red-50 text-red-700 p-4 rounded-lg font-medium border border-red-200"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'donatur'): ?>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return validasiForm(event)">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Instansi/Perusahaan</label>
                        <input type="text" name="nama_instansi" value="<?= htmlspecialchars($_SESSION['nama_organisasi'] ?? '') ?>" readonly class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-200 text-slate-500 cursor-not-allowed outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Penanggung Jawab (PJ)</label>
                        <input type="text" name="pj_donatur" value="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?>" readonly class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-200 text-slate-500 cursor-not-allowed outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jabatan PJ</label>
                        <input type="text" name="jabatan_donatur" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-slate-50 focus:bg-white transition" placeholder="Contoh: Manajer CSR" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kontak (No. HP/WA)</label>
                        <input type="text" name="kontak_donatur" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-slate-50 focus:bg-white transition" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Penawaran</label>
                    <select name="jenis_penawaran" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-slate-50 focus:bg-white transition" required>
                        <option value="">-- Pilih Jenis Bantuan --</option>
                        <option value="Sembako">Paket Sembako</option>
                        <option value="Dana Tunai">Dana Tunai</option>
                        <option value="Material">Material Bangunan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Detail Bantuan</label>
                    <textarea id="detail_bantuan" name="detail_bantuan" rows="4" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-slate-50 focus:bg-white transition" placeholder="Jelaskan detail bantuan yang akan diberikan. Minimal 20 Karakter..." required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Unggah Dokumen Pendukung (PDF/JPG/PNG)</label>
                    <input type="file" id="dokumen_donatur" name="dokumen_donatur" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-slate-50 focus:bg-white transition cursor-pointer" required>
                    <p class="text-xs text-slate-500 mt-1">*Surat keterangan, brosur, atau foto. (Maks 2MB).</p>
                    <div id="preview_dokumen_donatur" class="file-preview hidden"></div>
                </div>

                <div class="pt-4">
                    <button type="submit" name="submit_donatur" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-teal-500/30 transform transition duration-300">Kirim Penawaran Program Bantuan</button>
                </div>
            </form>

        <?php elseif ($_SESSION['role'] === 'desa'): ?>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return validasiForm(event)">
                
                <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-200 mb-6">
                    <h4 class="font-bold text-amber-900 mb-4 border-b border-amber-200 pb-2">Informasi Penanggung Jawab</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Penanggung Jawab</label>
                            <input type="text" name="nama_pj" value="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?>" readonly class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-200 text-slate-500 cursor-not-allowed outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Jabatan</label>
                            <input type="text" name="jabatan" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white transition" placeholder="Contoh: Kepala Desa / RT" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kontak PJ (No. HP/WA)</label>
                            <input type="text" name="kontak_pj" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white transition" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-200 mb-6">
                    <h4 class="font-bold text-amber-900 mb-4 border-b border-amber-200 pb-2">Informasi Wilayah Desa</h4>
                    <p class="text-xs text-slate-500 mb-4">Data wilayah diambil dari profil akun Anda. Hubungi admin jika perlu dikoreksi.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Provinsi</label>
                            <input type="text" name="provinsi" value="<?= htmlspecialchars($wilayah_desa['provinsi']) ?>" readonly class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-200 text-slate-500 cursor-not-allowed outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kota / Kabupaten</label>
                            <input type="text" name="kota" value="<?= htmlspecialchars($wilayah_desa['kota']) ?>" readonly class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-200 text-slate-500 cursor-not-allowed outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kecamatan</label>
                            <input type="text" name="kecamatan" value="<?= htmlspecialchars($wilayah_desa['kecamatan']) ?>" readonly class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-200 text-slate-500 cursor-not-allowed outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Desa / Kelurahan</label>
                            <input type="text" name="desa" value="<?= htmlspecialchars($wilayah_desa['desa']) ?>" readonly class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-200 text-slate-500 cursor-not-allowed outline-none" required>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Target Bantuan</label>
                        <select name="target_bantuan" id="target_bantuan" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-slate-50 focus:bg-white transition" required>
                            <option value="">-- Pilih Target --</option>
                            <option value="warga">Warga Terdampak</option>
                            <option value="fasilitas">Fasilitas Umum</option>
                        </select>
                    </div>
                    <div id="jumlah_kk_wrap" class="opacity-40 transition duration-300">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah KK Terdampak</label>
                        <input type="number" min="1" id="jumlah_kk" name="jumlah_kk" disabled placeholder="Pilih 'Warga Terdampak' dahulu" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-slate-50 focus:bg-white transition disabled:bg-slate-100 disabled:cursor-not-allowed">
                        <p id="jumlah_kk_hint" class="text-xs text-slate-400 mt-1">Otomatis terisi NULL untuk pengajuan fasilitas umum.</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan Permintaan / Kondisi</label>
                    <textarea id="alasan" name="alasan" rows="5" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-slate-50 focus:bg-white transition" placeholder="Jelaskan secara detail kondisi warga atau fasilitas yang membutuhkan bantuan. Minimal 20 Karakter..." required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Unggah Dokumen (Foto Kondisi / Surat Pengajuan)</label>
                    <input type="file" id="dokumen_desa" name="dokumen_desa" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-slate-50 focus:bg-white transition cursor-pointer" required>
                    <p class="text-xs text-slate-500 mt-1">*Unggah file PDF, JPG, atau PNG sebagai bukti autentik. (Maks 2MB).</p>
                    <div id="preview_dokumen_desa" class="file-preview hidden"></div>
                </div>
                
                <div class="pt-4">
                    <button type="submit" name="submit_desa" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-amber-500/30 transform transition duration-300">Ajukan Permintaan Bantuan</button>
                </div>
            </form>
        <?php endif; ?>

      </div>
    </div>
  </div>
</main>

<script>
const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB

// Preview nama file + validasi ukuran maksimal
function setupFilePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;

    input.addEventListener('change', function () {
        if (!this.files || !this.files[0]) {
            preview.classList.add('hidden');
            preview.innerHTML = '';
            return;
        }
        const file = this.files[0];
        const sizeKb = (file.size / 1024).toFixed(0);
        const tooBig = file.size > MAX_FILE_SIZE;

        preview.classList.remove('hidden');
        if (tooBig) {
            preview.classList.remove('text-teal-600', 'bg-teal-50', 'border-teal-100');
            preview.classList.add('text-red-600', 'bg-red-50', 'border-red-100');
            preview.innerHTML = '⚠️ <span class="truncate">' + file.name + ' (' + sizeKb + ' KB) &mdash; melebihi 2MB!</span>';
            this.value = '';
        } else {
            preview.classList.remove('text-red-600', 'bg-red-50', 'border-red-100');
            preview.classList.add('text-teal-600', 'bg-teal-50', 'border-teal-100');
            preview.innerHTML = '📎 <span class="truncate">' + file.name + ' (' + sizeKb + ' KB)</span>';
        }
    });
}
setupFilePreview('dokumen_donatur', 'preview_dokumen_donatur');
setupFilePreview('dokumen_desa', 'preview_dokumen_desa');

// Toggle field Jumlah KK berdasarkan Target Bantuan (desa)
(function () {
    const targetSel = document.getElementById('target_bantuan');
    const wrap = document.getElementById('jumlah_kk_wrap');
    const input = document.getElementById('jumlah_kk');
    const hint = document.getElementById('jumlah_kk_hint');
    if (!targetSel || !wrap || !input) return;

    targetSel.addEventListener('change', function () {
        if (this.value === 'warga') {
            wrap.classList.remove('opacity-40');
            input.disabled = false;
            input.placeholder = 'Contoh: 25';
            input.required = true;
            hint.textContent = 'Wajib diisi untuk kategori Warga Terdampak.';
        } else if (this.value === 'fasilitas') {
            wrap.classList.add('opacity-40');
            input.disabled = true;
            input.required = false;
            input.value = '';
            hint.textContent = "Otomatis terisi NULL untuk pengajuan fasilitas umum.";
        } else {
            wrap.classList.add('opacity-40');
            input.disabled = true;
            input.required = false;
            input.value = '';
            input.placeholder = "Pilih 'Warga Terdampak' dahulu";
            hint.textContent = "Otomatis terisi NULL untuk pengajuan fasilitas umum.";
        }
    });
})();

function validasiForm(event) {
    const role = "<?= $_SESSION['role'] ?>";

    // Validasi khusus form Desa
    if (role === 'desa') {
        const inputAlasan = document.getElementById('alasan');
        const inputJumlahKk = document.getElementById('jumlah_kk');
        const targetSel = document.getElementById('target_bantuan');

        // Cek Panjang Alasan
        if (inputAlasan.value.trim().length < 20) {
            alert("⚠️ Validasi Gagal:\nPenjelasan kondisi/alasan desa harus diisi minimal 20 karakter agar jelas.");
            inputAlasan.focus();
            event.preventDefault(); // Cegah form terkirim
            return false;
        }

        // Cek Jumlah KK (hanya jika target = warga & field aktif)
        if (targetSel.value === 'warga' && !inputJumlahKk.disabled) {
            if (inputJumlahKk.value === "" || parseInt(inputJumlahKk.value) <= 0) {
                alert("⚠️ Validasi Gagal:\nJumlah KK terdampak harus diisi dan lebih dari 0.");
                inputJumlahKk.focus();
                event.preventDefault();
                return false;
            }
        }
    } 
    
    // Validasi khusus form Donatur
    else if (role === 'donatur') {
        const inputDetail = document.getElementById('detail_bantuan');

        // Cek Panjang Detail Bantuan
        if (inputDetail.value.trim().length < 20) {
            alert("⚠️ Validasi Gagal:\nDetail bantuan harus diisi minimal 20 karakter agar spesifik.");
            inputDetail.focus();
            event.preventDefault(); // Cegah form terkirim
            return false;
        }
    }

    return true; 
}
</script>

@include('layouts.footer')