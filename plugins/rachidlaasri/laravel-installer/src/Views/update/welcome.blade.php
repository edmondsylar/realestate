@extends('vendor.installer.layouts.master-update')

@section('title', 'Welcome To The Updater')
@section('container')
    <p class="paragraph text-center">
    	{{ 'Welcome to the update wizard.' }}
    </p>
    <div class="buttons">
        <a href="{{ route('LaravelUpdater::overview') }}" class="button">{{ 'Passo successivo' }}</a>
    </div>
@stop
