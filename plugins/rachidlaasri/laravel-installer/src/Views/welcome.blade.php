@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ 'Welcome' }}
@endsection

@section('title')
    {{ 'Benvenuto al programma di installazione' }}
@endsection

@section('container')
    <p class="text-center">
      {{ 'Benvenuto alla configurazione guidata.' }}
    </p>
    <p class="text-center">
      <a href="{{ route('LaravelInstaller::requirements') }}" class="button">
        {{ 'Check Requirements' }}
        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
      </a>
    </p>
@endsection
