# Bozboz\Menus

## Installation

- `composer require bozboz/menus`
- Add `Bozboz\Menus\Providers\MenuServiceProvider::class,` to app.php

## Usage

Click "Menus" in the "Content" menu in the admin and add a menu.
Add items to the menu selecting either an entity or manual text/url
In your view use the menu blade directive like so `@menu(string $alias[, string $view = 'menus::menu'])` where "alias" is the alias of the menu you just created.
By default menus will be rendered using the 'menus::menu' view which will output the menu as a simple nested, unordered list.