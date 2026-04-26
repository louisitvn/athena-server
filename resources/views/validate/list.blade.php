<div class="row table-header mb-4 gutter-x-0 px-4">
    <div class="col col-sm-4">
        {{ trans('server::messages.file_name_id') }} 
    </div>
    <div class="col col-sm-4">
        {{ trans('server::messages.email') }}
    </div>
    <div class="col">
        {{ trans('server::messages.results') }} 
    </div>
    <div class="col text-end col-sm-2">
        {{ trans('server::messages.action') }}
    </div>
</div>


<div class="card ">
    <div class="card-body list-reord px-0">
        @if ($verificationCampaigns->isEmpty())
            <div class="text-center text-danger">{{ trans('server::messages.verification_campaigns.no_campaigns') }}</div> 
        @endif
        @foreach ($verificationCampaigns as $verificationCampaign)
            @php
                $verificationCampaign = $verificationCampaign->mapping();
                $progress = $verificationCampaign->getProgress();
            @endphp
            <div class="row gutter-x-0 mb-3 px-4">
                <div class="col col-sm-4">
                    <p class="mb-1 fw-bold">
                        @if ($verificationCampaign->type == \Acelle\Server\Model\VerificationCampaign::TYPE_UPLOAD)
                            <span class="material-symbols-rounded">list_alt</span> {{ $verificationCampaign->file_name ?? trans('server::messages.validate.bulk_validation') }}
                        @else
                            <span class="material-symbols-rounded">code</span> {{ trans('server::messages.validate.single_validation') }}
                        @endif
                    </p>
                    {{ Auth::user()->customer->formatDateTime($verificationCampaign->updated_at, 'datetime_full') }}
                </div>
                <div class="col col-sm-4" style="overflow: hidden">
                    <div class="pe-5">
                        <div class="progress" role="progressbar" aria-label="" aria-valuenow="{{ $progress['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar  bg-primary w-{{ $progress['progress'] }}" style="width:{{ $progress['progress'] }}%"></div>
                        </div>
                        <div class="ebadge mt-2 d-inline-block px-2">{{ $progress['current'] }}/{{ $progress['total'] }}</div>
                    </div>
                </div>
                <div class="col">
                    <span class="badge bg-{{ $verificationCampaign->status }} rd-50 {{ $verificationCampaign->isError() ? 'xtooltip' : '' }}"
                        title="{{ $verificationCampaign->error }}"
                    >{{ trans('server::messages.verification_campaign.status.' . $verificationCampaign->status) }}</span>
                </div> 
                
                
                <div data-control="row-actions" class="col text-end col-sm-2">
                    @if ($verificationCampaign->isNew() || $verificationCampaign->isPaused())
                        <a list-action="start" data-progress-url="{{ route('acelle_server.validate.progress', $verificationCampaign->uid) }}" class="xtooltip btn btn-light px-2" href="{{ route('acelle_server.validate.start', $verificationCampaign->uid) }}" title="{{ trans('server::messages.verification_campaigns.start') }}">
                            <span class="material-symbols-rounded fs-6">
                                play_circle
                            </span>
                        </a>
                    @endif

                    @if ($verificationCampaign->isRunning() || $verificationCampaign->isCompleted() || $verificationCampaign->isPaused())
                        <a list-action="progress" class="xtooltip btn btn-light px-2" href="{{ route('acelle_server.validate.progress', $verificationCampaign->uid) }}" title="{{ trans('server::messages.verification_campaigns.view_progress') }}">
                            <span class="material-symbols-rounded fs-6">
                                query_stats
                            </span>
                        </a>
                    @endif

                    @if ($verificationCampaign->isRunning())
                        <a list-action="pause" class="xtooltip btn btn-light px-2" href="{{ route('acelle_server.validate.pause', $verificationCampaign->uid) }}" title="{{ trans('server::messages.verification_campaign.pause') }}">
                            <span class="material-symbols-rounded fs-6">
                                pause
                            </span>
                        </a>
                    @endif

                    @if ($verificationCampaign->isCompleted())
                        <a class="xtooltip btn btn-light px-2" href="{{ route('acelle_server.validate.report', $verificationCampaign->uid) }}" title="{{ trans('server::messages.verification_campaigns.download_report') }}">
                            <span class="material-symbols-rounded fs-6">
                                download
                            </span>
                        </a>
                    @endif

                    @if ($verificationCampaign->isCompleted() || $verificationCampaign->isError() || $verificationCampaign->isPaused())
                        <a list-action="restart" data-progress-url="{{ route('acelle_server.validate.progress', $verificationCampaign->uid) }}" class="xtooltip btn btn-light px-2" href="{{ route('acelle_server.validate.restart', $verificationCampaign->uid) }}" title="{{ trans('server::messages.verification_campaigns.restart') }}">
                            <span class="material-symbols-rounded fs-6">
                                autorenew
                            </span>
                        </a>
                    @endif

                    <a list-action="delete" href="{{ route('acelle_server.validate.delete', $verificationCampaign->uid) }}" class="xtooltip btn btn-light-danger text-danger px-2" title="{{ trans('server::messages.verification_campaigns.delete') }}">
                        <span class="material-symbols-rounded fs-6">
                            delete
                        </span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div> 
