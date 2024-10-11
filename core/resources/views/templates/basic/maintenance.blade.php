@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="padding-top padding-bottom flex-column justify-content-center">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-7 text-center">
                    <img class="img-fluid w-100 mx-auto mb-5" src="{{ getImage(getFilePath('maintenance') . '/' . @$maintenance->data_values->image, getFileSize('maintenance')) }}" alt="image">
                    <p class="mx-auto text-center">@php echo $maintenance->data_values->description @endphp</p>
                </div>
            </div>
        </div>
    </div>
@endsection