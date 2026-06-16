<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LegacyController;

Route::match(['get', 'post'], '/', [LegacyController::class, 'index']);
Route::match(['get', 'post'], '/index.php', [LegacyController::class, 'index']);

Route::match(['get', 'post'], '/login.php', [LegacyController::class, 'login']);
Route::match(['get', 'post'], '/register.php', [LegacyController::class, 'register']);
Route::get('/logout.php', [LegacyController::class, 'logout']);

Route::match(['get', 'post'], '/contact.php', [LegacyController::class, 'contact']);
Route::match(['get', 'post'], '/portofolio.php', [LegacyController::class, 'portfolio']);
Route::match(['get', 'post'], '/detail.php', [LegacyController::class, 'detail']);
Route::get('/apply.php', [LegacyController::class, 'apply']);

Route::match(['get', 'post'], '/dashboard-admin.php', [LegacyController::class, 'dashboardAdmin']);
Route::get('/dashboard-desa.php', [LegacyController::class, 'dashboardDesa']);
Route::get('/dashboard-donatur.php', [LegacyController::class, 'dashboardDonatur']);

Route::match(['get', 'post'], '/edit.php', [LegacyController::class, 'edit']);
Route::match(['get', 'post'], '/edit_program.php', [LegacyController::class, 'editProgram']);

Route::get('/about.php', fn() => app(LegacyController::class)->staticPage('about'));
Route::get('/biodata-daffa.php', fn() => app(LegacyController::class)->staticPage('biodata-daffa'));
Route::get('/biodata-sarah.php', fn() => app(LegacyController::class)->staticPage('biodata-sarah'));
Route::get('/biodata-zerlina.php', fn() => app(LegacyController::class)->staticPage('biodata-zerlina'));
