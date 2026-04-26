@extends('layouts.core.frontend')

@section('content')
    <h1 class="wp-heading-inline">{{ trans('server::messages.Upload_Verify_Email') }}</h1>

    <p>
        {!! trans('server::messages.Upload_Verify_Email.intro') !!}
    </p>

    <form id="UploadForm"
        action="{{ action([App\Http\Controllers\ValidationController::class, 'upload']) }}"
        method="POST" enctype="multipart/form-data"
    >
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold small">{{ trans('server::messages.Email_list_file') }} </label>
            <input type="file" name="file" class="form-control" value="" accept=".txt, .csv" required>
            <div id="emailHelp" class="form-text">{{ trans('server::messages.file_upload_required') }}</div>
        </div>

        <button data-control="upload" type="submit" class="btn btn-secondary">
            <span data-control="loading" style="display:none;">
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span class="visually-hidden" role="status">{{ trans('server::messages.Loading_') }}</span>
            </span>

            {{ trans('server::messages.Upload') }}
        </button>
    </form>

    <script>
        var uploadForm = document.getElementById('UploadForm');
        var verifyButton = uploadForm.querySelector('[data-control="upload"]');
        var loadingIcon = verifyButton.querySelector('[data-control="loading"]');
        uploadForm.addEventListener('submit', function() {
            loadingIcon.style.display = 'inline-block';
            verifyButton.setAttribute('disabled', 'disabled');
            verifyButton.classList.add('disabled');
        });
    </script>
@endsection