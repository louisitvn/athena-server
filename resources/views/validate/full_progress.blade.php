@extends('layouts.core.frontend', [
    'menu' => 'validate',
])

@section('page_title')
    Campaign Progress
@endsection

@section('content')
@php
    $uid       = $verificationCampaign->uid;
    $jsonUrl   = route('acelle_server.validate.full_progress_json', $uid);
    $emailsUrl = route('acelle_server.validate.full_progress_emails', $uid);
    $reportUrl = route('acelle_server.validate.report', ['verification_campaign_uid' => $uid]);
    $listUrl   = route('acelle_server.validate.index');
@endphp

{{-- ── Page header ──────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <a href="{{ $listUrl }}" class="text-muted small text-decoration-none">
            <span class="material-symbols-rounded align-middle" style="font-size:1rem">arrow_back</span>
            Back to list
        </a>
        <h4 class="mb-0 mt-1 fw-bold highContrastColor">
            <span class="material-symbols-rounded align-middle fs-5">list_alt</span>
            {{ $verificationCampaign->file_name ?? 'Campaign #' . $verificationCampaign->id }}
        </h4>
        <div class="text-muted small mt-1">
            Started {{ $verificationCampaign->created_at?->format('Y-m-d H:i:s') }}
            &nbsp;·&nbsp;
            <span id="fp-last-update" class="text-muted">Connecting...</span>
        </div>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <div id="fp-status-badge">
            @include('server::validate._status', ['verificationCampaign' => $verificationCampaign])
        </div>
        <a id="fp-btn-report"
           href="{{ $reportUrl }}"
           class="btn btn-outline-primary {{ $verificationCampaign->isCompleted() ? '' : 'd-none' }}">
            <span class="material-symbols-rounded align-middle fs-6">download</span>
            Download Report
        </a>
    </div>
</div>

{{-- ── Overall progress bar ─────────────────────────────────────────────── --}}
<div class="card ecard mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold highContrastColor">Overall Progress</span>
            <span id="fp-percent" class="fw-bold fs-4 lightPurpleColor">—</span>
        </div>
        <div class="progress" style="height:12px;" role="progressbar">
            <div id="fp-progress-bar"
                 class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                 style="width:0%"></div>
        </div>
        <div class="d-flex justify-content-between mt-2 small">
            <span class="text-muted">
                <strong id="fp-verified" class="highContrastColor">—</strong> verified
            </span>
            <span class="text-muted">
                <strong id="fp-pending" class="highContrastColor">—</strong> pending
            </span>
            <span class="text-muted">
                <strong id="fp-total" class="highContrastColor">—</strong> total
            </span>
        </div>
    </div>
</div>

{{-- ── Stat cards ───────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl">
        <div class="card ecard h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="badge text-bg-success d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <span class="material-symbols-rounded" style="font-size:1rem">check_circle</span>
                    </div>
                    <span class="small text-muted">Deliverable</span>
                </div>
                <div class="fw-bold fs-3 highContrastColor" id="fp-deliverable">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="card ecard h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="badge text-bg-danger d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <span class="material-symbols-rounded" style="font-size:1rem">cancel</span>
                    </div>
                    <span class="small text-muted">Undeliverable</span>
                </div>
                <div class="fw-bold fs-3 highContrastColor" id="fp-undeliverable">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="card ecard h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="badge text-bg-warning d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <span class="material-symbols-rounded" style="font-size:1rem">warning</span>
                    </div>
                    <span class="small text-muted">Risky</span>
                </div>
                <div class="fw-bold fs-3 highContrastColor" id="fp-risky">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="card ecard h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="badge text-bg-secondary d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <span class="material-symbols-rounded" style="font-size:1rem">help</span>
                    </div>
                    <span class="small text-muted">Unknown</span>
                </div>
                <div class="fw-bold fs-3 highContrastColor" id="fp-unknown">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="card ecard h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="badge text-bg-light border d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <span class="material-symbols-rounded text-muted" style="font-size:1rem">hourglass_empty</span>
                    </div>
                    <span class="small text-muted">Pending</span>
                </div>
                <div class="fw-bold fs-3 highContrastColor" id="fp-stat-pending">—</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Queue + Servers row ──────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Batch / Queue --}}
    <div class="col-md-6">
        <div class="card ecard h-100">
            <div class="card-body">
                <h6 class="fw-semibold highContrastColor mb-3">
                    <span class="material-symbols-rounded align-middle fs-6">manage_history</span>
                    Queue / Batch Jobs
                </h6>
                <div id="fp-batch-body">
                    <p class="text-muted small mb-0">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Servers --}}
    <div class="col-md-6">
        <div class="card ecard h-100">
            <div class="card-body">
                <h6 class="fw-semibold highContrastColor mb-3">
                    <span class="material-symbols-rounded align-middle fs-6">dns</span>
                    Verification Servers
                </h6>
                <div id="fp-servers-body">
                    <p class="text-muted small mb-0">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Email address table ──────────────────────────────────────────────── --}}
