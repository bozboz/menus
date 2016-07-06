<?php

namespace Bozboz\Menus\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Route;
use Bozboz\Menus\Menu;
use Bozboz\Menus\MenuDecorator;
use Bozboz\Menus\Repository;
use Illuminate\Support\Facades\Redirect;

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

    public function clearCache(Repository $repo)
    {
        Menu::all()->each(function($menu) use ($repo) {
            $repo->clearCache($menu->alias);
        });

        return Redirect::back()->with('model.updated', 'Successfully cleared cache');
    }

    protected function getReportActions()
    {
        return array_merge(parent::getReportActions(), [
            $this->actions->custom(
                new Link(
                    $this->getActionName('clearCache'),
                    'Clear Cache',
                    'fa fa-recycle',
                    ['class' => 'btn-warning pull-right']
                ),
                new IsValid([$this->items, 'canEdit'])
            ),
        ]);
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
                new IsValid([$this->items, 'canView'])
            ),
        ], parent::getRowActions());
    }
}