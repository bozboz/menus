<?php

if (!function_exists('menu')) {
    function menu($alias, $view = 'menus::menu')
    {
        return view($view)->withMenu(
            app('menus')->getMenu($alias)
        );
    }
}
