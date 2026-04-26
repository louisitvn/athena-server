@extends('layouts.core.frontend_public', [
    'menu' => 'landingpage',
])

@section('page_title')
    {{ trans('server::messages.welcome_to_athena') }}
@endsection

@section('content')

<div class="wrapper">


    <nav class="navbar navbar-expand-lg navbar-light  ">
        <div class="container"> 
            <a href="{{ '#' }}" class="navbar-brand text-start"> 
                <img src="{{ getSiteLogoUrl('dark') }}?v=1" alt="EmailEVS"> 
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="https://rencontru.com/service/"> {{ trans('server::messages.ladi.menu.services') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://rencontru.com">{{ trans('server::messages.ladi.menu.sulations') }}</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="https://rencontru.com/shop2/">{{ trans('server::messages.ladi.menu.pricing') }}</a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link " href="https://rencontru.com" tabindex="-1" aria-disabled="true">{{ trans('server::messages.ladi.menu.resources') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="https://rencontru.com" tabindex="-1" aria-disabled="true">{{ trans('server::messages.ladi.menu.company') }}</a>
                    </li>
                </ul>
                <ul class="d-flex navbar-nav justify-items-center align-items-center">
                    <li class="nav-item dropdown"> 
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg class="language-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-120q-74.31 0-140-28.42-65.69-28.43-114.42-77.16-48.73-48.73-77.16-114.42Q120-405.69 120-480q0-74.54 28.42-140.12 28.43-65.57 77.16-114.3 48.73-48.73 114.42-77.16Q405.69-840 480-840q74.54 0 140.12 28.42 65.57 28.43 114.3 77.16 48.73 48.73 77.16 114.3Q840-554.54 840-480q0 74.31-28.42 140-28.43 65.69-77.16 114.42-48.73 48.73-114.3 77.16Q554.54-120 480-120Zm0-39.69q35.23-45.23 58.08-88.85 22.84-43.61 37.15-97.61H384.77q15.85 57.07 37.92 100.69 22.08 43.61 57.31 85.77Zm-50.92-6q-28-33-51.12-81.58-23.11-48.58-34.42-98.88H190.15q34.39 74.61 97.5 122.38 63.12 47.77 141.43 58.08Zm101.84 0q78.31-10.31 141.43-58.08 63.11-47.77 97.5-122.38H616.46q-15.15 51.07-38.27 99.65-23.11 48.58-47.27 80.81ZM173.85-386.15h161.38q-4.54-24.62-6.42-47.97-1.89-23.34-1.89-45.88 0-22.54 1.89-45.88 1.88-23.35 6.42-47.97H173.85q-6.54 20.77-10.2 45.27Q160-504.08 160-480t3.65 48.58q3.66 24.5 10.2 45.27Zm201.38 0h209.54q4.54-24.62 6.42-47.2 1.89-22.57 1.89-46.65t-1.89-46.65q-1.88-22.58-6.42-47.2H375.23q-4.54 24.62-6.42 47.2-1.89 22.57-1.89 46.65t1.89 46.65q1.88 22.58 6.42 47.2Zm249.54 0h161.38q6.54-20.77 10.2-45.27Q800-455.92 800-480t-3.65-48.58q-3.66-24.5-10.2-45.27H624.77q4.54 24.62 6.42 47.97 1.89 23.34 1.89 45.88 0 22.54-1.89 45.88-1.88 23.35-6.42 47.97Zm-8.31-227.7h153.39Q734.69-690 673.5-736.23q-61.19-46.23-142.58-58.85 28 36.85 50.35 84.27 22.35 47.43 35.19 96.96Zm-231.69 0h190.46q-15.85-56.3-39.08-101.84-23.23-45.54-56.15-84.62-32.92 39.08-56.15 84.62-23.23 45.54-39.08 101.84Zm-194.62 0h153.39q12.84-49.53 35.19-96.96 22.35-47.42 50.35-84.27-82.16 12.62-142.96 59.23-60.81 46.62-95.97 122Z"/></svg>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            
                            <li>
                                <a class="dropdown-item" href="#">
                                    {{ trans('server::messages.ladi.menu.english') }}
                                </a>
                            </li> 
                            <li>
                                <a class="dropdown-item" href="#">
                                    {{ trans('server::messages.ladi.menu.vietnamese') }} 
                                </a>
                            </li> 
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ '#' }}" class="btn-custom-login">{{ trans('server::messages.ladi.menu.login') }} </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/users/register') }}" class="btn btn-yellow btn-lg btn-custom-register">{{ trans('server::messages.ladi.menu.signup') }} </a>
                    </li>
                </ul> 
    
            </div>

        </div>
    </nav>


    <div class="hintro">  
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 col-sm-5 col-md-5"> 
                    <div class="intro"> 
                        <div class="my-4">
                            <h1>
                                {{ trans('server::messages.ladi.intro.title') }} 
                            </h1>
                        </div>
                        <p class="mb-4">
                            {{ trans('server::messages.ladi.intro.description') }}  
                        </p>
                        <div class="mb-4">
                            <div class="d-flex">
                                <div class="col-sm-8 col-md-8 mx-0 px-0"> 
                                    <a href="{{ url('/users/register') }}" class="btn btn-yellow btn-lg btn-custom-tryfree form-control h-auto"> 
                                        {{ trans('server::messages.ladi.intro.tryfree') }}  
                                    </a>
                                    <p class="mt-3">
                                        <i>
                                            {{ trans('server::messages.ladi.intro.get_free_monthly') }}  
                                        </i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-sm-7 col-md-7"> 
                    <div class="intro-image">
                        <img src="{{ asset('em/images/intro-image.png') }}?v=1" alt="">
                        <div class="intro-banner">
                                <div class="intro__image-decoration shape-color-5F"><svg id="intro_image" width="1020" height="969" viewBox="0 0 970 969" xmlns="http://www.w3.org/2000/svg"><circle cx="1179" cy="674.5" r="484.5" transform="translate(-694 -190)" fill="currentColor" fill-rule="evenodd"></circle></svg></div>       
                        </div>
                    </div> 
                </div>
                <div class="col-lg-12 col-sm-12 col-md-12">
                    <ul class="security-list">
                        <li>
                            <img id="security_img_2" alt="soc2" decoding="async" height="56" loading="lazy" src="{{ AppUrl::asset('em/images/soc2type2.png') }}" width="184">
                        </li>
                        <li>
                            <img id="security_img_3" alt="soc2" decoding="async" height="52" loading="lazy" src="{{ AppUrl::asset('em/images/iso-certified.png') }}" width="162">
                        </li>
                        <li>
                            <img id="security_img_hipaa" alt="hipaa" decoding="async" height="67" loading="lazy" src="{{ AppUrl::asset('em/images/hipaa-compliant.png') }}" width="128">
                        </li>
                        <li>
                            <img id="security_img_4" alt="gdpr" decoding="async" height="55" loading="lazy" src="{{ AppUrl::asset('em/images/eu-gdpr.png') }}" width="192">
                        </li>
                        <li>
                            <img id="security_img_5" alt="Data Privacy Framework (DPF)" decoding="async" height="57" loading="lazy" src="{{ AppUrl::asset('em/images/privacy-shield.png') }}" width="192"></li><li><img id="security_img_6" alt="ccpa" decoding="async" height="65" loading="lazy" src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=184,metadata=none/static/ccpa.png" width="184">
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="container partners_list">
        <div class="row">
            <div class="col-md-12">
                <div class="partners_list_header">
                    {{ trans('server::messages.ladi.partner.title') }} 
                </div>
            </div>
            <div class="col-md-12 text-center"> 

   
                <div class="container text-center my-3">
                    <div class="row mx-auto my-auto justify-content-center">
                        <div id="recipeCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner" role="listbox">
                                <div class="carousel-item active">
                                    <div class="col-md-3 ">
                                        <img src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=120,metadata=none/static/vistaprint-logo.webp" class="img-fluid">
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="col-md-3"> 
                                        <img src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=123,metadata=none/static/strava-logo.jpg" class="img-fluid">
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="col-md-3"> 
                                        <img src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=120,metadata=none/static/tata-nexarc-logo.png" class="img-fluid">
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="col-md-3"> 
                                        <img src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=105,metadata=none/static/unilever-logo.png" class="img-fluid">
                                     </div>
                                </div>
                                 
                            </div>
                            <a class="carousel-control-prev bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </a>
                            <a class="carousel-control-next bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </a>
                        </div>
                    </div>		
                </div>



              

            </div>
        </div>
    </div>

 
    <div class="verify"> 
        <div class="verify-content">
            <!-- VERIFY MODULE -->
            @include('server::landing_page._verify')
        </div>
    
    </div>

    <div class="validate">
        <div class="container">
            <div class="row align-items-center justify-content-center mb-4">
                <div class="col-sm-6 col-md-6 text-center">
                    <div class="validate-title text-center">
                        <h2>{{ trans('server::messages.ladi.validate.title') }} </h2> 
                    </div>
                    <p>{{ trans('server::messages.ladi.validate.description') }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-4">
                    <div class="card h-100 mb-4">
                        <div class="card-body"> 
                            <div class="align-item-center text-center">  
                                <div class="me-3 d-flex flex-column align-item-center text-center">
                                    <div class="d-flex justify-content-center align-items-center items-center mb-2">
                                        <img src="{{ asset('em/images/step-1.png') }}?v=1">
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="step-title">{{ trans('server::messages.ladi.validate.step_1') }}</p> 
                                    </div>
                                </div>
                                <div class="step-alias">   
                                    {{ trans('server::messages.ladi.validate.create_account') }}
                                </div>
                            </div> 
                        </div>
                    </div>                
                </div>
                <div class="col-sm-4 col-md-4">
                    <div class="card h-100 mb-4">
                        <div class="card-body"> 
                            <div class="align-item-center text-center">  
                                <div class="me-3 d-flex flex-column align-item-center text-center">
                                    <div class="d-flex justify-content-center align-items-center items-center mb-2">
                                        <img src="{{ asset('em/images/step-2.png') }}?v=1">
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="step-title">{{ trans('server::messages.ladi.validate.step_2') }}</p> 
                                    </div>
                                </div>
                                <div class="step-alias"> 
                                    {{ trans('server::messages.ladi.validate.upload_your_email') }}                                    
                                </div>
                            </div> 
                        </div>
                    </div>                
                </div>
                <div class="col-sm-4 col-md-4">
                    <div class="card h-100 mb-4">
                        <div class="card-body"> 
                            <div class="align-item-center text-center"> 
                                <div class="me-3 d-flex flex-column align-item-center text-center">
                                    <div class="d-flex justify-content-center align-items-center items-center mb-2">
                                        <img src="{{ asset('em/images/step-3.png') }}?v=1">
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="step-title ">{{ trans('server::messages.ladi.validate.step_3') }}</p> 
                                    </div>
                                </div>
                                <div class="step-alias">
                                    {{ trans('server::messages.ladi.validate.download_your_email_list') }}
                                </div>
                            </div> 
                        </div>
                    </div>                
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 text-center my-4 py-4">
                    <p class="mb-4">
                        {{ trans('server::messages.ladi.validate.send_email_with_confidence') }}
                    </p>
                    <a href="{{ url('/users/register') }}" class="btn btn-yellow btn-lg btn-custom-tryfree  h-auto">
                        {{ trans('server::messages.ladi.validate.tryfree') }}  
                    </a>
                    <p class="mt-2">
                        {{ trans('server::messages.ladi.validate.get_free_monthly') }}   
                    </p>
                </div>        
            </div>
        </div>
    </div>

    <div class="deliverability">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h2 class="deliverability-title">
                        {{ trans('server::messages.ladi.deliverability.title') }}  
                    </h2>
                </div>
            </div>
            <div class="row mt-4 pt-4">
                <div class="col-md-4">
                    <ul class="nav nav-tabs d-flex flex-column w-30 " role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="rdMain-tab" type="button"
                                data-bs-toggle="tab" data-bs-target="#Validation"
                                role="tab" aria-controls="rdMain" aria-selected="true">
                                <strong>{{ trans('server::messages.ladi.deliverability.validation') }} </strong> <img class="me-2" src="{{ asset('em/images/check-shape-lg-base.inline.png') }}" alt=""> 
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cuMain-tab" data-bs-toggle="tab"
                                data-bs-target="#Activity" type="button"
                                role="tab" aria-controls="cuMain" aria-selected="false">   {{ trans('server::messages.ladi.deliverability.activity') }}  <img class="me-2" src="{{ asset('em/images/activity-data-active.png') }}" alt=""> 
                            </button>
                        </li> 
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cuMain-tab" data-bs-toggle="tab"
                                data-bs-target="#Scoring" type="button"
                                role="tab" aria-controls="cuMain" aria-selected="false">{{ trans('server::messages.ladi.deliverability.scoring') }}  <img class="me-2" src="{{ asset('em/images/email-score-active.avif') }}" alt=""> </button>
                        </li> 
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cuMain-tab" data-bs-toggle="tab"
                                data-bs-target="#DMARC" type="button"
                                role="tab" aria-controls="cuMain" aria-selected="false">{{ trans('server::messages.ladi.deliverability.DMARC') }}  <img class="me-2" src="{{ asset('em/images/dmarc-active.png') }}" alt=""> </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cuMain-tab" data-bs-toggle="tab"
                                data-bs-target="#Blacklist" type="button"
                                role="tab" aria-controls="cuMain" aria-selected="false">{{ trans('server::messages.ladi.deliverability.blacklist') }} <img class="me-2" src="{{ asset('em/images/blacklist-active.png') }}" alt=""> </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cuMain-tab" data-bs-toggle="tab"
                                data-bs-target="#Testing" type="button"
                                role="tab" aria-controls="cuMain" aria-selected="false">{{ trans('server::messages.ladi.deliverability.testing_tools') }}  <img class="me-2" src="{{ asset('em/images/email-testing-active.png') }}" alt=""> </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cuMain-tab" data-bs-toggle="tab"
                                data-bs-target="#Finder" type="button"
                                role="tab" aria-controls="cuMain" aria-selected="false">{{ trans('server::messages.ladi.deliverability.email_finder') }}  <img class="me-2" src="{{ asset('em/images/email-finder-active.png') }}" alt=""> </button>
                        </li>
                
                    </ul>

                </div>
                <div class="col-md-8"> 

                        <div class="tab-content tab-custom" id="tunnelsTabContent">
                            
                            <div class="tab-pane fade show active" id="Validation" role="tabpanel"  aria-labelledby="rdMain-tab" tabindex="0">
                            
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3>{{ trans('server::messages.ladi.deliverability.clean_your_list') }}</h3>
                                        <p>{{ trans('server::messages.ladi.deliverability.clean_description') }}</p>
                                        <ul class="check mb-3">
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.deliverability.clean_invalid') }}  </li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.deliverability.clean_abuse') }} </li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.deliverability.clean_span_traps') }} </li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.deliverability.clean_disposable') }}</li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.deliverability.clean_catch_all') }}</li>
                                        </ul> 
                                        <p>
                                            {{ trans('server::messages.ladi.deliverability.plus_identify') }}
                                        </p>
                                        <a href="{{ route('acelle_server.validate.index') }}" class="btn btn-yellow btn-lg btn-custom-tryfree  h-auto">
                                            {{ trans('server::messages.ladi.deliverability.clean_your_list') }}
                                        </a>
                                    </div>
                                    <div class="col-sm-6"> 
                                            <img src="{{ asset('em/images/email-validation-homepage.png') }}?v=1" alt=""> 
                                    </div>
                                </div>
                                

                            </div>
                            <div class="tab-pane fade show " id="Activity" role="tabpanel"  aria-labelledby="rdMain-tab" tabindex="0">
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3> {{ trans('server::messages.ladi.deliverability.increase') }}</h3>
                                        {!! trans('server::messages.ladi.deliverability.increase_des') !!}
                                        
                                        <a href="{{ route('acelle_server.validate.index') }}" class="btn btn-yellow btn-lg btn-custom-tryfree  h-auto"> 
                                            {!! trans('server::messages.ladi.deliverability.find_my_active_data') !!} 
                                        </a>
                                    </div>
                                    <div class="col-sm-6"> 
                                            <img src="{{ asset('em/images/activity-data-homepage.png') }}?v=1" alt=""> 
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="Scoring" role="tabpanel"  aria-labelledby="cuMain-tab" tabindex="0">
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3>{{ trans('server::messages.ladi.deliverability.catch_all_email') }}</h3>
                                        {!! trans('server::messages.ladi.deliverability.catch_all_des') !!}
                                        <a href="{{ '#' }}" class="btn btn-yellow btn-lg btn-custom-tryfree  h-auto">
                                            {{ trans('server::messages.ladi.deliverability.score_my_email') }}
                                        </a>
                                    </div>
                                    <div class="col-sm-6"> 
                                        <img src="{{ asset('em/images/email-scoring-homepage.png') }}?v=1" alt=""> 
                                    </div>
                                </div> 
                            </div>

                            <div class="tab-pane fade" id="DMARC" role="tabpanel"  aria-labelledby="cuMain-tab" tabindex="0">
                            
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3>{{ trans('server::messages.ladi.deliverability.DMARC_security') }}  </h3>
                                        {!! trans('server::messages.ladi.deliverability.DMARC_security_des') !!} 
                                        <ul class="check mb-3">
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.deliverability.DMARC_security_in_depth') }}</li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.deliverability.DMARC_security_policy') }}</li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.deliverability.DMARC_security_automated_reporting') }}</li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt="">{{ trans('server::messages.ladi.deliverability.DMARC_security_instant_alerts') }}</li> 
                                        </ul> 
                                        <p>{{ trans('server::messages.ladi.deliverability.make_DMARC_monitoring') }}</p>
                                        <a href="#" class="btn btn-yellow btn-lg btn-custom-tryfree  h-auto">
                                            {{ trans('server::messages.ladi.deliverability.DMARC_monitor_my_domain') }}
                                        </a>
                                    </div>
                                    <div class="col-sm-6"> 
                                            <img src="{{ asset('em/images/dmarc-homepage.png') }}?v=1" alt=""> 
                                    </div>
                                </div> 

                            </div>
                            <div class="tab-pane fade show" id="Blacklist" role="tabpanel"  aria-labelledby="rdMain-tab" tabindex="0">
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3> {{ trans('server::messages.ladi.blacklist.247_title') }}</h3>
                                        {!! trans('server::messages.ladi.blacklist.247_des') !!} 
                                        <ul class="check mb-3">
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.blacklist.standard_email_domain') }}</li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.blacklist.ip4') }}</li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt=""> {{ trans('server::messages.ladi.blacklist.ip6') }} </li> 
                                        </ul> 
                                        <p>{{ trans('server::messages.ladi.blacklist.reputation') }}</p>
                                        <a href="{{ '#' }}" class="btn btn-yellow btn-lg btn-custom-tryfree  h-auto">
                                            {{ trans('server::messages.ladi.blacklist.try_monitoring') }}
                                        </a>
                                    </div>
                                    <div class="col-sm-6"> 
                                        <img src="{{ asset('em/images/blacklist-homepage.png') }}?v=1" alt=""> 
                                    </div>
                                </div> 

                            </div>

                            <div class="tab-pane fade" id="Testing" role="tabpanel"  aria-labelledby="cuMain-tab" tabindex="0">
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3>{{ trans('server::messages.ladi.deliverability.text_email') }}</h3>
                                        <p>{{ trans('server::messages.ladi.deliverability.text_des1') }}</p> 
                                        <ul class="check mb-3">
                                            <li><img src="{{ asset('em/images/check.png') }}" alt="">{{ trans('server::messages.ladi.deliverability.text_check1') }}</li>
                                            <li><img src="{{ asset('em/images/check.png') }}" alt="">{{ trans('server::messages.ladi.deliverability.text_check2') }}</li>
                                        </ul> 
                                        <p>{{ trans('server::messages.ladi.deliverability.text_des2') }}</p>
                                        <a href="{{ route('acelle_server.validate.index') }}" class="btn btn-yellow btn-lg btn-custom-tryfree  h-auto">
                                            {{ trans('server::messages.ladi.deliverability.test_my_email') }}
                                        </a>
                                    </div>
                                    <div class="col-sm-6"> 
                                            <img src="{{ asset('em/images/email-testing-homepage.png') }}" alt=""> 
                                    </div>
                                </div> 

                            </div>

                            <div class="tab-pane fade" id="Finder" role="tabpanel"  aria-labelledby="cuMain-tab" tabindex="0">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3>{{ trans('server::messages.ladi.blacklist.free_email') }}</h3>
                                        {!! trans('server::messages.ladi.blacklist.free_email_des') !!} 
                                        <a href="{{ '#' }}" class="btn btn-yellow btn-lg btn-custom-tryfree  h-auto">
                                            {{ trans('server::messages.ladi.blacklist.find_new_b2b_emails') }}
                                        </a>
                                    </div>
                                    <div class="col-sm-6"> 
                                            <img src="{{ asset('em/images/email-finder-homepage.png') }}" alt=""> 
                                    </div>
                                </div> 
                            </div>
                            
                        </div>
    

                </div>
            </div>
        </div>
    </div>



    <div class="whychoose ">
        <div class="container">
            <div class="row align-items-center justify-content-center mb-4">
                <div class="col-sm-6 col-md-6 text-center">
                    <div class="validate-title text-center">
                        <h2>{{ trans('server::messages.ladi.whychoose.title') }}</h2> 
                    </div>
                    {!! trans('server::messages.ladi.whychoose.des') !!}
                </div>
            </div>
            <div class="row mb-4 pb-4">
                <div class="col-sm-4 col-md-4">
                    <div class="card h-100 mb-4">
                        <div class="card-body"> 
                            <div class="align-item-center text-center">  
                                <div class="me-3 d-flex flex-column align-item-center text-center mb-4">
                                    <div class="d-flex justify-content-center align-items-center items-center mb-2">
                                        <img src="{{ asset('em/images/icon-envelope.webp') }}">
                                    </div>
                                </div>
                                <div class="step-alias mb-4">
                                    {{ trans('server::messages.ladi.whychoose.accurate_email_verification') }} 
                                </div>
                                <div class="step_entry">
                                    {!! trans('server::messages.ladi.whychoose.entry') !!}
                                </div>
                            </div> 
                        </div>
                    </div>                
                </div>
                <div class="col-sm-4 col-md-4">
                    <div class="card h-100 mb-4">
                        <div class="card-body"> 
                            <div class="align-item-center text-center">  
                                <div class="me-3 d-flex flex-column align-item-center text-center mb-4">
                                    <div class="d-flex justify-content-center align-items-center items-center mb-2">
                                        <img src="{{ asset('em/images/icon-shield.webp') }}">
                                    </div> 
                                </div>
                                <div class="step-alias mb-4">  
                                    {{ trans('server::messages.ladi.whychoose.secure_email_validation') }} 
                                </div>
                                <div class="step_entry">
                                    {!! trans('server::messages.ladi.whychoose.secure_email_validation_des') !!} 
                                </div>
                            </div> 
                        </div>
                    </div>                
                </div>
                <div class="col-sm-4 col-md-4">
                    <div class="card h-100 mb-4">
                        <div class="card-body"> 
                            <div class="align-item-center text-center"> 
                                <div class="me-3 d-flex flex-column align-item-center text-center mb-4">
                                    <div class="d-flex justify-content-center align-items-center items-center mb-2">
                                        <img src="{{ asset('em/images/icon-thumb-up.webp') }}">
                                    </div> 
                                </div>
                                <div class="step-alias mb-4">
                                    {{ trans('server::messages.ladi.whychoose.customer_support') }} 
                                </div>
                                <div class="step_entry">
                                    {!! trans('server::messages.ladi.whychoose.customer_support_des') !!} 
                                </div>
                            </div> 
                        </div>
                    </div>                
                </div>
            </div>

            <div class="row mt-4 pt-4">
                <div class="col-sm-7 col-md-7">
                    <div class="integration-intro"> 
                        <div class="integration-banner">
                            <img src="{{ asset('em/images/integration-img.avif') }}" style="max-width:100%" alt="">
                        </div>
                        <div class="integration-image">
                            <svg width="662" height="662" viewBox="0 0 662 662" xmlns="http://www.w3.org/2000/svg"><circle cx="339" cy="4950" r="331" transform="translate(-8 -4619)" fill="#FDF7DF" fill-rule="evenodd" fill-opacity=".597"></circle></svg>
                        </div>
                    </div> 
                </div>
                <div class="col-sm-5 col-md-5">
                    <div class="integration">
                        <div class="integration-title">
                            <h3>{{ trans('server::messages.ladi.whychoose.verification_favorite_platforms') }} </h3> 
                        </div>
                        <div class="integration-body">
                            {!! trans('server::messages.ladi.whychoose.verification_favorite_platforms_des') !!}  
                        </div>
                       
                    </div> 
                    <a href="{{ '#' }}" class="btn btn-yellow btn-lg btn-custom-tryfree h-auto">
                        {{ trans('server::messages.ladi.whychoose.view_all_integrations') }}
                    </a> 
                    
                </div>
            </div>
        </div>
    </div>

    <div class="guaranteeing">
        <div class="container">
            <div class="row align-items-center justify-content-center mb-4">
                <div class="col-sm-12 col-md-12 text-center">
                    <div class="guaranteeing-title">
                        <h2>{{ trans('server::messages.ladi.guaranteeing.title') }}</h2>
                        <p>{{ trans('server::messages.ladi.guaranteeing.title_des') }}.</p>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12  "> 
                    <div id="guaranteeingCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active">
                                <div class="col-md-6">
                                    <div class="carousel-body">
                                        <img src="{{ asset('em/images/Paul-Buchheit-Interview-350x200.jpg') }}?v=1" class="img-fluid">
                                        {{ trans('server::messages.ladi.guaranteeing.title_des') }}
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="col-md-6">  
                                    <div class="carousel-body">
                                        <img src="{{ asset('em/images/tech-news-1-350x200.jpg') }}?v=1" class="img-fluid">
                                        {!! trans('server::messages.ladi.guaranteeing.title_des2') !!}
                                    </div>
                                </div>
                            </div> 
                             
                        </div>
                        <a class="carousel-control-prev bg-transparent w-aut" href="#guaranteeingCarousel" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </a>
                        <a class="carousel-control-next bg-transparent w-aut" href="#guaranteeingCarousel" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </a>
                    </div>



                </div>
            </div>
            <div class="row align-items-center justify-content-center mb-4">
                <div class="col-sm-12 col-md-12 text-center">
                    <div class="guaranteeing-title">
                        <h2>
                            {{ trans('server::messages.ladi.guaranteeing.industry_leading') }}
                        </h2>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12"> 

 

                    <div id="industryCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active">
                                <div class="col-md-3 ">
                                    <img src="{{ asset('em/images/Spring3.avif') }}" class="img-fluid">
                                 </div>
                            </div>
                            <div class="carousel-item">
                                <div class="col-md-3"> 
                                    <img src="{{ asset('em/images/Spring6.avif') }}" class="img-fluid"> 
                                </div>
                            </div> 
                            <div class="carousel-item">
                                <div class="col-md-3"> 
                                    <img src="{{ asset('em/images/Spring7.avif') }}" class="img-fluid"> 
                                </div>
                            </div> 
                            <div class="carousel-item">
                                <div class="col-md-3"> 
                                    <img src="{{ asset('em/images/Spring8.avif') }}" class="img-fluid"> 
                                </div>
                            </div> 
                            <div class="carousel-item">
                                <div class="col-md-3"> 
                                    <img src="{{ asset('em/images/Spring9.avif') }}" class="img-fluid"> 
                                </div>
                            </div> 
                            <div class="carousel-item">
                                <div class="col-md-3"> 
                                    <img src="{{ asset('em/images/Spring12.avif') }}" class="img-fluid"> 
                                </div>
                            </div> 
                            <div class="carousel-item">
                                <div class="col-md-3"> 
                                    <img src="{{ asset('em/images/Spring11.avif') }}" class="img-fluid"> 
                                </div>
                            </div> 
                            <div class="carousel-item">
                                <div class="col-md-3"> 
                                    <img src="{{ asset('em/images/imagesource-study.avif') }}" class="img-fluid"> 
                                </div>
                            </div> 
                            <div class="carousel-item">
                                <div class="col-md-3"> 
                                    <img src="{{ asset('em/images/imagesource-study.avif') }}" class="img-fluid"> 
                                </div>
                            </div> 
                        </div>
                        <a class="carousel-control-prev bg-transparent w-aut" href="#guaranteeingCarousel" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </a>
                        <a class="carousel-control-next bg-transparent w-aut" href="#guaranteeingCarousel" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </a>
                    </div>


                </div>
            </div>
            <div class="row align-items-center justify-content-center mb-4">
                <div class="col-sm-12 col-md-12">
                    <div class="guaranteeing-title">
                        <h2>
                            {{ trans('server::messages.ladi.guaranteeing.last_our_blog') }}
                        </h2>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 text-center">
                    ...
                </div>
            </div>

        </div>
    </div>

