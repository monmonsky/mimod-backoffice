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
                <a class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <span class="iconify lucide--monitor-dot size-4"></span>
                    <span class="grow">Dashboard</span>
                </a>

                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Catalog</p>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-products" />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--package size-4"></span>
                        <span class="grow">Products</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">All Products</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Add Product</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Categories</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Brands</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Variants</span>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Sales</p>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-orders" />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--shopping-cart size-4"></span>
                        <span class="grow">Orders</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">All Orders</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Pending Orders</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Processing</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Shipped</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Delivered</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Returns</span>
                            </a>
                        </div>
                    </div>
                </div>
                <a class="menu-item" href="#">
                    <span class="iconify lucide--credit-card size-4"></span>
                    <span class="grow">Payments</span>
                </a>
                <a class="menu-item" href="#">
                    <span class="iconify lucide--truck size-4"></span>
                    <span class="grow">Shipments</span>
                </a>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-carts" />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--shopping-bag size-4"></span>
                        <span class="grow">Carts</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Active Carts</span>
                            </a>
                            <a
                                class="menu-item"
                                href="#">
                                <span class="grow">Abandoned Carts</span>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Customers</p>
                <a class="menu-item" href="#">
                    <span class="iconify lucide--users size-4"></span>
                    <span class="grow">All Customers</span>
                </a>
                <a class="menu-item" href="#">
                    <span class="iconify lucide--map-pin size-4"></span>
                    <span class="grow">Addresses</span>
                </a>
                <a class="menu-item" href="#">
                    <span class="iconify lucide--bell size-4"></span>
                    <span class="grow">Notifications</span>
                </a>

                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Marketing</p>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-promotions"
                        {{ request()->routeIs('promotions.*') ? 'checked' : '' }} />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--ticket size-4"></span>
                        <span class="grow">Promotions</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item {{ request()->routeIs('promotions.coupons') ? 'active' : '' }}"
                                href="{{ route('promotions.coupons') }}">
                                <span class="grow">Coupons</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('promotions.coupon-usage') ? 'active' : '' }}"
                                href="{{ route('promotions.coupon-usage') }}">
                                <span class="grow">Coupon Usage</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('promotions.campaigns') ? 'active' : '' }}"
                                href="{{ route('promotions.campaigns') }}">
                                <span class="grow">Campaigns</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('promotions.email-campaigns') ? 'active' : '' }}"
                                href="{{ route('promotions.email-campaigns') }}">
                                <span class="grow">Email Campaigns</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('promotions.email-templates') ? 'active' : '' }}"
                                href="{{ route('promotions.email-templates') }}">
                                <span class="grow">Email Templates</span>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Analytics</p>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-reports"
                        {{ request()->routeIs('reports.*') ? 'checked' : '' }} />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--file-text size-4"></span>
                        <span class="grow">Reports</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item {{ request()->routeIs('reports.sales') ? 'active' : '' }}"
                                href="{{ route('reports.sales') }}">
                                <span class="grow">Sales Report</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('reports.product-performance') ? 'active' : '' }}"
                                href="{{ route('reports.product-performance') }}">
                                <span class="grow">Product Performance</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('reports.customer') ? 'active' : '' }}"
                                href="{{ route('reports.customer') }}">
                                <span class="grow">Customer Report</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('reports.payment') ? 'active' : '' }}"
                                href="{{ route('reports.payment') }}">
                                <span class="grow">Payment Report</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('reports.inventory') ? 'active' : '' }}"
                                href="{{ route('reports.inventory') }}">
                                <span class="grow">Inventory Report</span>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Access Control</p>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-access-control"
                        {{ request()->routeIs('user.*') || request()->routeIs('role.*') || request()->routeIs('permission.*') || request()->routeIs('activity-log.*') || request()->routeIs('session.*') ? 'checked' : '' }} />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--shield size-4"></span>
                        <span class="grow">User Management</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item {{ request()->routeIs('user.*') ? 'active' : '' }}"
                                href="{{ route('user.index') }}">
                                <span class="grow">Users</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('role.*') ? 'active' : '' }}"
                                href="{{ route('role.index') }}">
                                <span class="grow">Roles</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('permission.*') ? 'active' : '' }}"
                                href="{{ route('permission.index') }}">
                                <span class="grow">Permissions</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('activity-log.*') ? 'active' : '' }}"
                                href="{{ route('activity-log.index') }}">
                                <span class="grow">Activity Logs</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('session.*') ? 'active' : '' }}"
                                href="{{ route('session.index') }}">
                                <span class="grow">Sessions</span>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="menu-label px-2.5 pt-3 pb-1.5 first:pt-0">Settings</p>
                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-settings-general"
                        {{ request()->routeIs('settings.store-info') || request()->routeIs('settings.email-settings') || request()->routeIs('settings.seo-meta') || request()->routeIs('settings.system-config') ? 'checked' : '' }} />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--settings size-4"></span>
                        <span class="grow">General</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item {{ request()->routeIs('settings.general.store') ? 'active' : '' }}"
                                href="{{ route('settings.general.store') }}">
                                <span class="grow">Store Info</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('settings.general.email') ? 'active' : '' }}"
                                href="{{ route('settings.general.email') }}">
                                <span class="grow">Email Settings</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('settings.general.seo') ? 'active' : '' }}"
                                href="{{ route('settings.general.seo') }}">
                                <span class="grow">SEO & Meta</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('settings.general.system') ? 'active' : '' }}"
                                href="{{ route('settings.general.system') }}">
                                <span class="grow">System Config</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-settings-payment"
                        {{ request()->routeIs('settings.payment.*') ? 'checked' : '' }} />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--wallet size-4"></span>
                        <span class="grow">Payment</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item {{ request()->routeIs('settings.payment.methods') ? 'active' : '' }}"
                                href="{{ route('settings.payment.methods') }}">
                                <span class="grow">Payment Methods</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('settings.payment.midtrans-config') ? 'active' : '' }}"
                                href="{{ route('settings.payment.midtrans-config') }}">
                                <span class="grow">Midtrans Config</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('settings.payment.tax-settings') ? 'active' : '' }}"
                                href="{{ route('settings.payment.tax-settings') }}">
                                <span class="grow">Tax Settings</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="group collapse">
                    <input
                        aria-label="Sidemenu item trigger"
                        type="checkbox"
                        class="peer"
                        name="sidebar-menu-settings-shipping"
                        {{ request()->routeIs('settings.shipping.*') ? 'checked' : '' }} />
                    <div class="collapse-title px-2.5 py-1.5">
                        <span class="iconify lucide--truck size-4"></span>
                        <span class="grow">Shipping</span>
                        <span
                            class="iconify lucide--chevron-right arrow-icon size-3.5"></span>
                    </div>
                    <div class="collapse-content ms-6.5 !p-0">
                        <div class="mt-0.5 space-y-0.5">
                            <a
                                class="menu-item {{ request()->routeIs('settings.shipping.methods') ? 'active' : '' }}"
                                href="{{ route('settings.shipping.methods') }}">
                                <span class="grow">Shipping Methods</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('settings.shipping.rajaongkir-config') ? 'active' : '' }}"
                                href="{{ route('settings.shipping.rajaongkir-config') }}">
                                <span class="grow">RajaOngkir Config</span>
                            </a>
                            <a
                                class="menu-item {{ request()->routeIs('settings.shipping.origin-address') ? 'active' : '' }}"
                                href="{{ route('settings.shipping.origin-address') }}">
                                <span class="grow">Origin Address</span>
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
