<meta charset="UTF-8" />
<meta name="author" content="Denish Navadiya" />
<meta
    name="keywords"
    content="HTML, CSS, daisyui, tailwindcss, admin, client, dashboard, ui kit, component" />
<meta
    name="description"
    content="Start your next project with Nexus, designed for effortless customization to streamline your development process" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="shortcut icon" href="{{ asset('assets/images/favicon-dark.png') }}" media="(prefers-color-scheme: dark)" />
<link rel="shortcut icon" href="{{ asset('assets/images/favicon-light.png') }}" media="(prefers-color-scheme: light)" />

<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}" />
<script>
    try {
        const localStorageItem = localStorage.getItem("__NEXUS_CONFIG_v3.0__")
        if (localStorageItem) {
            const theme = JSON.parse(localStorageItem).theme
            if (theme !== "system") {
                document.documentElement.setAttribute("data-theme", theme)
            }
        }
    } catch (err) {
        console.log(err)
    }
</script>
