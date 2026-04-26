@extends('layouts.core.frontend', [
    'menu' => 'validate',
])

@section('page_title')
    validate
@endsection

@section('content') 
    <div class="row mb-4"> 
        <div class="d-flex align-items-center justify-items-center">
            <div class="me-3 d-flex align-items-center">
                <div class="ebadge medium text-bg-success me-2 d-flex justify-content-center align-items-center">
                    <img src="{{ asset('em/images/emailColor.svg') }}"  alt="">
                </div>
                <div class="d-flex flex-column">
                    <p class="fs-4 mb-0 fw-semibold highContrastColor">{{ trans('server::messages.Email_Validation') }}</p>  
                </div>
            </div>
            <div class="">
                <a class=" lightPurpleColor" href="{{ route('acelle_server.validate.index') }}">< {{ trans('server::messages.Back') }}</a> 
            </div> 
        </div> 

    </div>
    <div class="row mb-4"> 
        <div class="col-12 col-sm-12">
            <div class="card ecard h-100">
                <div class="card-body p-0">
                    <div class="row">

                        <div class="col-4 col-md-4">
                            <form data-control="bulk-form" action="{{ route('acelle_server.validate.bulk_save') }}" method="post" name="frmlistemail" id="frmlistemail">  
                                @csrf
                                <div class=" p-4">
                                    <div class="mb-3">
                                        <h3>{{ trans('server::messages.enter_emails_to_validate') }}</h3>
                                        <p>{{ trans('server::messages.enter_emails_to_validate.2nd_line') }}</p>
                                        <textarea data-control="emails" id="listemail" class="form-control" rows="10" name="emails"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary fw-bold form-control" id="validate">{{ trans('server::messages.Validate') }}</button> 
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-8 col-md-8 border border-right-0 border-top-0 border-bottom-0 p-4"> 
                            <div class="d-flex justify-content-between mb-2">
                                <div class="me-3 d-flex align-items-center"> 
                                    <div class="d-flex flex-column">
                                        <p class="fs-4 mb-0 fw-semibold highContrastColor">{{ trans('server::messages.results') }}</p>  
                                    </div>
                                </div>
                                <div class="my-2">
                                    <button class="btn btn-primary fw-bold" disabled>{{ trans('server::messages.Export_to_CSV') }}</button> 
                                </div>
                            </div>

                            <div data-control="result">
                                <div class="fs-4 text-center py-5" style="    height: 250px;
                                display: flex;
                                justify-content: center;
                                align-items: center;">
                                    {{ trans('server::messages.email_appear_here') }}
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
      
