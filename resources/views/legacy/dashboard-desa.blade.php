@include('layouts.header')


<main class="pt-36 pb-20 min-h-screen bg-transparent">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-slate-900 mb-2">Dashboard Desa</h1>
                <p class="text-slate-600 text-lg">Halo, <span class="font-bold text-amber-600"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Perangkat Desa') ?></span> dari <span class="font-bold text-slate-800 border-b-2 border-amber-300"><?= htmlspecialchars($_SESSION['asal_desa'] ?? 'Desa Anda') ?></span> 👋</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @include('legacy.partials.stat-card', ['value' => $stats_desa_self['total'], 'label' => 'Total Pengajuan', 'icon' => 'list', 'color' => 'slate'])
            @include('legacy.partials.stat-card', ['value' => $stats_desa_self['pending'], 'label' => 'Menunggu Verifikasi', 'icon' => 'clock', 'color' => 'amber'])
            @include('legacy.partials.stat-card', ['value' => $stats_desa_self['approved'], 'label' => 'Disetujui', 'icon' => 'check', 'color' => 'green'])
            @include('legacy.partials.stat-card', ['value' => $stats_desa_self['funded'], 'label' => 'Sudah Didanai', 'icon' => 'handshake', 'color' => 'teal'])
        </div>

        <div class="flex flex-col lg:flex-row gap-8 w-full">

            <div class="w-full lg:w-1/3 p-8 bg-white/90 backdrop-blur-md rounded-2xl shadow-lg border-t-4 border-amber-500 h-fit hover:shadow-xl transition duration-300">
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-2xl mb-3">Ajukan Kebutuhan</h3>
                <p class="text-base text-slate-500 mb-8 leading-relaxed">Sampaikan kebutuhan bantuan sosial (Warga/Fasilitas) desa Anda ke dalam sistem SI BanTal agar dapat terhubung dengan donatur.</p>
                <a href="contact.php" class="block w-full text-center px-6 py-3.5 bg-amber-500 text-white rounded-full text-base font-bold hover:bg-amber-600 hover:shadow-lg transition duration-300">
                    Buat Pengajuan Baru
                </a>
            </div>

            <div class="w-full lg:w-2/3 flex flex-col gap-8">
                
                <div class="p-8 bg-white/90 backdrop-blur-md rounded-2xl shadow-lg border-t-4 border-slate-700 hover:shadow-xl transition duration-300">
                    <h3 class="font-bold text-slate-800 text-2xl mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Kebutuhan Desa Saya
                    </h3>
                    <p class="text-base text-slate-500 mb-6">Pantau status validasi form kebutuhan bantuan yang telah Anda ajukan ke Admin.</p>

                    <?php if(count($rows_my_req) > 0): ?>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-slate-100 text-slate-600 text-sm border-b border-slate-200">
                                <tr>
                                    <th class="p-4 font-bold">Tanggal Input</th>
                                    <th class="p-4 font-bold">Target</th>
                                    <th class="p-4 font-bold text-center">Status Admin</th>
                                    <th class="p-4 font-bold text-center">Status Dana</th>
                                    <th class="p-4 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-slate-700 bg-white">
                                <?php foreach($rows_my_req as $row): ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="p-4 whitespace-nowrap"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                    <td class="p-4">
                                        <span class="uppercase text-xs font-bold text-slate-500"><?= htmlspecialchars($row['target_bantuan'] === 'warga' ? 'Warga Terdampak' : 'Fasilitas Umum') ?></span>
                                        <?php if($row['jumlah_kk']): ?>
                                            <span class="block text-xs text-slate-400 mt-0.5"><?= (int) $row['jumlah_kk'] ?> KK</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        @include('legacy.partials.status-badge', ['status' => $row['status'], 'size' => 'sm'])
                                    </td>
                                    <td class="p-4 text-center">
                                        @include('legacy.partials.funded-badge', ['is_funded' => $row['is_funded'], 'tipe' => 'permintaan'])
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="detail.php?id=<?= $row['id'] ?>&tipe=permintaan" class="text-teal-600 hover:text-teal-800 font-bold text-sm bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-lg transition">Detail</a>
                                            <?php if($row['status'] == 'pending'): ?>
                                                <a href="edit_program.php?id=<?= $row['id'] ?>&tipe=permintaan" class="text-amber-600 hover:text-amber-800 font-bold text-sm bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition">Edit</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        @include('legacy.partials.empty-state', [
                            'title' => 'Belum ada form pengajuan yang Anda buat',
                            'subtitle' => 'Klik "Buat Pengajuan Baru" untuk mengajukan kebutuhan bantuan bagi desa Anda.',
                            'icon' => 'document',
                        ])
                    <?php endif; ?>
                </div>

                <div class="p-8 bg-white/90 backdrop-blur-md rounded-2xl shadow-lg border-t-4 border-amber-500 hover:shadow-xl transition duration-300">
                    <h3 class="font-bold text-slate-800 text-2xl mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path></svg>
                        Partisipasi Program Donatur
                    </h3>
                    <p class="text-base text-slate-500 mb-6">Daftar penawaran donatur dari halaman Program Aktif yang telah Anda klaim.</p>

                    <?php if(count($rows_apply) > 0): ?>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-amber-50 text-amber-800 text-sm border-b border-amber-200">
                                <tr>
                                    <th class="p-4 font-bold">Tgl Ambil</th>
                                    <th class="p-4 font-bold">Instansi Donatur</th>
                                    <th class="p-4 font-bold">Jenis Bantuan</th>
                                    <th class="p-4 font-bold text-center">Keterangan</th>
                                    <th class="p-4 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-slate-700 bg-white">
                                <?php foreach($rows_apply as $row): ?>
                                <tr class="border-b border-slate-100 hover:bg-amber-50">
                                    <td class="p-4 whitespace-nowrap"><?= date('d M Y', strtotime($row['tgl_apply'])) ?></td>
                                    <td class="p-4 font-bold text-slate-900"><?= htmlspecialchars($row['nama_instansi']) ?></td>
                                    <td class="p-4 uppercase text-xs font-bold text-slate-500"><?= htmlspecialchars($row['jenis_penawaran']) ?></td>
                                    <td class="p-4 text-center"><span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">Diproses Admin</span></td>
                                    <td class="p-4 text-center">
                                        <a href="detail.php?id=<?= $row['id'] ?>&tipe=penawaran" class="text-amber-600 hover:text-amber-800 font-bold text-sm bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition">Detail</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        @include('legacy.partials.empty-state', [
                            'title' => 'Anda belum mengambil program donatur manapun',
                            'subtitle' => 'Jelajahi halaman Program Aktif untuk menemukan penawaran bantuan yang sesuai.',
                            'icon' => 'handshake',
                        ])
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</main>

@include('layouts.footer')
