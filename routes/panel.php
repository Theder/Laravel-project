<?php

use App\Http\Controllers\Panel\PanelController;
use App\Http\Controllers\Panel\ProfileController;
use App\Http\Controllers\Panel\Info\FaqController;
use App\Http\Controllers\Panel\Info\KnowledgeController;
use App\Http\Controllers\Panel\Info\UsefullController;
use App\Http\Controllers\Panel\Info\TimelineController;
use App\Http\Controllers\Panel\Contact\TicketController;
use App\Http\Controllers\Panel\Contact\ContactFormController;
use App\Http\Controllers\Panel\Payment\PlanController;
use App\Http\Controllers\Panel\Payment\SubscriptionController;
use App\Http\Controllers\Panel\Payment\InvoiceController;
use App\Http\Controllers\Panel\Proxy\ProxyListController;
use App\Http\Controllers\Panel\Proyx\ProxyManagerController;
use App\Http\Middleware\InvoiceAccess;

Route::group(['prefix' => 'panel', 'middleware' => ['auth']], function () {
    Route::get('/', function() {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [PanelController::class, 'index'])->name('dashboard');
    Route::get('/welcome', [PanelController::class, 'index'])->name('welcome');

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        Route::get('/settings', [ProfileController::class, 'edit'])->name('profile.settings');
        Route::put('/settings', [ProfileController::class, 'update'])->name('profile.settings.update');
    });

    Route::get('/faq', [FaqController::class, 'private'])->name('faq.private');
    Route::get('/knowledge', [KnowledgeController::class, 'private'])->name('knowledge.private');
    Route::get('/knowledge/{knowledgeCategory:slug}', [KnowledgeController::class, 'privateCategory'])
        ->name('knowledge.private.category');
    Route::get('/knowledge/{knowledgeCategory:slug}/{knowledgeArticle:slug}', [KnowledgeController::class, 'privateArticle'])
        ->name('knowledge.private.article');
    Route::get('/usefull', [UsefullController::class, 'usefull'])->name('usefull');
    Route::post('/timeline/update', [TimelineController::class, 'timelineUpdate'])->name('timeline.read');

    Route::resource('/tickets', TicketController::class)->names([
        'index'     => 'panel.tickets.index',
        'store'     => 'panel.tickets.store',
        'show'      => 'panel.tickets.show',
        'update'    => 'panel.tickets.update'
    ])->except(['create', 'edit', 'destroy']);

    Route::get('/contact', [ContactFormController::class, 'formPrivate'])->name('contact.form.private');
    Route::post('/contact', [ContactFormController::class, 'sendPrivate'])->name('contact.send.private');

    Route::group(['prefix' => 'plans'], function () {
        Route::get('/', [PlanController::class, 'index'])->name('plans.list');
        Route::post('/{plan}/activate', [PlanController::class, 'activate'])->name('plans.activate');
        Route::get('/{plan}/activate/success', [PlanController::class, 'success'])
            ->name('plans.activate.success');
    });

    Route::post('/subscription/cansel', [SubscriptionController::class, 'cancel'])
        ->name('subscription.cancel');

    Route::group(['prefix' => 'invoices', 'middleware' => [InvoiceAccess::class]], function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/{invoice}/pdf', [InvoiceController::class, 'download'])->name('invoices.download');
    });

    Route::group(['prefix' => 'proxy-list'], function () {
        Route::get('/', [ProxyListController::class, 'index'])->name('proxy.list');
        Route::put('/{proxy}/note/edit', [ProxyListController::class, 'noteEdit'])
            ->name('proxy.list.note.edit');
        Route::get('/{proxy}/download', [ProxyListController::class, 'download'])
            ->name('proxy.list.download');
        Route::get('/{proxy}/show', [ProxyListController::class, 'show'])->name('proxy.list.show');
        Route::get('/export', [ProxyListController::class, 'export'])->name('proxy.list.export');
        Route::post('/{proxy}/verify', [ProxyListController::class, 'verify'])
            ->name('proxy.list.verify');
    });

    Route::get('/proxy-manager', [ProxyManagerController::class, 'index'])->name('panel.proxy.manager');
});