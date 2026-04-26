{{-- <div class="d-flex justify-content-between mb-2">
    <div class="me-3 d-flex align-items-center"> 
        <div class="d-flex flex-column">
            <p class="fs-4 mb-0 fw-semibold highContrastColor">Results</p>  
        </div>
    </div>
    <div class="my-2">
        <button class="btn btn-primary fw-bold" disabled>Export to CSV</button> 
    </div>
</div> --}}


<div>
    <div>
        <h1 class="wp-heading-inline" data-control="txtTitle">{{ trans('server::messages.verifying_wait') }}</h1>
        <div data-control="status">
            @include('server::validate._status', [
                'verificationCampaign' => $verificationCampaign,
            ])
        </div>
            
        <div style="text-align: center;" class="my-4">
            <span style="display: inline-block;">
                <span class="rounded bg-light import-circle-progress" style="
                    
                "><span class="fw-bold" data-control="current">{{ $verificationCampaign->getProgress()['current'] }}</span>/<span class="fw-bold" data-control="total">{{ $verificationCampaign->getProgress()['total'] }}</span>
            </span></span>
        </div>
        <div>
            <div data-control="progress" class="progress" role="progressbar" aria-label="Success example" aria-valuenow="{{ $verificationCampaign->getProgress()['progress'] }}" aria-valuemin="0" aria-valuemax="100" style="height:20px;">
                <div role="bar" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: {{ $verificationCampaign->getProgress()['progress'] }}%">
                    <span class="fw-bold fs-7" role="label">{{ $verificationCampaign->getProgress()['progress'] }}%</span></div>
            </div>
        </div>
            

        <div class="mt-1">
            <p data-control="txtNotice">{{ trans('server::messages.verifying_wait_2') }}</p>
        </div>
        
        <a style="" data-control="btnBackToList" href="{{ route('acelle_server.validate.index') }}" class="btn btn-primary me-1">{{ trans('server::messages.verify_file.return_to_list') }}</a>

        @if ($verificationCampaign->isCompleted())
            <a data-control="btnReport" href="{{ route('acelle_server.validate.report', [
                'verification_campaign_uid' => $verificationCampaign->uid,
            ]) }}" class="btn btn-outline-primary">{{ trans('server::messages.download_report') }}</a>
        @endif
    </div>
</div>

@if ($verificationCampaign->isCompleted())
    <hr>
    <h2>{{ trans('messages.emails') }}</h2>

    <div class="row table-header mb-3 gutter-x-0">  
        <div class="col col-sm-6 pe-1">
            {{ trans('server::messages.email') }}
        </div>
        <div class="col  col-sm-3  pe-1">
            {{ trans('server::messages.bulk.result') }}
        </div>
        <div class="col">
            {{ trans('server::messages.bulk.date') }}
        </div>
    </div> 

    <div style="
        overflow-y: auto;
        max-height: 300px;
    ">
        @foreach ($emailAddresses as $emailAddress)
            <div class="row table-row  gutter-x-0 mb-3">
                <div class="col col-sm-6 pe-1" style="overflow: hidden">
                    <a href="{{ route('acelle_server.validate.verify', [
                        'email' => $emailAddress->email,
                    ]) }}" data-control="detail" data-email="{{ $emailAddress->email }}" class="lightPurpleColor text-decoration-underline">{{ $emailAddress->email }}</a>
                </div>
                <div class="col col-sm-3 pe-1">
                    <a href="{{ route('acelle_server.validate.verify', [
                        'email' => $emailAddress->email,
                    ]) }}" data-control="detail" data-email="{{ $emailAddress->email }}">
                        @if ($emailAddress->isValid())
                            <span class="badge valid rd-50">{{ trans('server::messages.VALID') }}</span>
                        @elseif ($emailAddress->isInvalid())
                            <span class="badge invalid rd-50">{{ trans('server::messages.INVALID') }}</span>
                        @elseif ($emailAddress->isUnknown())
                            <span class="badge bg-secondary unknown rd-50">{{ trans('server::messages.UNKNOWN') }}</span>
                        @else
                            <span class="badge bg-{{ $emailAddress->verification_status }} rd-50">{{ trans('server::messages.verification_status.' . $emailAddress->verification_status) }}</span>
                        @endif
                    </a>
                </div>
                <div class="col pe-1">
                    {{ Auth::user()->customer->formatDateTime($emailAddress->created_at, 'datetime_full') }}
                </div> 
            </div>
        @endforeach
    </div>
@endif

<script>
    $(function() {
        new VerifySingleEmail({
            links: $('[data-control="detail"]'),
        });

        var bulkProgressManager = new BulkProgressManager({
            url: '{!! route('acelle_server.validate.bulk_result', [
                'verification_campaign_uid' => $verificationCampaign->uid,
            ]) !!}',
            container: $('[data-control="result"]'),
        });

        @if ($verificationCampaign->isRunning())
            setTimeout(function() {
                bulkProgressManager.check();
            }, 1000)

        @else
            // enable validate button
            $('#validate').removeClass('disabled');
            $('#validate').removeClass('pe-none');
        @endif
    });

    var BulkProgressManager = class {
        constructor(options) {
            this.url = options.url;
            this.container = options.container;
        }

        check() {
            var _this = this;

            $.ajax({
                url: _this.url,
            }).done(function(res) {
                _this.container.html(res);
            });
        }
    }

    var VerifySingleEmail = class {
        constructor(options) {
            var _this = this;
            this.links = options.links;
            this.popup = new Popup();

            this.links.on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                _this.verify(url);
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

        verify(url) {
            var _this = this;
            _this.showLoading();
            $.ajax({
                url: url,
                type: 'POST',
            }).done(function(response) {
                _this.popup.loadHtml(response);
            });
        }
    }
</script>