<script>
    $(function() {
        new ValidateBulkForm({
            resultBox: $('[data-control="result"]'),
            form: $('[data-control="bulk-form"]'),
            emailsInput: $('[data-control="emails"]'),
        });
    });

    var ValidateBulkForm = class {
        constructor(options) {
            var _this = this;
            this.form = options.form;
            this.resultBox = options.resultBox;
            this.emailsInput = options.emailsInput;

            this.form.on('submit', function(e) {
                e.preventDefault();

                if (!_this.validate()) {
                    return;
                }

                _this.showLoading();

                _this.run();

                
            });
        }

        run() {
            var _this = this;
            var data = this.form.serialize();

            // disable validate button
            $('#validate').addClass('disabled');
            $('#validate').addClass('pe-none');

            $.ajax({
                url: this.form.attr('action'), // Replace with your server-side upload handler URL
                type: 'POST',
                data: data,
                globalError: false,
            }).done(function(response) {
                _this.resultBox.html(response);
            }).fail(function(response) {
                // console.log(response);
                var message = response.responseJSON.message;

                // update error
                _this.resultBox.html(`
                    <div class="text-center d-flex justify-content-center py-5">
                        <div>
                            <p>
                                <svg class="text-muted2" style="width: 90px;
height: 90px;
fill: currentcolor;" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m612-320.46-152-152V-660h40v172.46L639.54-348 612-320.46Zm-454.15 1.15q-16.85-32.84-26-68.46-9.16-35.61-11.39-72.23h40.23q2.23 31.77 10.43 62 8.19 30.23 22.34 58.69l-35.61 20ZM120.46-500q2.23-36.62 11.77-72.35t26.62-69.34l34.61 20q-14.92 28.46-22.73 59.07Q162.92-532 160.69-500h-40.23Zm163.46 321.38q-31.61-20.76-58.46-47.34-26.84-26.58-47.61-58.19l34.61-20.77q17.85 27.23 41.04 50.42 23.19 23.19 50.42 41.04l-20 34.84Zm-70.46-477.46-35.61-20q20.77-31.61 47.61-58.07 26.85-26.47 58.46-47.23l20 34.84q-27 17.85-49.8 40.66-22.81 22.8-40.66 49.8ZM460-120.46q-36.62-2.23-72.35-11.77t-69.34-26.62l20-34.61q28.46 14.92 59.07 22.73Q428-162.92 460-160.69v40.23ZM338.31-766.54l-20-34.61q33.61-17.08 69.34-26.62 35.73-9.54 72.35-11.77v40.23q-32 2.23-62.62 10.04-30.61 7.81-59.07 22.73ZM500-120.46v-40.23q32-2.23 62.62-10.04 30.61-7.81 59.07-22.73l20 34.61q-33.61 17.08-69.34 26.62-35.73 9.54-72.35 11.77Zm121.69-646.08q-28.46-14.92-59.07-22.73Q532-797.08 500-799.31v-40.23q36.62 2.23 72.35 11.77t69.34 26.62l-20 34.61Zm54.39 587.92-20-34.84q27-17.85 49.8-40.66 22.81-22.8 40.66-49.8l34.84 20q-20.76 31.61-47.23 58.19-26.46 26.58-58.07 47.11Zm70.46-478.23q-17.85-27-40.66-49.42-22.8-22.42-49.8-40.27l20-34.84q31.61 20.53 58.07 46.61 26.47 26.08 47 57.69l-34.61 20.23ZM799.31-500q-2.23-32-10.43-62.62-8.19-30.61-23.11-59.84l35.38-20.23q16.85 33.84 26.5 69.57 9.66 35.74 11.89 73.12h-40.23Zm1.84 181.69-34.61-20q14.92-28.46 22.73-59.07Q797.08-428 799.31-460h40.23q-2.23 36.62-11.77 72.35t-26.62 69.34Z"/></svg>
                            </p>
                            <p class="fs-5">`+message+`</p>
                        </div>
                    </div>
                `);

                // enable validate button
                $('#validate').removeClass('disabled');
                $('#validate').removeClass('pe-none');
            });
        }

        showLoading() {
            this.resultBox.html(`
                <div  style="    height: 250px;
                                display: flex;
                                justify-content: center;
                                align-items: center;">
                    <div class="text-center mb-4 py-4">
                    <h4 class="fw-bold text-center">Please wait...</h4>
                    <div class="spinner-border text-primary" role="status" style="width: 5rem; height: 5rem;border-width: 0.6em;">
                        <span class="visually-hidden">{{ trans('server::messages.Loading_') }}</span>
                    </div>
                </div>
            `);
        }

        validate() {
            $('.email-list-invalid').remove();

            const textarea = this.emailsInput.val();
            const resultDiv = $('#result');
            const lines = textarea.split('\n');

            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            let atLeastOneValid = false;
            let invalidEmails = [];

            lines.forEach((line, index) => {
                const email = line.trim();
                if (email.length > 0 && !emailPattern.test(email)) {
                    invalidEmails.push(`Line ${index + 1}: "${email}"`);
                } else if (email.length > 0) {
                    atLeastOneValid = true;
                }
            });

            if (atLeastOneValid) {
                if (invalidEmails.length === 0) {
                    return true;
                } else {
                    this.emailsInput.after(`
                        <div class="text-danger email-list-invalid">{{ trans('server::messages.email_list_not_valid') }}</div>
                    `);
                    return false;
                }
            } else {
                this.emailsInput.after(`
                    <div class="text-danger email-list-invalid">{{ trans('server::messages.email_list_not_valid') }}</div>
                `);
                return false;
            }
        }
    }
