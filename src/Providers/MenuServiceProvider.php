<?php

namespace Bozboz\Menus\Providers;

use Bozboz\Menus\Http\Composers\Menu;
use Bozboz\Menus\Items\Item;
use Bozboz\Menus\Repository;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('menus', function ($app) {
            return $this->app[Repository::class];
        });
    }

    public function boot()
    {
        $packageRoot = __DIR__ . '/../..';

        $this->loadViewsFrom("$packageRoot/resources/views", 'menus');

        $this->publishes([
            "$packageRoot/database/migrations" => database_path('migrations')
        ]);

        $this->buildAdminMenu();

        $this->registerPermissions();

        require "$packageRoot/helpers.php";

        if (! $this->app->routesAreCached()) {
            require "$packageRoot/src/Http/routes.php";
        }

        Blade::directive('menu', function($expression) {
            return "<?php echo menu{$expression}; ?>";
        });

        Item::saved(function($item) {
            if ($item->menu) {
                $menuAlias = $item->menu->alias;
                $this->app['menus']->clearCache($menuAlias);
            }
        });
        Item::deleted(function($item) {
            if ($item->menu) {
                $menuAlias = $item->menu->alias;
                $this->app['menus']->clearCache($menuAlias);
            }
        });
    }

    protected function buildAdminMenu()
    {
        $this->app['events']->listen('admin.renderMenu', function($menu)
        {
            $url = $this->app['url'];

            if ($menu->gate('view_menus')) {
                $menu->appendToItem('Config', ['Navigation Menus' => 'admin.menus.index']);
            }
        });
    }

    protected function registerPermissions()
    {
        $this->app['permission.handler']->define([

            'view_menus' => 'Bozboz\Permissions\Rules\ModelRule',
            'create_menus' => 'Bozboz\Permissions\Rules\ModelRule',
            'delete_menus' => 'Bozboz\Permissions\Rules\ModelRule',
            'edit_menus' => 'Bozboz\Permissions\Rules\ModelRule',

            'view_menu_items' => 'Bozboz\Permissions\Rules\ModelRule',
            'create_menu_items' => 'Bozboz\Permissions\Rules\ModelRule',
            'delete_menu_items' => 'Bozboz\Permissions\Rules\ModelRule',
            'edit_menu_items' => 'Bozboz\Permissions\Rules\ModelRule',

        ]);
    }
}