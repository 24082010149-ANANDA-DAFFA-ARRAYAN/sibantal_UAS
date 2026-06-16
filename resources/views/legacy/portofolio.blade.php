@include('layouts.header')


<main class="pt-36 pb-20 min-h-screen bg-transparent">
  <div class="container mx-auto px-4 relative z-10">

    <div class="text-center mb-10">
      <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Program Bantuan Aktif</h1>
      <?php if ($role === 'desa'): ?>
        <p class="text-slate-600 max-w-2xl mx-auto font-medium">Temukan berbagai penawaran bantuan dari instansi/donatur yang siap disalurkan ke desa Anda.</p>
      <?php elseif ($role === 'donatur'): ?>
        <p class="text-slate-600 max-w-2xl mx-auto font-medium">Daftar desa yang membutuhkan uluran tangan Anda. Pilih program yang ingin Anda danai untuk mulai membawa perubahan.</p>
      <?php else: ?>
        <p class="text-slate-600 max-w-2xl mx-auto font-medium">Jelajahi ekosistem SI BanTal. Lihat desa yang membutuhkan bantuan atau temukan donatur yang siap menyalurkan dananya.</p>
      <?php endif; ?>
    </div>

    <div class="bg-white/80 backdrop-blur-md p-4 rounded-xl shadow-lg border border-white/50 mb-10 max-w-5xl mx-auto">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
                <label class="sr-only">Kata kunci</label>
                <div class="relative">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama instansi, desa, atau kata kunci..." class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-white/90">
                </div>
            </div>

            <?php if ($role === 'desa' || $role === 'guest'): ?>
            <div class="w-full md:w-56">
                <select name="kategori_penawaran" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none bg-white/90">
                    <option value="">Semua Jenis Bantuan</option>
                    <option value="Sembako" <?= ($kategori_penawaran == 'Sembako') ? 'selected' : '' ?>>Sembako</option>
                    <option value="Dana Tunai" <?= ($kategori_penawaran == 'Dana Tunai') ? 'selected' : '' ?>>Dana Tunai</option>
                    <option value="Material" <?= ($kategori_penawaran == 'Material') ? 'selected' : '' ?>>Material Bangunan</option>
                    <option value="Lainnya" <?= ($kategori_penawaran == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                </select>
            </div>
            <?php endif; ?>

            <?php if ($role === 'donatur' || $role === 'guest'): ?>
            <div class="w-full md:w-56">
                <select name="kategori_permintaan" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white/90">
                    <option value="">Semua Target Desa</option>
                    <option value="warga" <?= ($kategori_permintaan == 'warga') ? 'selected' : '' ?>>Warga Terdampak</option>
                    <option value="fasilitas" <?= ($kategori_permintaan == 'fasilitas') ? 'selected' : '' ?>>Fasilitas Umum</option>
                </select>
            </div>
            <?php endif; ?>

            <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-3 px-8 rounded-lg transition shadow-md whitespace-nowrap">Cari</button>
            <?php if($search != '' || $kategori_penawaran != '' || $kategori_permintaan != ''): ?>
                <a href="portofolio.php" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-4 rounded-lg transition text-center flex items-center justify-center whitespace-nowrap">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($role === 'donatur' || $role === 'guest'): ?>

        <?php if ($role === 'guest'): ?>
            <div class="flex items-center justify-between mb-6 mt-12 flex-wrap gap-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center mr-3 shadow-md"><span class="text-white font-bold">1</span></div>
                    <h2 class="text-2xl font-bold text-slate-800">Daftar Kebutuhan Desa</h2>
                </div>
                <?php if ($total_permintaan > 0): ?>
                    <span class="text-sm font-semibold text-slate-400"><?= $total_permintaan ?> program ditemukan</span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="flex items-center justify-end mb-4">
                <?php if ($total_permintaan > 0): ?>
                    <span class="text-sm font-semibold text-slate-400"><?= $total_permintaan ?> program ditemukan</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-4">
            <?php if(count($query_permintaan) > 0): ?>
                <?php foreach($query_permintaan as $row): ?>
                    <?php
                        $link_tujuan = ($role === 'guest') ? 'login.php' : 'detail.php?id=' . $row['id'] . '&tipe=permintaan';
                    ?>
                    <a href="<?= $link_tujuan ?>" class="block group h-full">
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-white/50 overflow-hidden group-hover:shadow-2xl group-hover:-translate-y-2 transition-all duration-300 transform flex flex-col h-full cursor-pointer">
                            <div class="bg-amber-500 p-4 relative">
                                <span class="bg-white text-amber-600 text-xs font-bold px-3 py-1 rounded-full absolute top-4 right-4 shadow capitalize"><?= htmlspecialchars($row['target_bantuan'] === 'warga' ? 'Warga Terdampak' : 'Fasilitas Umum') ?></span>
                                <h3 class="text-xl font-bold text-white mt-6">Desa <?= htmlspecialchars($row['desa']) ?></h3>
                                <p class="text-amber-100 text-sm"><?= htmlspecialchars($row['kecamatan']) ?>, <?= htmlspecialchars($row['kota']) ?></p>
                            </div>
                            <div class="p-6 flex-grow flex flex-col">
                                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Kondisi / Alasan</h4>
                                <p class="text-slate-700 text-sm mb-4 line-clamp-3"><?= htmlspecialchars($row['alasan']) ?></p>
                                <?php if($row['jumlah_kk']): ?>
                                    <div class="bg-slate-50 p-2 rounded border border-slate-100 mb-4 flex items-center justify-center">
                                        <span class="text-sm font-bold text-slate-600">Dibutuhkan untuk <?= (int) $row['jumlah_kk'] ?> KK</span>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-auto pt-4 border-t border-slate-100">
                                    <p class="text-xs text-slate-400 mb-3">Tgl Pengajuan: <?= date('d M Y', strtotime($row['created_at'])) ?></p>

                                    <?php if ($role === 'guest'): ?>
                                        <div class="block text-center w-full bg-slate-800 group-hover:bg-slate-900 text-white font-bold py-2.5 rounded-lg transition duration-300 mt-3 shadow-md">🔒 Login untuk Mendanai</div>
                                    <?php else: ?>
                                        <div class="block text-center w-full bg-teal-500 group-hover:bg-teal-600 text-white font-bold py-2.5 rounded-lg transition duration-300 mt-3 shadow-md shadow-teal-500/30">Lihat Detail &amp; Danai</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                @include('legacy.partials.empty-state', [
                    'title' => ($search != '' || $kategori_permintaan != '') ? 'Tidak ada hasil yang cocok' : 'Belum ada pengajuan bantuan dari desa yang diverifikasi',
                    'subtitle' => ($search != '' || $kategori_permintaan != '') ? 'Coba ubah kata kunci atau filter target bantuan, atau reset pencarian.' : 'Pantau halaman ini secara berkala untuk pembaruan kebutuhan desa terbaru.',
                    'icon' => ($search != '' || $kategori_permintaan != '') ? 'search' : 'inbox',
                ])
            <?php endif; ?>
        </div>

        @include('legacy.partials.pagination', [
            'current_page' => $page_permintaan,
            'total_pages' => $total_pages_permintaan,
            'page_param' => 'page_permintaan',
        ])
    <?php endif; ?>

    <?php if ($role === 'desa' || $role === 'guest'): ?>

        <?php if ($role === 'guest'): ?>
            <div class="flex items-center justify-between mb-6 mt-16 border-t border-slate-200/50 pt-12 flex-wrap gap-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-teal-500 rounded-full flex items-center justify-center mr-3 shadow-md"><span class="text-white font-bold">2</span></div>
                    <h2 class="text-2xl font-bold text-slate-800">Daftar Penawaran Donatur</h2>
                </div>
                <?php if ($total_penawaran > 0): ?>
                    <span class="text-sm font-semibold text-slate-400"><?= $total_penawaran ?> program ditemukan</span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="flex items-center justify-end mb-4">
                <?php if ($total_penawaran > 0): ?>
                    <span class="text-sm font-semibold text-slate-400"><?= $total_penawaran ?> program ditemukan</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-4">
            <?php if(count($query_penawaran) > 0): ?>
                <?php foreach($query_penawaran as $row): ?>
                    <?php
                        $link_tujuan = ($role === 'guest') ? 'login.php' : 'detail.php?id=' . $row['id'] . '&tipe=penawaran';
                    ?>
                    <a href="<?= $link_tujuan ?>" class="block group h-full">
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-white/50 overflow-hidden group-hover:shadow-2xl group-hover:-translate-y-2 transition-all duration-300 transform flex flex-col h-full cursor-pointer">
                            <div class="bg-teal-500 p-4 relative">
                                <span class="bg-white text-teal-600 text-xs font-bold px-3 py-1 rounded-full absolute top-4 right-4 shadow"><?= htmlspecialchars($row['jenis_penawaran']) ?></span>
                                <h3 class="text-xl font-bold text-white mt-6"><?= htmlspecialchars($row['nama_instansi']) ?></h3>
                                <p class="text-teal-100 text-sm">PJ: <?= htmlspecialchars($row['pj_donatur']) ?></p>
                            </div>
                            <div class="p-6 flex-grow flex flex-col">
                                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Detail Bantuan</h4>
                                <p class="text-slate-700 text-sm mb-4 line-clamp-3"><?= htmlspecialchars($row['detail_bantuan']) ?></p>
                                <div class="mt-auto pt-4 border-t border-slate-100">
                                    <p class="text-xs text-slate-400 mb-3">Tgl Penawaran: <?= date('d M Y', strtotime($row['created_at'])) ?></p>

                                    <?php if ($role === 'guest'): ?>
                                        <div class="block text-center w-full bg-slate-800 group-hover:bg-slate-900 text-white font-bold py-2.5 rounded-lg transition duration-300 mt-3 shadow-md">🔒 Login untuk Mengajukan</div>
                                    <?php else: ?>
                                        <div class="block text-center w-full bg-amber-500 group-hover:bg-amber-600 text-white font-bold py-2.5 rounded-lg transition duration-300 mt-3 shadow-md shadow-amber-500/30">Lihat Detail &amp; Klaim</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                @include('legacy.partials.empty-state', [
                    'title' => ($search != '' || $kategori_penawaran != '') ? 'Tidak ada hasil yang cocok' : 'Belum ada penawaran bantuan dari donatur yang tersedia saat ini',
                    'subtitle' => ($search != '' || $kategori_penawaran != '') ? 'Coba ubah kata kunci atau filter jenis bantuan, atau reset pencarian.' : 'Donatur baru akan tampil di sini setelah diverifikasi oleh Admin.',
                    'icon' => ($search != '' || $kategori_penawaran != '') ? 'search' : 'handshake',
                ])
            <?php endif; ?>
        </div>

        @include('legacy.partials.pagination', [
            'current_page' => $page_penawaran,
            'total_pages' => $total_pages_penawaran,
            'page_param' => 'page_penawaran',
        ])
    <?php endif; ?>

  </div>
</main>

@include('layouts.footer')