</script>


    <div class="modal" tabindex="-1" id="emailmodel">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header w-100 align-items-center justify-item-center">
                <div class="d-flex w-100  align-items-center justify-item-center flex-column">
                    <div class="cicle mailbgcolor medium text-bg-success me-2 d-flex justify-content-center align-items-center mb-3">
                        <img src="{{ asset('em/images/email.svg') }}" alt="">
                    </div> 
                    <div class="bage-email w-100" role="alert"> 
                        <h5 class="modal-title" id="my_email">Modal title</h5>
                    </div>
                </div>
              
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              
                <div class="row">
                    <div class="col-sm-4"> 
                        <div class="mb-1">
                            <label for="estatus" class="form-label">Status</label>
                            <input type="text" class="form-control form-control-sm" id="status" name="status" placeholder="name@example.com">
                        </div>
                        <div class="mb-1">
                            <label for="estatus" class="form-label">DID YOU MEAN</label>
                            <input type="text" class="form-control form-control-sm" id="mean" name="mean" placeholder="name@example.com">
                        </div>
                        <div class="mb-1">
                            <label for="estatus" class="form-label">DOMAIN AGE DAYS</label>
                            <input type="text" class="form-control form-control-sm" id="domain-age" name="domain-age" placeholder="name@example.com">
                        </div>
                        <div class="mb-1">
                            <label for="estatus" class="form-label">MX RECORD</label>
                            <input type="text" class="form-control form-control-sm" id="mx-record" name="mx-record" placeholder="name@example.com">
                        </div> 
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-1">
                            <label for="estatus" class="form-label">Sub Status</label>
                            <input type="text" class="form-control form-control-sm" id="sub-status" name="sub-status" placeholder="name@example.com">
                        </div> 
                        <div class="mb-1">
                            <label for="estatus" class="form-label">Account</label>
                            <input type="text" class="form-control form-control-sm" id="account" name="account" placeholder="name@example.com">
                        </div> 
                        <div class="mb-1">
                            <label for="estatus" class="form-label">SMTP PROVIDER</label>
                            <input type="text" class="form-control form-control-sm" id="smtp-provider" name="smtp-provider" placeholder="name@example.com">
                        </div> 
                        <div class="mb-1">
                            <label for="estatus" class="form-label"> FIRST NAME</label>
                            <input type="text" class="form-control form-control-sm" id="first-name" name="first-name" placeholder="name@example.com">
                        </div> 
                       

                    </div>
                    <div class="col-sm-4"> 
                        <div class="mb-1">
                            <label for="estatus" class="form-label">Free Email</label>
                            <input type="text" class="form-control form-control-sm" id="freee-mail" name="freee-mail" placeholder="name@example.com">
                        </div>
                        <div class="mb-1">
                            <label for="estatus" class="form-label">Domain</label>
                            <input type="text" class="form-control form-control-sm" id="domain" name="domain" placeholder="name@example.com">
                        </div> 
                        <div class="mb-1">
                            <label for="estatus" class="form-label">MX FOUND</label>
                            <input type="text" class="form-control form-control-sm" id="mx-found" name="mmx-found" placeholder="name@example.com">
                        </div> 
                        <div class="mb-1">
                            <label for="estatus" class="form-label">LAST NAME</label>
                            <input type="text" class="form-control form-control-sm" id="last-name" name="last-name" placeholder="name@example.com">
                        </div> 

                    </div>
                </div>
            </div> 
          </div>
        </div>
    </div> 

    
<script>
    //--- for click all record
    function findUserByEmail(email) {
        return email_listings.find(elisting => elisting.email === email);
    }
    function show_popup(elisting){
        if(elisting){
            emailmodel.querySelector('#my_email').innerHTML = elisting.email;

            emailmodel.querySelector('#status').value = elisting.status;

            var estatus = emailmodel.querySelector('#status');
            estatus.className = "form-control form-control-sm border";
            
            if(elisting.status ==='VALID'){ 
                estatus.classList.add('border-success');
                estatus.classList.add('text-success');
            }

            if(elisting.status ==='INVALID'){
                estatus.classList.add('border-danger');
                estatus.classList.add('text-danger');
            }

            emailmodel.querySelector('#mean').value = elisting.mean;
            emailmodel.querySelector('#domain-age').value = elisting.domain_age;
            emailmodel.querySelector('#mx-record').value = elisting.mx_record;

            emailmodel.querySelector('#sub-status').value = elisting.sub_status;
            emailmodel.querySelector('#account').value = elisting.account;
            emailmodel.querySelector('#smtp-provider').value = elisting.smtp_provider;
            emailmodel.querySelector('#first-name').value = elisting.first_name;

            emailmodel.querySelector('#freee-mail').value = elisting.free_email;
            emailmodel.querySelector('#domain').value = elisting.domain;
            emailmodel.querySelector('#mx-found').value = elisting.mx_found;
            emailmodel.querySelector('#last-name').value = elisting.last_name;

            $(emailmodel).modal("show");
        }
    }
    listings.forEach(button => {
        button.addEventListener('click', () => {
            event.preventDefault();
            const dataemail = event.target.getAttribute('data-email');
            const foundUser = findUserByEmail(dataemail);
            if (foundUser) { 
                show_popup(foundUser);
            }
        });
    });

    //---- show model infomation
    

</script>

@endsection