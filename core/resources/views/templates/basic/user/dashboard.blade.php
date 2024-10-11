@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="notice"></div>
        @php
            $kyc = getContent('kyc_info.content', true);
            $notice = getContent('notice.content', true);
        @endphp
        @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
            <div class="alert alert--danger" role="alert">
                <div class="alert__icon"><i class="fas fa-file-signature"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('KYC Documents Rejected')</span><br>
                    <span>
                        <i>{{ __(@$kyc->data_values->reject_content) }} </i>
                        <a href="javascript::void(0)" class="link-color" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Click here')</a>
                        @lang('to show the reason').

                        <a href="{{ route('user.kyc.form') }}" class="link-color">@lang('Click Here')</a>
                        @lang('to Re-submit Documents').
                        <a href="{{ route('user.kyc.data') }}" class="link-color">@lang('See KYC Data')</a>
                    </span>
                </p>
            </div>
        @elseif ($user->kv == Status::KYC_UNVERIFIED)
            <div class="alert alert--info" role="alert">
                <div class="alert__icon"><i class="fas fa-file-signature"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('KYC Verification Required')</span><br>
                    <span><i>{{ __(@$kyc->data_values->verification_content) }}</i>
                        <a href="{{ route('user.kyc.form') }}" class="link-color">@lang('Click here')</a>
                        @lang('to submit KYC information').
                    </span>
                </p>
            </div>
        @elseif($user->kv == Status::KYC_PENDING)
            <div class="alert alert--warning" role="alert">
                <div class="alert__icon"><i class="fas fa-user-check"></i></div>
                <p class="alert__message">
                    <span class="fw-bold">@lang('KYC Verification Pending')</span><br>
                    <span>{{ __(@$kyc->data_values->pending_content) }} <a href="{{ route('user.kyc.data') }}" class="link-color">@lang('Click here')</a>
                        @lang('to see your submitted information')
                    </span>
                </p>
            </div>
        @endif

        @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
            <div class="modal fade" id="kycRejectionReason">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                            <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>{{ auth()->user()->kyc_rejection_reason }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (@$notice->data_values->notice_content != null && !$user->plan_id)
            <div class="card custom--card">
                <div class="card-header">
                    <h5>@lang('Notice')</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        {{ __($notice->data_values->notice_content) }}
                    </p>
                </div>
            </div>
        @endif

        {{-- dashboard main --}}
        <div class="row g-3 mt-3 mb-4">

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Deposit')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($totalDeposit) }}</h3>
                    <div class="widget-lists">
                        <div class="row">
                            <div class="col-4">
                                <p class="fw-bold">@lang('Submitted')</p>
                                <span>{{ showAmount($submittedDeposit) }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Pending')</p>
                                <span>{{ showAmount($pendingDeposit) }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Rejected')</p>
                                <span>{{ showAmount($rejectedDeposit) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Widthdraw')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($totalWithdraw) }}</h3>
                    <div class="widget-lists">
                        <div class="row">
                            <div class="col-4">
                                <p class="fw-bold">@lang('Submitted')</p>
                                <span>{{ showAmount($submittedWithdraw) }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Pending')</p>
                                <span>{{ showAmount($pendingWithdraw) }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Rejected')</p>
                                <span>{{ showAmount($rejectWithdraw) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Referral Commission')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($user->total_ref_com) }}
                    </h3>
                    <div class="widget-lists">
                        <div class="row">
                            <div class="col-4">
                                <p class="fw-bold">@lang('Referrals')</p>
                                <span>{{ $totalRef }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Left')</p>
                                <span>{{ $totalLeft }}</span>
                            </div>
                            <div class="col-4">
                                <p class="fw-bold">@lang('Right')</p>
                                <span>{{ $totalRight }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Invest')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($user->total_invest) }}
                    </h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Binary Commission')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ showAmount($user->total_binary_com) }}
                    </h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total BV')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ $totalBv }}</h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Left BV')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ getAmount($user->userExtra->bv_left) }}</h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Right BV')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ getAmount($user->userExtra->bv_right) }}</h3>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-widget">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary">@lang('Total Bv Cut')</h5>
                    </div>
                    <h3 class="text--secondary my-4">{{ getAmount($totalBvCut) }}</h3>
                </div>
            </div>

        </div>

        <div class="mb-4">
            <h4 class="mb-2">@lang('Binary Summery')</h4>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card custom--card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table--responsive--md table">
                                <thead>
                                    <tr>
                                        <th>@lang('Paid left')</th>
                                        <th>@lang('Paid right')</th>
                                        <th>@lang('Free left')</th>
                                        <th>@lang('Free right')</th>
                                        <th>@lang('Bv left')</th>
                                        <th>@lang('Bv right')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $logs->paid_left }}</td>
                                        <td>{{ $logs->paid_right }}</td>
                                        <td>{{ $logs->free_left }}</td>
                                        <td>{{ $logs->free_right }}</td>
                                        <td>{{ getAmount($logs->bv_left) }}</td>
                                        <td>{{ getAmount($logs->bv_right) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
