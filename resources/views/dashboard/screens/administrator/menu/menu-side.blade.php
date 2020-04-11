<h4 class="header-title mb-0">{{__('Add menu item')}}</h4>
@php
    $items = ['page', 'post', 'home', 'link', 'category', 'tag', 'home_type'];
@endphp
@foreach($items as $item)
    @include('dashboard.screens.administrator.menu.item-' . $item)
@endforeach