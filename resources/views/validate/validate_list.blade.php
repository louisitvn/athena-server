@extends('layouts.core.frontend', [
    'menu' => 'validate',
])

@section('page_title')
    validate
@endsection

@section('content') 
    <form data-control="ValidateListUploadForm" action="{{ route('acelle_server.validate.upload') }}" enctype="multipart/form-data">
        @csrf

        <div class="row mt-4"> 
            <div class="col-md-12">
                <div class="card ecard h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-3 d-flex align-item-center">
                                <div class="ebadge medium text-bg-success me-2 d-flex justify-content-center align-items-center me-3">
                                    <img src="{{ asset('em/images/emailColor.svg') }}"  alt="">
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="fs-4 mb-0 fw-semibold highContrastColor">{{ trans('server::messages.Individual_Email_Validation') }} </p> 
                                    <p>{{ trans('server::messages.up_to_25_per_email') }}</p>
                                </div>
                            </div>
                            <div class="my-2">
                                <a href="{{ route('acelle_server.validate.bulk') }}" class="btn btn-primary fw-bold text-nowrap">{{ trans('server::messages.get_started') }}</a> 
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">  
            <div class="col-md-12 h-100 py-4">

                <div class="card upload-bg h-100 p-4">
                    <div class="card-title text-center mb-0">
                        <h3 class="pb-0 upload-title mb-0">{{ trans('server::messages.Upload_your_file') }}</h3>
                    </div>
                    <div class="card-body pt-0"> 

                        <div class="mb-4 text-center upload-supports">
                            <p class="form-label">{{ trans('server::messages.file_upload_required') }}</p> 
                            <p class="form-label">{!! trans('server::messages.verify_list.please_checkout_sample', [
                                'csv_link' => url('files/verify_email_list_sample.csv'),
                            ]) !!}</p>
                        </div>

                        <div class="dropzones mb-4">
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col-xl-8 dragdrop">
                                        <div class="mb-3">
                                            <img src="{{ asset('em/images/folder.svg') }}" alt="">
                                        </div>
                                        <div class="file-upload-contain">
                                            <input id="multiplefileupload" name="file" type="file" accept=".xlsx,.csv" multiple />
                                        </div>
                                        <h3>{!! trans('server::messages.Drag_and_drop_your_browse') !!}</h3>

                                    </div>
                                </div>
                            </div>
                        </div>
<!-- 25-0205 MORI
                        <div class="mb-4 text-center upload-connect">
                            <label class="form-label">Connect to your cloud based files</label> 
                        </div>

                        <div class="mb-4 uploadarea">
                            <div class="container">
                                <div class="drow mb-4 justify-content-center"> 
                                    <div class="upload-items text-center">
                                        <div class="upload-items-content">
                                            <div class="img" style="background-image: url({{ asset('em/images/dropbox.svg') }})"></div>
                                            <h6 class="upload-items-title">dropbox</h6>
                                        </div>
                                    </div>
                                    <div class="upload-items text-center w-25">
                                        <div class="upload-items-content">
                                            <div class="img" style="background-image: url({{ asset('em/images/drive.svg') }})"></div>
                                            <h6 class="upload-items-title">drive</h6>  
                                        </div>                                   
                                    </div>
                                    <div class="upload-items text-center w-25">
                                        <div class="upload-items-content">
                                            <div class="img" style="background-image: url({{ asset('em/images/onedrive.svg') }}"></div>
                                            <h6 class="upload-items-title">onedrive</h6> 
                                        </div>
                                    </div>
                                    <div class="upload-items text-center w-25">
                                        <div class="upload-items-content">
                                            <div class="img" style="background-image: url({{ asset('em/images/amazons.png') }}"></div>
                                            <h6 class="upload-items-title">amazons</h6> 
                                        </div>
                                    </div>
                                    <div class=" upload-items text-center w-25">
                                        <div class="upload-items-content">
                                            <div class="img" style="background-image: url({{ asset('em/images/sftp.png') }}"></div>
                                            <h6 class="upload-items-title">sftp</h6> 
                                        </div>
                                    </div>
                                </div> 
                            </div> 
                        </div> 

25-0205 MORI -->

                    </div>
                </div>  
            </div>
        </div>

