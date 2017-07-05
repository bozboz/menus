<?php

use Illuminate\Support\HtmlString;

if (!function_exists('menu')) {
    function menu($alias, $view = null, $params = [])
    {
        $params['depth'] = 1;
        return new HtmlString(view($view ?: 'menus::menu', $params)->withMenu(
            app('menus')->getMenu($alias)
        )->render());
    }
}
