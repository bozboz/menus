<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin', 'namespace' => 'Bozboz\Menus\Http\Controllers\Admin'], function() {
    Route::resource('menus', 'MenuController');
    Route::resource('menu-items', 'ItemController', ['except' => ['show', 'create']]);
    Route::get('menu-items/{menu}/create', 'ItemController@createForMenu');
});