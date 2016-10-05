<li class="{{ $className or 'menu' }}__item">
    <a class="{{ $className or 'menu' }}__link" href="{{ url($item->url) }}">{{ $item->name }}</a>
    @include('menus::menu', ['menu' => $item->children, 'depth' => ++$depth])
</li>