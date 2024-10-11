    <div class="modal fade" id="bvInfoModal" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0">@lang('Business Volume (BV) info')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="text--danger">@lang('When someone from your below tree subscribe this plan, You will get this Business Volume  which will be used for matching bonus').</span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="refComInfoModal" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0">@lang('Referral Commission info')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="text--danger">@lang('When Your Direct-Referred/Sponsored  User Subscribe in') <b> @lang('ANY PLAN') </b>,
                        @lang('You will get this amount').</span>
                    <br>
                    <br>
                    <span class="text--success"> @lang('This is the reason You should Choose a Plan With Bigger Referral Commission').</span>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="treeComInfoModal" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0">@lang('Commission to tree info')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="text--danger">@lang('When someone from your below tree subscribe this plan, You will get this amount as Tree Commission'). </span>
                </div>
            </div>
        </div>
    </div>
    @auth
        <div class="modal fade" id="planConfirmationModal" role="dialog" tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                        <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form method="get" action="{{ route('user.deposit.index', ['type' => 'payment']) }}">
                        <div class="modal-body py-3">
                            <p class="question m-0"></p>
                            <input name="plan_id" type="hidden" value="">
                        </div>
                        <div class="modal-footer py-2">
                            <button class="btn btn-dark btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                            <button class="btn btn--base btn--sm" type="submit">@lang('Yes')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="modal fade" id="loginModal" role="dialog" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title m-0">@lang('Confirmation Alert!')</h5>
                        <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body py-3">
                        <span class="text-center">@lang('Please login to subscribe plans.')</span>
                    </div>
                    <div class="modal-footer py-2">
                        <button class="btn btn-dark btn--sm" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                        <a href="{{ route('user.login') }}" class="btn btn--base btn--sm">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    @endauth

    @push('script')
        <script>
            (function($) {
                "use strict";
                $(document).on('click', '.planConfirmationBtn', function() {
                    var modal = $('#planConfirmationModal');
                    let data = $(this).data();
                    modal.find('input[name=plan_id]').val(`${data.plan_id}`);
                    modal.find('.question').text(`${data.question}`);
                    modal.modal('show');
                });
            })(jQuery);
        </script>
    @endpush
