<?php
// Badge status pendanaan/klaim (is_funded) terpusat.
// Cara pakai: @include('legacy.partials.funded-badge', ['is_funded' => $row['is_funded'], 'tipe' => 'permintaan'])
// tipe: 'permintaan' -> label "Didanai" / 'penawaran' -> label "Diambil Desa"

$__tipe = $tipe ?? 'permintaan';
$__label_done = $__tipe === 'permintaan' ? 'Sudah Didanai' : 'Sudah Diambil';
?>
<?php if (($is_funded ?? 0) == 1): ?>
    <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wider bg-teal-100 text-teal-700 shadow-sm">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
        <?= $__label_done ?>
    </span>
<?php else: ?>
    <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wider bg-amber-100 text-amber-700 shadow-sm">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Belum Tersalurkan
    </span>
<?php endif; ?>
