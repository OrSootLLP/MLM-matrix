@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mb-4">
                    <h3 class="dashboard-title">@lang('Add User') <i class="fas fa-question-circle text-muted text--small"
                            data-bs-toggle="tooltip" data-bs-placement="top"></i></h3>
                </div>
                <div class="card custom--card">
                    <div class="card-body">
                        <form class="register" method="post" action="{{ route('user.add.user') }}">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Referral Username')</label>
                                        <input class="form-control form--control referral ref_id" name="referral" type="text" value="{{ auth()->user()->username }}"
                                            autocomplete="off" required>
                                        <div id="ref"></div>
                                        <span id="referral"></span>
                                    </div>
                                </div>                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('First Name')</label>
                                        <input class="form-control form--control" name="firstname" type="text" value="{{ old('firstname') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Last Name')</label>
                                        <input class="form-control form--control" name="lastname" type="text" value="{{ old('lastname') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Email')</label>
                                        <input class="form-control form--control" name="email" type="email" value="{{ old('email') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Password')</label>
                                        <input class="form-control form--control" name="password" type="password"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Confirm password')</label>
                                        <input class="form-control form--control" name="password_confirmation" type="password" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> @lang('Plans')</label>
                                        <select class="select2 form-control" name="plan" required>
                                            <option value="" selected disabled>Select Plan</option>
                                            @foreach ($plans as $key => $plan)
                                                <option value="{{ $plan->id }}">{{ __($plan->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                                            
                                <div class="form-group col-md-12">
                                    <input class="btn btn--base w-100" type="submit" name="doSubmit" value="@lang('Add User')">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .form-group {
            margin-bottom: 10px;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

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
            
        })(jQuery);
    </script>
@endpush