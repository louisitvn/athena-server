<div class="mb-4 basetextcolor ">
    <ul class="nav nav-tabs tab-monitor" role="tablist">
        <li class="nav-item me-4" role="presentation">
            <a href="{{ route('acelle_server.monitor.index') }}" class="nav-link {{ !request()->tab ? 'active' : '' }} fs-4 py-1 mb-0 fw-semibold basetextcolor">
                {{ trans('messages.all') }}
            </a>
        </li>
        <li class="nav-item me-4" role="presentation">
            <a href="{{ route('acelle_server.monitor.index', [
                'tab' => 'valid',
            ]) }}" class="nav-link {{ request()->tab == 'valid' ? 'active' : '' }} fs-4 py-1 mb-0 fw-semibold basetextcolor">
                {{ trans('server::messages.module.valid') }}
            </a>
        </li>
        <li class="nav-item me-4" role="presentation">
            <a href="{{ route('acelle_server.monitor.index', [
                'tab' => 'invalid',
            ]) }}" class="nav-link {{ request()->tab == 'invalid' ? 'active' : '' }} fs-4 py-1 mb-0 fw-semibold basetextcolor">
                {{ trans('server::messages.module.invalid') }}
            </a>
        </li>
        <li class="nav-item me-4" role="presentation">
            <a href="{{ route('acelle_server.monitor.index', [
                'tab' => 'unknown',
            ]) }}" class="nav-link {{ request()->tab == 'unknown' ? 'active' : '' }} fs-4 py-1 mb-0 fw-semibold basetextcolor">
                {{ trans('server::messages.module.unknown') }}
            </a>
        </li>
        <li class="nav-item me-4" role="presentation">
            <a href="{{ route('acelle_server.monitor.index', [
                'tab' => 'risky',
            ]) }}" class="nav-link {{ request()->tab == 'risky' ? 'active' : '' }} fs-4 py-1 mb-0 fw-semibold basetextcolor">
                {{ trans('server::messages.module.risky') }}
            </a>
        </li>
    </ul>
</div>