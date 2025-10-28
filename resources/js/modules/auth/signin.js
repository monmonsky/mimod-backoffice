import Ajax from "../../utils/ajax";
import Toast from "../../components/toast";

$(document).ready(function () {
    $("#submit-form").submit(async function (e) {
        e.preventDefault();

        const formData = $('#submit-form').serialize()

        const params = new URLSearchParams(formData);

        const requestParam = Object.fromEntries(params);

        await Ajax.post('/api/auth/login', requestParam, {
            useGlobalLoading: false,        // Disable global loading
            loadingTarget: '#submitButton',      // Use target-specific loading,
            showToast: false,
            onSuccess: (response) => {
                 if (response.status) {
                    Toast.showToast(response.message, 'success', 3000);

                    $.cookie("auth_token", response.data.token);

                    setTimeout(() => {
                        window.location.href = '/'
                    }, 1000);
                } else {
                    Toast.showToast(response.message, 'error', 3000);
                }
            },
        });
    });
})
