@include('dashboard.components.header')
<div class="account-pages hh-dashboard mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-pattern">
                    <div class="card-body p-4 hh-relative">
                        <form id="hh-login-form" class="form form-sm form-action" action="{{ url('auth/login') }}"
                              data-reload-time="1500"
                              method="post">
                            @include('common.loading')
                            <div class="text-center m-auto">
                                <a href="{{ dashboard_url() }}">
                                    <span>
                                        @php
                                            $logo = get_option('logo');
                                            $logo_url = get_attachment_url($logo, [80, 80]);
                                        @endphp
                                        <img src="{{ $logo_url }}" alt="Logo">
                                    </span>
                                </a>
                                <p class="text-muted mb-4 mt-3">{{__('Enter your account to access dashboard.')}}</p>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">{{__('Email address')}}</label>
                                <input class="form-control has-validation" data-validation="required" type="text"
                                       id="email" name="email" placeholder="{{__('Enter your email')}}">
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">{{__('Password')}}</label>
                                <input class="form-control has-validation" data-validation="required|min:6:ms"
                                       type="password" id="password" name="password"
                                       placeholder="{{__('Enter your password')}}">
                            </div>
                            <div class="form-group mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="checkbox-signin" checked>
                                    <label class="custom-control-label" for="checkbox-signin">{{__('Remember me')}}</label>
                                </div>
                            </div>
                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-primary btn-block text-uppercase" type="submit"> {{__('Log In')}}</button>
                            </div>
                            <div class="form-message">

                            </div>
                            <div class="text-center">
                                <p class="mt-3 text-muted">{{__('Log in with')}}</p>
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
                        </form>
                    </div> <!-- end card-body -->
                </div>
                <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p><a href="{{ url('auth/reset-password') }}" class="text-white-50 ml-1">{{__('Forgot your password?')}}</a></p>
                        <p class="text-white-50">{{__("Don't have an account?")}} <a href="{{ url('auth/sign-up') }}"
                                                                           class="text-white ml-1"><b>{{__('Sign Up')}}</b></a>
                        </p>
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
