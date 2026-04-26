@extends('layouts.popup.small')

@section('content')
    <h3 class="h2 mb-2">{{ trans('messages.something_went_wrong') }}</h3>
    <div class="alert alert-danger">
        {{ $message }}
    </div>
@endsection