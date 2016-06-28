<?php

namespace Bozboz\Menus\Items;

use Bozboz\Admin\Base\Model;
use Bozboz\Admin\Base\Sorting\NestedSortableTrait;
use Bozboz\Admin\Base\Sorting\Sortable;
use Bozboz\Jam\Entities\Entity;
use Bozboz\Menus\Menu;
use Kalnoy\Nestedset\NodeTrait;

class Item extends Model implements Sortable
{
    protected $table = 'menu_items';

    protected $fillable = [
        'menu_id',
        'entity_id',
        'name',
        'url',
        'override_name',
        'override_url',
        'include_children',
        'max_depth',
    ];

    protected $nullable = [
        'entity_id',
        'name',
        'url',
        'max_depth',
    ];

    use NodeTrait, NestedSortableTrait;

    public function sortBy()
    {
        return '_lft';
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function getNameAttribute()
    {
        if (array_key_exists('name', $this->attributes) && $this->attributes['name'] != '') {
            return $this->attributes['name'];
        } else if ($this->entity) {
            return $this->entity->name;
        }
    }

    public function getUrlAttribute()
    {
        if (array_key_exists('url', $this->attributes) && $this->attributes['url'] != '') {
            return $this->attributes['url'];
        } else if ($this->entity) {
            return url($this->entity->canonical_path ?: '/');
        }
    }

    public function setOverrideNameAttribute($name)
    {
        $this->attributes['name'] = $name;
    }

    public function setOverrideUrlAttribute($url)
    {
        $this->attributes['url'] = $url;
    }

    public function getOverrideNameAttribute($name)
    {
        if (array_key_exists('name', $this->attributes)) {
            return $this->attributes['name'];
        }
    }

    public function getOverrideUrlAttribute($url)
    {
        if (array_key_exists('url', $this->attributes)) {
            return $this->attributes['url'];
        }
    }
}