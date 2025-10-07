<!doctype html>
<html lang="en" class="group/html" data-theme="light">
    <head>
        <script>
            try {
                const localStorageItem = localStorage.getItem("__NEXUS_CONFIG_v3.0__");
                if (localStorageItem) {
                    const theme = JSON.parse(localStorageItem).theme;
                    if (theme === "system") {
                        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        document.documentElement.setAttribute("data-theme", prefersDark ? 'dark' : 'light');
                    } else {
                        document.documentElement.setAttribute("data-theme", theme);
                    }
                } else {
                    document.documentElement.setAttribute("data-theme", 'light');
                }
            } catch (err) {
                document.documentElement.setAttribute("data-theme", 'light');
            }
        </script>

        {{-- Global User Permissions --}}
        <script>
            window.userPermissions = @json(auth()->check() ? auth()->user()->permissions->pluck('name')->toArray() : []);

            window.hasPermission = function(permission) {
                return window.userPermissions && window.userPermissions.includes(permission);
            };
        </script>

        <title>@yield('title', 'Dashboard') - Minimoda</title>
        @include('partials.head')

    </head>

    <body>
        <!--  Start: Layout - Main -->

        <div class="size-full">
            <div class="flex">

                @include('partials.sidebar-dynamic')

                <div class="flex h-screen min-w-0 grow flex-col overflow-auto">
                    
                    @include('partials.topbar')

                    <!--  Start: Layout - Content -->
                    <div id="layout-content">
                        @yield('content')
                    </div>

                    <!--  End: Layout - Content -->
                    @include('partials.footer')
                </div>
            </div>

            @include('partials.rightbar')
        </div>

        <!--  End: Layout - Main -->

        @include('partials.footer-scripts')
        @yield('customjs')
    </body>
</html>
