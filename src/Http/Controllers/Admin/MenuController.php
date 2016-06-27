<?php

namespace Bozboz\Menus\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Route;
use Bozboz\Menus\Menu;
use Bozboz\Menus\MenuDecorator;

class MenuController extends ModelAdminController
{
    protected $useActions = true;
    private $items;

    public function __construct(MenuDecorator $decorator, ItemController $items)
    {
        parent::__construct($decorator);
        $this->items = $items;
    }

    public function show($id)
    {
        $menu = Menu::findOrFail($id);
        return $this->items->indexFormMenu($menu);
    }

    protected function getRowActions()
    {
        return array_merge([
            $this->actions->custom(
                new Link(
                    $this->getActionName('show'),
                    'View Items',
                    'fa fa-list',
                    ['class' => 'btn-default']
                ),
                new IsValid([$this, 'canView'])
            ),
        ], parent::getRowActions());
    }
}