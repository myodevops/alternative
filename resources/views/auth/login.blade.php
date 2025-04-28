@if (config('auth.method') === 'sanctum')
    @include('auth.sanctum')
@elseif (config('auth.method') === 'passkey')
    @include('auth.passkey')
@else
    <p style="color: red;">Login method not supported. Set the AUTH_METHOD environment variable to 'sanctum' or 'passkey'.</p>
@endif