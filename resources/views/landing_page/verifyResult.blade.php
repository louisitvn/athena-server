@extends('layouts.popup.small')

@section('content')
    <div>
        <div class="text-center mb-3">
            <div class="text-center mb-3">
                <span class="d-inline-block" style="
                    background: var(--lightPurpleColor);
                    border-radius: 50%;
                    line-height: 42px;
                    width: 50px;
                    height: 50px;
                    font-size: 24px;
                ">
                    <span class="material-symbols-rounded text-white">
                        forward_to_inbox
                    </span>
                </span>
            </div>
            <div>
                <div style="
                    background-color: #f0e7fc82;
                    padding: .5rem 1rem;
                    line-height: 50px;
                    font-weight: 600;
                    font-size: 18px;
                    line-height: 22px;
                    text-align: center;
                    color: var(--lightPurpleColor);
                    word-break: break-all;
                ">{{ request()->email }}</div>
            </div>
        </div>

        @if ($emailAddress->verification_error)
            <div class="alert alert-danger">
                {{ $emailAddress->verification_error }}
            </div>
        @endif

        <div class="row">
            <div class="col-sm-4"> 
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.Status') }} </label>
                    <input type="text" class="form-control py-1 pe-none text-uppercase {{ $emailAddress->isValid() ? 'border-success text-success' : 'border-danger text-danger' }}" id="estatus" name="estatus"
                        value="{{ $emailAddress->verification_status }}"
                    >
                </div>
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.DID_YOU_MEAN') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="estatus" name="estatus" value="Unknown">
                </div>
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.DOMAIN_AGE_DAYS') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="estatus" name="estatus" value="Unknown">
                </div>
                <div class="mb-1 xtooltip">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.MX_RECORD') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="estatus" name="estatus" value="{{ json_encode($mxs) }}">
                </div> 
            </div>
            <div class="col-sm-4">
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.Sub_Status') }}</label>
                    <input type="text" class="form-control py-1 pe-none pe-none {{ $emailAddress->isValid() ? 'border-success text-success' : 'border-danger text-danger' }}" id="estatus" name="estatus" value="None">
                </div>
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.Account') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="esubstatus" name="esubstatus" value="{{ explode('@', request()->email)[0] }}">
                </div> 
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.SMTP_PROVIDER') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="esubstatus" name="esubstatus" value="Unknown">
                </div> 
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1"> {{ trans('server::messages.FIRST_NAME') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="esubstatus" name="esubstatus" value="Unknown">
                </div> 
               

            </div>
            <div class="col-sm-4"> 
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.Free_Email') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="esubstatus" name="esubstatus" value="Unknown">
                </div>
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.Domain') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="esubstatus" name="esubstatus" value="{{ explode('@', request()->email)[1] }}">
                </div> 
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.MX_FOUND') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="esubstatus" name="esubstatus" value="{{ count($mxs) ? trans('messages.yes') : trans('messages.no') }}">
                </div> 
                <div class="mb-1">
                    <label for="estatus" class="form-label mb-1">{{ trans('server::messages.LAST_NAME') }}</label>
                    <input type="text" class="form-control py-1 pe-none text-dark" id="esubstatus" name="esubstatus" value="Unknown">
                </div> 

            </div>
        </div>
    </div>
@endsection