<?php

declare(strict_types=1);

use Balemy\LdapCommander\Controller\SiteController;
use Balemy\LdapCommander\Modules\EntityBrowser\EntityController;
use Balemy\LdapCommander\Modules\GroupManager\GroupController;
use Balemy\LdapCommander\Modules\SchemaBrowser\SchemaController;
use Balemy\LdapCommander\Modules\ServerConfig\ReferentialIntegrity\Controller as RefIntController;
use Balemy\LdapCommander\Modules\Session\AuthController;
use Balemy\LdapCommander\Modules\Session\SessionLoaderMiddleware;
use Balemy\LdapCommander\Modules\SlapdConfig\Controllers\AccessControlController;
use Balemy\LdapCommander\Modules\SlapdConfig\Controllers\BindUserController;
use Balemy\LdapCommander\Modules\UserManager\UserController;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Route::get('/')->action([SiteController::class, 'index'])->name('home'),
    Group::create()
        ->middleware(SessionLoaderMiddleware::class)
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

            Route::get('/server')->action([\Balemy\LdapCommander\Modules\ServerInfo\ServerController::class, 'index'])->name('server'),

            Route::get('/users')->action([UserController::class, 'list'])->name('user-list'),
            Route::methods([Method::GET, Method::POST], '/user/edit')->action([UserController::class, 'edit'])->name('user-edit'),
            Route::methods([Method::GET, Method::POST], '/user/delete')->action([UserController::class, 'delete'])->name('user-delete'),

            Route::get('/groups')->action([GroupController::class, 'list'])->name('group-list'),
            Route::methods([Method::GET, Method::POST], '/group/edit')->action([GroupController::class, 'edit'])->name('group-edit'),
            Route::methods([Method::GET, Method::POST], '/group/members')->action([GroupController::class, 'members'])->name('group-members'),
            Route::methods([Method::GET, Method::POST], '/group/delete')->action([GroupController::class, 'delete'])->name('group-delete'),

            Route::methods([Method::GET, Method::POST], '/server-config/refint/edit')->action([RefIntController::class, 'edit'])->name('server-config-refint-edit'),
            Route::methods([Method::GET, Method::POST], '/server-config/memberof/edit')->action([\Balemy\LdapCommander\Modules\ServerConfig\MemberOf\Controller::class, 'edit'])->name('server-config-memberof-edit'),

            Route::methods([Method::GET, Method::POST], '/raw-query')->action([Balemy\LdapCommander\Modules\RawQuery\RawQueryController::class, 'index'])->name('raw-query'),

            Route::get('/bind-users')->action([BindUserController::class, 'list'])->name('bind-user-list'),
            Route::methods([Method::GET, Method::POST], '/bind-user/edit')->action([BindUserController::class, 'edit'])->name('bind-user-edit'),
            Route::methods([Method::GET, Method::POST], '/bind-user/delete')->action([BindUserController::class, 'delete'])->name('bind-user-delete'),

        ),
    Group::create()
        ->middleware(SessionLoaderMiddleware::class)
        ->middleware(\Balemy\LdapCommander\LDAP\Middlewares\ConfigConnectionMiddleware::class)
        ->routes(
            Route::methods([Method::GET, Method::POST], '/access-control')->action([AccessControlController::class, 'index'])->name('access-control'),
            Route::methods([Method::GET, Method::POST], '/module-config')->action([\Balemy\LdapCommander\Modules\SlapdConfig\Controllers\ModuleConfigController::class, 'index'])->name('module-config'),
            Route::methods([Method::GET, Method::POST], '/module-config/memberOf')->action([\Balemy\LdapCommander\Modules\SlapdConfig\Controllers\ModuleConfigController::class, 'memberOf'])->name('module-config-memberof'),
        ),
        Route::methods([Method::GET, Method::POST], '/login')->action([AuthController::class, 'login'])->name('login'),
    Route::methods([Method::GET, Method::POST], '/logout')->action([AuthController::class, 'logout'])->name('logout'),
];
