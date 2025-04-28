@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('js')
    @webpassCdn
    @alternativeAsset('passkey-login')
@endpush

@push('css')
    @alternativeCss('alternative')
@endpush

@section('auth_header', 'Login with Passkey')

@section('auth_body')
    <button id="passkey-login-button" class="btn btn-block btn-primary">
        <span class="fas fa-key"></span> Login with Passkey
    </button>
@stop

@section('auth_footer')
    <p class="text-center">
        <a href="{{ route('register-passkey') }}">Don't have a Passkey? Register here</a>
    </p>
@stop
