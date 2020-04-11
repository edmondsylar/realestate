@include('dashboard.components.header')
<div id="wrapper">
    @include('dashboard.components.top-bar')
    @include('dashboard.components.nav')
    <div class="content-page">
        <div class="content">
            @include('dashboard.components.breadcrumb', ['heading' => __('Package')])
            {{--Start Content--}}
            <div class="card-box">
                <div class="row">
                    <!-- Pricing Title-->
                    <div class="col-lg-12">
                        <h3 class="mb-2">{{__('Our Packages')}}</h3>
                        <p class="text-muted mb-4">
                            {{__('We have plans and prices that fit your business perfectly. Make your client site a success with our products.')}}
                        </p>
                    </div>
                    <!-- Plans -->
                    @if($allPackages['total'] > 0)
                        @foreach($allPackages['results'] as $item)
                            @include('dashboard.components.item-package')
                        @endforeach
                    @else
                        <div class="col-lg-12">
                            <div class="alert alert-warning">
                            {{__('There are no membership package. Please contact to Amin for more detail.')}}
                            </div>
                        </div>
                    @endif
                    <!-- end row -->
                </div>
            </div>
            {{--End content--}}
            @include('dashboard.components.footer-content')
        </div>
    </div>
</div>
@include('dashboard.components.footer')
