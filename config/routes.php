<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\EntityController;
use App\Controller\SiteController;
use App\Middleware\LDAPConnect;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Route::get('/')->action([SiteController::class, 'index'])->name('home'),
    Group::create()
        ->middleware(LDAPConnect::class)
        ->routes(
            Route::get('/entity')->action([EntityController::class, 'open'])->name('entity'),
            Route::methods([Method::GET, Method::POST], '/edit')->action([EntityController::class, 'edit'])->name('entity-edit'),
            Route::get('/browse')->action([EntityController::class, 'list'])->name('entity-list'),
            Route::get('/delete')->action([EntityController::class, 'delete'])->name('entity-delete'),
            Route::get('/entity-bin-download')->action([EntityController::class, 'downloadBinaryAttribute'])->name('entity-attribute-download'),
        ),
    Route::methods([Method::GET, Method::POST], '/login')->action([AuthController::class, 'login'])->name('login'),
    Route::methods([Method::GET, Method::POST], '/logout')->action([AuthController::class, 'logout'])->name('logout'),
];