<div class="mb-4 mt-4"> 
    @include('elements/_per_page_select', ["items" => $verificationCampaigns])
</div>

<script>
    $(function() {
        // start campaign
        window.campaignStart = new CampaignStart({
            button: $('[list-action="start"]'),
            callback: function(progressUrl) {
                window.campaignProgress.load(progressUrl);
            }
        });

        // vew stat
        window.campaignProgress = new CampaignProgress({
            button: $('[list-action="progress"]')
        });

        // delete campaign
        new CampaignDelete({
            button: $('[list-action="delete"]')
        });

        // pause campaign
        new CampaignPause({
            button: $('[list-action="pause"]')
        });

         // restart campaign
         new CampaignRestart({
            button: $('[list-action="restart"]'),
            callback: function(url) {
                window.campaignProgress.load(url);
            }
        });
    });

    var CampaignStart = class {
        constructor(options) {
            var _this = this;
            this.button = options.button;
            this.callback = options.callback;

            this.button.on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                var progressUrl = $(this).attr('data-progress-url');
                _this.start(url, progressUrl);
            });
        }

        start(url, progressUrl) {
            var _this = this;
            addMaskLoading();

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                }
            }).done(function(result, textStatus, jqXHR) {
                ValidateIndex.getList().load();

                if (_this.callback) {
                    _this.callback(progressUrl);
                }
            }).fail(function(res) {
                
            }).always(function() {
                removeMaskLoading();
            });
        }
    }

    var CampaignDelete = class {
        constructor(options) {
            var _this = this;
            this.button = options.button;

            this.button.on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                _this.delete(url);
            });
        }

        delete(url) {
            new Dialog('confirm', {
                message: 'Are you sure you want to delete this campaign',
                ok: function() {
                    addMaskLoading();

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        }
                    }).done(function(result, textStatus, jqXHR) {
                        ValidateIndex.getList().load();
                    }).fail(function(res) {
                        
                    }).always(function() {
                        removeMaskLoading();
                    });
                }
            })
            
        }
    }

    var CampaignRestart = class {
        constructor(options) {
            var _this = this;
            this.button = options.button;
            this.callback = options.callback;

            this.button.on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var progressUrl = $(this).attr('data-progress-url');
                _this.restart(url, progressUrl);
            });
        }

        restart(url, progressUrl) {
            var _this = this;

            new Dialog('confirm', {
                message: 'Are you sure you want to restart this campaign',
                ok: function() {
                    addMaskLoading();

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        }
                    }).done(function(result, textStatus, jqXHR) {
                        ValidateIndex.getList().load();

                        if (_this.callback) {
                            _this.callback(progressUrl);
                        }
                    }).fail(function(res) {
                        
                    }).always(function() {
                        removeMaskLoading();
                    });
                }
            })
            
        }
    }

    var CampaignProgress = class {
        constructor(options) {
            var _this = this;
            this.button = options.button;
            this.popup = window.campaignProgressPopup;

            this.button.on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                _this.load(url);
            });
        }

        load(url) {
            this.popup.load(url);
        }
    }

    var CampaignPause = class {
        constructor(options) {
            var _this = this;
            this.button = options.button;

            this.button.on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                _this.pause(url);
            });
        }

        pause(url) {
            new Dialog('confirm', {
                message: 'Are you sure you want to pause this campaign',
                ok: function() {
                    addMaskLoading();

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        }
                    }).done(function(result, textStatus, jqXHR) {
                        ValidateIndex.getList().load();
                    }).fail(function(res) {
                        
                    }).always(function() {
                        removeMaskLoading();
                    });
                }
            })
            
        }
    }
</script>