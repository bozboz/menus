<?php

namespace Bozboz\Menus\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\NestedReport;
use Bozboz\Menus\Items\ItemDecorator;
use Bozboz\Menus\Menu;
use Illuminate\Support\Facades\Redirect;

class ItemController extends ModelAdminController
{
    protected $useActions = true;
    private $menu;

    public function __construct(ItemDecorator $decorator)
    {
        parent::__construct($decorator);
    }

    protected function getListingReport()
    {
        return new NestedReport($this->decorator);
    }

    public function indexFormMenu(Menu $menu)
    {
        if ( ! $this->canView()) App::abort(403);

        $this->menu = $menu;

        $report = $this->getListingReport();

        $report->injectValues(['menu' => $menu->id]);

        $report->setReportActions($this->getReportActions());
        $report->setRowActions($this->getRowActions());

        return $report->render();
    }

    public function createforMenu(Menu $menu)
    {
        $instance = $this->decorator->newModelInstance();

        if ( ! $this->canCreate($instance)) App::abort(403);

        $instance->menu()->associate($menu);

        return $this->renderFormFor($instance, $this->createView, 'POST', 'store');
    }

    public function getReportActions()
    {
        return [
            $this->actions->create(
                [$this->getActionName('createForMenu'), $this->menu->id],
                [$this, 'canCreate'],
                'New',
                ['class' => 'space-left pull-right btn-success']
            ),
            $this->actions->custom(
                new Link(
                    '\Bozboz\Menus\Http\Controllers\Admin\MenuController@index',
                    'Back to menus',
                    'fa fa-list',
                    ['class' => 'pull-right btn-default']
                ),
                new IsValid([$this, 'canView'])
            ),
        ];
    }

    protected function getSuccessResponse($instance)
    {
        return Redirect::route('admin.menus.show', $instance->menu->id);
    }

    protected function getListingUrl($instance)
    {
        return action($this->getListingAction($instance), $instance->menu->id);
    }

    protected function getListingAction($instance)
    {
        return '\Bozboz\Menus\Http\Controllers\Admin\MenuController@show';
    }

    protected function viewPermissions($stack)
    {
        $stack->add('view_menu_items');
    }

    protected function editPermissions($stack, $instance)
    {
        $stack->add('edit_menu_items', $instance);
    }

    protected function createPermissions($stack, $instance)
    {
        $stack->add('create_menu_items', $instance);
    }

    protected function deletePermissions($stack, $instance)
    {
        $stack->add('delete_menu_items', $instance);
    }
}