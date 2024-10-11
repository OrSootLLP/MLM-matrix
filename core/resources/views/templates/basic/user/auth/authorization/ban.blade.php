@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $banned = @getContent('banned.content', true);
    @endphp
    <div class="banned-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-8 col-12 text-center">
                    <div class="ban-section">
                        <h4 class="text-center text--danger mb-4">
                            {{ __(@$banned->data_values->heading) }}
                        </h4>
                        <img alt="@lang('Ban Image')" src="{{ getImage('assets/images/frontend/banned/' . @$banned->data_values->image) }}">
                        <div class="mt-4">
                            <p class="fw-bold mb-2">@lang('Reason')</p>
                            <p>{{ __($user->ban_reason) }}</p>
                        </div>
                        <a class="btn btn--base mt-4" href="{{ route('home') }}">
                            <i class="las la-undo"></i>
                            @lang('Browse '){{ gs('site_name') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
         header,footer,.page-header,.header-top{
            display: none;
        }
        .banned-section {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
@endpush
