@extends($activeTemplate . 'layouts.frontend')
@section('content')

    @php
        $content = getContent('register.content', true);
    @endphp
    @if (gs('registration'))
        <section class="account-section padding-bottom padding-top">
            <div class="container">
                <div class="account-wrapper">
                    <div class="login-area account-area">
                        <div class="row m-0">
                            <div class="col-lg-5 p-0">
                                <div class="change-catagory-area bg_img"
                                    data-background="{{ frontendImage('register', @$content->data_values->background_image, '450x970') }}">
                                    <h4 class="title">@lang('Welcome To') {{ __(gs('site_name')) }}</h4>
                                    <p>@lang('Already have an account?')</p>
                                    <a class="custom-button account-control-button" href="{{ route('user.login') }}">@lang('Login')</a>
                                </div>
                            </div>
                            <div class="col-lg-7 p-0">
                                <div class="common-form-style bg-one create-account">
                                    <h4 class="title">{{ __(@$content->data_values->heading) }}</h4>
                                    <p class="mb-sm-4 mb-3">{{ __(@$content->data_values->short_details) }}</p>

                                    <form class="create-account-form row verify-gcaptcha" method="post" action="{{ route('user.register') }}">
                                        @csrf
                                        @if ($refUser == null)
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">@lang('Referral Username')</label>
                                                    <input class="referral ref_id" name="referral" type="text" value="{{ old('referral') }}"
                                                        autocomplete="off" required>
                                                    <div id="ref"></div>
                                                    <span id="referral"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">@lang('Position')</label>
                                                    <select class="position select2" id="position" name="position" data-minimum-results-for-search="-1"
                                                        required disabled>
                                                        <option value="">@lang('Select position')*</option>
                                                        @foreach (mlmPositions() as $k => $v)
                                                            <option value="{{ $k }}">{{ __($v) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span id="position-test"><span class="text--danger"></span></span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">@lang('Referral Username')</label>
                                                    <input class="referral" name="referral" type="text" value="{{ $refUser->username }}" required
                                                        readonly>
                                                </div>
                                                <input name="referrer_id" type="hidden" value="{{ $refUser->id }}">
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">@lang('Position')</label>
                                                    <select class="position" id="position" required disabled>
                                                        <option value="">@lang('Select position')*</option>
                                                        @foreach (mlmPositions() as $k => $v)
                                                            <option value="{{ $k }}" @if ($pos == $k) selected @endif>
                                                                {{ __($v) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input name="position" type="hidden" value="{{ $pos }}">
                                                    <strong class='text--success'>@lang('Your are joining under') {{ $joining }}
                                                        @lang('at')
                                                        {{ $position }} </strong>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('First Name')</label>
                                                <input name="firstname" type="text" value="{{ old('firstname') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Last Name')</label>
                                                <input name="lastname" type="text" value="{{ old('lastname') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Email')</label>
                                                <input class="checkUser" name="email" type="email" value="{{ old('email') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Password')</label>
                                                <input class="@if (gs('secure_password')) secure-password @endif" name="password" type="password"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Confirm password')</label>
                                                <input name="password_confirmation" type="password" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <x-captcha isCustom="true" />
                                        </div>

                                        @if (gs('agree'))
                                            @php
                                                $policyPages = getContent('policy_pages.element', false, orderById: true);
                                            @endphp
                                            <div class="col-md-12 mb-3">
                                                <div class="form--check">
                                                    <input class="form-check-input" id="agree" name="agree" type="checkbox"
                                                        @checked(old('agree')) required>
                                                    <label class="form-check-label" for="agree">
                                                        @lang('I agree with')
                                                    </label>
                                                    <span class="ms-1">
                                                        @foreach ($policyPages as $policy)
                                                            <a href="{{ route('policy.pages', $policy->slug) }}"
                                                                target="_blank">{{ __($policy->data_values->title) }}</a>
                                                            @if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    </span>
                                                </div>

                                            </div>
                                        @endif
                                        <div class="form-group col-md-12">
                                            <input class="w-100" type="submit" value="@lang('Create an Account')">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="existModalCenter" role="dialog" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('You are with us')</h5>
                        <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5 class="h5 text-center">@lang('You already have an account please Login ')</h5>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-dark h-auto w-auto" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                        <a class="btn btn--base" href="{{ route('user.login') }}">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        @include($activeTemplate . 'partials.registration_disabled')
    @endif

@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

@push('script')
    <script>
        "use strict";
        (function($) {

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                var data = {
                    email: value,
                    _token: token
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });

            var not_select_msg = $('#position-test').html();

            var positionDetails = null;

            $('.ref_id').on('focusout', function() {

                var ref_id = $('.ref_id').val();

                if (ref_id) {
                    var token = "{{ csrf_token() }}";
                    $.ajax({
                        type: "POST",
                        url: "{{ route('check.referral') }}",
                        data: {
                            'ref_id': ref_id,
                            '_token': token
                        },
                        success: function(data) {
                            if (data.success) {
                                $('select[name=position]').removeAttr('disabled');
                                $('#position-test').text('');
                                $("#ref").html(
                                    `<span class="help-block"><strong class="text--success">@lang('Referrer username matched')</strong></span>`
                                );
                            } else {
                                $('select[name=position]').attr('disabled', true);
                                $('#position-test').html(not_select_msg);
                                $("#ref").html(
                                    `<span class="help-block"><strong class="text--danger">@lang('Referrer username not found')</strong></span>`
                                );
                            }
                            positionDetails = data;
                            updateHand();
                        }
                    });
                } else {
                    $("#position-test").html(
                        `<span class="help-block"><strong class="text--danger">@lang('Enter referral username first')</strong></span>`
                    );
                }
            });

            $('#position').on('change', function() {
                updateHand();
            });

            function updateHand() {
                var pos = $('#position').val(),
                    className = null,
                    text = null;
                if (pos && positionDetails.success == true) {
                    className = 'text--success';
                    text =
                        `<span class="help-block"><strong class="text--success">Your are joining under ${positionDetails.position[pos]} at ${pos==1?'left':'right'} </strong></span>`;
                } else {
                    className = 'text--danger';
                    if (positionDetails.success == true) text = `@lang('Select your position')`;
                    else if ($('.ref_id').val()) text = `@lang('Please enter a valid referral username')`;
                    else text = `@lang('Enter referral username first')`;

                }
                $("#position-test").html(
                    `<span class="help-block"><strong class="${className}">${text}</strong></span>`)
            }
            @if (old('position'))
                $(`select[name=position]`).val('{{ old('position') }}');
                $(`select[name=referral]`).change();
            @endif

        })(jQuery);
    </script>
@endpush
