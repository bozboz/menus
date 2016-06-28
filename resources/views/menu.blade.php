@if ($menu && $menu->count())
    <ul>
        @each ('menus::item', $menu, 'item')
    </ul>
@endif