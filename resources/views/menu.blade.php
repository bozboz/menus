@if ($menu && $menu->count())
    <ul class="{{ $className or 'menu' }}__list {{ $className or 'menu' }}__list--depth-{{ $depth }} secret-list">
        @foreach ($menu as $item)
            @include('menus::item')
        @endforeach
    </ul>
@endif
