<?php

namespace Bozboz\Menus;

use Bozboz\Menus\Menu;
use Illuminate\Contracts\Cache\Repository as CacheContract;

class Repository
{
    private $cache;

    public function __construct(CacheContract $cache)
    {
        $this->cache = $cache;
    }

    public function getMenu($alias)
    {
        return $this->cache->remember($this->getCacheKey($alias), 10, function() use ($alias) {
            $menu = Menu::with(['items.entity.template', 'items.entity.paths' => function($query) {
                $query->whereNull('canonical_id');
            }])->whereAlias($alias)->first();

            $items = $menu->items->map(function($item) {
                $menuItem = $item->entity;

                $item->name = $menuItem->name;
                $item->url = url($menuItem->canonical_path);

                if ($item->include_children) {
                    $query = $menuItem->descendants()->with('template')->withCanonicalPath();

                    if ($item->max_depth) {
                        $query->withDepth()->having('depth', '<=', $item->max_depth);
                    }

                    $item->children = $query->get()->filter(function($item) {
                        return $item->template->type()->isVisible();
                    })->transform(function($item) {
                        $item->url = url($item->canonical_path);
                        return $item;
                    })->toTree();
                }

                return $item;
            })/*->toTree()*/;

            return $items;
        });
    }

    protected function getCacheKey($alias)
    {
        return "Bozboz\Menus|$alias";
    }
}