@extends('layouts.popup.small')

@section('content')
    <div class="text-center mb-4 py-4">
        <h4 class="fw-bold text-center">{{ trans('server::messages.please_wait') }} </h4>
        <div class="spinner-border text-primary" role="status" style="width: 5rem; height: 5rem;border-width: 0.6em;">
            <span class="visually-hidden">{{ trans('server::messages.Loading_') }} </span>
        </div>
    </div>

    <script>
        $(function() {
            new VerifyManager({
                url: '{{ route('acelle_server.landing.verify', [
                    'email' => request()->email,
                ]) }}',
            });
        });

        var VerifyManager = class {
            constructor(options) {
                var _this = this;
                this.url = options.url;

                this.verify();
            }

            verify() {
                $.ajax({
                    url: this.url,
                    type: 'POST',
                }).done(function(response) {
                    window.aVerify.popup.loadHtml(response);
                });
            }
        }
    </script>
@endsection