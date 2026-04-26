@extends('layouts.popup.small')

@section('content')
@php
    $formId = 'F_' . uniqid();
@endphp

    <div id="{{ $formId }}">
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

        <a href="{{ route('acelle_server.validate.full_progress', $verificationCampaign->uid) }}"
           target="_blank"
           class="btn btn-outline-secondary me-1">
            <span class="material-symbols-rounded align-middle fs-6">open_in_new</span>
            View full progress
        </a>

        <a style="display:none" data-control="btnReport" href="{{ route('acelle_server.validate.report', [
            'verification_campaign_uid' => $verificationCampaign->uid,
        ]) }}" class="btn btn-outline-primary">{{ trans('server::messages.download_report') }}</a>
    </div>

    <script>
        $(function() {
            var progressManager = new ProgressManager('{!! route('acelle_server.validate.progress_json', [
                'verification_campaign_uid' => $verificationCampaign->uid,
            ]) !!}'
            );

            progressManager.check();
        });

        var ProgressManager = class {
            constructor(url) {
                this.url = url;
            }

            showReportButton() {
                $('#{{ $formId }} [data-control="btnBackToList"]').show();
                $('#{{ $formId }} [data-control="btnReport"]').show();
                $('#{{ $formId }} [data-control="txtTitle"]').text('完了');
                $('#{{ $formId }} [data-control="txtTitle"]').text('検証が完了しました。以下のレポートをダウンロードできます。');
            }

            setProgressBar(progress) {
                $('#{{ $formId }} [data-control="progress"]').attr('aria-valuenow', progress);
                $('#{{ $formId }} [data-control="progress"] [role="bar"]').css('width', progress + '%');
                $('#{{ $formId }} [data-control="progress"] [role="label"]').html(progress + '%');
            }

            check() {
                var _this = this;

                $.ajax({
                    url: _this.url,
                    dataType: 'json',
                }).done(function(res) {
                    var progress = res.progress;

                    //
                    $('#{{ $formId }} [data-control="total"]').html(res.total);
                    $('#{{ $formId }} [data-control="current"]').html(res.current);
                    $('#{{ $formId }} [data-control="status"]').html(res.status);

                    _this.setProgressBar(progress);

                    if (progress < 100) {
                        // again
                        setTimeout(() => {
                            _this.check();
                        }, 1000);
                    } else {
                        _this.showReportButton();
                    }
                });
            }
        }
    </script>
@endsection