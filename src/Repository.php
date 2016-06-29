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

            $items = $menu->items->map(function($item) {
                $menuItem = $item->entity;

                if ($menuItem && $item->include_children) {
                    $query = $menuItem->descendants()->active()->with('template')->withCanonicalPath();

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
            })->toTree();

            return $items;
        return $this->cache->remember($this->getCacheKey($alias), 60, function() use ($alias) {

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
}