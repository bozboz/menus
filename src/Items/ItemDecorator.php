<?php

namespace Bozboz\Menus\Items;

use Bozboz\Jam\Entities\Entity;
use Bozboz\Menus\MenuDecorator;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Jam\Templates\Template;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\TreeSelectField;
use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Reports\Filters\HiddenFilter;
use Bozboz\Admin\Reports\Filters\RelationFilter;

class ItemDecorator extends ModelAdminDecorator
{
    private $menus;

    public function __construct(Item $model, MenuDecorator $menus)
    {
        parent::__construct($model);
        $this->menus = $menus;
    }

    public function getHeading($plural = false)
    {
        $heading = 'Menu Item';

        return $plural? str_plural($heading) : $heading;
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
                $this->entityOptions($instance),
                [
                    'name' => 'entity_id',
                    'class' => 'select2'
                ]
            ),
            new CheckboxField('include_children'),
            new TextField('descendant_field'),
            new TextField('max_depth'),
            new TextField('override_name'),
            new TextField('override_url'),
        ];
    }

    protected function entityOptions($instance)
    {
        return Entity::active()->orderBy('_lft')
            ->whereIn(
                'template_id',
                Template::whereIn('type_alias', app('EntityMapper')->getAll()->keys())->pluck('id')
            )
            ->with('template')->get()
            ->filter(function($entity) {
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