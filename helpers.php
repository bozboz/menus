<?php

if (!function_exists('menu')) {
    function menu($alias, $view = 'menus::menu', $params = [])
    {
        return view($view, $params)->withMenu(
            app('menus')->getMenu($alias)
        )->render();
    }
}
