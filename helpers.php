<?php

use Illuminate\Support\HtmlString;

if (!function_exists('menu')) {
    function menu($alias, $view = 'menus::menu', $params = [])
    {
        $params['depth'] = 1;
        return new HtmlString(view($view, $params)->withMenu(
            app('menus')->getMenu($alias)
        )->render());
    }
}
