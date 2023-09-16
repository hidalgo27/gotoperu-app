<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="RKqlAT_PUqYYY0MkFZc-h48BgcRpUvekk7ZGAojEjHA" />
    <!-- Title y meta descripcion-->

    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! JsonLd::generate() !!}

    <link href="{{asset('icons/favicon.ico')}}" rel="icon" type="image/x-icon">
    <!-- Styles -->
    <link href="{{ asset('css/block.css') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    {{--    <link href="{{ asset('css/ionicons.min.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('themify-icons/themify-icons.css') }}" rel="stylesheet">


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KK52HEG2LE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-KK52HEG2LE');
    </script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-153176828-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-153176828-1');
    </script>


</head>
<body data-spy="scroll" data-target="#navbar-scroll">
<div id="app">

    <div class="menu-container">
        <div class="container mt-3">
            <div class="row justify-content-between align-items-center">
                <div class="col-4 d-none d-sm-inline">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                            <i data-feather="phone-call" class="text-white" stroke-width="1"></i>
                        </div>
                        <div class="col">
                            <ul class="accordion-social p-0 m-0">
{{--                                <li class="item">--}}
{{--                                    <div class="social-links">--}}
{{--                                        <a href=""> <img src="{{asset('images/icons/mx.svg')}}" alt="" width="40" class="w-" loading="lazy"> </a>--}}
{{--                                    </div>--}}
{{--                                    <div class="social-info">--}}
{{--                                        <a href="tel:+523341625836">+52 (33) 41625836</a>--}}
{{--                                    </div>--}}
{{--                                </li>--}}
                                <li class="item">
                                    <div class="social-links">
                                        <a href=""> <img src="{{asset('images/icons/pe.svg')}}" alt="" width="40" class="w-" loading="lazy"> </a>
                                    </div>
                                    <div class="social-info">
                                        <a href="tel:51922300244">+51 922 300 244</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
{{--                    <div class="form-group m-0 has-search">--}}
{{--                        <span class="fa fa-search form-control-feedback"></span>--}}
{{--                        <input type="text" class="form-control form-control-search shadow-none border-0 text-white" placeholder="BUSCAR">--}}
{{--                    </div>--}}
                </div>
                <div class="col-auto mx-auto">
                    <a href="{{route('home_path')}}"><img src="{{asset('images/logo-gotoperu-white.png')}}" width="250" alt="logo andesviagens" class="img-fluid" loading="lazy"></a>
                </div>
                <div class="col-4 d-none d-sm-inline text-right">
                    <a href="#consulte" class="btn btn-inquire float-right ml-3 font-weight-bold px-4 text-white rounded-0">COTIZAR VIAJE</a>
{{--                    <img src="{{asset('images/icons/mx.svg')}}" alt="" width="30" class="w-" loading="lazy"> +52 (33) 41625836 |--}}
{{--                    <img src="{{asset('images/icons/mx.svg')}}" alt="" width="30" class="w-" loading="lazy"> +52 (33) 41625836--}}
                </div>
            </div>
        </div>
        @include('layouts.page.menu')

    </div>

    @yield('content')

    <div id="redes">
        <div class="container-fluid">
            <div class="row justify-content-end">
                <div class="col-auto">
                    <div class="bg-dark rounded px-3 mx-3 shadow clearfix float-right">
                        <a href="https://api.whatsapp.com/send?phone=51922300244" target="_blank" class="font-weight-bold text-white stretched-link">
                            Escríbenos por WhatsApp
                            <img src="{{asset('images/icons/whatsapp-i.png')}}" class="py-1" alt="logo whatsapp" width="50" data-toggle="tooltip" data-placement="top" title="Perú" loading="lazy">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <footer class="position-relative">
        <img src="{{asset('images/footer.jpg')}}" alt="" class="w-100" loading="lazy">
        <div class="bg-g-dark footer-info">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h6 class="text-white font-weight-bold">PERÚ</h6>
                        <div class="line-subtitle"></div>
                        <p class="m-0 text-g-yellow"><i data-feather="chevron-right" class="text-white" stroke-width="1"></i> Av. El Sol 449, Oficina 301.</p>
                        <p class="m-0 text-g-yellow"><i data-feather="chevron-right" class="text-white" stroke-width="1"></i> Cusco Centro Historico</p>
                        <p class="m-0 text-g-yellow"><i data-feather="chevron-right" class="text-white" stroke-width="1"></i> Ciudad del Cusco</p>
                    </div>
                    <div class="col">
                        <h6 class="text-white font-weight-bold">MÉXICO</h6>
                        <div class="line-subtitle"></div>
                        <p class="m-0 text-g-yellow"><i data-feather="chevron-right" class="text-white" stroke-width="1"></i> Avenida Rio Churubusco 601</p>
                        <p class="m-0 text-g-yellow"><i data-feather="chevron-right" class="text-white" stroke-width="1"></i> Xoco</p>
                        <p class="m-0 text-g-yellow"><i data-feather="chevron-right" class="text-white" stroke-width="1"></i> Coyoacán, Ciudad de México 03330</p>
                    </div>
                    <div class="col">
                        <h6 class="text-white font-weight-bold">Compartir en:</h6>
                        <div class="line-subtitle"></div>
                        <div class="row justify-content-start">
                            <div class="col-auto text-center">
                                <a href="https://www.facebook.com/GOTOPERUcom/" target="_blank" class="mx-2">
                                    <i data-feather="facebook" class="text-white" stroke-width="1"></i>
                                </a>
                                <a href="https://twitter.com/GOTOPERUCOM" target="_blank" class="mx-2">
                                    <i data-feather="twitter" class="text-white" stroke-width="1"></i>
                                </a>
                                <a href="https://www.youtube.com/channel/UCWjJ10j-_BfNTDnmjBug8Ng" target="_blank" class="mx-2">
                                    <i data-feather="youtube" class="text-white" stroke-width="1"></i>
                                </a>
                                <a href="https://www.instagram.com/go.to.peru/" target="_blank" class="mx-2">
                                    <i data-feather="instagram" class="text-white" stroke-width="1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center my-5">
                    <div class="tl-1"></div>
                    <div class="tl-2"><img src="{{asset('images/logo-andes-ave-white.png')}}" alt="" class="w-100" loading="lazy"></div>
                    <div class="tl-3"></div>
                </div>
                <div class="row mb-4 text-center">
                    <div class="col">
                        <a href="{{route('sobre_nosotros_path')}}" class="text-white">Nosotros</a>
                    </div>
                    <div class="col">
                        <a href="https://api.whatsapp.com/send?phone=51922300244" target="_blank" class="text-white">Contáctanos</a>
                    </div>
                    <div class="col">
                        <a href="" class="text-white">Políticas del Sitio</a>
                    </div>
                    <div class="col">
                        <a href="" class="text-white">Aviso de Privacidad</a>
                    </div>
                    <div class="col">
                        <a href="" class="text-white">Agentes de Viaje</a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col text-center">
                        <small class="text-white">Grupo GOTOPERU 2022. Todos los derechos reservados.</small>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</div>
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="https://apps.elfsight.com/p/platform.js"></script>
<script src="https://player.vimeo.com/api/player.js"></script>
{{--<script src="https://d335luupugsy2.cloudfront.net/js/loader-scripts/d1b447b4-892c-4562-a6dc-536d0eb23944-loader.js" ></script>--}}
@stack('scripts')
<script>
    feather.replace();

    $(document).ready(function(){
        $('.venobox').venobox();
    });

    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if( target.length ) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top
            }, 1000);
        }
    });
