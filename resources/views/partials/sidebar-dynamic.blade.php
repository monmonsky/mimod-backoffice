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

                {{-- Dashboard --}}
                @if(hasPermission('dashboard.view'))
                    <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Overview</p>
                    <a class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <span class="iconify lucide--monitor-dot size-4"></span>
                        <span class="grow">Dashboard</span>
                    </a>
                @endif

                {{-- Access Control --}}
                @if(hasAnyPermission(['access-control.users.view', 'access-control.roles.view', 'access-control.permissions.view', 'access-control.modules.view', 'access-control.activity-logs.view']))
                    <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Access Control</p>

                    @if(hasPermission('access-control.users.view'))
                        <a class="menu-item {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}">
                            <span class="iconify lucide--users size-4"></span>
                            <span class="grow">Users</span>
                        </a>
                    @endif

                    @if(hasPermission('access-control.roles.view'))
                        <a class="menu-item {{ request()->routeIs('role.*') ? 'active' : '' }}" href="{{ route('role.index') }}">
                            <span class="iconify lucide--shield size-4"></span>
                            <span class="grow">Roles</span>
                        </a>
                    @endif

                    @if(hasPermission('access-control.permissions.view'))
                        <a class="menu-item {{ request()->routeIs('permission.*') ? 'active' : '' }}" href="{{ route('permission.index') }}">
                            <span class="iconify lucide--key-round size-4"></span>
                            <span class="grow">Permissions</span>
                        </a>
                    @endif

                    @if(hasPermission('access-control.modules.view'))
                        <a class="menu-item {{ request()->routeIs('modules.*') ? 'active' : '' }}" href="{{ route('modules.index') }}">
                            <span class="iconify lucide--layers size-4"></span>
                            <span class="grow">Modules</span>
                        </a>
                    @endif

                    @if(hasPermission('access-control.activity-logs.view'))
                        <a class="menu-item {{ request()->routeIs('activity-log.*') ? 'active' : '' }}" href="{{ route('activity-log.index') }}">
                            <span class="iconify lucide--file-text size-4"></span>
                            <span class="grow">Activity Logs</span>
                        </a>
                    @endif
                @endif

                {{-- Settings --}}
                @php
                    $hasGeneralsPermission = hasAnyPermission([
                        'settings.generals.store-info.view',
                        'settings.generals.email-settings.view',
                        'settings.generals.seo-meta.view',
                        'settings.generals.system-config.view',
                        'settings.generals.api-tokens.view'
                    ]);

                    $hasPaymentsPermission = hasAnyPermission([
                        'settings.payments.payment-methods.view',
                        'settings.payments.midtrans-config.view',
                        'settings.payments.tax-settings.view'
                    ]);

                    $hasShippingsPermission = hasAnyPermission([
                        'settings.shippings.shipping-methods.view',
                        'settings.shippings.rajaongkir-config.view',
                        'settings.shippings.origin-address.view'
                    ]);

                    $hasAnySettings = $hasGeneralsPermission || $hasPaymentsPermission || $hasShippingsPermission;
                @endphp

                @if($hasAnySettings)
                    <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Settings</p>

                    {{-- Generals --}}
                    @if($hasGeneralsPermission)
                        <div class="group collapse">
                            <input
                                aria-label="Sidemenu item trigger"
                                type="checkbox"
                                class="peer"
                                name="sidebar-menu-settings-generals"
                                {{ request()->routeIs('settings.generals.*') ? 'checked' : '' }} />
                            <div class="collapse-title px-2.5 py-1.5">
                                <span class="iconify lucide--settings size-4"></span>
                                <span class="grow">Generals</span>
                                <span class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                            </div>
                            <div class="collapse-content ms-6.5 !p-0">
                                <div class="mt-0.5 space-y-0.5">
                                    @if(hasPermission('settings.generals.store-info.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.generals.store') ? 'active' : '' }}"
                                           href="{{ route('settings.generals.store') }}">
                                            <span class="grow">Store Info</span>
                                        </a>
                                    @endif

                                    @if(hasPermission('settings.generals.email-settings.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.generals.email') ? 'active' : '' }}"
                                           href="{{ route('settings.generals.email') }}">
                                            <span class="grow">Email Settings</span>
                                        </a>
                                    @endif

                                    @if(hasPermission('settings.generals.seo-meta.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.generals.seo') ? 'active' : '' }}"
                                           href="{{ route('settings.generals.seo') }}">
                                            <span class="grow">SEO & Meta</span>
                                        </a>
                                    @endif

                                    @if(hasPermission('settings.generals.system-config.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.generals.system') ? 'active' : '' }}"
                                           href="{{ route('settings.generals.system') }}">
                                            <span class="grow">System Config</span>
                                        </a>
                                    @endif

                                    @if(hasPermission('settings.generals.api-tokens.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.generals.api-tokens') ? 'active' : '' }}"
                                           href="{{ route('settings.generals.api-tokens') }}">
                                            <span class="grow">API Tokens</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Payments --}}
                    @if($hasPaymentsPermission)
                        <div class="group collapse">
                            <input
                                aria-label="Sidemenu item trigger"
                                type="checkbox"
                                class="peer"
                                name="sidebar-menu-settings-payments"
                                {{ request()->routeIs('settings.payments.*') ? 'checked' : '' }} />
                            <div class="collapse-title px-2.5 py-1.5">
                                <span class="iconify lucide--wallet size-4"></span>
                                <span class="grow">Payments</span>
                                <span class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                            </div>
                            <div class="collapse-content ms-6.5 !p-0">
                                <div class="mt-0.5 space-y-0.5">
                                    @if(hasPermission('settings.payments.payment-methods.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.payments.methods') ? 'active' : '' }}"
                                           href="{{ route('settings.payments.methods') }}">
                                            <span class="grow">Payment Methods</span>
                                        </a>
                                    @endif

                                    @if(hasPermission('settings.payments.midtrans-config.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.payments.midtrans-config') ? 'active' : '' }}"
                                           href="{{ route('settings.payments.midtrans-config') }}">
                                            <span class="grow">Midtrans Config</span>
                                        </a>
                                    @endif

                                    @if(hasPermission('settings.payments.tax-settings.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.payments.tax-settings') ? 'active' : '' }}"
                                           href="{{ route('settings.payments.tax-settings') }}">
                                            <span class="grow">Tax Settings</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Shippings --}}
                    @if($hasShippingsPermission)
                        <div class="group collapse">
                            <input
                                aria-label="Sidemenu item trigger"
                                type="checkbox"
                                class="peer"
                                name="sidebar-menu-settings-shippings"
                                {{ request()->routeIs('settings.shippings.*') ? 'checked' : '' }} />
                            <div class="collapse-title px-2.5 py-1.5">
                                <span class="iconify lucide--truck size-4"></span>
                                <span class="grow">Shippings</span>
                                <span class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                            </div>
                            <div class="collapse-content ms-6.5 !p-0">
                                <div class="mt-0.5 space-y-0.5">
                                    @if(hasPermission('settings.shippings.shipping-methods.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.shippings.methods') ? 'active' : '' }}"
                                           href="{{ route('settings.shippings.methods') }}">
                                            <span class="grow">Shipping Methods</span>
                                        </a>
                                    @endif

                                    @if(hasPermission('settings.shippings.rajaongkir-config.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.shippings.rajaongkir-config') ? 'active' : '' }}"
                                           href="{{ route('settings.shippings.rajaongkir-config') }}">
                                            <span class="grow">RajaOngkir Config</span>
                                        </a>
                                    @endif

                                    @if(hasPermission('settings.shippings.origin-address.view'))
                                        <a class="menu-item {{ request()->routeIs('settings.shippings.origin-address') ? 'active' : '' }}"
                                           href="{{ route('settings.shippings.origin-address') }}">
                                            <span class="grow">Origin Address</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

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
                        <img src="{{ userAvatar() }}" alt="Avatar" />
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
