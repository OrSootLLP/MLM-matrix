@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="mb-4">
                    <h3>@lang('Withdraw Via') {{ $withdraw->method->name }}</h3>
                </div>
                <div class="card custom--card">
                    <div class="card-body">
                        <div class="alert alert--primary">
                            <p class="mb-0"><i class="las la-info-circle"></i> @lang('You are requesting')
                                <b>{{ showAmount($withdraw->amount) }}</b> @lang('for withdraw.') @lang('The admin will send you')
                                <b class="text--success">{{ showAmount($withdraw->final_amount, currencyFormat: false) . ' ' . $withdraw->currency }}
                                </b> @lang('to your account.')
                            </p>
                        </div>
                        <form class="disableSubmission" action="{{ route('user.withdraw.submit') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-2">
                                @php
                                    echo $withdraw->method->description;
                                @endphp
                            </div>
                            <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}" />
                            @if (auth()->user()->ts)
                                <div class="form-group">
                                    <label>@lang('Google Authenticator Code')</label>
                                    <input class="form-control form--control" name="authenticator_code" type="text"
                                        required>
                                </div>
                            @endif
                            <div class="form-group">
                                <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
