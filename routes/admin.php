<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Info\FaqCategoryController;
use App\Http\Controllers\Admin\Info\FaqController;
use App\Http\Controllers\Admin\Info\KnowledgeCategoryController;
use App\Http\Controllers\Admin\Info\KnowledgeArticleController;
use App\Http\Controllers\Admin\Info\UsefullController;
use App\Http\Controllers\Admin\Info\TimelineController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProxyController;
use App\Http\Controllers\Admin\Payment\PlanController;
use App\Http\Controllers\Admin\Payment\CouponController;
use App\Http\Controllers\Admin\Payment\SubscriptionController;
use App\Http\Controllers\Admin\Payment\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ThemeEditorController;

Route::group(['prefix' => 'admin', 'middleware' => ['auth.admin']], function () {
    Route::get('/', function() {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::group(['prefix' => 'info'], function () {
        Route::resource('/faqCategory', FaqCategoryController::class);
        Route::resource('/faq', FaqController::class);
        Route::resource('/knowledgeCategory', KnowledgeCategoryController::class);
        Route::resource('/knowledgeArticle', KnowledgeArticleController::class);
        Route::resource('/usefull', UsefullController::class);
        Route::resource('/timeline', TimelineController::class);
    });

    Route::resource('/tickets', TicketController::class)->names([
        'index'     => 'admin.tickets.index',
        'create'    => 'admin.tickets.create',
        'store'     => 'admin.tickets.store',
        'edit'      => 'admin.tickets.edit',
        'update'    => 'admin.tickets.update',
    ])->except(['show', 'destroy']);
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])
        ->name('admin.tickets.close');

    Route::get('/setting', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/update', [SettingsController::class, 'update'])->name('admin.settings.update');


    Route::resource('/proxies', ProxyController::class)->except(['show', 'create']);
    Route::group(['prefix' => 'proxies'], function () {
        Route::post('/{proxy}/verify', [ProxyController::class, 'verify'])->name('proxies.verify');
        Route::post('/verify/bulk', [ProxyController::class, 'bulkVerify'])->name('proxies.verify.bulk');
        Route::post('/verify/all', [ProxyController::class, 'verifyAll'])->name('proxies.verify.all');
        Route::delete('/destroy/bulk', [ProxyController::class, 'bulkDestroy'])
            ->name('proxies.destroy.bulk');
        Route::post('/import', [ProxyController::class, 'import'])->name('proxies.import');
        Route::get('/export', [ProxyController::class, 'export'])->name('proxies.export');
        Route::get('/export/all', [ProxyController::class, 'exportAll'])->name('proxies.export.all');
        Route::post('/cheker/url', [ProxyController::class, 'checkerUrl'])
            ->name('proxies.checker.update');
        Route::put('/{proxy}/type', [ProxyController::class, 'updateType'])->name('proxies.type.update');
    });
    

    Route::resource('/plans', PlanController::class);
    Route::resource('/coupons', CouponController::class);
    Route::resource('/subscriptions', SubscriptionController::class)
        ->only(['index']);

    Route::resource('/orders', OrderController::class);
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/{user}/history', [OrderController::class, 'orderHistory'])
            ->name('orders.history');
        Route::post('/{order}/refund', [OrderController::class, 'refund'])
            ->name('orders.refund');
        Route::post('/{order}/refund-cancel', [OrderController::class, 'refundAndCancel'])
            ->name('orders.refund.cancel');
    });

    Route::resource('/users', UserController::class)->except(['create', 'show', 'update']);
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/export', [UserController::class, 'export'])->name('users.bulk.export');
        Route::get('/export/all', [UserController::class, 'exportAll'])->name('users.export.all');
        Route::delete('/destroy/bulk', [UserController::class, 'bulkDestroy'])
            ->name('users.destroy.bulk');
        Route::post('/{user}/notes/save', [UserController::class, 'notesSave'])
            ->name('users.notes.save');
        Route::put('/{user}/profile/update', [UserController::class, 'profileUpdate'])
            ->name('users.profile.update');
        Route::put('/{user}/bussiness/update', [UserController::class, 'bussinessUpdate'])
            ->name('users.bussiness.update');
        Route::delete('/{order}/{proxy}/remove', [UserController::class, 'removeProxy'])
            ->name('users.order.proxy.remove');
        Route::put('/{order}/{proxy}/reasign', [UserController::class, 'reasignProxies'])
            ->name('users.order.proxy.reasign');
        Route::post('/{order}/add', [UserController::class, 'addProxy'])->name('users.order.proxy.add');
        Route::post('/{user}/tickets/new', [UserController::class, 'createTicket'])
            ->name('users.tickets.create');
        Route::post('/{user}/test-proxy/add', [UserController::class, 'addTestProxy'])
            ->name('users.proxy.test.add');
        Route::delete('/{user}/test-proxy/{proxy}/remove', [UserController::class, 'removeTestProxy'])
            ->name('users.proxy.test.remove');
    });

    Route::group(['prefix' => 'theme-editor'], function () {
        Route::get('/', [ThemeEditorController::class, 'index'])->name('theme.editor.index');
        Route::get('/edit', [ThemeEditorController::class, 'edit'])->name('theme.editor.edit');
        Route::put('/', [ThemeEditorController::class, 'update'])->name('theme.editor.update');
    });
});
