@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('js')
    @webpassCdn
    @alternativeAsset('passkey-register')
@endpush

@push('css')
    @alternativeCss('alternative')
@endpush

@section('auth_header', 'Register your Passkey')

@section('auth_body')
    <div class="text-center mb-4">
        <p>Press the button below to register a new Passkey on this device.</p>
    </div>

    <button id="passkey-register-button" class="btn btn-block btn-success">
        <span class="fas fa-key"></span> Register Passkey
    </button>
@stop

@section('auth_footer')
    <p class="text-center">
        <a href="{{ route('login') }}">Already have a Passkey? Login here</a>
    </p>
@stop
