<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <base href="">
    <title>{{ config('settings.APP_NAME') }} | @yield('title', 'Dashboard')</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <meta property="og:locale" content="pt-br" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="" />
    <meta property="og:url" content="{{ env('APP_URL') }}" />
    <meta property="og:site_name" content="{{ config('settings.APP_NAME') }}" />
    <meta name="csrf_token" content="{{ csrf_token() }}" />
    <link rel="canonical" href="{{ env('APP_URL') }}" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <link rel="shortcut icon" href="{{ asset('/assets/media/icone-ouseai.png') }}" />
    <link href="{{ asset('/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/css/custom.css') }}" rel="stylesheet" type="text/css" />

    {{ $stylesheet ?? '' }}

</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-enabled">

    <div class="d-flex flex-column flex-root">

        <div class="page d-flex flex-row flex-column-fluid">

            <div id="kt_aside" class="aside aside-extended bg-white" data-kt-drawer="true" data-kt-drawer-name="aside"
                data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                data-kt-drawer-width="auto" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_toggle">
                <div class="aside-primary d-flex flex-column align-items-lg-center flex-row-auto">

                    <div class="aside-logo d-none d-lg-flex flex-column align-items-center flex-column-auto py-10"
                        id="kt_aside_logo">
                        <a href="{{ env('APP_URL') }}">
                            <img alt="Logo" src="{{ asset('/assets/media/icone-ouseai.png') }}" class="h-50px" />
                        </a>
                    </div>

                    <div class="aside-nav d-flex flex-column align-items-center flex-column-fluid w-100 pt-5 pt-lg-0"
                        id="kt_aside_nav">

                        <div class="hover-scroll-y mb-10" data-kt-scroll="true"
                            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
                            data-kt-scroll-wrappers="#kt_aside_nav"
                            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-offset="0px">

                            <ul class="nav flex-column">

                                <li class="nav-item mb-4" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                    data-bs-placement="right" data-bs-dismiss="click" title="Agentes">
                                    <a class="nav-link btn btn-warning btn-icon active" data-bs-toggle="tab"
                                        href="#kt_aside_nav_tab_agentes">
                                        <i class="fa-solid fa-robot text-black fs-2"></i>
                                    </a>
                                </li>

                                @if (Auth::user()->role == 1 or Auth::user()->role == 2)
                                    <li class="nav-item mb-4" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                        data-bs-placement="right" data-bs-dismiss="click" title="Admin">
                                        <a class="nav-link btn btn-warning btn-icon" data-bs-toggle="tab"
                                            href="#kt_aside_nav_tab_admin">
                                            <i class="fa-solid fa-grip text-black fs-2"></i>
                                        </a>
                                    </li>
                                @endif

                            </ul>

                        </div>
                    </div>

                    <div class="aside-footer d-flex flex-column align-items-center flex-column-auto"
                        id="kt_aside_footer">

                        <div class="d-flex align-items-center mb-10" id="kt_header_user_menu_toggle">
                            <div class="cursor-pointer symbol symbol-40px" data-kt-menu-trigger="click"
                                data-kt-menu-overflow="true" data-kt-menu-placement="top-start" data-bs-toggle="tooltip"
                                data-bs-placement="right" data-bs-dismiss="click" title="Meu Perfil">
                                <i class="bi bi-person-fill fs-3x"></i>
                            </div>

                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px"
                                data-kt-menu="true">

                                <div class="menu-item px-3">
                                    <div class="menu-content d-flex align-items-center px-3">
                                        <div class="symbol symbol-50px me-5">
                                            <i class="bi bi-person-fill fs-3x"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="fw-bolder d-flex align-items-center fs-5">
                                                {{ Auth::user()->name }}</div>
                                            <a href="#"
                                                class="fw-bold text-muted text-hover-warning fs-7">{{ Auth::user()->email }}</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="separator my-2"></div>

                                <div class="menu-item px-5">
                                    <a href="{{ route('profile.edit') }}"
                                        class="menu-link text-hover-warning px-5">Meu Perfil</a>
                                </div>

                                <div class="separator my-2"></div>

                                <form class="p-0 m-0" method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <div class="menu-item px-5">
                                        <a href="#" id="logoutButton"
                                            onclick="event.preventDefault();this.closest('form').submit();"
                                            class="menu-link text-hover-warning px-5">Sair</a>
                                    </div>
                                </form>

                                {{-- <div class="separator my-2"></div>

                                <div class="menu-item px-5">
                                    <div class="menu-content px-5">
                                        <label
                                            class="form-check form-switch form-check-custom form-check-solid pulse pulse-success"
                                            for="kt_user_menu_dark_mode_toggle">
                                            <input class="form-check-input w-30px h-20px" type="checkbox"
                                                value="1" name="mode" id="kt_user_menu_dark_mode_toggle"
                                                data-kt-url="../dist/index.html" />
                                            <span class="pulse-ring ms-n1"></span>
                                            <span class="form-check-label text-gray-600 fs-7">Dark Mode</span>
                                        </label>
                                    </div>
                                </div> --}}
                            </div>

                        </div>

                    </div>

                </div>

                <div class="aside-secondary d-flex flex-row-fluid">

                    <div class="aside-workspace my-5 p-5" id="kt_aside_wordspace">
                        <div class="d-flex h-100 flex-column">

                            <div class="flex-column-fluid hover-scroll-y" data-kt-scroll="true"
                                data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                                data-kt-scroll-wrappers="#kt_aside_wordspace"
                                data-kt-scroll-dependencies="#kt_aside_secondary_footer" data-kt-scroll-offset="0px">

                                <div class="tab-content">

                                    <div class="tab-pane fade active show" id="kt_aside_nav_tab_agentes"
                                        role="tabpanel">

                                        <div class="m-0">

                                            <div class="m-0">

                                                <div class="menu-item">
                                                    <div class="menu-content pb-2"><span
                                                            class="menu-section text-muted text-uppercase fs-8 ls-1">AGENTES</span>
                                                    </div>
                                                </div>

                                                @if ($agents->isEmpty())
                                                    <div class="alert alert-info">
                                                        Nenhum agente cadastrado. Clique no botão "Novo Agente" para
                                                        começar.
                                                    </div>
                                                @else
                                                    @foreach ($agents as $agent)
                                                        <div class="mb-2">
                                                            <a href="{{ route('agents.show', $agent) }}"
                                                                class="custom-list d-flex  px-5 py-4">
                                                                <div class="symbol symbol-40px me-5">
                                                                    <span class="symbol-label">
                                                                        <i class="{{ $agent->icon }} fs-2"></i>
                                                                    </span>
                                                                </div>
                                                                <div class="d-flex flex-column flex-grow-1">
                                                                    <h5
                                                                        class="custom-list-title fw-bold text-gray-800 mb-1">
                                                                        {{ $agent->name }}</h5>
                                                                    <span class="text-gray-400 fw-semibold mb-3">
                                                                        {{ Str::limit($agent->description, 100) }}
                                                                    </span>
                                                                    {{-- <span class="text-gray-400">
                                                                        @foreach ($agent->categories as $category)
                                                                            <span
                                                                                class="badge badge-light-dark">{{ $category->name }}</span>
                                                                        @endforeach
                                                                    </span> --}}
                                                                </div>
                                                            </a>
                                                            <div class="menu-item"><!--begin:Menu content-->
                                                                <div class="menu-content">
                                                                    <div class="separator"></div>
                                                                </div><!--end:Menu content-->
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </div>
                                        </div>
                                    </div>

                                    @if (Auth::user()->role == 1 or Auth::user()->role == 2)
                                        <div class="tab-pane fade" id="kt_aside_nav_tab_admin" role="tabpanel">
                                            <div class="menu menu-column menu-sub-indention menu-rounded menu-active-bg menu-title-gray-600 menu-icon-gray-400 menu-state-primary menu-arrow-gray-500 fw-semibold fs-6 px-2 my-5 my-lg-0"
                                                id="kt_aside_menu" data-kt-menu="true">

                                                <div id="kt_aside_menu_wrapper">
                                                    <div class="menu-item">
                                                        <div class="menu-content pb-2"><span
                                                                class="menu-section text-muted text-uppercase fs-8 ls-1">AGENTES</span>
                                                        </div>
                                                    </div>

                                                    <div class="menu-item">
                                                        <a class="menu-link" href="{{ route('agents.index') }}">
                                                            <span class="menu-title">Listar Agentes
                                                            </span>
                                                        </a>
                                                        <a class="menu-link" href="{{ route('agents.create') }}">
                                                            <span class="menu-title">Cadastrar Agente
                                                            </span>
                                                        </a>
                                                        <a class="menu-link" href="{{ route('categories.index') }}">
                                                            <span class="menu-title">Listar Categorias
                                                            </span>
                                                        </a>
                                                        <a class="menu-link" href="{{ route('categories.create') }}">
                                                            <span class="menu-title">Cadastrar Categoria
                                                            </span>
                                                        </a>
                                                    </div>

                                                    <div class="menu-item"><!--begin:Menu content-->
                                                        <div class="menu-content">
                                                            <div class="separator"></div>
                                                        </div><!--end:Menu content-->
                                                    </div>

                                                </div>

                                                <div id="kt_aside_menu_wrapper">
                                                    <div class="menu-item">
                                                        <div class="menu-content pb-2"><span
                                                                class="menu-section text-muted text-uppercase fs-8 ls-1">USUÁRIOS</span>
                                                        </div>
                                                    </div>

                                                    <div class="menu-item">
                                                        <a class="menu-link" href="{{ route('users.index') }}">
                                                            <span class="menu-title">Listar Usuários
                                                            </span>
                                                        </a>
                                                        <a class="menu-link" href="{{ route('users.create') }}">
                                                            <span class="menu-title">Cadastrar Usuário
                                                            </span>
                                                        </a>
                                                    </div>

                                                    <div class="menu-item"><!--begin:Menu content-->
                                                        <div class="menu-content">
                                                            <div class="separator"></div>
                                                        </div><!--end:Menu content-->
                                                    </div>

                                                </div>

                                                @if (Auth::user()->role == 1)
                                                    <div id="kt_aside_menu_wrapper">
                                                        <div class="menu-item">
                                                            <div class="menu-content pb-2"><span
                                                                    class="menu-section text-muted text-uppercase fs-8 ls-1">CONFIGURAÇÕES</span>
                                                            </div>
                                                        </div>

                                                        <div class="menu-item">
                                                            <a class="menu-link" href="{{ route('config.index') }}">
                                                                <span class="menu-title">Configurações
                                                                </span>
                                                            </a>
                                                        </div>

                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <button
                    class="btn btn-sm btn-icon btn-warning btn-active-warning position-absolute translate-middle start-100 end-0 bottom-0 shadow-sm d-none d-lg-flex"
                    data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                    data-kt-toggle-name="aside-minimize" style="margin-bottom: 1.35rem">
                    <span class="svg-icon svg-icon-2 rotate-180">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <rect opacity="0.5" x="6" y="11" width="13" height="2" rx="1"
                                fill="black" />
                            <path
                                d="M8.56569 11.4343L12.75 7.25C13.1642 6.83579 13.1642 6.16421 12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75L5.70711 11.2929C5.31658 11.6834 5.31658 12.3166 5.70711 12.7071L11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25C13.1642 17.8358 13.1642 17.1642 12.75 16.75L8.56569 12.5657C8.25327 12.2533 8.25327 11.7467 8.56569 11.4343Z"
                                fill="black" />
                        </svg>
                    </span>
                </button>

            </div>

            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">


                <div id="kt_header" class="header" data-kt-sticky="true" data-kt-sticky-name="header"
                    data-kt-sticky-offset="{default: '200px', lg: '300px'}">

                    <div class="container-xxl d-flex align-items-center justify-content-between"
                        id="kt_header_container">

                        <div class="page-title d-flex flex-column align-items-start justify-content-center flex-wrap me-lg-2 pb-5 pb-lg-0"
                            data-kt-swapper="true" data-kt-swapper-mode="prepend"
                            data-kt-swapper-parent="{default: '#kt_content_container', lg: '#kt_header_container'}">
                            <h1 class="text-dark fw-bold my-0 fs-2">@yield('title', 'Dashboard')</h1>
                            <ul class="breadcrumb breadcrumb-line text-muted fw-bold fs-base my-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="/dashboard" class="text-muted">Home</a>
                                </li>
                                <li class="breadcrumb-item text-dark">@yield('title', 'Dashboard')</li>
                            </ul>
                        </div>

                        <div class="d-flex d-lg-none align-items-center ms-n2 me-2">
                            <div class="btn btn-icon btn-active-icon-primary" id="kt_aside_toggle">
                                <span class="svg-icon svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                                            fill="black" />
                                        <path opacity="0.3"
                                            d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                                            fill="black" />
                                    </svg>
                                </span>
                            </div>
                            <a href="" class="d-flex align-items-center">
                                <img alt="Logo" src="{{ asset('/assets/media/icone-ouseai.png') }}"
                                    class="h-40px" />
                            </a>
                        </div>

                        <div class="d-flex flex-shrink-0">

                        </div>
                    </div>
                </div>

                {{ $slot }}

                <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                    <div class="container-xxl d-flex flex-column flex-md-row flex-stack">

                        <div class="text-dark order-2 order-md-1">
                            <span class="text-gray-400 fw-bold me-1">Criado por</span>
                            <a href="https://alysonpaiva.com.br" target="_blank"
                                class="text-muted text-hover-warning fw-bold me-2 fs-6">Alyson Paiva</a>
                        </div>

                        <ul class="menu menu-gray-600 menu-hover-primary fw-bold order-1">
                            <li class="menu-item">
                                <a href="" target="_blank"
                                    class="menu-link text-hover-warning px-2">Suporte</a>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <span class="svg-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none">
                <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1"
                    transform="rotate(90 13 6)" fill="black" />
                <path
                    d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                    fill="black" />
            </svg>
        </span>
    </div>

    <script src="{{ asset('/assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('/assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('/assets/js/custom/widgets.js') }}"></script>

    @yield('scripts')
</body>

</html>
