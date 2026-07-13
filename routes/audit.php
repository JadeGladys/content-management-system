<?php

use App\Http\Controllers\Audit\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/audit', [AuditLogController::class, 'index'])
        ->name('audit.index');
    
    Route::post('/admin/audit/{auditLog}/restore', [AuditLogController::class, 'restore'])
        ->name('audit.restore');
});