</script>
    <script>
        /*global $ */
        $(document).ready(function() {

            "use strict";

            $('.menu > ul > li:has( > ul)').addClass('menu-dropdown-icon');
            //Checks if li has sub (ul) and adds class for toggle icon - just an UI

            $('.menu > ul > li > ul:not(:has(ul))').addClass('normal-sub');
            //Checks if drodown menu's li elements have anothere level (ul), if not the dropdown is shown as regular dropdown, not a mega menu (thanks Luka Kladaric)

            $(".menu > ul").before("<a href=\"#\" class=\"menu-mobile text-white\">Menu</a>");

            //Adds menu-mobile class (for mobile toggle menu) before the normal menu
            //Mobile menu is hidden if width is more then 959px, but normal menu is displayed
            //Normal menu is hidden if width is below 959px, and jquery adds mobile menu
            //Done this way so it can be used with wordpress without any trouble

            $(".menu > ul > li").hover(function(e) {
                if ($(window).width() > 943) {
                    $(this).children("ul").stop(true, false).fadeToggle(150);
                    e.preventDefault();
                }
            });
            //If width is more than 943px dropdowns are displayed on hover

            $(".menu > ul > li").click(function() {
                if ($(window).width() <= 943) {
                    $(this).children("ul").fadeToggle(150);
                }
            });
            //If width is less or equal to 943px dropdowns are displayed on click (thanks Aman Jain from stackoverflow)

            $(".menu-mobile").click(function(e) {
                $(".menu > ul").toggleClass('show-on-mobile');
                e.preventDefault();
            });
            //when clicked on mobile-menu, normal menu is shown as a list, classic rwd menu story (thanks mwl from stackoverflow)

        });
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });


    </script>
{{--    <!-- begin olark code -->--}}
{{--    <script type="text/javascript" async> ;(function(o,l,a,r,k,y){if(o.olark)return; r="script";y=l.createElement(r);r=l.getElementsByTagName(r)[0]; y.async=1;y.src="//"+a;r.parentNode.insertBefore(y,r); y=o.olark=function(){k.s.push(arguments);k.t.push(+new Date)}; y.extend=function(i,j){y("extend",i,j)}; y.identify=function(i){y("identify",k.i=i)}; y.configure=function(i,j){y("configure",i,j);k.c[i]=j}; k=y._={s:[],t:[+new Date],c:{},l:a}; })(window,document,"static.olark.com/jsclient/loader.js");--}}
{{--        /* custom configuration goes here (www.olark.com/documentation) */--}}
{{--        olark.identify('7508-290-10-8562');</script>--}}
{{--    <!-- end olark code -->--}}





    <!-- Start of HubSpot Embed Code -->
{{--    <script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/6987988.js"></script>--}}
    <!-- End of HubSpot Embed Code -->
{{--    <script type="text/javascript" src="//downloads.mailchimp.com/js/signup-forms/popup/unique-methods/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script><script type="text/javascript">window.dojoRequire(["mojo/signup-forms/Loader"], function(L) { L.start({"baseUrl":"mc.us4.list-manage.com","uuid":"fa3bd9ea0b7c6d934efc75509","lid":"6f367adb75","uniqueMethods":true}) })</script>--}}
{{--    <script type="text/javascript" src="https://widget.sirena.app/get?token=zdF9nfRDq1GYnzBarfuv3NTwoEBBmEga" async="true"></script>--}}

</body>
</html>
