@extends('vendor.installer.layouts.master')

@section('template_title')
    Step 3 | Environment Settings | Classic Editor
@endsection

@section('title')
    <i class="fa fa-code fa-fw" aria-hidden="true"></i> Classic Environment Editor
@endsection

@section('container')

    <form method="post" action="{{ route('LaravelInstaller::environmentSaveClassic') }}">
        {!! csrf_field() !!}
        <textarea class="textarea" name="envConfig">{{ $envConfig }}</textarea>
        <a class="button float-right" href="{{ route('LaravelInstaller::database') }}">
            <i class="fa fa-check fa-fw" aria-hidden="true"></i>
            Save and Install
            <i class="fa fa-angle-double-right fa-fw" aria-hidden="true"></i>
        </a>
        <div class="buttons buttons--right">
            <button class="button button--light" type="submit">
            	<i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>
             	{!! trans('installer_messages.environment.classic.save') !!}
            </button>
        </div>
    </form>

    @if( ! isset($environment['errors']))
        <div class="buttons-container">
            <a class="button float-left" href="{{ route('LaravelInstaller::environmentWizard') }}">
                <i class="fa fa-sliders fa-fw" aria-hidden="true"></i>
                Use Form Wizard
            </a>

        </div>
    @endif

@endsection