<!-- 25-0205 MORI

        <div class="row mb-4"> 
            <div class="col-md-6">
                <div class="card ecard ecard-one h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-3 d-flex align-item-center">
                                <div class="ebadge medium text-bg-success me-2 d-flex justify-content-center align-items-center">
                                    <img class="w-50" src="{{ asset('em/images/mail-list.svg') }}"  alt="">
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="fs-4 mb-0 fw-semibold highContrastColor">Setup Email Validation Rules </p> 
                                    <p><a href="#" class="lightPurpleColor">Allow or block specific emails, email domains, or mx records. ›</a></p>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card ecard ecard-two h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-3 d-flex align-item-center">
                                <div class="ebadge medium text-bg-success me-2 d-flex justify-content-center align-items-center">
                                    <img class="w-50" src="{{ asset('em/images/checked.svg') }}"  alt="">
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="fs-4 mb-0 fw-semibold highContrastColor">Need more credits? </p> 
                                    <p><a href="#" class="lightPurpleColor">Let’s go ›</a></p>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>

 25-0205 MORI -->
   

        <div class="modal " tabindex="-1" id="emailimportmodel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header w-100 align-items-center justify-item-center">
                        <div class="w-100  align-items-center justify-item-center flex-column"> 
                            <div class="import-head w-100 text-center justify-item-center" role="alert"> 
                                <h3 class="modal-title" id="signemail-target">Match the columns in your file</h3>
                                <p class="mb-2">Displaying the first few rows of your file: </p>
                            </div>
                        </div> 
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <div class="card mb-4">
                            <div class="card-body px-3">
                                <div class="container-fuid import-email-list">
                                    <div class="row  import-row-items">
                                        <div class="col-sm-4">
                                            <select class="form-select custom-select " id="emailadd">
                                                <option selected>Email Address</option>
                                                <option value="1">First Name</option>
                                                <option value="2">Last Name</option>
                                                <option value="3">Gender</option>
                                                <option value="3">IP Address</option>
                                                <option value="3">Custom</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <select class="form-select custom-select" id="other"> 
                                                <option>Email Address</option>
                                                <option value="1">First Name</option>
                                                <option value="2">Last Name</option>
                                                <option value="3">Gender</option>
                                                <option value="3">IP Address</option>
                                                <option value="3" selected>Custom</option>
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="row import-row-items">
                                        <div class="col-sm-4">huy@test.com</div>
                                        <div class="col-sm-4">Mr. Huy</div>
                                    </div>
                                    <div class="row import-row-items">
                                        <div class="col-sm-4">huy2@test.com</div>
                                        <div class="col-sm-4">Mr. Huy 2</div>
                                    </div>
                                    <div class="row import-row-items">
                                        <div class="col-sm-4">canhodyclong@test.com</div>
                                        <div class="col-sm-4">Mr. long</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="import-confirm text-center">
                            <div class="d-flex justify-content-center text-center">
                                <label class="form-check-label me-3" > Does your first row contain labels? </label> 
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck2" checked>
                                    <label class="form-check-label" for="defaultCheck2"> Yes </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck3" >
                                    <label class="form-check-label" for="defaultCheck3">   No </label>
                                </div>  
                            </div>
                            <div class="import-note"> 
                                If your file contains a header on the first populated row, we'll skip it. 
                            </div>
                            <div class="mb-4 d-flex justify-content-center text-center">
                                <label class="form-check-label me-3" > Can we remove duplicate emails? </label> 
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck5" checked>
                                    <label class="form-check-label" for="defaultCheck5"> Yes </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck6" >
                                    <label class="form-check-label" for="defaultCheck6">   No </label>
                                </div>  
                            </div>
                            
                            <div class="row"> 
                                <div class="col-sm-12 text-center">
                                    <button type="button" class="btn btn-success">Next Step</button>
                                </div>
                            </div>
                        </div>
                            
                    </div>    
                </div> 
            </div>
        </div> 
    </form>

<script>
    $(function() {
        window.uploadList = new UploadList({
            input: $('#multiplefileupload'),
        });
    });

    var UploadList = class {
        constructor(options) {
            var _this = this;
            this.input = options.input;
            this.form = this.input.closest('form');
            this.popup = new Popup();

            this.input.on('change', function(e) {
                e.preventDefault();

                _this.upload();
            });
        }

        upload() {
            this.showLoading();

            var formData = new FormData(this.form[0]);
            $.ajax({
                url: this.form.attr('action'), // Replace with your server-side upload handler URL
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(response) {
                // window.uploadList.popup.loadHtml(response);
                window.location = '{{ route('acelle_server.validate.index') }}';
            });
        }

        showLoading() {
            this.popup.loadHtml(`
                <div class="modal-dialog shadow modal-default">
                    <div class="modal-content">
                        <div class="modal-header">
                            <a href="javascript:;" class="material-symbols-rounded back" style="display: inline;">keyboard_backspace</a>
                            <h5 class="modal-title text-center" style="width:100%">
                                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            
                            <!-- display flash message -->
                            
                            <!-- main inner content -->
                                <div class="text-center mb-4 py-4">
                        <h4 class="fw-bold text-center">Please wait...</h4>
                        <div class="spinner-border text-primary" role="status" style="width: 5rem; height: 5rem;border-width: 0.6em;">
                            <span class="visually-hidden">{{ trans('server::messages.Loading_') }}</span>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            `);
        }

        
    }

    // var multiplefileupload = document.querySelector('[id="multiplefileupload"]');
    // var emailimportmodel = document.querySelector('[id="emailimportmodel"]');
    // multiplefileupload.addEventListener('change', function(e){
    //     //e.preventDefault();
    //     $(emailimportmodel).modal("show");

    // });
</script>

@endsection