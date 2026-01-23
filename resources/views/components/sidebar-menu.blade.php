@props(['menus'])

<div class="menu">
    <div class="main-menu">
        <div class="scroll">
            <ul class="list-unstyled">
                @foreach ($menus as $menu)
                    <li class="{{ request()->is(trim($menu['url'], '/') . '*') ? 'active' : '' }}">
                        <a href="{{ $menu['has_children'] ? '#' . $menu['slug'] : url($menu['url']) }}">
                            <i class="{{ $menu['icon'] ?? 'iconsminds-shop-4' }}"></i>
                            <span>{{ $menu['name'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="sub-menu">
        <div class="scroll">
            @foreach ($menus as $menu)
                @if ($menu['has_children'])
                    <ul class="list-unstyled" data-link="{{ $menu['slug'] }}">
                        @foreach ($menu['children'] as $child)
                            @if (!empty($child['children']))
                                {{-- Submenu with children --}}
                                <li>
                                    <a href="#" data-toggle="collapse"
                                        data-target="#collapse{{ Str::slug($child['name']) }}" aria-expanded="false"
                                        aria-controls="collapse{{ Str::slug($child['name']) }}"
                                        class="rotate-arrow-icon collapsed">
                                        <i class="simple-icon-arrow-down"></i>
                                        <span class="d-inline-block">{{ $child['name'] }}</span>
                                    </a>
                                    <div id="collapse{{ Str::slug($child['name']) }}" class="collapse">
                                        <ul class="list-unstyled inner-level-menu">
                                            @foreach ($child['children'] as $subChild)
                                                <li>
                                                    <a href="{{ url($subChild['url']) }}">
                                                        <i class="{{ $subChild['icon'] ?? 'simple-icon-layers' }}"></i>
                                                        <span class="d-inline-block">{{ $subChild['name'] }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @else
                                {{-- Single menu item --}}
                                <li>
                                    <a href="{{ url($child['url']) }}">
                                        <i class="{{ $child['icon'] ?? 'simple-icon-layers' }}"></i>
                                        <span class="d-inline-block">{{ $child['name'] }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            @endforeach
        </div>
    </div>
</div>
