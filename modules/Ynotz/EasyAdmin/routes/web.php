<?php
use Illuminate\Support\Facades\Route;
use Ynotz\EasyAdmin\Http\Controllers\DashboardController;
use Ynotz\EasyAdmin\Http\Controllers\MasterController;

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin'], function () {
    // Route::get('dashboard', [config('dashboard_controller'), config('dashboard_method')])->name('dashboard');
    Route::get('eaasyadmin/fetch/{service}/{method}', [MasterController::class, 'fetch'])->name('easyadmin.fetch');
});
?>

