<!--  Start: Layout - Sidebar Dynamic -->

<input
    type="checkbox"
    id="layout-sidebar-toggle-trigger"
    class="hidden"
    aria-label="Toggle layout sidebar" />
<input
    type="checkbox"
    id="layout-sidebar-hover-trigger"
    class="hidden"
    aria-label="Dense layout sidebar" />
<div id="layout-sidebar-hover" class="bg-base-300 h-screen w-1"></div>

<div id="layout-sidebar" class="sidebar-menu sidebar-menu-activation">
    <div class="flex min-h-16 items-center justify-between gap-3 ps-5 pe-4">
        <a href="{{ route('dashboard') }}">
            @include('partials.logo')
        </a>
        <label
            for="layout-sidebar-hover-trigger"
            title="Toggle sidebar hover"
            class="btn btn-circle btn-ghost btn-sm text-base-content/50 relative max-lg:hidden">
            <span
                class="iconify lucide--panel-left-close absolute size-4.5 opacity-100 transition-all duration-300 group-has-[[id=layout-sidebar-hover-trigger]:checked]/html:opacity-0"></span>
            <span
                class="iconify lucide--panel-left-dashed absolute size-4.5 opacity-0 transition-all duration-300 group-has-[[id=layout-sidebar-hover-trigger]:checked]/html:opacity-100"></span>
        </label>
    </div>
    <div class="relative min-h-0 grow">
        <div data-simplebar class="size-full">
            <div class="mb-3 space-y-0.5 px-2.5">

                @php
                    $grouped = groupModulesBySection($sidebarModules ?? []);

                    // Map group names to display labels
                    $groupLabels = [
                        'overview' => 'Overview',
                        'catalog' => 'Catalog',
                        'access_control' => 'Access Control',
                        'settings' => 'Settings',
                    ];
                @endphp

                {{-- Loop through all groups dynamically --}}
                @foreach($grouped as $groupKey => $groupModules)
                    @php
                        $groupLabel = $groupLabels[$groupKey] ?? ucwords(str_replace('_', ' ', $groupKey));
                    @endphp

                    <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">{{ $groupLabel }}</p>
                    {!! renderSidebarMenu($groupModules) !!}
                @endforeach

            </div>
        </div>
        <div
            class="from-base-100/60 pointer-events-none absolute start-0 end-0 bottom-0 h-7 bg-linear-to-t to-transparent"></div>
    </div>
    <div class="mb-2">
        <hr class="border-base-300 my-2 border-dashed" />
        <div class="dropdown dropdown-top dropdown-end w-full">
            <div
                tabindex="0"
                role="button"
                class="bg-base-200 hover:bg-base-300 rounded-box mx-2 mt-0 flex cursor-pointer items-center gap-2.5 px-3 py-2 transition-all">
                <div class="avatar">
                    <div class="bg-base-200 mask mask-squircle w-8">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ currentUser('email', 'default') }}" alt="Avatar" />
                    </div>
                </div>
                <div class="grow -space-y-0.5">
                    <p class="text-sm font-medium">{{ userName() }}</p>
                    <p class="text-base-content/60 text-xs">{{ userRole('Guest') }}</p>
                </div>
                <span class="iconify lucide--chevrons-up-down text-base-content/60 size-4"></span>
            </div>
            <ul
                role="menu"
                tabindex="0"
                class="dropdown-content menu bg-base-100 rounded-box shadow-base-content/4 mb-1 w-48 p-1 shadow-[0px_-10px_40px_0px]">
                <li>
                    <a href="#">
                        <span class="iconify lucide--user size-4"></span>
                        <span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="iconify lucide--settings size-4"></span>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="iconify lucide--help-circle size-4"></span>
                        <span>Help</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="iconify lucide--log-out size-4"></span>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<label for="layout-sidebar-toggle-trigger" id="layout-sidebar-backdrop"></label>

<!--  End: Layout - Sidebar Dynamic -->
