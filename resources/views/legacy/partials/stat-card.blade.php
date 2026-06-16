<?php
// Kartu ringkasan statistik.
// Required : $value, $label
// Optional : $icon  ('list'|'check'|'clock'|'cross'|'send'|'handshake'|'home'|'users')
//            $color ('teal'|'amber'|'green'|'red'|'slate')  default 'teal'

$__icons = [
    'list'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>',
    'check'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'clock'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'cross'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>',
    'send'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>',
    'handshake' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>',
    'home'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>',
    'users'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>',
];

$__colors = [
    'teal'  => ['bg' => 'bg-teal-50',  'icon' => 'text-teal-500',  'border' => 'border-teal-500',  'value' => 'text-teal-600'],
    'amber' => ['bg' => 'bg-amber-50', 'icon' => 'text-amber-500', 'border' => 'border-amber-500', 'value' => 'text-amber-600'],
    'green' => ['bg' => 'bg-green-50', 'icon' => 'text-green-500', 'border' => 'border-green-500', 'value' => 'text-green-600'],
    'red'   => ['bg' => 'bg-red-50',   'icon' => 'text-red-500',   'border' => 'border-red-500',   'value' => 'text-red-600'],
    'slate' => ['bg' => 'bg-slate-100','icon' => 'text-slate-500', 'border' => 'border-slate-400', 'value' => 'text-slate-700'],
];

$__c = $__colors[$color ?? 'teal'] ?? $__colors['teal'];
$__svg = $__icons[$icon ?? 'list'] ?? $__icons['list'];
?>
<div class="bg-white rounded-2xl shadow-md border-t-4 <?= $__c['border'] ?> p-5 flex items-center gap-4 hover:shadow-lg transition duration-300">
    <div class="w-12 h-12 rounded-xl <?= $__c['bg'] ?> <?= $__c['icon'] ?> flex items-center justify-center shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $__svg ?></svg>
    </div>
    <div class="min-w-0">
        <p class="text-2xl font-extrabold <?= $__c['value'] ?> leading-tight"><?= $value ?></p>
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide truncate"><?= $label ?></p>
    </div>
</div>
