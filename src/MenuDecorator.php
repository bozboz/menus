<?php

namespace Bozboz\Menus;

use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Fields\TextField;
use Illuminate\Database\Eloquent\Builder;

class MenuDecorator extends ModelAdminDecorator
{
    public function __construct(Menu $model)
    {
        parent::__construct($model);
    }

    public function getLabel($instance)
    {
        return $instance->name;
    }

    public function getFields($instance)
    {
        return [
            new TextField('name'),
            new TextField('alias'),
        ];
    }

    public function modifyListingQuery(Builder $query)
    {
        $query->orderBy('name');
    }
}
