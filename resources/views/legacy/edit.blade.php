@include('layouts.header')


<main class="relative pt-36 pb-20 min-h-screen overflow-hidden">

  <div class="absolute inset-0 z-0 pointer-events-none">
    <div class="absolute top-[-15%] right-[-10%] w-[40rem] h-[40rem] bg-blue-200/40 rounded-full mix-blend-multiply filter blur-[110px] opacity-70"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[35rem] h-[35rem] bg-teal-200/40 rounded-full mix-blend-multiply filter blur-[100px] opacity-60"></div>
  </div>

  <div class="container mx-auto px-4 max-w-3xl relative z-10">

    <a href="dashboard-admin.php" class="inline-flex items-center text-slate-500 hover:text-teal-600 font-bold mb-6 transition bg-white/60 backdrop-blur-sm px-4 py-2 rounded-full border border-slate-200 shadow-sm text-sm">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Panel Admin
    </a>

    <div class="bg-white/90 backdrop-blur-md rounded-3xl shadow-2xl border border-white overflow-hidden">

      <div class="<?= ($tipe === 'donatur') ? 'bg-teal-500' : 'bg-amber-500' ?> p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-32 h-32 rounded-full bg-white/10 blur-xl"></div>
        <div class="relative z-10 flex items-start justify-between gap-4 flex-wrap">
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Mode Admin &mdash; Edit Data
                </span>
                <h2 class="text-3xl font-bold">
                    Data <?= ($tipe === 'donatur') ? 'Penawaran Donatur' : 'Pengajuan Desa' ?>
                </h2>
                <p class="<?= ($tipe === 'donatur') ? 'text-teal-50' : 'text-amber-50' ?> mt-1 text-sm">
                    Perbaiki data sebelum diteruskan ke proses verifikasi.
                </p>
            </div>
            @include('legacy.partials.status-badge', ['status' => $data['status'] ?? 'pending'])
        </div>
      </div>

      <div class="p-8 md:p-10">

        <?= $pesan ?>

        <?php if ($tipe === 'donatur'): ?>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">

                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <h4 class="font-bold text-slate-700 mb-4 border-b border-slate-200 pb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Informasi Instansi &amp; PJ
                        <span class="ml-auto text-xs font-normal text-slate-400 bg-slate-200 px-2 py-0.5 rounded-full">Hanya dapat diubah oleh Donatur</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nama Instansi</label>
                            <input type="text" value="<?= htmlspecialchars($data['nama_instansi']) ?>" readonly class="w-full px-4 py-3 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed outline-none text-sm">
                        </div>
                        <div>
                            <label class="form-label">Penanggung Jawab (PJ)</label>
                            <input type="text" value="<?= htmlspecialchars($data['pj_donatur']) ?>" readonly class="w-full px-4 py-3 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed outline-none text-sm">
                        </div>
                        <div>
                            <label class="form-label">Jabatan PJ</label>
                            <input type="text" value="<?= htmlspecialchars($data['jabatan_donatur'] ?? '') ?>" readonly class="w-full px-4 py-3 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed outline-none text-sm">
                        </div>
                        <div>
                            <label class="form-label">Kontak (No. HP/WA)</label>
                            <input type="text" value="<?= htmlspecialchars($data['kontak_donatur'] ?? '') ?>" readonly class="w-full px-4 py-3 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed outline-none text-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label">Jenis Penawaran</label>
                    <select name="jenis_penawaran" class="form-input" required>
                        <option value="Sembako" <?= ($data['jenis_penawaran'] == 'Sembako') ? 'selected' : '' ?>>Paket Sembako</option>
                        <option value="Dana Tunai" <?= ($data['jenis_penawaran'] == 'Dana Tunai') ? 'selected' : '' ?>>Dana Tunai</option>
                        <option value="Material" <?= ($data['jenis_penawaran'] == 'Material') ? 'selected' : '' ?>>Material Bangunan</option>
                        <option value="Lainnya" <?= ($data['jenis_penawaran'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Detail Bantuan</label>
                    <textarea name="detail_bantuan" rows="4" class="form-input" required><?= htmlspecialchars($data['detail_bantuan']) ?></textarea>
                </div>

                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <h4 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Dokumen Lampiran
                    </h4>
                    <?php if (!empty($data['dokumen_donatur'])): ?>
                        <div class="mb-3">
                            @include('legacy.partials.document-link', ['filename' => $data['dokumen_donatur']])
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-slate-400 mb-3">Belum ada dokumen terlampir.</p>
                    <?php endif; ?>
                    <label class="form-label">Ganti Dokumen (Opsional)</label>
                    <input type="file" name="dokumen_donatur" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="form-input cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">*Biarkan kosong jika tidak ingin mengubah dokumen sebelumnya.</p>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 bg-teal-500 text-white font-bold py-3.5 rounded-xl hover:bg-teal-600 shadow-lg shadow-teal-500/30 transform transition duration-300">
                        Simpan Perubahan
                    </button>
                    <a href="dashboard-admin.php" class="flex-1 sm:flex-none text-center bg-white border-2 border-slate-200 text-slate-600 font-bold py-3.5 px-6 rounded-xl hover:bg-slate-50 transition">
                        Batal
                    </a>
                </div>
            </form>

        <?php elseif ($tipe === 'desa'): ?>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">

                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <h4 class="font-bold text-slate-700 mb-4 border-b border-slate-200 pb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Informasi Desa &amp; PJ
                        <span class="ml-auto text-xs font-normal text-slate-400 bg-slate-200 px-2 py-0.5 rounded-full">Hanya dapat diubah oleh Perangkat Desa</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nama Desa / Kelurahan</label>
                            <input type="text" value="<?= htmlspecialchars($data['desa']) ?>" readonly class="w-full px-4 py-3 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed outline-none text-sm">
                        </div>
                        <div>
                            <label class="form-label">Penanggung Jawab (PJ)</label>
                            <input type="text" value="<?= htmlspecialchars($data['nama_pj']) ?>" readonly class="w-full px-4 py-3 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed outline-none text-sm">
                        </div>
                        <div>
                            <label class="form-label">Jabatan</label>
                            <input type="text" value="<?= htmlspecialchars($data['jabatan'] ?? '') ?>" readonly class="w-full px-4 py-3 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed outline-none text-sm">
                        </div>
                        <div>
                            <label class="form-label">Kontak PJ (No. HP/WA)</label>
                            <input type="text" value="<?= htmlspecialchars($data['kontak_pj'] ?? '') ?>" readonly class="w-full px-4 py-3 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed outline-none text-sm">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="form-label">Target Bantuan</label>
                        <select name="target_bantuan" id="edit_target_bantuan" class="form-input" required>
                            <option value="warga" <?= ($data['target_bantuan'] == 'warga') ? 'selected' : '' ?>>Warga Terdampak</option>
                            <option value="fasilitas" <?= ($data['target_bantuan'] == 'fasilitas') ? 'selected' : '' ?>>Fasilitas Umum</option>
                        </select>
                    </div>
                    <div id="edit_jumlah_kk_wrap" class="<?= ($data['target_bantuan'] == 'fasilitas') ? 'opacity-50' : '' ?> transition duration-300">
                        <label class="form-label">Jumlah KK</label>
                        <input type="number" min="1" name="jumlah_kk" id="edit_jumlah_kk" value="<?= htmlspecialchars($data['jumlah_kk'] ?? '') ?>" class="form-input" <?= ($data['target_bantuan'] == 'fasilitas') ? 'disabled' : '' ?>>
                    </div>
                </div>

                <div>
                    <label class="form-label">Alasan / Kondisi</label>
                    <textarea name="alasan" rows="5" class="form-input" required><?= htmlspecialchars($data['alasan']) ?></textarea>
                </div>

                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <h4 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Dokumen Lampiran
                    </h4>
                    <?php if (!empty($data['dokumen_desa'])): ?>
                        <div class="mb-3">
                            @include('legacy.partials.document-link', ['filename' => $data['dokumen_desa']])
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-slate-400 mb-3">Belum ada dokumen terlampir.</p>
                    <?php endif; ?>
                    <label class="form-label">Ganti Dokumen (Opsional)</label>
                    <input type="file" name="dokumen_desa" accept=".pdf,.jpg,.jpeg,.png" class="form-input cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">*Biarkan kosong jika tidak ingin mengubah dokumen sebelumnya.</p>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 bg-amber-500 text-white font-bold py-3.5 rounded-xl hover:bg-amber-600 shadow-lg shadow-amber-500/30 transform transition duration-300">
                        Simpan Perubahan
                    </button>
                    <a href="dashboard-admin.php" class="flex-1 sm:flex-none text-center bg-white border-2 border-slate-200 text-slate-600 font-bold py-3.5 px-6 rounded-xl hover:bg-slate-50 transition">
                        Batal
                    </a>
                </div>
            </form>

            <script>
                (function () {
                    const targetSel = document.getElementById('edit_target_bantuan');
                    const wrap = document.getElementById('edit_jumlah_kk_wrap');
                    const input = document.getElementById('edit_jumlah_kk');
                    if (!targetSel || !wrap || !input) return;
                    targetSel.addEventListener('change', function () {
                        if (this.value === 'fasilitas') {
                            wrap.classList.add('opacity-50');
                            input.disabled = true;
                        } else {
                            wrap.classList.remove('opacity-50');
                            input.disabled = false;
                        }
                    });
                })();
            </script>
        <?php endif; ?>

      </div>
    </div>
  </div>
</main>

@include('layouts.footer')