<div class="multiple">
    <div class="container">
        <div class="row align-items-center justify-content-center mb-4">
            <div class="col-sm-5 col-md-5">
                <div class="multiple-title ">
                    <h2>{{ trans('server::messages.ladi.multiple.title') }}</h2> 
                </div>
                {!! trans('server::messages.ladi.multiple.des') !!}
            </div> 
            <div class="col-sm-7 col-md-7 ">
                <div class="multiple-banner">
                    <img src="{{ asset('em/images/reward-img.webp') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="featuredon">
    <div class="container">
        <div class="row align-items-center justify-content-center mb-4">
            <div class="col-sm-12 col-md-12">
                <div class="featuredon-title">
                    <h3>{{ trans('server::messages.ladi.multiple.as_featured_on')}}</h3> 
                </div> 
            </div> 
            <div class="col-sm-12 col-md-12 ">
                <div class="featuredon-banner"> 
                    <ul class="featuredon-list">
                        <li>
                          <a rel="nofollow noopener noreferrer" href="#" target="_blank" title="As seen on INC 5000">
                            <img id="featured_img_1" alt="Inc Logo" decoding="async" height="34" loading="lazy" src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=145.5,metadata=none/static/inc-logo.webp" width="97">
                          </a>
                        </li>
                        <li>
                          <a rel="nofollow noopener noreferrer" href="#" title="Msn Logo" target="_blank">
                            <img id="featured_img_2" alt="Msn Logo" decoding="async" height="40" loading="lazy" src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=110,metadata=none/static/msn_logo.jpg" width="110">
                          </a>
                        </li>
                        <li>
                          <a rel="nofollow noopener noreferrer" href="#" title="{{ get_app_name() }} as seen on Money.com" target="_blank">
                            <img id="featured_img_3" alt="Money Logo" decoding="async" height="36" loading="lazy" src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=112,metadata=none/static/money-logo.webp" width="112">
                          </a>
                        </li>
                        <li>
                          <a rel="nofollow noopener noreferrer" href="#" title="{{ get_app_name() }} as seen on BuzzFeed" target="_blank">
                            <img id="featured_img_4" alt="Buzz Feed Logo" decoding="async" height="23" loading="lazy" src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=128,metadata=none/static/buzz-feed-logo.webp" width="128">
                          </a>
                        </li>
                        <li>
                          <a rel="nofollow noopener noreferrer" href="#" title="Entrepreneur Logo" target="_blank">
                            <img id="featured_img_5" alt="Entrepreneur Logo" decoding="async" height="28" loading="lazy" src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=139,metadata=none/static/entrepreneur-logo.webp" width="139">
                          </a>
                        </li>
                        <li>
                          <a rel="nofollow noopener noreferrer" href="h#" title="{{ get_app_name() }} as seen on Forbes" target="_blank">
                            <img id="featured_img_6" alt="Forbes Logo" decoding="async" height="28" loading="lazy" src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=112,metadata=none/static/forbes-logo.webp" width="112">
                          </a>
                        </li>
                        <li>
                          <a rel="nofollow noopener noreferrer" href="#" title="StarterStory Logo" target="_blank">
                            <img id="featured_img_7" alt="StarterStory Logo" decoding="async" height="21" loading="lazy" src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=220,metadata=none/static/starter_story_logo.png" width="220">
                          </a>
                        </li>
                        <li>
                          <a href="#" title="Natfluence Logo" target="_blank" rel="nofollow">
                            <img id="featured_img_8" alt="Natfluence Logo" decoding="async" height="21" loading="lazy" src="https://www.zerobounce.net/cdn-cgi/image/fit=scale-down,format=auto,quality=90,width=120,metadata=none/static/natfluence.jpg" width="120">
                          </a>
                        </li>
                      </ul>


                </div>
            </div>
        </div>
    </div>
