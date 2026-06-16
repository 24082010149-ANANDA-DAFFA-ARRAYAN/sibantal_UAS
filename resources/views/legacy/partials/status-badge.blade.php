<?php
// Komponen badge status terpusat agar label & warna konsisten
// di seluruh halaman (detail, dashboard admin, dashboard desa/donatur, portofolio).
//
// Cara pakai: @include('legacy.partials.status-badge', ['status' => $row['status']])
// Opsional: ['size' => 'sm'] untuk versi lebih kecil (di dalam tabel)

$__size = ($size ?? 'md') === 'sm' ? 'text-[10px] px-2.5 py-1' : 'text-xs px-3 py-1.5';

$__map = [
    'pending' => [
        'label' => 'Menunggu Verifikasi',
        'class' => 'bg-slate-200 text-slate-700',
        'icon'  => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    ],
    'approved' => [
        'label' => 'Disetujui',
        'class' => 'bg-green-100 text-green-700',
        'icon'  => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>',
    ],
    'rejected' => [
        'label' => 'Ditolak',
        'class' => 'bg-red-100 text-red-700',
        'icon'  => '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>',
    ],
];

$__data = $__map[$status] ?? ['label' => '-', 'class' => 'bg-slate-100 text-slate-500', 'icon' => ''];
?>
<span class="inline-flex items-center gap-1.5 <?= $__size ?> rounded-full font-bold uppercase tracking-wider shadow-sm <?= $__data['class'] ?>">
    <?= $__data['icon'] ?>
    <?= $__data['label'] ?>
</span>
