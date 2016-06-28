<?php

if (!function_exists('menu')) {
    function menu($alias, $view = 'menus::menu')
    {
        try {
            echo view($view)->withMenu(
                app('menus')->getMenu($alias)
            )->render();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
