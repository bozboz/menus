<?php

namespace Bozboz\Menus\Providers;

use Bozboz\Menus\Http\Composers\Menu;
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

        $permissions = $this->app['permission.handler'];

        require "$packageRoot/permissions.php";
        require "$packageRoot/helpers.php";

        if (! $this->app->routesAreCached()) {
            require "$packageRoot/src/Http/routes.php";
        }

        Blade::directive('menu', function($expression) {
            return "<?php echo menu($expression); ?>";
        });
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