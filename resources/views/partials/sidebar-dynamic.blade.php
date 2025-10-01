<!--  Start: Layout - Sidebar -->

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
                    // Helper function to generate URL from route name or direct URL
                    $getUrl = function($route) {
                        if (!$route) return '#';
                        // Check if it's a route name (contains dot) or direct URL
                        if (str_contains($route, '.')) {
                            try {
                                return route($route);
                            } catch (\Exception $e) {
                                return '#';
                            }
                        }
                        // If starts with /, it's a direct URL
                        return str_starts_with($route, '/') ? $route : '/' . $route;
                    };

                    // Helper to check if route is active
                    $isActive = function($route) {
                        if (!$route) return false;
                        if (str_contains($route, '.')) {
                            return request()->routeIs($route . '*');
                        }
                        return request()->is(ltrim($route, '/') . '*');
                    };

                    // Group modules by component
                    $groupedModules = collect($sidebarModules)->groupBy('component');
                @endphp

                @forelse ($groupedModules as $componentName => $modules)
                    {{-- Component Label --}}
                    <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">{{ $componentName }}</p>

                    @foreach ($modules as $module)
                    @if (empty($module->children))
                        {{-- Single Menu Item --}}
                        <a class="menu-item {{ $isActive($module->route) ? 'active' : '' }}"
                           href="{{ $getUrl($module->route) }}">
                            @if($module->icon)
                                <span class="iconify {{ $module->icon }} size-4"></span>
                            @endif
                            <span class="grow">{{ $module->display_name ?? $module->name }}</span>
                        </a>
                    @else
                        {{-- Menu with Children --}}
                        @php
                            $hasActiveChild = collect($module->children)->contains(fn($child) => $isActive($child->route));
                        @endphp
                        <div class="group collapse">
                            <input
                                aria-label="Sidemenu item trigger"
                                type="checkbox"
                                class="peer"
                                name="sidebar-menu-{{ \Illuminate\Support\Str::slug($module->name) }}"
                                {{ $hasActiveChild ? 'checked' : '' }} />
                            <div class="collapse-title px-2.5 py-1.5">
                                @if($module->icon)
                                    <span class="iconify {{ $module->icon }} size-4"></span>
                                @endif
                                <span class="grow">{{ $module->display_name ?? $module->name }}</span>
                                <span class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                            </div>
                            <div class="collapse-content ms-6.5 !p-0">
                                <div class="mt-0.5 space-y-0.5">
                                    @foreach ($module->children as $child)
                                        <a
                                            class="menu-item {{ $isActive($child->route) ? 'active' : '' }}"
                                            href="{{ $getUrl($child->route) }}">
                                            <span class="grow">{{ $child->display_name ?? $child->name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    @endforeach
                @empty
                    {{-- Fallback if no modules found --}}
                    <p class="menu-label px-2.5 pt-3 pb-1.5">No modules available</p>
                @endforelse
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
                        <img src="./images/avatars/1.png" alt="Avatar" />
                    </div>
                </div>
                <div class="grow -space-y-0.5">
                    <p class="text-sm font-medium">Denish N</p>
                    <p class="text-base-content/60 text-xs">@withden</p>
                </div>
                <span
                    class="iconify lucide--chevrons-up-down text-base-content/60 size-4"></span>
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
            </ul>
        </div>
    </div>
</div>

<label for="layout-sidebar-toggle-trigger" id="layout-sidebar-backdrop"></label>

<!--  End: Layout - Sidebar -->
