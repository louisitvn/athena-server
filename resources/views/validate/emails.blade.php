@extends('layouts.core.frontend', [
    'menu' => 'validate',
])

@section('page_title')
    validate
@endsection

@section('content') 
    <div id="ValidateIndexContainer">
        <div class="row">
            <div class="col-md-6">
                <div class="card ecard h-100">
                    <div class="card-body">
                        <div class="">
                            <div class="mb-2"> 
                                <div class="d-flex flex-column align-items-center">
                                    <p class="fs-4 py-2 mb-0 fw-semibold highContrastColor">{{ trans('server::messages.upload_list_integration') }} </p> 
                                </div>
                            </div>
                            <div class="mb-0">
                                <button class="btn btn-primary fw-bold text-nowrap w-100" id="list">{{ trans('server::messages.validate.new_list') }}</button>
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card ecard h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-3 d-flex align-item-center pe-4">
                                <div class="ebadge medium text-bg-success me-4 d-flex justify-content-center align-items-center">
                                    <img src="{{ asset('em/images/emailColor.svg') }}"  alt="">
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="fs-4 mb-0 fw-semibold highContrastColor">{{ trans('server::messages.single_email_validation') }}  </p> 
                                    <p class="text-muted fw-600">{{ trans('server::messages.verify_up_to_emails') }}</p>
                                </div>
                            </div>
                            <div class="my-2">
                                <a href="{{ route('acelle_server.validate.bulk') }}" class="btn btn-primary fw-bold text-nowrap" id="bulk">
                                   {{ trans('server::messages.get_started') }}</a> 
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-4">
                <div class="d-flex justify-content-between filter-box">
                    <div class="ltitle">
                        <label class="fs-4 py-2 mb-0 fw-semibold highContrastColor">{{ trans('server::messages.results') }}</label>
                        <p>{{ trans('server::messages.files_selected') }} </p>
                    </div>
                    <div class="laction d-flex align-items-center w-50 justify-content-end">

                        <div class="form-check form-switch mr-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                            <label class="form-check-label" for="flexSwitchCheckDefault">
                            {{ trans('server::messages.view_all_files') }} 
                                <img src="{{ asset('em/images/tooltip.svg') }}"  alt="">
                            </label>
                        </div>
                        <div class="d-flex">
                            <div class="input-group">
                                <span class="input-group-text border-right-0" id="basic-addon1">
                                    <img src="{{ asset('em/images/search.svg') }}"  alt="">
                                </span>
                                <input type="text" name="keyword" class="form-control border-left-0" placeholder="{{ trans('server::messages.search_list') }}" aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div> 
        </div>

        <div id="ValidateIndexContent" class="pml-table-container">



        </div>
    
        
    </div>

<script>
    var bulk = document.querySelector('[id="bulk"]');
    var list = document.querySelector('[id="list"]');
    bulk.addEventListener('click', function(e){
        window.location.href = "{{ route('acelle_server.validate.bulk') }}";
    }); 
    list.addEventListener('click', function(e){
        window.location.href = "{{ route('acelle_server.validate.legacy_list') }}";
    }); 
</script>

<script>
    var ValidateIndex = {
        list: null,
        getList: function() {
            if (!this.list) {
                this.list =  makeList({
                    url: '{{ route('acelle_server.validate.emails_list') }}',
                    container: $('#ValidateIndexContainer'),
                    content: $('#ValidateIndexContent')
                });
            }
            return this.list;
        }
    };

    var CopySegment = class {
        constructor() {
            this.popup = new Popup();
        }
    }

    $(document).ready(function() {
        ValidateIndex.getList().load();

        //
        window.copySegment = new CopySegment();

        //
        window.campaignProgressPopup = new CampaignProgressPopup();
    });

    var CampaignProgressPopup = class {
        constructor() {
            var _this = this;
            this.popup = new Popup({
                onclose: function() {
                    ValidateIndex.getList().load();
                }
            });
        }

        load(url) {
            this.popup.load(url);
        }
    }
</script>
    

@endsection