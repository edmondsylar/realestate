@include('dashboard.components.header')
<div class="account-pages hh-dashboard mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-pattern">
                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <a href="{{ dashboard_url() }}">
                                    <span>
                                        @php
                                            $logo = get_option('logo');
                                            $logo_url = get_attachment_url($logo, [80, 80]);
                                        @endphp
                                        <img src="{{ $logo_url }}" alt="Logo">
                                    </span>
                            </a>
                            <p class="text-muted mb-4 mt-3">{{__("Don't have an account? Create your account, it takes less than a minute")}}</p>
                        </div>

                        <form id="hh-sign-up-form" action="{{ url('auth/sign-up') }}" method="post" data-reload-time="1500"
                              class="form form-action">
                            @include('common.loading')
                            <div class="form-group">
                                <label for="first-name">{{__('First Name')}}</label>
                                <input class="form-control" type="text" id="first-name" name="first_name"  placeholder="{{__('First Name')}}">
                            </div>
                            <div class="form-group">
                                <label for="last-name">{{__('First Name')}}</label>
                                <input class="form-control" type="text" id="last-name" name="last_name" placeholder="{{__('Last Name')}}">
                            </div>
                            <div class="form-group">
                                <label for="email-address">{{__('Email address')}}</label>
                                <input class="form-control has-validation" data-validation="required|email" type="email"
                                       id="email-address" name="email" placeholder="{{__('Email')}}">
                            </div>
                            <div class="form-group">
                                <label for="password">{{__('Password')}}</label>
                                <input class="form-control has-validation" data-validation="required|min:6:ms"
                                       name="password" type="password" id="password" placeholder="{{__('Password')}}">
                            </div>
                            <div class="form-group">
                                <div class="checkbox checkbox-success">
                                    <input type="checkbox" id="term-condition" name="term_condition" value="1">
                                    <label for="term-condition">{{__('I accept')}} <a href="javascript: void(0);" class="text-dark">{{__('Terms and Conditions')}}</a></label>
                                </div>
                            </div>
                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-primary btn-block text-uppercase" type="submit"> Sign Up</button>
                            </div>
                            <div class="form-message">

                            </div>
                        </form>
                        <div class="text-center">
                            <h5 class="mt-3 text-muted">{{__('Sign up using')}}</h5>
                            <ul class="social-list list-inline mt-3 mb-0">
                                @if(social_enable('facebook'))
                                    <li class="list-inline-item">
                                        <a href="{{ FacebookLogin::get_inst()->getLoginUrl() }}"
                                           class="social-list-item border-primary text-primary"><i
                                                    class="mdi mdi-facebook"></i></a>
                                    </li>
                                @endif
                                @if(social_enable('google'))
                                    <li class="list-inline-item">
                                        <a href="{{ GoogleLogin::get_inst()->getLoginUrl() }}"
                                           class="social-list-item border-danger text-danger"><i
                                                    class="mdi mdi-google"></i></a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div> <!-- end card-body -->
                </div>
                <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-white-50">{{__('Already have account?')}} <a href="{{ url('auth/login') }}" class="text-white ml-1"><b>{{__('Sign In')}}</b></a></p>
                    </div> <!-- end col -->
                </div>
                <!-- end row -->

            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
@include('dashboard.components.footer')
