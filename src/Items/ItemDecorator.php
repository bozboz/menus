<?php

namespace Bozboz\Menus\Items;

use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Reports\Filters\HiddenFilter;
use Bozboz\Admin\Reports\Filters\RelationFilter;
use Bozboz\Menus\MenuDecorator;

class ItemDecorator extends ModelAdminDecorator
{
    private $menus;

    public function __construct(Item $model, MenuDecorator $menus)
    {
        parent::__construct($model);
        $this->menus = $menus;
    }

    public function getLabel($instance)
    {
        return $instance->menuabble_id . ' - ' . $instance->menuable_type;
    }

    public function getFields($instance)
    {
        return [
            new HiddenField('menu_id'),
        ];
    }

    public function getListingFilters()
    {
        return [
            new HiddenFilter(new RelationFilter($this->model->menu(), $this->menus))
        ];
    }
}