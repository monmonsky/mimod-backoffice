const authToken = $('meta[name="auth-token"]').attr("content");
const csrfToken = $('meta[name="csrf-token"]').attr("content");

/**
 * Global API Client
 * A reusable AJAX client to handle API requests
 */
const ApiClient = {
    // Base configuration that can be extended
    config: {
        headers: {
            Authorization: "Bearer " + authToken,
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        baseUrl: "",
        defaultDataType: "json",
    },

    // Initialize with custom configuration
    init: function (customConfig) {
        this.config = { ...this.config, ...customConfig };
        return this;
    },

    // Main request method
    request: function (
        endpoint,
        method,
        data = {},
        successCallback,
        errorCallback,
        options = {}
    ) {
        const requestConfig = {
            url: this.config.baseUrl + endpoint,
            type: method,
            data: data,
            dataType: options.dataType || this.config.defaultDataType,
            headers: { ...this.config.headers, ...options.headers },
            success: successCallback || function () { },
            error:
                errorCallback ||
                function (xhr, status, error) {
                    console.error("API request failed:", error);
                },
        };

        return $.ajax(requestConfig);
    },

    // Convenience methods for common HTTP verbs
    get: function (
        endpoint,
        data,
        successCallback,
        errorCallback,
        options = {}
    ) {
        return this.request(
            endpoint,
            "GET",
            data,
            successCallback,
            errorCallback,
            options
        );
    },

    post: function (
        endpoint,
        data,
        successCallback,
        errorCallback,
        options = {}
    ) {
        return this.request(
            endpoint,
            "POST",
            data,
            successCallback,
            errorCallback,
            options
        );
    },

    // DataTables specific method for handling server-side processing
    datatable: function (endpoint, data, callback, settings) {
        return this.request(
            endpoint,
            "GET",
            data,
            function (response) {
                callback(response);
            },
            function (xhr, status, error) {
                console.error("DataTable request failed:", error);
                // Return empty result set to prevent DataTables error
                callback({
                    draw: data.draw,
                    recordsTotal: 0,
                    recordsFiltered: 0,
                    data: [],
                });
            }
        );
    },

    auth_token: () => { return authToken },

    csrf_token: () => { return csrfToken }
};

export default ApiClient;
