@if ($menu && $menu->count())
    <ul class="{{ $className or 'menu' }}__list {{ $className or 'menu' }}__list--depth-{{ $depth }}">
        @foreach ($menu as $item)
            @include('menus::item')
        @endforeach
    </ul>
@endif