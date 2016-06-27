<?php

namespace Bozboz\Menus;

use Bozboz\Admin\Base\DynamicSlugTrait;
use Bozboz\Admin\Base\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
    ];

    use DynamicSlugTrait;

    public function getSlugSourceField()
    {
        return 'alias';
    }

    public function items()
    {
        // return $this->hasMany(Items::class);
    }
}