@extends('layouts.popup.small')

@section('content')
    <h1 class="wp-heading-inline" id="txtTitle">{{ trans('server::messages.verifying_wait') }}</h1>
    <div style="text-align: center;" class="my-4">
        <span style="display: inline-block;">
            <span class="rounded bg-light import-circle-progress" style="
                
            "><span class="fw-bold" data-control="current">--</span>/<span class="fw-bold" data-control="total">--</span>
        </span></span>
    </div>
    <div>
        <div data-control="progress" class="progress" role="progressbar" aria-label="Success example" aria-valuenow="--" aria-valuemin="0" aria-valuemax="100" style="height:20px;">
            <div role="bar" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 0%">
                <span class="fw-bold fs-7" role="label">--%</span></div>
        </div>
    </div>
        

    <div class="mt-1">
        <p id="txtNotice">{{ trans('server::messages.verifying_wait_2') }}</p>
    </div>
    
    <a style="display:none" id="btnBackToList" href="javascript:;" class="btn btn-primary me-1 close">{{ trans('server::messages.landing_page.go_back') }}</a>

    <a style="display:none" id="btnReport" href="{{ route('acelle_server.landing.upload_report', [
        'file_name' => $file_name
    ]) }}" class="btn btn-outline-primary">{{ trans('server::messages.download_report') }}</a>

    <script>
        $(function() {
            var progressManager = new ProgressManager('{!! route('acelle_server.landing.upload_progress_json', [
                'file_name' => $file_name,
            ]) !!}'
            );

            progressManager.check();
        });

        var ProgressManager = class {
            constructor(url) {
                this.url = url;
            }

            showReportButton() {
                $('#btnBackToList').show();
                $('#btnReport').show();
                $('#txtTitle').text('完了');
                $('#txtNotice').text('検証が完了しました。以下のレポートをダウンロードできます。');
            }

            setProgressBar(progress) {
                $('[data-control="progress"]').attr('aria-valuenow', progress);
                $('[data-control="progress"] [role="bar"]').css('width', progress + '%');
                $('[data-control="progress"] [role="label"]').html(progress + '%');
            }

            check() {
                var _this = this;

                $.ajax({
                    url: _this.url,
                    dataType: 'json',
                }).done(function(res) {
                    var progress = res.progress;

                    //
                    $('[data-control="total"]').html(res.total);
                    $('[data-control="current"]').html(res.current);

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