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
            Route::get('/entity/move')->action([EntityController::class, 'move'])->name('entity-move'),

            Route::get('/schema')->action([SchemaController::class, 'index'])->name('schema'),
            Route::get('/schema/object-class')->action([SchemaController::class, 'displayObjectClass'])->name('schema-objectclass'),
            Route::get('/schema/attribute')->action([SchemaController::class, 'displayAttribute'])->name('schema-attribute'),

            Route::get('/server')->action([\App\Controller\ServerController::class, 'index'])->name('server'),

            Route::get('/users')->action([\App\Controller\UserController::class, 'list'])->name('user-list'),
            Route::methods([Method::GET, Method::POST], '/user/edit')->action([\App\Controller\UserController::class, 'edit'])->name('user-edit'),

            Route::get('/groups')->action([\App\Controller\GroupController::class, 'list'])->name('group-list'),
            Route::methods([Method::GET, Method::POST], '/group/add')->action([\App\Controller\GroupController::class, 'add'])->name('group-add'),
            Route::methods([Method::GET, Method::POST], '/group/edit')->action([\App\Controller\GroupController::class, 'edit'])->name('group-edit'),
            Route::methods([Method::GET, Method::POST], '/group/members')->action([\App\Controller\GroupController::class, 'members'])->name('group-members'),
            Route::methods([Method::GET, Method::POST], '/group/delete')->action([\App\Controller\GroupController::class, 'delete'])->name('group-delete'),
        ),
    Route::methods([Method::GET, Method::POST], '/login')->action([AuthController::class, 'login'])->name('login'),
    Route::methods([Method::GET, Method::POST], '/logout')->action([AuthController::class, 'logout'])->name('logout'),
];
