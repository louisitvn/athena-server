@if ($emails->isEmpty())
    <div class="text-center py-5 text-muted">
        <span class="material-symbols-rounded fs-1">inbox</span>
        <p class="mt-2">No emails found.</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:40%">Email</th>
                    <th style="width:15%">Status</th>
                    <th style="width:20%">Verified by</th>
                    <th style="width:25%">Verified at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($emails as $email)
                    <tr>
                        <td class="text-break fw-medium">{{ $email->email }}</td>
                        <td>
                            @php
                                $badgeClass = match($email->verification_status) {
                                    'deliverable'   => 'bg-success',
                                    'undeliverable' => 'bg-danger',
                                    'risky'         => 'bg-warning text-dark',
                                    'unknown'       => 'bg-secondary',
                                    default         => 'bg-light text-dark border',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} rd-50">{{ $email->verification_status }}</span>
                        </td>
                        <td class="text-muted small">{{ $email->last_verification_by ?? '—' }}</td>
                        <td class="text-muted small">
                            {{ $email->last_verification_at
                                ? \Carbon\Carbon::parse($email->last_verification_at)->format('Y-m-d H:i:s')
                                : '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($emails->lastPage() > 1)
        <div class="d-flex align-items-center justify-content-between px-3 pt-3 pb-1 border-top">
            <div class="text-muted small">
                Showing {{ $emails->firstItem() }}–{{ $emails->lastItem() }} of {{ $emails->total() }}
            </div>
            <div class="fp-pagination d-flex gap-1">
                @if ($emails->onFirstPage())
                    <button class="btn btn-sm btn-light" disabled>‹</button>
                @else
                    <button class="btn btn-sm btn-light fp-page" data-page="{{ $emails->currentPage() - 1 }}">‹</button>
                @endif

                @foreach (range(max(1, $emails->currentPage() - 2), min($emails->lastPage(), $emails->currentPage() + 2)) as $page)
                    <button class="btn btn-sm {{ $page == $emails->currentPage() ? 'btn-primary' : 'btn-light' }} fp-page"
                        data-page="{{ $page }}">{{ $page }}</button>
                @endforeach

                @if ($emails->currentPage() < $emails->lastPage())
                    <button class="btn btn-sm btn-light fp-page" data-page="{{ $emails->currentPage() + 1 }}">›</button>
                @else
                    <button class="btn btn-sm btn-light" disabled>›</button>
                @endif
            </div>
        </div>
    @else
        <div class="text-muted small px-3 pt-2 pb-1">
            {{ $emails->total() }} {{ Str::plural('email', $emails->total()) }}
        </div>
    @endif
@endif
