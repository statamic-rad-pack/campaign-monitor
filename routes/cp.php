<?php

use Illuminate\Support\Facades\Route;
use StatamicRadPack\CampaignMonitor\Http\Controllers;

Route::name('campaign-monitor.')->prefix('campaign-monitor')->group(function () {
    Route::get('config', [Controllers\ConfigController::class, 'edit'])->name('config.edit');
    Route::post('config', [Controllers\ConfigController::class, 'update'])->name('config.update');

    Route::get('form-fields/{form}', [Controllers\GetFormFieldsController::class, '__invoke'])->name('form-fields');
    Route::get('custom-fields/{list}', [Controllers\GetCustomFieldsController::class, '__invoke'])->name('custom-fields');
    Route::get('user-fields', [Controllers\GetUserFieldsController::class, '__invoke'])->name('user-fields');
});
