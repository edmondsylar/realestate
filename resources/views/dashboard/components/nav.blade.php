<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">
    <div class="slimscroll-menu">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
                <?php
                $currentScreen = Request::route()->getName();
                $prefix = Config::get('awebooking.prefix_dashboard');
                $menuItems = get_menu_dashboard();
                ?>
                @if ($menuItems)
                    @foreach ($menuItems as $menu)
                        @if ($menu['type'] == 'heading')
                            <li class="menu-title">{{ __($menu['label']) }}</li>
                        @endif
                        @if ($menu['type'] === 'item')
                            <?php
                            $url = 'javascript:void(0);';
                            $icon = '';
                            $active = ($currentScreen == $menu['route']) ? 'active' : '';
                            if (!empty($menu['icon'])) {
                                $icon = get_icon($menu['icon'], '#555', '20px');
                            }
                            if (!empty($menu['screen'])) {
                                $url = url(Config::get('awebooking.prefix_dashboard') . '/' . $menu['screen']);
                            }
                            ?>
                            <li><a href="{{ $url }}" class="{{ $active }}">{!! $icon !!}
                                    <span>{{ __($menu['label']) }}</span></a>
                            </li>
                        @endif
                        @if ($menu['type'] === 'parent')
                            <?php
                            $icon = '';
                            if (!empty($menu['icon'])) {
                                $icon = get_icon($menu['icon'], '#555', '20px');
                            }
                            ?>
                            <li class="@if(in_array($currentScreen, $menu['route'])) active @endif"><a class="@if(in_array($currentScreen, $menu['route'])) active @endif" aria-expanded="<?php if(in_array($currentScreen, $menu['route'])) echo 'true'; ?>" href="javascript: void(0);">{!! $icon !!}<span>{{ __($menu['label']) }}</span>
                                    <span class="menu-arrow"></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    @foreach ($menu['child'] as $child)
                                        @if ($child['type'] === 'item')
                                            <?php
                                            $url = 'javascript:void(0);';
                                            $icon = '';
                                            $active = ($currentScreen == $child['route']) ? 'active' : '';
                                            if (!empty($child['icon'])) {
                                                $icon = '<i class="' . $child['icon'] . '"></i>';
                                            }
                                            if (!empty($child['screen'])) {
                                                $url = url(Config::get('awebooking.prefix_dashboard') . '/' . $child['screen']);
                                            }
                                            ?>
                                            <li class="{{ $active }}"><a href="{{ $url }}"
                                                                         class="{{ $active }}">{{{ $icon }}}
                                                    <span>{{ __($child['label']) }}</span></a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>

        </div>
        <!-- End Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->
</div>
<!-- Left Sidebar End -->
