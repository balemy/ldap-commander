<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\EntityController;
use App\Controller\SchemaController;
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
            Route::methods([Method::GET, Method::POST], '/entity/edit')->action([EntityController::class, 'edit'])->name('entity-edit'),
            Route::get('/entity/browse')->action([EntityController::class, 'list'])->name('entity-list'),
            Route::get('/entity/delete')->action([EntityController::class, 'delete'])->name('entity-delete'),
            Route::get('/entity/download-binattr')->action([EntityController::class, 'downloadBinaryAttribute'])->name('entity-attribute-download'),
            Route::get('/entity/rename')->action([EntityController::class, 'rename'])->name('entity-rename'),

            Route::get('/schema')->action([SchemaController::class, 'index'])->name('schema'),
            Route::get('/schema/object-class')->action([SchemaController::class, 'displayObjectClass'])->name('schema-objectclass'),
            Route::get('/schema/attribute')->action([SchemaController::class, 'displayAttribute'])->name('schema-attribute'),
        ),
    Route::methods([Method::GET, Method::POST], '/login')->action([AuthController::class, 'login'])->name('login'),
    Route::methods([Method::GET, Method::POST], '/logout')->action([AuthController::class, 'logout'])->name('logout'),
];
