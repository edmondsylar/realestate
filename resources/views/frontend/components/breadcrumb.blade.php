<nav aria-label="breadcrumb">
    @if(isset($inContainer))
        <div class="container">
            @endif
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <a href="{{ url('/') }}">{{__('Home')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{ $currentPage }}</li>
            </ol>
            @if(isset($inContainer))
        </div>
    @endif
</nav>