<div class="card ecard">
    <div class="card-body pb-0">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <h6 class="fw-semibold highContrastColor mb-0 me-auto">
                <span class="material-symbols-rounded align-middle fs-6">email</span>
                Email Addresses
            </h6>

            {{-- Search --}}
            <div class="input-group input-group-sm" style="max-width:220px;">
                <span class="input-group-text bg-transparent border-end-0">
                    <span class="material-symbols-rounded" style="font-size:.9rem">search</span>
                </span>
                <input id="fp-search" type="text" class="form-control border-start-0" placeholder="Search email...">
            </div>

            {{-- Status filter --}}
            <select id="fp-filter-status" class="form-select form-select-sm" style="max-width:160px;">
                <option value="all">All statuses</option>
                <option value="deliverable">Deliverable</option>
                <option value="undeliverable">Undeliverable</option>
                <option value="risky">Risky</option>
                <option value="unknown">Unknown</option>
                <option value="new">Pending</option>
            </select>

            {{-- Per-page --}}
            <select id="fp-per-page" class="form-select form-select-sm" style="max-width:85px;">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <div id="fp-emails-container">
        <div class="text-center py-5 text-muted small">
            <span class="material-symbols-rounded d-block mb-2" style="font-size:2rem">hourglass_top</span>
            Loading...
        </div>
    </div>
</div>

