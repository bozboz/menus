<?php

namespace Bozboz\Menus\Items;

use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TreeSelectField;
use Bozboz\Admin\Reports\Filters\HiddenFilter;
use Bozboz\Admin\Reports\Filters\RelationFilter;
use Bozboz\Jam\Entities\Entity;
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
        return $instance->name;
    }

    public function getColumns($instance)
    {
        return [
            'Name' => $instance->name,
            'URL' => link_to($instance->url),
        ];
    }

    public function getFields($instance)
    {
        return [
            new HiddenField('menu_id'),
            new HiddenField('menuable_type', Entity::class),
            new TreeSelectField(
                $this->entityOptions(),
                [
                    'name' => 'entity_id',
                    'class' => 'select2'
                ]
            ),
            new CheckboxField('include_children'),
            new TextField('max_depth'),
            new TextField('override_name'),
            new TextField('override_url'),
        ];
    }

    private function entityOptions()
    {
        return Entity::active()->orderBy('_lft')->with('template')->get()->filter(function($entity) {
            return $entity->template->type()->isVisible();
        });
    }

    public function getListingFilters()
    {
        return [
            new HiddenFilter(new RelationFilter($this->model->menu(), $this->menus))
        ];
    }
}