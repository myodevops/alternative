document.addEventListener('DOMContentLoaded', () => {
    const registerButton = document.getElementById('passkey-register-button');

    if (!registerButton) {
        console.error('Register button not found');
        return;
    }

    registerButton.addEventListener('click', async (event) => {
        event.preventDefault();

        try {
            if (Webpass.isUnsupported()) {
                alert("Your browser doesn't support WebAuthn.");
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

            const { credential, success, error } = await Webpass.attest(
                '/webauthn/register/options',
                '/webauthn/register',
                {
                    csrf: csrfToken
                }
            );

            if (success) {
                Swal.fire({
                    title: 'Passkey registered successfully!',
                    icon: 'success',
                    confirmButtonText: 'Go to Dashboard'
                }).then(() => {
                    window.location.href = '/dashboard';
                });
            } else {
                Swal.fire({
                    title: 'Passkey registration failed',
                    text: error || 'Unknown error',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        } catch (error) {
            console.error('Error during Passkey registration', error);
            Swal.fire({
                title: 'Error',
                text: error.message || 'An unexpected error occurred',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        }
    });
});
