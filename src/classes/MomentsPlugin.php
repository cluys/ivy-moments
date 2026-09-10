<?php

namespace Cluys\Plugin\Moments;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Ivy\Plugin\Application\Contracts\PluginInterface;
use Ivy\Shared\Presentation\Routing\Route;
use Ivy\Template\Infrastructure\Manager\AssetManager;
use Ivy\User\Application\Service\AuthService;

class MomentsPlugin implements PluginInterface
{
    public function register(AuthService $auth): void
    {
        Route::mount('/moment', function () {
            Route::get('/([a-z0-9_-]+)', '\Moment\MomentTemplate@page')
                ->before('\Ivy\User\Presentation\Controller\AdminController@before');

            Route::post('/insert/(\d+)(/\w+)?(/[a-z0-9_-]+)?', '\Moment\MomentTemplate@insert')
                ->before('\Ivy\User\Presentation\Controller\AdminController@before');

            Route::post('/create', '\Moment\MomentController@create')
                ->before('\Ivy\User\Presentation\Controller\AdminController@before');

            Route::post('/save/(\d+)(/\w+)?(/[a-z0-9_-]+)?', '\Moment\MomentController@save')
                ->before('\Ivy\User\Presentation\Controller\AdminController@before');

            Route::post('/update/(\d+)(/\w+)?(/[a-z0-9_-]+)?', '\Moment\MomentController@update')
                ->before('\Ivy\User\Presentation\Controller\AdminController@before');

            Route::post('/delete/(\d+)(/\w+)?(/[a-z0-9_-]+)?', '\Moment\MomentController@delete')
                ->before('\Ivy\User\Presentation\Controller\AdminController@before');
        });

        // ItemRegistry::register('moment', Moment::class);

        // Add the admin JS if required.
        // AssetManager::addJS('plugins/moment/js/add_moment_admin.js');
    }

    public function install(): void
    {
        Capsule::schema()->create('moments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->string('title', 255)->nullable();
            $table->integer('token')->nullable();

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
        });

        Capsule::schema()->table('profiles', function (Blueprint $table) {
            $table->date('birthday')->nullable();
        });

//        $existing = (new Tag)
//            ->where('value', 'Moment')
//            ->fetchOne();
//
//        if (! $existing) {
//            (new Tag)
//                ->populate(['value' => 'Moment'])
//                ->insert();
//        }
    }

    public function uninstall(): void
    {
        Capsule::schema()->table('profiles', function (Blueprint $table) {
            $table->dropColumn('birthday');
        });

        Capsule::schema()->dropIfExists('moments');
    }
}
