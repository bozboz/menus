<li>
@if ($item->path)
    <a href="{{ url($item->path) }}">{{ $item->name }}</a>
    @if ($item->children && $item->children->count())
        <ul>
            @each ('menus::item', $item->children, 'item')
        </ul>
    @endif
@endif
</li>