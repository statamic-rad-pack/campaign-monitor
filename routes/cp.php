<?php

use Illuminate\Support\Facades\Route;
use StatamicRadPack\CampaignMonitor\Http\Controllers\GetFormFieldsController;
use StatamicRadPack\CampaignMonitor\Http\Controllers\GetCustomFieldsController;
use StatamicRadPack\CampaignMonitor\Http\Controllers\GetUserFieldsController;

Route::name('campaign-monitor.')->prefix('campaign-monitor')->group(function () {
    Route::get('form-fields/{form}', [GetFormFieldsController::class, '__invoke'])->name('form-fields');
    Route::get('custom-fields/{list}', [GetCustomFieldsController::class, '__invoke'])->name('custom-fields');
    Route::get('user-fields', [GetUserFieldsController::class, '__invoke'])->name('user-fields');
});
