<?php
$user = Sentinel::getUser();
?>
@if($user->inRole('administrator'))
    @include('dashboard.screens.administrator.home')
@elseif($user->inRole('partner'))
    @include('dashboard.screens.partner.home')
@else
    @include('dashboard.screens.customer.home')
@endif;
