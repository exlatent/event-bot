<?php

declare(strict_types=1);

use App\Domain\Rbac\AdminPanelPermission;
use App\Domain\Rbac\CheckAccess;
use App\Web;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
//    Route::get('/')->action(Web\HomePage\Action::class)->name('home'),
    Group::create('/telegram')
        ->routes(
            Route::post('/webhook')->action(Web\Telegram\Webhook\Action::class)->name('telegram:webhook')
        ),
    Group::create()->routes(
        Route::methods(['GET', 'POST'], '/login')
            ->action(Web\Login\Action::class)
            ->name('login'),

        Route::post('/logout')
            ->action(Web\Logout\Action::class)
            ->name('logout')
    ),


    Group::create('/admin')
//        ->middleware(CheckAccess::definition(AdminPanelPermission::PERM_NAME))
        ->routes(
            Group::create('/source')
                ->routes(
                    Route::get('')->action(Web\Admin\Source\Index\Action::class)->name('admin:source:index'),
                    Route::methods(['GET', 'POST'], '/update/{id}')
                        ->action(Web\Admin\Source\Update\Action::class)->name('admin:source:update'),
                    Route::methods(['GET', 'POST'], '/create')
                        ->action(Web\Admin\Source\Create\Action::class)->name('admin:source:create'),
                ),
            Group::create('/message')
                ->routes(
                    Route::methods(['GET', 'POST'],
                        '')->action(Web\Admin\Message\Index\Action::class)->name('admin:message:index'),
                    Route::get('/view/{id}')->action(Web\Admin\Message\View\Action::class)->name('admin:message:view'),
                    Route::methods(['GET', 'POST'], '/update/{id}')
                        ->action(Web\Admin\Message\Update\Action::class)->name('admin:message:update'),
                    Route::post('/delete/{id}')
                        ->action(Web\Admin\Message\Delete\Action::class)->name('admin:message:delete'),
                ),
            Group::create('/event')
                ->routes(
                    Route::get('')->action(Web\Admin\Event\Index\Action::class)->name('admin:event:index'),
                    Route::get('/view/{id}')->action(Web\Admin\Event\View\Action::class)->name('admin:event:view'),
                    Route::methods(['GET', 'POST'], '/update/{id}')
                        ->action(Web\Admin\Event\Update\Action::class)->name('admin:event:update'),
                    Route::methods(['GET', 'POST'], '/create')
                        ->action(Web\Admin\Event\Create\Action::class)->name('admin:event:create'),
                )
        ),
];
