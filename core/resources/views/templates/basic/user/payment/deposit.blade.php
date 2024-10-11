@extends($activeTemplate . 'layouts.master')

@section('content')
    @php
        $planId = null;
        if ($plan) {
            $planId = $plan->id;
            $amount = $plan->price;
        } else {
            $amount = old('amount');
        }
    @endphp

    <div class="dashboard-inner">

        <div class="mb-4">
            <div class="d-flex justify-content-between mb-3 flex-wrap gap-1 text-end">
                @if ($planId)
                    <h3 class="dashboard-title">@lang('Payment - '){{ __(@$plan->name) }}</h3>
                @else
                    <h3 class="dashboard-title">@lang('Deposit Funds') <i class="fas fa-question-circle text-muted text--small" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="@lang('Add funds using our system\'s gateway. The deposited amount will be credited to the account balance.')"></i></h3>
                @endif
                <a class="btn btn--base btn--smd" href="{{ route('user.deposit.history') }}">@lang('Deposit History')</a>
            </div>
        </div>

        <div class="custom--card card">
            <div class="card-body">
                <form action="{{ @$planId ? route('user.plan.purchase') : route('user.deposit.insert') }}" method="post" class="deposit-form">
                    @csrf
                    <input type="hidden" name="currency">
                    <input type="hidden" name="id" value="{{ @$planId }}">
                    <div class="gateway-card">
                        <div class="row justify-content-center gy-sm-4 gy-3">
                            <div class="col-lg-6">
                                <div class="payment-system-list is-scrollable gateway-option-list">

                                    @if ($plan)
                                        <label for="account_balance" class="payment-item gateway-option">
                                            <div class="payment-item__info">
                                                <span class="payment-item__check"></span>
                                                <span class="payment-item__name">@lang('Account Balance')
                                                    ({{ showAmount(auth()->user()->balance) }})</span>
                                            </div>
                                            <div class="payment-item__thumb">
                                                <img class="payment-item__thumb-img" src="{{ getImage(null, avatar: true) }}" alt="@lang('account-balance')">
                                            </div>
                                            <input class="payment-item__radio gateway-input" id="account_balance" hidden type="radio" name="gateway"
                                                value="wallet" @if (old('gateway')) @checked(old('gateway') == 'wallet') @endif>
                                        </label>
                                    @endif


                                    @foreach ($gatewayCurrency as $data)
                                        <label for="{{ titleToKey($data->name) }}"
                                            class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                            <div class="payment-item__info">
                                                <span class="payment-item__check"></span>
                                                <span class="payment-item__name">{{ __($data->name) }}</span>
                                            </div>
                                            <div class="payment-item__thumb">
                                                <img class="payment-item__thumb-img"
                                                    src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}" alt="@lang('payment-thumb')">
                                            </div>
                                            <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}" hidden
                                                data-gateway='@json($data)' type="radio" name="gateway"
                                                value="{{ $data->method_code }}"
                                                @if (old('gateway')) @checked(old('gateway') == $data->method_code) @else
                                        @checked($loop->first) @endif
                                                data-min-amount="{{ showAmount($data->min_amount) }}"
                                                data-max-amount="{{ showAmount($data->max_amount) }}">
                                        </label>
                                    @endforeach
                                    @if ($gatewayCurrency->count() > 4)
                                        <button type="button" class="payment-item__btn more-gateway-option">
                                            <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                                            <span class="payment-item__btn__icon"><i class="fas fa-chevron-down"></i></span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="payment-system-list p-3">
                                    <div class="deposit-info">
                                        <div class="deposit-info__title">
                                            <p class="text mb-0">@lang('Amount')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <div class="deposit-info__input-group input-group">
                                                <span class="deposit-info__input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="text" class="form-control form--control amount" name="amount"
                                                    placeholder="@lang('00.00')" value="{{ old('amount', getAmount(@$plan->price)) }}"
                                                    @if (@$plan) readonly @endif autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="deposit-info hideInfo">
                                        <div class="deposit-info__title">
                                            <p class="text has-icon"> @lang('Limit')
                                                <span></span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="gateway-limit">@lang('0.00')</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="deposit-info hideInfo">
                                        <div class="deposit-info__title">
                                            <p class="text has-icon">@lang('Processing Charge')
                                                <span data-bs-toggle="tooltip" title="@lang('Processing charge for payment gateways')" class="proccessing-fee-info"><i
                                                        class="las la-info-circle"></i>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="processing-fee">@lang('0.00')</span>
                                                {{ __(gs('cur_text')) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="deposit-info total-amount pt-3">
                                        <div class="deposit-info__title">
                                            <p class="text">@lang('Total')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="final-amount">@lang('0.00')</span>
                                                {{ __(gs('cur_text')) }}</p>
                                        </div>
                                    </div>

                                    <div class="deposit-info  hideInfo gateway-conversion d-none total-amount pt-2">
                                        <div class="deposit-info__title">
                                            <p class="text">@lang('Conversion')
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"></p>
                                        </div>
                                    </div>
                                    <div class="deposit-info hideInfo conversion-currency d-none total-amount pt-2">
                                        <div class="deposit-info__title">
                                            <p class="text">
                                                @lang('In') <span class="gateway-currency"></span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text">
                                                <span class="in-currency"></span>
                                            </p>

                                        </div>
                                    </div>
                                    <div class="d-none hideInfo crypto-message mb-3">
                                        @lang('Conversion with') <span class="gateway-currency"></span> @lang('and final value will Show on next step')
                                    </div>
                                    <button type="submit" class="btn btn--base w-100" disabled>
                                        @if (@$planId)
                                            @lang('Confirm Payment')
                                        @else
                                            @lang('Confirm Deposit')
                                        @endif
                                    </button>
                                    <div class="info-text pt-3">
                                        <p class="text">@lang('Ensuring your funds grow safely through our secure deposit process with world-class payment options.')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            var amount = parseFloat($('.amount').val() || 0);
            var gateway, minAmount, maxAmount;


            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val());
                if (!amount) {
                    amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', function(e) {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                let gatewayValue = $('.gateway-input:checked').val();

                if (gatewayValue == 'wallet') {
                    @if (auth()->user()->balance < @$plan->price)
                        $(".deposit-form button[type=submit]").attr('disabled', true);
                    @else
                        $(".deposit-form button[type=submit]").removeAttr('disabled');
                    @endif
                    var totalAmount = parseFloat('{{ @$plan->price }}');
                    $('.hideInfo').addClass('d-none')
                    $(".final-amount").text(totalAmount.toFixed(2));
                } else {
                    $('.hideInfo').removeClass('d-none')

                    gateway = gatewayElement.data('gateway');
                    minAmount = gatewayElement.data('min-amount');
                    maxAmount = gatewayElement.data('max-amount');

                    let processingFeeInfo =
                        `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`
                    $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                    calculation();
                }
            }

            gatewayChange();

            $(".more-gateway-option").on("click", function(e) {
                let paymentList = $(".gateway-option-list");
                paymentList.find(".gateway-option").removeClass("d-none");
                $(this).addClass('d-none');
                paymentList.animate({
                    scrollTop: (paymentList.height() - 60)
                }, 'slow');
            });

            function calculation() {
                if (!gateway) return;
                $(".gateway-limit").text(minAmount + " - " + maxAmount);

                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.percent_charge);
                    fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                    $(".deposit-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
                    $('.deposit-form').addClass('adjust-height')

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ? 8 : 2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.deposit-form').removeClass('adjust-height')
                }

                if (gateway.method.crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            $('.gateway-input').change();
        })(jQuery);
    </script>
@endpush