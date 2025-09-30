import ApiClient from "../../helpers/client";
import Toast from "../../components/toast";

$(document).ready(function () {
    $("#submit-form").submit(function (e) {
        e.preventDefault();

        const formData = $('#submit-form').serialize()

        const params = new URLSearchParams(formData);

        const requestParam = Object.fromEntries(params);

        ApiClient.post(
            '/login',
            requestParam,
            function (response) {
                if (response.status) {
                    Toast.showToast(response.message, 'success', 3000);
                } else {
                    Toast.showToast(response.message, 'error', 3000);
                }
            }
        );
    });
})