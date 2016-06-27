<?php

namespace Bozboz\Menus\Providers;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        $packageRoot = __DIR__ . '/../..';

        $this->loadViewsFrom("$packageRoot/resources/views", 'menus');

        $this->publishes([
            "$packageRoot/database/migrations" => database_path('migrations')
        ]);

        // $this->registerEntityTypes();

        $this->buildAdminMenu();

        $permissions = $this->app['permission.handler'];

        require "$packageRoot/permissions.php";

        if (! $this->app->routesAreCached()) {
            require "$packageRoot/src/Http/routes.php";
        }
    }

    protected function buildAdminMenu()
    {
        $this->app['events']->listen('admin.renderMenu', function($menu)
        {
            $url = $this->app['url'];

            if ($menu->gate('view_menus')) {
                $menu['Menus'] = $url->route('admin.menus.index');
            }
        });
    }
}