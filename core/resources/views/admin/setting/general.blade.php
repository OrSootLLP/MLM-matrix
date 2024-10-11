@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label> @lang('Site Title')</label>
                                    <input class="form-control" name="site_name" type="text" value="{{ gs('site_name') }}" required>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-sm-6">
                                <label> @lang('Timezone')</label>
                                <select class="select2 form-control" name="timezone">
                                    @foreach ($timezones as $key => $timezone)
                                        <option value="{{ @$key }}" @selected(@$key == $currentTimezone)>{{ __($timezone) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Currency')</label>
                                    <input class="form-control" name="cur_text" type="text" value="{{ gs('cur_text') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Currency Symbol')</label>
                                    <input class="form-control" name="cur_sym" type="text" value="{{ gs('cur_sym') }}" required>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-6">
                                <label> @lang('Site Base Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 p-0">
                                        <input class="form-control colorPicker" type='text' value="{{ gs('base_color') }}">
                                    </span>
                                    <input class="form-control colorCode" name="base_color" type="text" value="{{ gs('base_color') }}">
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-sm-6">
                                <label> @lang('Record to Display Per page')</label>
                                <select class="select2 form-control" name="paginate_number" data-minimum-results-for-search="-1">
                                    <option value="20" @selected(gs('paginate_number') == 20)>@lang('20 items per page')</option>
                                    <option value="50" @selected(gs('paginate_number') == 50)>@lang('50 items per page')</option>
                                    <option value="100" @selected(gs('paginate_number') == 100)>@lang('100 items per page')</option>
                                </select>
                            </div>

                            <div class="form-group col-lg-4 col-sm-6">
                                <label> @lang('Currency Showing Format')</label>
                                <select class="select2 form-control" name="currency_format" data-minimum-results-for-search="-1">
                                    <option value="1" @selected(gs('currency_format') == Status::CUR_BOTH)>@lang('Show Currency Text and Symbol Both')</option>
                                    <option value="2" @selected(gs('currency_format') == Status::CUR_TEXT)>@lang('Show Currency Text Only')</option>
                                    <option value="3" @selected(gs('currency_format') == Status::CUR_SYM)>@lang('Show Currency Symbol Only')</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Balance Transfer Fixed Charge')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="balance_transfer_fixed_charge" type="number"
                                            value="{{ getAmount(gs('balance_transfer_fixed_charge')) }}" step="any">
                                        <div class="input-group-text">
                                            {{ __(gs('cur_text')) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Balance Transfer Percent Charge')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="balance_transfer_per_charge" type="number"
                                            value="{{ getAmount(gs('balance_transfer_per_charge')) }}" step="any">
                                        <div class="input-group-text">%</div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Balance Transfer Minimum Limit')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="balance_transfer_min" type="number"
                                            value="{{ getAmount(gs('balance_transfer_min')) }}" step="any">
                                        <div class="input-group-text">
                                            {{ __(gs('cur_text')) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Balance Transfer Maximum Limit')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="balance_transfer_max" type="number"
                                            value="{{ getAmount(gs('balance_transfer_max')) }}" step="any">
                                        <div class="input-group-text">
                                            {{ __(gs('cur_text')) }}
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label for="epin_charge">@lang('E-Pin Generate Charge')</label>
                                    <div class="input-group">
                                        <input class="form-control form-control-lg" id="epin_charge" name="epin_charge" type="text"
                                            value="{{ getAmount(gs('epin_charge')) }}" required="">
                                        <div class="input-group-text">%</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/spectrum.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";


            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function(color) {
                    $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function() {
                var clr = $(this).val();
                $(this).parents('.input-group').find('.colorPicker').spectrum({
                    color: clr,
                });
            });
        })(jQuery);
    </script>
@endpush
