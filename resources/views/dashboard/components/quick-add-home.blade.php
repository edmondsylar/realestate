@php
    enqueue_script('nice-select-js');
    enqueue_style('nice-select-css');

    enqueue_script('flatpickr-js');
    enqueue_style('flatpickr-css');
@endphp
<a class="btn btn-success waves-effect waves-light" href="{{ dashboard_url('add-new-home') }}">
    <i class="ti-plus mr-1"></i>
   {{__('Create New')}}
</a>