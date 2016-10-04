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
        return $this->cache->remember($this->getCacheKey($alias), 60, function() use ($alias) {

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

                if ( ! $menuItem) {
                    return $item;
                }

                if ($item->include_children) {
                    $query = $menuItem->descendants()->active()->with('template')->withCanonicalPath();

                    if ($item->max_depth) {
                        $query->withDepth()->having('depth', '<=', $item->max_depth);
                    }
                    $item->children = $query->get()->filter(function($child) {
                        return $child->template->type()->isVisible();
                    })->map(function($item) {
                        $item->url = '/' . $item->canonical_path;
                        return $item;
                    });

                    $sortBy = $menuItem->template->type()->getEntity()->sortBy();

                    $item->children = $item->children->sortBy($sortBy)->toTree();
                }

                if ($item->descendant_field) {
                    $menuItem->injectValues();
                    $item->children = $menuItem->{$item->descendant_field};
                }

                return $item;
            })->toTree()->map(function($item) {
                return $this->generateMenuItem($item);
            });

            return $items;
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