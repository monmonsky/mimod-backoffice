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
        <a href="./dashboards-ecommerce.html">
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
                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Overview</p>
                <a class="menu-item false {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <span class="iconify lucide--monitor-dot size-4"></span>
                    <span class="grow">Dashboard</span>
                </a>
                
                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Manage</p>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-parent-item" />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--package size-4"></span>
                        <span class="grow">Products</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-orders.html">
                                <span class="grow">All Products</span>
                            </a>
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-products.html">
                                <span class="grow">Add New</span>
                            </a>
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-customers.html">
                                <span class="grow">Categories</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-parent-item" />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--receipt-text size-4"></span>
                        <span class="grow">Orders</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-orders.html">
                                <span class="grow">All Orders</span>
                            </a>
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-products.html">
                                <span class="grow">Return</span>
                            </a>
                            
                        </div>
                    </div>
                </div>
                <a class="menu-item" href="./apps-chat.html">
                    <span class="iconify lucide--users size-4"></span>
                    <span class="grow">Customers</span>
                </a>
                <a class="menu-item" href="./apps-chat.html">
                    <span class="iconify lucide--warehouse size-4"></span>
                    <span class="grow">Inventory</span>
                </a>
                <a class="menu-item" href="./apps-chat.html">
                    <span class="iconify lucide--dollar-sign size-4"></span>
                    <span class="grow">Payouts</span>
                </a>

                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Management</p>
                <a class="menu-item false {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}">
                    <span class="iconify lucide--user size-4"></span>
                    <span class="grow">User</span>
                </a>
                <a class="menu-item false" target="_blank" href="./landing.html">
                    <span class="iconify lucide--component size-4"></span>
                    <span class="grow">Role</span>
                </a>

                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Setting</p>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-parent-item" />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--store size-4"></span>
                        <span class="grow">Store</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-orders.html">
                                <span class="grow">General</span>
                            </a>
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-products.html">
                                <span class="grow">Page</span>
                            </a>
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-products.html">
                                <span class="grow">SEO</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-parent-item" />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--unplug size-4"></span>
                        <span class="grow">Service</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-orders.html">
                                <span class="grow">Payment</span>
                            </a>
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-products.html">
                                <span class="grow">Shipping</span>
                            </a>
                            <a
                                class="menu-item false"
                                href="./apps-ecommerce-products.html">
                                <span class="grow">Maps</span>
                            </a>
                        </div>
                    </div>
                </div>

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
                    <a href="./pages/settings.html">
                        <span class="iconify lucide--user size-4"></span>
                        <span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a href="./pages/settings.html">
                        <span class="iconify lucide--settings size-4"></span>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="./pages/get-help.html">
                        <span class="iconify lucide--help-circle size-4"></span>
                        <span>Help</span>
                    </a>
                </li>
                <li>
                    <div>
                        <span class="iconify lucide--bell size-4"></span>
                        <span>Notification</span>
                    </div>
                </li>
                <li>
                    <div>
                        <span
                            class="iconify lucide--arrow-left-right size-4"></span>
                        <span>Switch Account</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<label for="layout-sidebar-toggle-trigger" id="layout-sidebar-backdrop"></label>

<!--  End: Layout - Sidebar -->
