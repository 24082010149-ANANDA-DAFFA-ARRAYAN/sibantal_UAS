<?php
// Empty state dengan ikon, konsisten di seluruh halaman.
// Cara pakai: @include('legacy.partials.empty-state', ['title' => '...', 'subtitle' => '...', 'icon' => 'inbox'])
// icon: 'inbox' | 'search' | 'handshake' | 'document'

$__icons = [
    'inbox' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7.5L7.5 3h9L21 7.5M3 7.5V18a2 2 0 002 2h14a2 2 0 002-2V7.5M3 7.5h18M8.25 12h7.5"></path>',
    'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16zM9 11h4"></path>',
    'handshake' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 16l-2.5-2.5a1.5 1.5 0 00-2.121 0l-.879.879a1.5 1.5 0 000 2.121L8 19m3-3l3.5 3.5a1.5 1.5 0 002.121 0l.879-.879a1.5 1.5 0 000-2.121L14 13m-3 3l3-3m-7.5-1.5L3 8m18 0l-3.5 3.5M9.5 9.5L13 6"></path>',
    'document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
];
$__icon = $__icons[$icon ?? 'inbox'] ?? $__icons['inbox'];
?>
<div class="col-span-full flex flex-col items-center justify-center text-center py-16 px-6 bg-white/60 backdrop-blur-sm rounded-2xl border border-dashed border-slate-300">
    <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $__icon ?></svg>
    </div>
    <p class="font-bold text-slate-700 mb-1"><?= $title ?? 'Belum ada data' ?></p>
    <?php if (!empty($subtitle)): ?>
        <p class="text-sm text-slate-500 max-w-sm"><?= $subtitle ?></p>
    <?php endif; ?>
</div>
