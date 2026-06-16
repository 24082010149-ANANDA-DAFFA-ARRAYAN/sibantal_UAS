<?php
// Navigasi pagination generik.
// Required : $current_page, $total_pages
// Optional : $page_param  (nama query param, default 'page')
//            $base_params (array GET params lain yang dipertahankan, default $_GET)

if (!function_exists('si_bantal_page_url')) {
    function si_bantal_page_url(array $base, string $param, int $page): string
    {
        $params = $base;
        $params[$param] = $page;
        return '?' . http_build_query($params);
    }
}

$page_param   = $page_param ?? 'page';
$base_params  = $base_params ?? $_GET;
$total_pages  = max(1, (int) ($total_pages ?? 1));
$current_page = min($total_pages, max(1, (int) ($current_page ?? 1)));

$__nav_btn   = 'min-w-[2.25rem] h-9 px-3 inline-flex items-center justify-center rounded-lg text-sm font-bold transition';
$__active    = 'bg-teal-500 text-white shadow-md shadow-teal-500/30';
$__inactive  = 'bg-white text-slate-600 border border-slate-200 hover:bg-teal-50 hover:border-teal-300';
$__disabled  = 'bg-slate-100 text-slate-300 cursor-not-allowed border border-slate-200';

// Window halaman: 1 ... (cur-1) cur (cur+1) ... last
$__window = [];
for ($p = max(1, $current_page - 1); $p <= min($total_pages, $current_page + 1); $p++) {
    $__window[] = $p;
}
?>
<?php if ($total_pages > 1): ?>
<nav class="flex items-center justify-center gap-1.5 mt-8 flex-wrap" aria-label="Pagination">
    <?php if ($current_page > 1): ?>
        <a href="<?= si_bantal_page_url($base_params, $page_param, $current_page - 1) ?>" class="<?= $__nav_btn ?> <?= $__inactive ?>">&lsaquo;</a>
    <?php else: ?>
        <span class="<?= $__nav_btn ?> <?= $__disabled ?>">&lsaquo;</span>
    <?php endif; ?>

    <?php if (!in_array(1, $__window)): ?>
        <a href="<?= si_bantal_page_url($base_params, $page_param, 1) ?>" class="<?= $__nav_btn ?> <?= $__inactive ?>">1</a>
        <span class="px-1 text-slate-400">&hellip;</span>
    <?php endif; ?>

    <?php foreach ($__window as $p): ?>
        <?php if ($p === $current_page): ?>
            <span class="<?= $__nav_btn ?> <?= $__active ?>"><?= $p ?></span>
        <?php else: ?>
            <a href="<?= si_bantal_page_url($base_params, $page_param, $p) ?>" class="<?= $__nav_btn ?> <?= $__inactive ?>"><?= $p ?></a>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!in_array($total_pages, $__window)): ?>
        <span class="px-1 text-slate-400">&hellip;</span>
        <a href="<?= si_bantal_page_url($base_params, $page_param, $total_pages) ?>" class="<?= $__nav_btn ?> <?= $__inactive ?>"><?= $total_pages ?></a>
    <?php endif; ?>

    <?php if ($current_page < $total_pages): ?>
        <a href="<?= si_bantal_page_url($base_params, $page_param, $current_page + 1) ?>" class="<?= $__nav_btn ?> <?= $__inactive ?>">&rsaquo;</a>
    <?php else: ?>
        <span class="<?= $__nav_btn ?> <?= $__disabled ?>">&rsaquo;</span>
    <?php endif; ?>
</nav>
<?php endif; ?>