<script>
$(function() {
    var FP = {
        jsonUrl:   '{{ $jsonUrl }}',
        emailsUrl: '{{ $emailsUrl }}',

        emailPage:    1,
        emailSearch:  '',
        emailStatus:  'all',
        emailPerPage: 25,
        emailSortBy:  'id',
        emailSortDir: 'desc',

        fetchedAt:  null,
        isDone:     false,

        init: function() {
            this.pollStats();
            this.loadEmails();
            this.bindEvents();
            setInterval(function() { FP.updateLastUpdateLabel(); }, 1000);
        },

        // ─── Stats polling ──────────────────────────────────────────
        pollStats: function() {
            $.ajax({ url: this.jsonUrl, dataType: 'json' })
                .done(function(res) { FP.renderStats(res); })
                .always(function() {
                    if (!FP.isDone) {
                        setTimeout(function() { FP.pollStats(); }, 2000);
                    }
                });
        },

        renderStats: function(res) {
            var p = res.progress;

            $('#fp-percent').text(p.percent + '%');
            $('#fp-progress-bar').css('width', p.percent + '%');

            $('#fp-verified').text(Number(p.verified).toLocaleString());
            $('#fp-pending').text(Number(p.pending).toLocaleString());
            $('#fp-total').text(Number(p.total).toLocaleString());
            $('#fp-deliverable').text(Number(p.deliverable).toLocaleString());
            $('#fp-undeliverable').text(Number(p.undeliverable).toLocaleString());
            $('#fp-risky').text(Number(p.risky).toLocaleString());
            $('#fp-unknown').text(Number(p.unknown).toLocaleString());
            $('#fp-stat-pending').text(Number(p.pending).toLocaleString());

            $('#fp-status-badge').html(res.status_html);

            if (res.campaign.status === 'completed' || res.campaign.status === 'error') {
                FP.isDone = true;
                $('#fp-progress-bar').removeClass('progress-bar-animated progress-bar-striped');
                $('#fp-btn-report').removeClass('d-none');
            }

            FP.renderBatch(res.batch);
            FP.renderServers(res.servers, p.total);

            FP.fetchedAt = new Date(res.fetched_at);
            FP.updateLastUpdateLabel();
        },

        renderBatch: function(batch) {
            if (!batch) {
                $('#fp-batch-body').html('<p class="text-muted small mb-0">No batch information available.</p>');
                return;
            }

            var html = '<div class="row g-2 text-center mb-3">';
            html += FP.batchCell('Total jobs',     batch.total_jobs,      '');
            html += FP.batchCell('Processed',      batch.processed_jobs,  'text-success');
            html += FP.batchCell('Pending',        batch.pending_jobs,    batch.pending_jobs > 0 ? 'text-primary' : 'text-muted');
            html += FP.batchCell('Failed',         batch.failed_jobs,     batch.failed_jobs  > 0 ? 'text-danger'  : 'text-muted');
            html += '</div>';

            if (batch.progress !== undefined) {
                html += '<div class="mb-1 d-flex justify-content-between small text-muted">'
                      + '<span>Job execution progress</span>'
                      + '<span class="fw-semibold">' + batch.progress + '%</span></div>';
                html += '<div class="progress mb-3" style="height:6px;">'
                      + '<div class="progress-bar bg-primary" style="width:' + batch.progress + '%"></div></div>';
            }

            if (batch.finished_at) {
                html += '<p class="text-muted small mb-0">Finished at: '
                      + new Date(batch.finished_at).toLocaleString() + '</p>';
            }
            if (batch.cancelled) {
                html += '<span class="badge bg-warning text-dark mt-1">Cancelled</span>';
            }

            $('#fp-batch-body').html(html);
        },

        batchCell: function(label, val, cls) {
            return '<div class="col-3">'
                + '<div class="border rounded py-2 px-1">'
                + '<div class="fw-bold highContrastColor ' + cls + '">' + (val !== undefined ? val : '—') + '</div>'
                + '<div class="text-muted" style="font-size:.7rem;">' + label + '</div>'
                + '</div></div>';
        },

        renderServers: function(servers, total) {
            if (!servers || servers.length === 0) {
                $('#fp-servers-body').html('<p class="text-muted small mb-0">No verification jobs dispatched yet.</p>');
                return;
            }

            var html = '<div class="table-responsive">'
                     + '<table class="table table-sm table-hover mb-0">'
                     + '<thead class="table-light"><tr>'
                     + '<th>Server</th><th class="text-end">Emails handled</th><th class="text-end">Share</th>'
                     + '</tr></thead><tbody>';

            servers.forEach(function(s) {
                var pct = total > 0 ? (s.count / total * 100).toFixed(1) : '0';
                html += '<tr>'
                      + '<td class="text-break fw-medium">'
                      + '<span class="material-symbols-rounded align-middle" style="font-size:.9rem">dns</span> '
                      + s.name + '</td>'
                      + '<td class="text-end fw-bold">' + Number(s.count).toLocaleString() + '</td>'
                      + '<td class="text-end text-muted">' + pct + '%</td>'
                      + '</tr>';
            });

            html += '</tbody></table></div>';
            $('#fp-servers-body').html(html);
        },

        // ─── Last-update ticker ────────────────────────────────────
        updateLastUpdateLabel: function() {
            if (!this.fetchedAt) return;
            var secs = Math.round((new Date() - this.fetchedAt) / 1000);
            var label = secs <= 1  ? 'Just updated'
                      : secs < 60  ? 'Updated ' + secs + 's ago'
                      :              'Updated ' + Math.round(secs / 60) + 'm ago';
            $('#fp-last-update').text(label);
        },

        // ─── Email list ────────────────────────────────────────────
        bindEvents: function() {
            var searchDelay;

            $('#fp-search').on('input', function() {
                clearTimeout(searchDelay);
                var val = $(this).val();
                searchDelay = setTimeout(function() {
                    FP.emailSearch = val;
                    FP.emailPage   = 1;
                    FP.loadEmails();
                }, 350);
            });

            $('#fp-filter-status').on('change', function() {
                FP.emailStatus = $(this).val();
                FP.emailPage   = 1;
                FP.loadEmails();
            });

            $('#fp-per-page').on('change', function() {
                FP.emailPerPage = parseInt($(this).val());
                FP.emailPage    = 1;
                FP.loadEmails();
            });

            $(document).on('click', '.fp-page', function() {
                FP.emailPage = parseInt($(this).data('page'));
                FP.loadEmails();
            });
        },

        loadEmails: function() {
            $('#fp-emails-container').css('opacity', '0.5');

            $.ajax({
                url:  this.emailsUrl,
                data: {
                    search:   this.emailSearch,
                    status:   this.emailStatus,
                    page:     this.emailPage,
                    per_page: this.emailPerPage,
                    sort_by:  this.emailSortBy,
                    sort_dir: this.emailSortDir,
                },
            }).done(function(html) {
                $('#fp-emails-container').html(html).css('opacity', '1');
            });

            // auto-refresh email list while running
            if (!this.isDone) {
                clearTimeout(this._emailTimer);
                this._emailTimer = setTimeout(function() { FP.loadEmails(); }, 5000);
            }
        },
    };

    FP.init();
});
</script>
@endsection
