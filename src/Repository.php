<?php

namespace Bozboz\Menus;

use Bozboz\Menus\Items\Item;
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
        return $this->cache->remember($this->getCacheKey($alias), 60, function() use ($alias)
        {
            $menu = Menu::with([
                'items' => function($query) {
                    $query->orderBy('_lft');
                },
                'items.entity.paths' => function($query) {
                    $query->whereNull('canonical_id');
                },
                'items.entity.template',
            ])->whereAlias($alias)->first();

            if (! $menu) {
                return collect();
            }

            return $menu->items->each(function($item) {
                $item->loadChildren();
            })->toTree()->map(function($item) {
                return $this->generateMenuItem($item);
            });
        });
    }

    public function clearCache($menuAlias)
    {
        $this->cache->forget($this->getCacheKey($menuAlias));
        $this->getMenu($menuAlias);
    }

    protected function getCacheKey($alias)
    {
        return "Bozboz\Menus|$alias";
    }

    protected function generateMenuItem($item)
    {
        return (object)[
            'name' => $item->name,
            'url' => $item->url,
            'children' => $item->children->map(function($child) {
                return $this->generateMenuItem($child);
            }),
        ];
    }
}