<?php

namespace Bozboz\Menus\Items;

use Bozboz\Admin\Base\Model;
use Bozboz\Menus\Menu;

class Item extends Model
{
    protected $table = 'menu_items';

    protected $fillable = ['menu_id'];

    public function menuable()
    {
        return $this->morphTo();
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}