@php
    $contact        = getContent('contact_us.content', true);
    $language       = App\Models\Language::all();
    $selectLanguage = App\Models\Language::where('code', session('lang'))->first();
@endphp

<div class="header-top">
    <div class="container">
        <div class="header-top-area">
            <div class="header-top-item">
                <a href="Mailto:{{ @$contact->data_values->email_address }}"><i class="fa fa-envelope"></i>
                    {{ @$contact->data_values->email_address }}</a>
            </div>
            <div class="header-top-item">
                <a href="tel:{{ @$contact->data_values->contact_number }}"><i class="fa fa-mobile-alt"></i>
                    {{ @$contact->data_values->contact_number }}</a>
            </div>
            @if (gs('multi_language'))
                <div class="language dropdown d-none d-lg-block ms-auto">
                    <button class="language-wrapper" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="language-content">
                            <div class="language_flag">
                                <img src="{{ getImage(getFilePath('language') . '/' . $selectLanguage->image), '50x50' }}" alt="flag">
                            </div>
                            <p class="language_text_select">{{ __($selectLanguage->name) }}</p>
                        </div>
                        <span class="collapse-icon"><i class="las la-angle-down"></i></span>
                    </button>
                    <div class="dropdown-menu langList_dropdow py-2" style="">
                        <ul class="langList">
                            @foreach ($language as $item)
                                <li class="language-list langSel" data-code={{ $item->code }}>
                                    <div class="language_flag">
                                        <img src="{{ getImage(getFilePath('language') . '/' . $item->image), '50x50' }}" alt="flag">
                                    </div>
                                    <p class="language_text">{{ __($item->name) }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<header class="header-bottom">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="header-area">
                    <div class="logo">
                        <a href="{{ route('home') }}"><img src="{{ siteLogo('dark') }}" alt="logo"></a>
                    </div>
                    <ul class="menu">
                        <li><a href="{{ url('/') }}">@lang('Home')</a></li>

                        @foreach ($pages as $k => $data)
                            <li><a href="{{ route('pages', [$data->slug]) }}">{{ trans($data->name) }}</a></li>
                        @endforeach
                        <li><a href="{{ route('plan') }}">@lang('Plan')</a></li>
                        <li><a href="{{ route('blog') }}">@lang('Blog')</a></li>
                        <li><a href="{{ route('contact') }}">@lang('Contact')</a></li>

                        @auth
                            <li><a href="javascript:void(0)">@lang('Account')</a>
                                <ul class="submenu">
                                    <li><a href="{{ route('user.home') }}">@lang('Dashboard')</a></li>
                                    <li><a href="{{ route('user.logout') }}">@lang('Logout')</a></li>
                                </ul>
                            </li>
                        @else
                            <li>
                                <a href="javascript:void(0)">@lang('Account')</a>
                                <ul class="submenu">
                                    <li><a href="{{ route('user.login') }}">@lang('Log In')</a>
                                    </li>
                                    <li><a href="{{ route('user.register') }}">@lang('Register')</a></li>
                                </ul>
                            </li>
                        @endauth

                        @if (gs('multi_language'))
                            <li class="language dropdown d-lg-none">
                                <button class="language-wrapper style-two" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="language-content">
                                        <div class="language_flag">
                                            <img src="{{ getImage(getFilePath('language') . '/' . $selectLanguage->image), '50x50' }}"
                                                alt="flag">
                                        </div>
                                        <p class="language_text_select">{{ __($selectLanguage->name) }}</p>
                                    </div>
                                    <span class="collapse-icon"><i class="las la-angle-down"></i></span>
                                </button>
                                <div class="dropdown-menu langList_dropdow py-2" style="">
                                    <ul class="langList">
                                        @foreach ($language as $item)
                                            <li class="language-list langSel" data-code={{ $item->code }}>
                                                <div class="language_flag">
                                                    <img src="{{ getImage(getFilePath('language') . '/' . $item->image), '50x50' }}" alt="flag">
                                                </div>
                                                <p class="language_text">{{ __($item->name) }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>

                        @endif
                    </ul>
                    <div class="header-bar d-lg-none">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

@push('script')
    <script>
        $(document).ready(function() {
            const $mainlangList = $(".langList");
            const $langBtn = $(".language-content");
            const $langListItem = $mainlangList.children();

            $langListItem.each(function() {
                const $innerItem = $(this);
                const $languageText = $innerItem.find(".language_text");
                const $languageFlag = $innerItem.find(".language_flag");

                $innerItem.on("click", function(e) {
                    $langBtn.find(".language_text_select").text($languageText.text());
                    $langBtn.find(".language_flag").html($languageFlag.html());
                });
            });
        });
    </script>
@endpush
