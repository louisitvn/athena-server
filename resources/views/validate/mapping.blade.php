@extends('layouts.popup.small')

@section('content')
    <form data-control="ValidateListUploadForm" action="{{ route('acelle_server.validate.import') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="w-100  align-items-center justify-item-center flex-column"> 
            <div class="import-head w-100 text-center justify-item-center" role="alert"> 
                <h3 class="modal-title" id="signemail-target">Match the columns in your file</h3>
                <p class="mb-2">Displaying the first few rows of your file: </p>
            </div>
        </div> 

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
                        {{-- <div class="col-sm-4">
                            <select class="form-select custom-select" id="other">
                                <option>Email Address</option>
                                <option value="1">First Name</option>
                                <option value="2">Last Name</option>
                                <option value="3">Gender</option>
                                <option value="3">IP Address</option>
                                <option value="3" selected>Custom</option>
                            </select>
                        </div> --}}
                    </div>
                    <div class="row import-row-items">
                        <div class="col-sm-4">huy@test.com</div>
                        {{-- <div class="col-sm-4">Mr. Huy</div> --}}
                    </div>
                    <div class="row import-row-items">
                        <div class="col-sm-4">huy2@test.com</div>
                        {{-- <div class="col-sm-4">Mr. Huy 2</div> --}}
                    </div>
                    <div class="row import-row-items">
                        <div class="col-sm-4">canhodyclong@test.com</div>
                        {{-- <div class="col-sm-4">Mr. long</div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="import-confirm text-center">
            <div class="d-flex justify-content-center text-center">
                <label class="form-check-label me-3"> Does your first row contain labels? </label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck2" checked>
                    <label class="form-check-label" for="defaultCheck2"> Yes </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck3">
                    <label class="form-check-label" for="defaultCheck3"> No </label>
                </div>
            </div>
            <div class="import-note">
                If your file contains a header on the first populated row, we'll skip it.
            </div>
            <div class="mb-4 d-flex justify-content-center text-center">
                <label class="form-check-label me-3"> Can we remove duplicate emails? </label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck5" checked>
                    <label class="form-check-label" for="defaultCheck5"> Yes </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck6">
                    <label class="form-check-label" for="defaultCheck6"> No </label>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 text-center">
                    <button data-control="submit" type="submit" class="btn btn-success">Next Step</button>
                </div>
            </div>
        </div>

    </form>

    <script>
        $(function() {
            $('[data-control="ValidateListUploadForm"]').on('submit', function() {
                setTimeout(function() {
                    window.uploadList.showLoading();
                }, 200);
            });
        });
    </script>
@endsection