</div>


<div class="callout">
    <div class="shell-alt">
        <div class="callout_shape"></div>
        <div class="container">
            <div class="row align-items-center justify-content-center mb-4">
                <div class="col-md-6">
                    <div class="shell-title">
                        {{ trans('server::messages.ladi.callout.title')}}
                    </div>
                    <a href="{{ '#' }}" class="btn btn-yellow btn-lg btn-custom-tryfree  h-auto">
                        {{ trans('server::messages.ladi.callout.try_it_free')}}
                    </a>
                    <p class="mt-3">
                        <i>
                            {{ trans('server::messages.ladi.callout.get_free_monthly')}}
                        </i>
                    </p>

                </div>
                <div class="col-md-6">
                    <div class="callout-banner">
                        <div class="callout-image">
                            <img src="{{ asset('em/images/callout-1-2.png') }}?v=1" alt="">
                        </div>
                        <div class="callout-icon">
                            <svg width="31" height="25" viewBox="0 0 31 25" xmlns="http://www.w3.org/2000/svg"><g stroke="#1E8BC2" stroke-width="3" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round"><path d="M29 8.854v11.117a3 3 0 01-3 3H5a3 3 0 01-3-3V4.25A2.25 2.25 0 014.25 2H29l-.932.91"></path><path d="M28.068 2.965L15.5 13.17 2.986 2.965"></path></g></svg>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>


