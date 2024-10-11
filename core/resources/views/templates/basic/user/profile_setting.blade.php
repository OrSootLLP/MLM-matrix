@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="mb-4">
            <h3 class="mb-2">@lang('Profile')</h3>
        </div>
        <div class="card custom--card">
            <div class="card-body">
                <form class="register" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-4">
                        <div class="text-center">
                            <x-image-uploader id="image" name="image" type="userProfile" image="{{ $user->image }}" :required=false />
                        </div>
                        <div class="form-group col-sm-6">
                            <label class="form-label">@lang('First Name')</label>
                            <input class="form-control form--control" name="firstname" type="text" value="{{ $user->firstname }}" required>
                        </div>
                        <div class="form-group col-sm-6">
                            <label class="form-label">@lang('Last Name')</label>
                            <input class="form-control form--control" name="lastname" type="text" value="{{ $user->lastname }}" required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label class="form-label">@lang('E-mail Address')</label>
                            <input class="form-control form--control" value="{{ $user->email }}" readonly>
                        </div>
                        <div class="form-group col-sm-6">
                            <label class="form-label">@lang('Mobile Number')</label>
                            <input class="form-control form--control" value="{{ $user->mobile }}" readonly>
                        </div>

                        <div class="form-group col-sm-6">
                            <label class="form-label">@lang('Address')</label>
                            <input class="form-control form--control" name="address" type="text" value="{{ @$user->address }}">
                        </div>
                        <div class="form-group col-sm-6">
                            <label class="form-label">@lang('State')</label>
                            <input class="form-control form--control" name="state" type="text" value="{{ @$user->state }}">
                        </div>

                        <div class="form-group col-sm-4 py-2">
                            <label class="form-label">@lang('Zip Code')</label>
                            <input class="form-control form--control" name="zip" type="text" value="{{ @$user->zip }}">
                        </div>

                        <div class="form-group col-sm-4 py-2">
                            <label class="form-label">@lang('City')</label>
                            <input class="form-control form--control" name="city" type="text" value="{{ @$user->city }}">
                        </div>

                        <div class="form-group col-sm-4 py-2">
                            <label class="form-label">@lang('Country')</label>
                            <input class="form-control form--control" value="{{ @$user->country_name }}" disabled>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>

        </div>
    @endsection

    @push('script')
        <script>
            (function($) {
                "use strict";

                function proPicURL(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var preview = $(input).closest('.image-upload-wrapper').find('.image-upload-preview');
                            $(preview).css('background-image', 'url(' + e.target.result + ')');
                            $(preview).addClass('has-image');
                            $(preview).hide();
                            $(preview).fadeIn(650);
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                $(".image-upload-input").on('change', function() {
                    proPicURL(this);
                });
                $(".remove-image").on('click', function() {
                    $(this).parents(".image-upload-preview").css('background-image', 'none');
                    $(this).parents(".image-upload-preview").removeClass('has-image');
                    $(this).parents(".image-upload-wrapper").find('input[type=file]').val('');
                });

            })(jQuery);
        </script>
    @endpush

    @push('style')
        <style>
            .form-group {
                margin-bottom: 0;
            }

            .image-upload-wrapper {
                height: 150px !important;
                width: 150px !important;
                margin: 0 auto;
            }

            .image-upload-input-wrapper label {
                margin-bottom: -8px;
                height: 35px;
                width: 35px;
                font-size: 15px;
            }
        </style>
    @endpush
