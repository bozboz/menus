<li>
    <a href="{{ url($item->url) }}">{{ $item->name }}</a>
    @include('menus::menu', ['menu' => $item->children])
</li>