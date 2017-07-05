<li class="{{ $className or 'menu' }}__item">
    <a href="{{ url($item->url) }}"
       class="
            {{ $className or 'menu' }}__link
            {{starts_with(trim(request()->path(), '/'), trim($item->url, '/')) || request()->path() === $item->url ? 'active' : ''}}
            {{$item->children && count($item->children) ? 'has-children' : ''}}
       "
    >
       {{ $item->name }}
    </a>
    @include('menus::menu', ['menu' => $item->children, 'depth' => ++$depth])
</li>
