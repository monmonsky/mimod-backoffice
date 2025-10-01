<!doctype html>
<html lang="en" class="group/html">
    <head>

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
