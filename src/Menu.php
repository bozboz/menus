<?php

namespace Bozboz\Menus;

use Bozboz\Admin\Base\DynamicSlugTrait;
use Bozboz\Admin\Base\Model;
use Bozboz\Menus\Items\Item;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'alias',
    ];

    use DynamicSlugTrait;

    public function getSlugSourceField()
    {
        return 'alias';
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}