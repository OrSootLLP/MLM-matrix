<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>

    @include('partials.seo')

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/iziToast_custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/select2.min.css') }}" rel="stylesheet">

    <link href="{{ asset($activeTemplateTrue . 'users/css/lib/animate.css') }}" rel="stylesheet">
    <!-- Plugin Link -->
    <link href="{{ asset($activeTemplateTrue . 'users/css/lib/slick.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'users/css/lib/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'users/css/lib/apexcharts.css') }}" rel="stylesheet">

    <link href="{{ asset($activeTemplateTrue . 'users/css/main.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'users/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'users/css/color.php') }}?color={{ gs('base_color') }}" rel="stylesheet">

    @stack('style-lib')

    @stack('style')
    <style>
        .pb-120 {
            padding-bottom: clamp(40px, 4vw, 40px);
        }

        .pt-120 {
            padding-top: clamp(40px, 4vw, 40px);
        }

        .container {
            max-width: 1140px;
        }
    </style>

</head>

@php echo loadExtension('google-analytics') @endphp

<body>
    <div class="d-flex flex-wrap">
        @include($activeTemplate . 'partials.sidebar')
        <div class="dashboard-wrapper">
            @include($activeTemplate . 'partials.topbar')
            <div class="dashboard-container container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>

    <!-- Pluglin Link -->
    <script src="{{ asset($activeTemplateTrue . 'users/js/lib/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'users/js/lib/magnific-popup.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'users/js/lib/apexcharts.min.js') }}"></script>
    <!-- Main js -->
    <script src="{{ asset($activeTemplateTrue . 'users/js/main.js') }}"></script>

    @stack('script-lib')

    @include('partials.notify')

    @php echo loadExtension('tawk-chat') @endphp

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')

    <script>
        (function($) {
            "use strict";
            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            function formatState(state) {
                if (!state.id) return state.text;
                let gatewayData = $(state.element).data();
                return $(
                    `<div class="d-flex gap-2">${gatewayData.imageSrc ? `<div class="select2-image-wrapper"><img class="select2-image" src="${gatewayData.imageSrc}"></div>` : '' }<div class="select2-content"> <p class="select2-title">${gatewayData.title}</p><p class="select2-subtitle">${gatewayData.subtitle}</p></div></div>`
                    );
            }

            $('.select2').each(function(index, element) {
                $(element).select2({
                    templateResult: formatState,
                    minimumResultsForSearch: "-1"
                });
            });

            $('.select2-searchable').each(function(index, element) {
                $(element).select2({
                    templateResult: formatState,
                    minimumResultsForSearch: "1"
                });
            });


            $('.select2-basic').each(function(index, element) {
                $(element).select2({
                    dropdownParent: $(element).closest('.select2-parent')
                });
            });

            $.each($('.select2'), function() {
                $(this)
                    .wrap(`<div class="position-relative"></div>`)
                    .select2({
                        dropdownParent: $(this).parent()
                    });
            });


            var inputElements = $('[type=text],[type=password],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input, select, textarea'), function(i, element) {

                if (element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').addClass('required');
                }

            });


            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });


            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
                        colum.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });


            let disableSubmission = false;
            $('.disableSubmission').on('submit', function(e) {
                if (disableSubmission) {
                    e.preventDefault()
                } else {
                    disableSubmission = true;
                }
            });

        })(jQuery);
    </script>

</body>

</html>