<footer class="footer">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-3">
                <p class="mb-2">
                    <a href="/" class="logo" title="{{ get_app_name() }}">  
                        <img width="180" src="{{ getSiteLogoUrl('dark') }}" alt=""  id="footer_zb_logo" > 
                    </a>
                </p>
                <p class="mb-4" id="footer_rights_reserved">{!! trans('server::messages.ladi.footer.copyright') !!} </p>
            </div>
            <div class="col-md-3">
                <h3 class="h6">{{ trans('server::messages.ladi.footer.company')}} </h3>
            </div>
            <div class="col-md-3">
                <h3 class="h6">{{ trans('server::messages.ladi.footer.legal')}}</h3>
            </div>
            <div class="col-md-3">
                <h3 class="h6">{{ trans('server::messages.ladi.footer.contact_us')}}</h3>
                <div class="fcontact">
                    <ul>
                        <li id="footer_us_contact">US: <a id="footer_us_contact_number" href="tel:+18885009521">1-888-500-9521</a>
                    </li>
                        <li id="footer_eu_contact">EU: <a id="footer_eu_contact_number" href="tel:+443308084814">+44-330-808-4814</a>
                    </li>
                    <li id="footer_support_guaranteed">{{ trans('server::messages.ladi.footer.247_support') }}</li>
                    <li>
                        <a id="footer_mail_to" href="mailto:support@{{ get_app_name() }}.net">support@athena.com</a>
                    </li>
                    </ul>
                </div>
                <div class="socials">
                    <ul>
                      <li>
                        <a href="#" target="_blank" rel="nofollow noopener noreferrer" title="{{ get_app_name() }} Facebook">
                          <div id="socials_facebook" class="bg-facebook"></div> 
                        </a>
                      </li>
                      <li>
                        <a href="#" target="_blank" rel="nofollow noopener noreferrer" title="{{ get_app_name() }} Twitter">
                          <div id="socials_twitter" class="bg-twitter"></div> 
                        </a>
                      </li>
                      <li>
                        <a href="#" target="_blank" rel="nofollow noopener noreferrer" title="{{ get_app_name() }} LinkedIn">
                          <div id="socials_linkedin" class="bg-linkedin"></div> 
                        </a>
                      </li>
                      <li>
                        <a href="#" target="_blank" rel="nofollow noopener noreferrer" title="{{ get_app_name() }} YouTube">
                          <div id="socials_youtube" class="bg-youtube"></div> 
                        </a>
                      </li>
                      <li>
                        <a href="#" target="_blank" rel="nofollow noopener noreferrer" title="{{ get_app_name() }} Instagram">
                          <div id="socials_instagram" class="bg-instagram"></div> 
                        </a>
                      </li>
                      <li>
                        <a href="#" target="_blank" rel="nofollow noopener noreferrer" title="{{ get_app_name() }} Tumblr">
                          <div id="socials_tumblr" class="bg-tumblr"></div>
                           
                        </a>
                      </li>
                    </ul>
                  </div>
            </div>
        </div>
        <div class="row mb-4"> 
            <div class="col-md-12"><hr></div>
        </div>
        <div class="row mb-4"> 
            <div class="col-md-12 text-center">
                <a href="3">
                    <img src="" alt="">
                </a>
                <a href="3">
                    <img src="" alt="">
                </a>
            </div>
            
        </div>
    </div> 
   
  </footer>

 
</div>










<script>
    document.querySelectorAll('.carousel').forEach(carousel => {
        let items = carousel.querySelectorAll('.carousel-item');

        items.forEach((el) => {
            const minPerSlide = 4;
            let next = el.nextElementSibling;
            for (let i = 1; i < minPerSlide; i++) {
                if (!next) {
                    // wrap carousel by using first child
                    next = items[0];
                }
                let cloneChild = next.cloneNode(true);
                el.appendChild(cloneChild.children[0]);
                next = next.nextElementSibling;
            }
        });
    });




</script>






@endsection
