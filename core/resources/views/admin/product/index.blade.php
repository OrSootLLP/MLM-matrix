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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Business Volume (BV)')</th>
                                    <th>@lang('Purchase Volume (PV)')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $key => $product)
                                    <tr>
                                        <td>{{ __($product->name) }}</td>
                                        <td>{{ showAmount($product->price) }}</td>
                                        <td>{{ $product->bv }}</td>
                                        <td> {{ $product->pv }}</td>
                                        <td>
                                            @php echo $product->statusBadge @endphp
                                        </td>

                                        <td>
                                            <button class="btn btn-sm btn-outline--primary edit" data-toggle="tooltip" data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}" data-bv="{{ $product->bv }}" data-price="{{ getAmount($product->price) }}"
                                                data-pv="{{ $product->pv }}" data-image="{{ getImage(getFilePath('product').'/'.$product->image, getFileSize('product')) }}" data-description="{{ $product->description }}"
                                                data-original-title="@lang('Edit')" type="button">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </button>

                                            @if ($product->status == Status::DISABLE)
                                                <button class="btn btn-sm btn-outline--success confirmationBtn ms-1" data-question="@lang('Are you sure to enable this product?')"
                                                    data-action="{{ route('admin.product.status', $product->id) }}">
                                                    <i class="la la-eye"></i> @lang('Enable')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn ms-1" data-question="@lang('Are you sure to disable this product?')"
                                                    data-action="{{ route('admin.product.status', $product->id) }}">
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
                @if ($products->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($products) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{--    edit modal --}}
    <div class="modal fade" id="edit-product" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit Plan')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.product.save') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input class="form-control product_id" name="id" type="hidden">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Name')</label>
                                <input class="form-control name" name="name" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label> @lang('Price') </label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input class="form-control price" name="price" type="number" step="any" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>@lang('Business Volume (BV)')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="@lang('When someone buys this product, all of his ancestors will get this value which will be used for a matching bonus.')"></i>
                            <input class="form-control bv" name="bv" type="number" required>
                        </div>
                        
                        <div class="form-group">
                            <label>@lang('Purchase Volume (PV)')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="@lang('When someone buys this product, all of his ancestors will get this value which will be used for a matching bonus.')"></i>
                            <input class="form-control pv" name="pv" type="number" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Description')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="@lang('Edit product description.')"></i>
                            <textarea class="form-control description" name="description" rows="10" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label> @lang('Image')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="@lang('Update an Image.')"></i>
                            <x-image-uploader :size="getFileSize('product')" class="w-100 image" type="gateway" :required=false name="image" id="editImage" />
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-product" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New product')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.product.save') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <input class="form-control product_id" name="id" type="hidden">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Name')</label>
                                <input class="form-control" name="name" type="text" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Price') </label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input class="form-control" name="price" type="number" step="any" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label> @lang('Business Volume (BV)')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="@lang('When someone buys this product, all of his ancestors will get this value which will be used for a matching bonus.')"></i>
                            <input class="form-control" name="bv" type="number" type="number" required>
                        </div>
                        <div class="form-group">
                            <label> @lang('Purchase Volume (PV)')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="@lang('When someone buys this product, all of his ancestors will get this value which will be used for a matching bonus.')"></i>
                            <input class="form-control" name="pv" type="number" type="number" required>
                        </div>
                        <div class="form-group">
                            <label> @lang('Description')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="@lang('Insert product details.')"></i>
                            <textarea class="form-control" name="description" rows="10" required></textarea>
                        </div>
                        <div class="form-group">
                            <label> @lang('Image')</label> <i class="fas fa-question-circle text--gray" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="@lang('Add an Image.')"></i>
                            <x-image-uploader class="w-100" type="gateway" :required=true name="image" id="addImage" />
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
    <button class="btn btn-sm btn-outline--primary add-product" type="button">
        <i class="la la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.edit').on('click', function() {
                var modal = $('#edit-product');
                modal.find('.name').val($(this).data('name'));
                modal.find('.price').val($(this).data('price'));
                modal.find('.bv').val($(this).data('bv'));
                modal.find('.pv').val($(this).data('pv'));
                modal.find('.description').val($(this).data('description'));
                let image = $(this).data('image');
                modal.find(".image-upload-preview").css("background-image", "url("+$(this).data('image')+")");
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            $('.add-product').on('click', function() {
                var modal = $('#add-product');
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush