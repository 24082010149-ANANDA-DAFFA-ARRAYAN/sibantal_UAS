<?php
// Tampilan link dokumen lampiran: ikon sesuai ekstensi + nama file yang dirapikan.
// Required : $filename (nama file tersimpan di /uploads, format: {timestamp}_{prefix}{nama_asli})
// Optional : $size = 'sm' untuk versi ringkas di dalam tabel (default 'md')

if (!function_exists('si_bantal_clean_filename')) {
    function si_bantal_clean_filename(string $stored): string
    {
        // Buang prefix "{timestamp}_" lalu prefix internal (desa_, edit_, edit_desa_)
        $clean = preg_replace('/^\d+_/', '', $stored);
        $clean = preg_replace('/^(edit_desa_|edit_|desa_)/', '', $clean);
        return $clean !== '' ? $clean : $stored;
    }
}

$__clean = si_bantal_clean_filename($filename);
$__ext   = strtolower(pathinfo($__clean, PATHINFO_EXTENSION));
$__sm    = ($size ?? 'md') === 'sm';

if (in_array($__ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
    $__icon_bg = 'bg-violet-100 text-violet-600';
    $__icon = '<svg class="w-full h-full p-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
} elseif ($__ext === 'pdf') {
    $__icon_bg = 'bg-red-100 text-red-600';
    $__icon = '<svg class="w-full h-full p-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h4m1-12H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V8.414a1 1 0 00-.293-.707l-4.414-4.414A1 1 0 0013.586 3z"></path></svg>';
} elseif (in_array($__ext, ['doc', 'docx'])) {
    $__icon_bg = 'bg-blue-100 text-blue-600';
    $__icon = '<svg class="w-full h-full p-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
} else {
    $__icon_bg = 'bg-slate-100 text-slate-500';
    $__icon = '<svg class="w-full h-full p-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
}
?>
<?php if ($__sm): ?>
    <a href="uploads/<?= rawurlencode($filename) ?>" target="_blank" rel="noopener"
       class="inline-flex items-center gap-1.5 text-teal-600 hover:text-teal-800 hover:underline text-xs font-semibold max-w-[10rem]">
        <span class="w-5 h-5 rounded shrink-0 <?= $__icon_bg ?>"><?= $__icon ?></span>
        <span class="truncate"><?= htmlspecialchars($__clean) ?></span>
    </a>
<?php else: ?>
    <a href="uploads/<?= rawurlencode($filename) ?>" target="_blank" rel="noopener"
       class="flex items-center w-full p-4 bg-white border border-slate-200 shadow-sm rounded-xl hover:bg-slate-50 hover:border-teal-300 transition group">
        <span class="w-10 h-10 rounded-lg mr-3 shrink-0 <?= $__icon_bg ?> group-hover:scale-110 transition transform"><?= $__icon ?></span>
        <span class="flex-1 text-left overflow-hidden">
            <span class="block text-sm font-bold text-slate-700 truncate"><?= htmlspecialchars($__clean) ?></span>
            <span class="block text-xs text-slate-400 uppercase">File <?= $__ext ?: 'lampiran' ?> &middot; Klik untuk membuka</span>
        </span>
        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-500 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
    </a>
<?php endif; ?>
