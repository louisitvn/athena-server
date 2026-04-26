<div class="container mt-5">
    <div class="">
        <div class="text-center">
            {!! trans('server::messages.ladi.validator.h2') !!}
            {!! trans('server::messages.ladi.validator.intro') !!}
        </div>
        <div class="email-validator__form">
            <form id="verify-email-form" method="POST">
                <div class="verify-email-form-body d-flex align-items-center justify-content-center mt-4 w-100">
                    <div class="d-flex   align-items-center justify-content-center">
                        <button verify-control="upload" type="button" for="file" class="upload-file_label"> 
                            <img id="validator_component_upload_img" height="26" src="{{ asset('em/images/upload.png') }} " width="25">
                            <span id="validator_component_upload_text">{{ trans('server::messages.ladi.upload_your_list') }}</span>
                        </button>
                        <input verify-control="file" type="file" name="file" id="file" accept=".csv, .xls, .xlsx, .txt"
                        style="display: none;"> 
                    </div>  
                    <div class=" align-items-center upload-divider">
                        <span>{{ trans('server::messages.ladi.OR') }}</span>
                    </div> 
                    <div class="d-flex align-items-center  me-3">
                        <input verify-control="email" type="email" aria-label="{{ trans('server::messages.ladi.Type_your_email') }}" id="email-validator" name="email-validator"
                            class="form-control " placeholder="email@example.com" value="" required> 
                    </div>
                    <div class="d-flex align-items-center last">
                        <button type="submit" class="btn btn-primary w-100 btn-custom-verify" verify-control="verify">
                            <i class="ico-svg-send"><svg id="validator_component_verify_img" width="28" height="28" viewBox="0 0 28 28" xmlns="http://www.w3.org/2000/svg"><g stroke="currentColor" stroke-width="3" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round"><path d="M24.27 2.098L2.775 10.922c-1.12.459-.999 2.082.176 2.372L8.87 14.75"></path><path d="M16.225 11.63l-3.087 3.108a2.666 2.666 0 00-.769 2.055l.52 7.864c.085 1.274 1.8 1.618 2.369.474l10.607-21.32c.514-1.033-.527-2.152-1.596-1.713"></path></g></svg></i>
                            {{ trans('server::messages.ladi.Verify') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        window.aVerify = new AVerify({
            form: $('#verify-email-form'),
            uploadUrl: '{{ route('acelle_server.landing.upload') }}',
            verifyUrl: '{{ route('acelle_server.landing.verify') }}',
        });
    });

    

    var AVerify = class {
        constructor(options) {
            this.form = options.form;
            this.uploadUrl = options.uploadUrl;
            this.verifyUrl = options.verifyUrl;
            this.popup = new Popup();

            this.events();
        }

        getUploadButton() {
            return this.form.find('[verify-control="upload"]');
        }

        getFileInput() {
            return this.form.find('[verify-control="file"]');
        }

        getVerifyButton() {
            return this.form.find('[verify-control="verify"]');
        }

        getEmailInput() {
            return this.form.find('[verify-control="email"]');
        }

        events() {
            var _this = this;

            // uploa button click
            this.getUploadButton().on('click', (e) => {
                e.preventDefault();

                this.browseFile();
            });

            // file selected
            this.getFileInput().on('change', (e) => {
                e.preventDefault();

                this.upload();
            });

            // verify button
            this.form.on('submit', function(e) {
                e.preventDefault();

                _this.verify();
            });
        }

        verify() {
            this.showLoading();
            this.popup.load(this.verifyUrl + '?email=' + this.getEmailInput().val());
        }

        browseFile() {
            this.getFileInput().click();
        }

        upload() {
            this.showLoading();

            var formData = new FormData(this.form[0]);
            $.ajax({
                url: this.uploadUrl, // Replace with your server-side upload handler URL
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
            }).done((response) => {
                this.popup.loadHtml(response);
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
</script>