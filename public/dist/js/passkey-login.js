document.addEventListener('DOMContentLoaded', () => {
    const loginButton = document.getElementById('passkey-login-button');

    if (!loginButton) {
        console.error('Login button not found');
        return;
    }

    loginButton.addEventListener('click', async (event) => {
        event.preventDefault();

        try {
            if (Webpass.isUnsupported()) {
                alert("Your browser doesn't support WebAuthn.");
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

            const { user, success, error } = await Webpass.assert(
                '/webauthn/login/options',
                '/webauthn/login',
                {
                    csrf: csrfToken
                }
            );

            if (success) {
                window.location.href = '/dashboard';
            } else {
                if (error?.name === 'InvalidStateError' || error?.name === 'NotAllowedError') {
                    Swal.fire({
                        title: 'No Passkey found',
                        text: 'It looks like you haven\'t registered your Passkey yet. Would you like to register now?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Register Passkey',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/register-passkey';
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Login Failed',
                        text: error?.message || 'An unexpected error occurred during Passkey login.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
                console.error(error);
            }
        } catch (error) {
            console.error('Error during Passkey login', error);
        }
    });
});
