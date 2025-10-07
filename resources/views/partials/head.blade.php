<meta charset="UTF-8" />
<meta name="author" content="Denish Navadiya" />
<meta
    name="keywords"
    content="HTML, CSS, daisyui, tailwindcss, admin, client, dashboard, ui kit, component" />
<meta
    name="description"
    content="Start your next project with Nexus, designed for effortless customization to streamline your development process" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="shortcut icon" href="{{ asset('images/favicon-dark.png') }}" media="(prefers-color-scheme: dark)" />
<link rel="shortcut icon" href="{{ asset('images/favicon-light.png') }}" media="(prefers-color-scheme: light)" />

@vite(['resources/css/app.css', 'resources/js/partials/theme-init.js'])
@stack('styles')
