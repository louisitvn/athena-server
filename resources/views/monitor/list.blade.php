<div class="row table-header mb-4 gutter-x-0">
    <div class="col col-sm-1 w-3">
        <input type="checkbox" name="cball">
    </div>
    <div class="col col-sm-4">
        {{ trans('server::messages.file_name_id') }} 
    </div>
    <div class="col col-sm-4">
        {{ trans('server::messages.email') }}
    </div>
    <div class="col">
        {{ trans('server::messages.results') }} 
    </div>
    <div class="col text-end col-sm-1">
        {{ trans('server::messages.action') }}
    </div>
</div>


<div class="card ">
    <div class="card-body list-reord px-3">
        @if ($emailAddresses->isEmpty())
            <div class="text-center text-danger">{{ trans('server::messages.no_email_validations') }}</div> 
        @endif
        @foreach ($emailAddresses as $emailAddress)
            <div class="row gutter-x-0 mb-3">
                <div class="col col-sm-1 w-3">
                    <input type="checkbox" name="cball">
                </div>
                <div class="col col-sm-4 pe-1">
                    <p class="mb-1 fw-bold">
                        @if ($emailAddress->apiKey)
                            <span class="material-symbols-rounded">api</span> {{ $emailAddress->apiKey->name }}
                        @elseif ($emailAddress->file_path)
                            <span class="material-symbols-rounded">list_alt</span> {{ $emailAddress->getFileName() }}
                        @else
                            <span class="material-symbols-rounded">code</span> {{ trans('server::messages.validate.single_validation') }}
                        @endif
                    </p>
                    {{ Auth::user()->customer->formatDateTime($emailAddress->updated_at, 'datetime_full') }}
                </div>
                <div class="col col-sm-4 pe-1" style="overflow: hidden">
                    {{ $emailAddress->email }}
                </div>
                <div class="col">
                    @if ($emailAddress->isValid())
                        <span class="badge valid rd-50">{{ trans('server::messages.VALID') }}</span>
                    @elseif ($emailAddress->isInvalid())
                        <span class="badge invalid rd-50">{{ trans('server::messages.INVALID') }}</span>
                    @elseif ($emailAddress->isUnknown())
                        <span class="badge bg-secondary unknown rd-50">{{ trans('server::messages.UNKNOWN') }}</span>
                    @else
                        <span class="badge bg-secondary {{ $emailAddress->verification_status }} rd-50">{{ trans('server::messages.verification_status.' . $emailAddress->verification_status) }}</span>
                    @endif
                </div> 
                
                
                <div class="col text-end col-sm-1">
                    <a href="#">
                        <i class="bx bx-edit-alt me-1"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div> 
<div class="mb-4 mt-4"> 
    @include('elements/_per_page_select', ["items" => $emailAddresses])
</div>