@include('layouts.header')


<main class="pt-36 pb-20 min-h-screen bg-slate-100">
  <div class="container mx-auto px-4">

    <div class="bg-slate-900 text-white rounded-2xl shadow-xl p-8 mb-8 flex flex-col md:flex-row justify-between items-center relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-teal-500 rounded-full opacity-20 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-32 h-32 bg-amber-500 rounded-full opacity-10 blur-2xl"></div>

        <div class="relative z-10 mb-4 md:mb-0">
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest bg-white/10 px-3 py-1 rounded-full mb-3">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                Panel Administrator
            </span>
            <h1 class="text-3xl font-bold">Verifikasi Program Bantuan</h1>
            <p class="text-slate-400 mt-1">Kelola dan tinjau pengajuan desa serta penawaran donatur.</p>
        </div>

        <div class="relative z-10 flex items-center gap-3 md:gap-4">
            <div class="text-center px-4 py-2 border border-slate-700 rounded-lg bg-slate-800">
                <span class="block text-xs text-slate-400">Antrean Desa</span>
                <span class="text-xl font-bold text-amber-400"><?= $count_desa ?></span>
            </div>
            <div class="text-center px-4 py-2 border border-slate-700 rounded-lg bg-slate-800">
                <span class="block text-xs text-slate-400">Antrean Donatur</span>
                <span class="text-xl font-bold text-teal-400"><?= $count_donatur ?></span>
            </div>
        </div>
    </div> <?= $pesan ?>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @include('legacy.partials.stat-card', ['value' => $stats_desa['pending'] + $stats_donatur['pending'], 'label' => 'Menunggu Verifikasi', 'icon' => 'clock', 'color' => 'slate'])
        @include('legacy.partials.stat-card', ['value' => $stats_desa['approved'] + $stats_donatur['approved'], 'label' => 'Disetujui', 'icon' => 'check', 'color' => 'green'])
        @include('legacy.partials.stat-card', ['value' => $stats_desa['rejected'] + $stats_donatur['rejected'], 'label' => 'Ditolak', 'icon' => 'cross', 'color' => 'red'])
        @include('legacy.partials.stat-card', ['value' => $stats_desa['funded'] + $stats_donatur['funded'], 'label' => 'Sudah Tersalurkan', 'icon' => 'handshake', 'color' => 'teal'])
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden mb-10">
        <div class="bg-amber-500 p-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Data Pengajuan Perangkat Desa
            </h2>
            <span class="text-amber-50 text-sm font-semibold bg-white/10 px-3 py-1 rounded-full"><?= $total_desa ?> data</span>
        </div>
        <div class="overflow-x-auto p-4">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-200 text-sm text-slate-600">
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Desa / PJ</th>
                        <th class="p-3">Kebutuhan</th>
                        <th class="p-3">Dokumen</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi Admin</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php if(count($query_desa) > 0): ?>
                        <?php foreach($query_desa as $row): ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-3 text-slate-500 whitespace-nowrap"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                            <td class="p-3">
                                <span class="font-bold text-slate-800 block"><?= htmlspecialchars($row['desa']) ?></span>
                                <span class="text-xs text-slate-500"><?= htmlspecialchars($row['nama_pj']) ?></span>
                            </td>
                            <td class="p-3">
                                <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded text-xs font-bold"><?= ucfirst(htmlspecialchars($row['target_bantuan'])) ?></span>
                                <?php if($row['jumlah_kk']): ?>
                                    <span class="block text-xs text-slate-400 mt-1"><?= (int) $row['jumlah_kk'] ?> KK</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <?php if(!empty($row['dokumen_desa'])): ?>
                                    @include('legacy.partials.document-link', ['filename' => $row['dokumen_desa'], 'size' => 'sm'])
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    @include('legacy.partials.status-badge', ['status' => $row['status'], 'size' => 'sm'])
                                    @include('legacy.partials.funded-badge', ['is_funded' => $row['is_funded'], 'tipe' => 'permintaan'])
                                </div>
                            </td>

                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <?php if($row['status'] == 'pending'): ?>
                                        <form method="POST" class="inline-flex gap-1">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="tipe" value="desa">
                                            <button type="submit" name="action_status" value="approved" class="w-8 h-8 inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white rounded-lg transition" title="Setujui">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                            <button type="submit" name="action_status" value="rejected" class="w-8 h-8 inline-flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-lg transition" title="Tolak">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                        <span class="w-px h-6 bg-slate-200 mx-0.5"></span>
                                    <?php endif; ?>

                                    <a href="detail.php?id=<?= $row['id'] ?>&tipe=permintaan" class="w-8 h-8 inline-flex items-center justify-center bg-teal-500 hover:bg-teal-600 text-white rounded-lg transition" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="edit.php?id=<?= $row['id'] ?>&tipe=desa" class="w-8 h-8 inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?')">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="tipe" value="desa">
                                        <button type="submit" name="hapus_data" class="w-8 h-8 inline-flex items-center justify-center bg-slate-700 hover:bg-red-600 text-white rounded-lg transition" title="Hapus Permanen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="p-0">
                            @include('legacy.partials.empty-state', ['title' => 'Belum ada data pengajuan desa', 'icon' => 'inbox'])
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 pb-6">
            @include('legacy.partials.pagination', [
                'current_page' => $page_desa,
                'total_pages' => $total_pages_desa,
                'page_param' => 'page_desa',
            ])
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden mb-10">
        <div class="bg-teal-500 p-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Data Penawaran Donatur
            </h2>
            <span class="text-teal-50 text-sm font-semibold bg-white/10 px-3 py-1 rounded-full"><?= $total_donatur ?> data</span>
        </div>
        <div class="overflow-x-auto p-4">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-200 text-sm text-slate-600">
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Instansi / PJ</th>
                        <th class="p-3">Jenis Bantuan</th>
                        <th class="p-3">Dokumen</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi Admin</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php if(count($query_donatur) > 0): ?>
                        <?php foreach($query_donatur as $row): ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-3 text-slate-500 whitespace-nowrap"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                            <td class="p-3">
                                <span class="font-bold text-slate-800 block"><?= htmlspecialchars($row['nama_instansi']) ?></span>
                                <span class="text-xs text-slate-500"><?= htmlspecialchars($row['pj_donatur']) ?></span>
                            </td>
                            <td class="p-3">
                                <span class="bg-teal-100 text-teal-800 px-2 py-1 rounded text-xs font-bold"><?= htmlspecialchars($row['jenis_penawaran']) ?></span>
                            </td>
                            <td class="p-3">
                                <?php if(!empty($row['dokumen_donatur'])): ?>
                                    @include('legacy.partials.document-link', ['filename' => $row['dokumen_donatur'], 'size' => 'sm'])
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    @include('legacy.partials.status-badge', ['status' => $row['status'], 'size' => 'sm'])
                                    @include('legacy.partials.funded-badge', ['is_funded' => $row['is_funded'], 'tipe' => 'penawaran'])
                                </div>
                            </td>

                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <?php if($row['status'] == 'pending'): ?>
                                        <form method="POST" class="inline-flex gap-1">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="tipe" value="donatur">
                                            <button type="submit" name="action_status" value="approved" class="w-8 h-8 inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white rounded-lg transition" title="Setujui">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                            <button type="submit" name="action_status" value="rejected" class="w-8 h-8 inline-flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-lg transition" title="Tolak">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                        <span class="w-px h-6 bg-slate-200 mx-0.5"></span>
                                    <?php endif; ?>

                                    <a href="detail.php?id=<?= $row['id'] ?>&tipe=penawaran" class="w-8 h-8 inline-flex items-center justify-center bg-teal-500 hover:bg-teal-600 text-white rounded-lg transition" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="edit.php?id=<?= $row['id'] ?>&tipe=donatur" class="w-8 h-8 inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?')">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="tipe" value="donatur">
                                        <button type="submit" name="hapus_data" class="w-8 h-8 inline-flex items-center justify-center bg-slate-700 hover:bg-red-600 text-white rounded-lg transition" title="Hapus Permanen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="p-0">
                            @include('legacy.partials.empty-state', ['title' => 'Belum ada data penawaran donatur', 'icon' => 'inbox'])
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 pb-6">
            @include('legacy.partials.pagination', [
                'current_page' => $page_donatur,
                'total_pages' => $total_pages_donatur,
                'page_param' => 'page_donatur',
            ])
        </div>
    </div>

  </div>
</main>

@include('layouts.footer')
