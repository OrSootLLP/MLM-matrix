@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Store')</th>
                                    <th>@lang('State')</th>
                                    <th>@lang('District')</th>
                                    <th>@lang('Commission')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Number')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($franchises as $key => $franchise)
                                    <tr>
                                        <td>{{ __($franchise->store) }}</td>
                                        <td>{{ $franchise->state->state_title }}</td>
                                        <td>{{ $franchise->district->district_title }}</td>
                                        <td>{{ $franchise->commission }}</td>
                                        <td> {{ $franchise->name }}</td>
                                        <td> {{ $franchise->mobile }}</td>
                                        <td>
                                            @php echo $franchise->statusBadge @endphp
                                        </td>
                                        
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary edit" data-toggle="tooltip" data-id="{{ $franchise->id }}"
                                                data-name="{{ $franchise->name }}" data-commission="{{ $franchise->commission }}" data-mobile="{{ $franchise->mobile }}"
                                                data-store="{{ $franchise->store }}" data-state="{{ $franchise->state_id }}" data-district="{{ $franchise->district_id }}" 
                                                data-bank="{{ $franchise->bank_name }}" data-ifsc="{{ $franchise->ifsc_code }}" data-account="{{ $franchise->account_number }}""
                                                data-pan="{{ $franchise->pan_number }}" data-original-title="@lang('Edit')" type="button">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </button>

                                            @if ($franchise->status == Status::DISABLE)
                                                <button class="btn btn-sm btn-outline--success confirmationBtn ms-1" data-question="@lang('Are you sure to enable this franchise?')"
                                                    data-action="{{ route('admin.franchise.status', $franchise->id) }}">
                                                    <i class="la la-eye"></i> @lang('Enable')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn ms-1" data-question="@lang('Are you sure to disable this franchise?')"
                                                    data-action="{{ route('admin.franchise.status', $franchise->id) }}">
                                                    <i class="la la-eye-slash"></i> @lang('Disable')
                                                </button>
                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($franchises->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($franchises) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{--    edit modal --}}
    <div class="modal fade" id="edit-franchise" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit Plan')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.franchise.save') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input class="form-control franchise_id" name="id" type="hidden">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Store')</label>
                                <input class="form-control store" name="store" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('State') </label>
                                <select class="select2 form-control state" name="state" id="editState" required>
                                    <option value="" selected disabled>Select State</option>
                                    @foreach ($states as $key => $state)
                                        <option value="{{ $state->state_id }}">{{ __($state->state_title) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('District') </label>
                                <select class="select2 form-control district" name="district" id="editDistrict" required>
                                    <option value="" selected disabled>Select District</option>
                                    @foreach ($districts as $key => $district)
                                        <option value="{{ $district->districtid }}">{{ __($district->district_title) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Commission')</label>
                                <input class="form-control commission" name="commission" type="text" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Name')</label>
                                <input class="form-control name" name="name" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Mobile')</label>
                                <input class="form-control mobile" name="mobile" type="text" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Pan Number')</label>
                                <input class="form-control pan" name="pan_number" type="text" maxlength="10" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Bank Name')</label>
                                <input class="form-control bank" name="bank_name" type="text" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Account Number')</label>
                                <input class="form-control account" name="account_number" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('IFSC Code')</label>
                                <input class="form-control ifsc" name="ifsc_code" type="text" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-franchise" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New franchise')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.franchise.save') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input class="form-control franchise_id" name="id" type="hidden">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Store')</label>
                                <input class="form-control" name="store" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('State') </label>
                                <select class="select2 form-control" name="state" id="addState" required>
                                    <option value="" selected disabled>Select State</option>
                                    @foreach ($states as $key => $state)
                                        <option value="{{ $state->state_id }}">{{ __($state->state_title) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('District') </label>
                                <select class="select2 form-control" name="district" id="addDistrict" required>
                                    <option value="" selected disabled>Select District</option>
                                    @foreach ($districts as $key => $district)
                                        <option value="{{ $district->districtid }}">{{ __($district->district_title) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Commission')</label>
                                <input class="form-control" name="commission" type="text" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Name')</label>
                                <input class="form-control" name="name" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Mobile')</label>
                                <input class="form-control" name="mobile" type="text" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Pan Number')</label>
                                <input class="form-control" name="pan_number" type="text" maxlength="10" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Bank Name')</label>
                                <input class="form-control" name="bank_name" type="text" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Account Number')</label>
                                <input class="form-control" name="account_number" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('IFSC Code')</label>
                                <input class="form-control" name="ifsc_code" type="text" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary add-franchise" type="button">
        <i class="la la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.edit').on('click', function() {
                var modal = $('#edit-franchise');
                modal.find('.store').val($(this).data('store'));
                modal.find('.state').val($(this).data('state')).change();
                modal.find('.district').val($(this).data('district')).change();
                modal.find('.commission').val($(this).data('commission'));
                modal.find('.name').val($(this).data('name'));
                modal.find('.mobile').val($(this).data('mobile'));
                modal.find('.pan').val($(this).data('pan'));
                modal.find('.ifsc').val($(this).data('ifsc'));
                modal.find('.account').val($(this).data('account'));
                modal.find('.bank').val($(this).data('bank'));
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });
            
            $('.add-franchise').on('click', function() {
                var modal = $('#add-franchise');
                modal.modal('show');
            });
            
        })(jQuery);
    </script>
@endpush