<!--  Start: Layout - Topbar -->

<div
    role="navigation"
    aria-label="Navbar"
    class="flex items-center justify-between px-3"
    id="layout-topbar">
    <div class="inline-flex items-center gap-3">
        <label
            class="btn btn-square btn-ghost btn-sm group-has-[[id=layout-sidebar-hover-trigger]:checked]/html:hidden"
            aria-label="Leftmenu toggle"
            for="layout-sidebar-toggle-trigger">
            <span class="iconify lucide--menu size-5"></span>
        </label>
        <label
            class="btn btn-square btn-ghost btn-sm hidden group-has-[[id=layout-sidebar-hover-trigger]:checked]/html:flex"
            aria-label="Leftmenu toggle"
            for="layout-sidebar-hover-trigger">
            <span class="iconify lucide--menu size-5"></span>
        </label>
        <button
            class="btn btn-outline btn-sm btn-ghost border-base-300 text-base-content/70 hidden h-9 w-48 justify-start gap-2 !text-sm md:flex"
            onclick="document.getElementById('topbar-search-modal')?.showModal()">
            <span class="iconify lucide--search size-4"></span>
            <span>Search</span>
        </button>
        <button
            class="btn btn-outline btn-sm btn-square btn-ghost border-base-300 text-base-content/70 flex size-9 md:hidden"
            aria-label="Search"
            onclick="document.getElementById('topbar-search-modal')?.showModal()">
            <span class="iconify lucide--search size-4"></span>
        </button>
        <dialog id="topbar-search-modal" class="modal p-0">
            <div class="modal-box bg-transparent p-0 shadow-none">
                <div class="bg-base-100 rounded-box">
                    <div class="input w-full border-0 !outline-none">
                        <span
                            class="iconify lucide--search text-base-content/60 size-4.5"></span>
                        <input
                            type="search"
                            class="grow"
                            placeholder="Search"
                            aria-label="Search" />
                        <form method="dialog">
                            <button
                                class="btn btn-xs btn-circle btn-ghost"
                                aria-label="Close">
                                <span
                                    class="iconify lucide--x text-base-content/80 size-4"></span>
                            </button>
                        </form>
                    </div>
                    <div
                        class="border-base-300 flex items-center gap-3 border-t px-2 py-2">
                        <div class="flex items-center gap-0.5">
                            <div
                                class="border-base-300 bg-base-200 flex size-5 items-center justify-center rounded-sm border shadow-xs">
                                <span
                                    class="iconify lucide--arrow-up size-3.5"></span>
                            </div>
                            <div
                                class="border-base-300 bg-base-200 flex size-5 items-center justify-center rounded-sm border shadow-xs">
                                <span
                                    class="iconify lucide--arrow-down size-3.5"></span>
                            </div>
                            <p class="text-base-content/80 ms-1 text-sm">
                                Navigate
                            </p>
                        </div>
                        <div class="flex items-center gap-0.5 max-sm:hidden">
                            <div
                                class="border-base-300 bg-base-200 flex size-5 items-center justify-center rounded-sm border shadow-xs">
                                <span
                                    class="iconify lucide--undo-2 size-3.5"></span>
                            </div>
                            <p class="text-base-content/80 ms-1 text-sm">
                                Return
                            </p>
                        </div>
                        <div class="flex items-center gap-0.5">
                            <div
                                class="border-base-300 bg-base-200 flex size-5 items-center justify-center rounded-sm border shadow-xs">
                                <span
                                    class="iconify lucide--corner-down-left size-3.5"></span>
                            </div>
                            <p class="text-base-content/80 ms-1 text-sm">
                                Open
                            </p>
                        </div>
                        <div class="ms-auto flex items-center gap-0.5">
                            <div
                                class="border-base-300 bg-base-200 flex h-5 items-center justify-center rounded-sm border px-1 text-sm/none shadow-xs">
                                esc
                            </div>
                            <p class="text-base-content/80 ms-1 text-sm">
                                Close
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-base-100 rounded-box mt-4">
                    <div class="px-5 py-3">
                        <p class="text-base-content/80 text-sm font-medium">
                            I'm looking for...
                        </p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <div
                                class="border-base-300 hover:bg-base-200 rounded-box cursor-pointer border px-2.5 py-1 text-sm/none">
                                Writer
                            </div>
                            <div
                                class="border-base-300 hover:bg-base-200 rounded-box cursor-pointer border px-2.5 py-1 text-sm/none">
                                Editor
                            </div>
                            <div
                                class="border-base-300 hover:bg-base-200 rounded-box cursor-pointer border px-2.5 py-1 text-sm/none">
                                Explainer
                            </div>
                            <div
                                class="border-base-300 hover:bg-base-200 rounded-box flex cursor-pointer items-center gap-1 border border-dashed px-2.5 py-1 text-sm/none">
                                <span
                                    class="iconify lucide--plus size-3.5"></span>
                                Action
                            </div>
                        </div>
                    </div>
                    <hr class="border-base-300 h-px border-dashed" />

                    <ul class="menu w-full pt-1">
                        <li class="menu-title">Talk to assistant</li>
                        <li>
                            <div class="group">
                                <div
                                    class="from-primary to-primary/80 mask mask-squircle text-primary-content flex size-5 items-center justify-center bg-linear-to-b leading-none font-medium">
                                    R
                                </div>
                                <p class="grow text-sm">Research Buddy</p>
                                <div
                                    class="flex translate-x-2 items-center gap-2.5 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">
                                    <span
                                        class="iconify lucide--star text-orange-500"></span>
                                    <div class="flex items-center gap-0.5">
                                        <div
                                            class="border-base-300 flex size-5 items-center justify-center rounded-sm border shadow-xs">
                                            <span
                                                class="iconify lucide--corner-down-left size-3.5"></span>
                                        </div>
                                        <p class="ms-1 text-sm opacity-80">
                                            Select
                                        </p>
                                    </div>
                                    <span
                                        class="iconify lucide--ellipsis-vertical opacity-80"></span>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="group">
                                <div
                                    class="from-secondary to-secondary/80 mask mask-squircle text-secondary-content flex size-5 items-center justify-center bg-linear-to-b leading-none font-medium">
                                    T
                                </div>
                                <p class="grow text-sm">Task Planner</p>
                                <div
                                    class="flex translate-x-2 items-center gap-2.5 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">
                                    <span
                                        class="iconify lucide--star text-orange-500"></span>
                                    <div class="flex items-center gap-0.5">
                                        <div
                                            class="border-base-300 flex size-5 items-center justify-center rounded-sm border shadow-xs">
                                            <span
                                                class="iconify lucide--corner-down-left size-3.5"></span>
                                        </div>
                                        <p class="ms-1 text-sm opacity-80">
                                            Select
                                        </p>
                                    </div>
                                    <span
                                        class="iconify lucide--ellipsis-vertical opacity-80"></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="group">
                                <div
                                    class="from-success to-success/80 mask mask-squircle text-success-content flex size-5 items-center justify-center bg-linear-to-b leading-none font-medium">
                                    S
                                </div>
                                <p class="grow text-sm">Sparking Ideas</p>
                                <div
                                    class="flex translate-x-2 items-center gap-2.5 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">
                                    <span
                                        class="iconify lucide--star text-orange-500"></span>
                                    <div class="flex items-center gap-0.5">
                                        <div
                                            class="border-base-300 flex size-5 items-center justify-center rounded-sm border shadow-xs">
                                            <span
                                                class="iconify lucide--corner-down-left size-3.5"></span>
                                        </div>
                                        <p class="ms-1 text-sm opacity-80">
                                            Select
                                        </p>
                                    </div>
                                    <span
                                        class="iconify lucide--ellipsis-vertical opacity-80"></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="group">
                                <div
                                    class="from-warning to-warning/80 mask mask-squircle text-warning-content flex size-5 items-center justify-center bg-linear-to-b leading-none font-medium">
                                    D
                                </div>
                                <p class="grow text-sm">Docs Assistant</p>
                                <div
                                    class="flex translate-x-2 items-center gap-2.5 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">
                                    <span
                                        class="iconify lucide--star text-orange-500"></span>
                                    <div class="flex items-center gap-0.5">
                                        <div
                                            class="border-base-300 flex size-5 items-center justify-center rounded-sm border shadow-xs">
                                            <span
                                                class="iconify lucide--corner-down-left size-3.5"></span>
                                        </div>
                                        <p class="ms-1 text-sm opacity-80">
                                            Select
                                        </p>
                                    </div>
                                    <span
                                        class="iconify lucide--ellipsis-vertical opacity-80"></span>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <hr class="border-base-300 h-px border-dashed" />

                    <ul class="menu w-full pt-1">
                        <li
                            class="menu-title flex flex-row items-center justify-between gap-2">
                            <span>Tasks Manager</span>
                            <span>Progress</span>
                        </li>
                        <li>
                            <div>
                                <span
                                    class="iconify lucide--notebook size-4"></span>
                                <p class="grow text-sm">Creating an essay</p>
                                <progress
                                    class="progress progress-primary h-1 w-30"
                                    value="60"
                                    max="100"></progress>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span
                                    class="iconify lucide--message-circle size-4"></span>
                                <p class="grow text-sm">Summarizing chat</p>
                                <progress
                                    class="progress progress-secondary h-1 w-30"
                                    value="80"
                                    max="100"></progress>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span
                                    class="iconify lucide--code size-4"></span>
                                <p class="grow text-sm">Fixing syntax</p>
                                <progress
                                    class="progress progress-accent h-1 w-30"
                                    value="35"
                                    max="100"></progress>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span
                                    class="iconify lucide--book-open size-4"></span>
                                <p class="grow text-sm">Reading docs</p>
                                <progress
                                    class="progress progress-info h-1 w-30"
                                    value="90"
                                    max="100"></progress>
                            </div>
                        </li>
                        <li>
                            <div>
                                <span
                                    class="iconify lucide--lightbulb size-4"></span>
                                <p class="grow text-sm">Generating ideas</p>
                                <progress
                                    class="progress progress-warning h-1 w-30"
                                    value="50"
                                    max="100"></progress>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
    <div class="inline-flex items-center gap-0.5">

        <button
            aria-label="Toggle Theme"
            class="btn btn-sm btn-circle btn-ghost relative overflow-hidden"
            data-theme-control="toggle">
            <span
                class="iconify lucide--sun absolute size-4.5 -translate-y-4 opacity-0 transition-all duration-300 group-data-[theme=light]/html:translate-y-0 group-data-[theme=light]/html:opacity-100"></span>
            <span
                class="iconify lucide--moon absolute size-4.5 translate-y-4 opacity-0 transition-all duration-300 group-data-[theme=dark]/html:translate-y-0 group-data-[theme=dark]/html:opacity-100"></span>
            <span
                class="iconify lucide--palette absolute size-4.5 opacity-100 group-data-[theme=dark]/html:opacity-0 group-data-[theme=light]/html:opacity-0"></span>
        </button>

        <label
            for="layout-rightbar-drawer"
            class="btn btn-circle btn-ghost btn-sm drawer-button">
            <span class="iconify lucide--settings-2 size-4.5"></span>
        </label>

        <!-- Order Notifications -->
        <div class="dropdown dropdown-bottom sm:dropdown-end dropdown-center">
            <div
                tabindex="0"
                role="button"
                class="btn btn-circle btn-ghost btn-sm relative"
                aria-label="Order Notifications"
                id="order-notification-btn">
                <span class="iconify lucide--shopping-cart size-4.5"></span>
                <div
                    id="order-notification-badge"
                    class="status status-error status-sm absolute end-1 top-1 hidden"></div>
            </div>
            <div
                tabindex="0"
                class="dropdown-content bg-base-100 rounded-box mt-1 w-84 shadow-md duration-1000 hover:shadow-lg">
                <div class="bg-base-200/30 rounded-t-box border-base-200 border-b ps-4 pe-2 pt-3">
                    <div class="flex items-center justify-between">
                        <p class="font-medium">Pending Orders</p>
                        <button
                            class="btn btn-xs btn-circle btn-ghost"
                            aria-label="Close"
                            onclick="document.activeElement.blur()">
                            <span class="iconify lucide--x size-4"></span>
                        </button>
                    </div>
                </div>
                <div id="order-notification-content" class="max-h-96 overflow-y-auto">
                    <div class="flex items-center justify-center p-8">
                        <div class="loading loading-spinner loading-sm"></div>
                    </div>
                </div>
                <hr class="border-base-200" />
                <div class="flex items-center justify-center px-2 py-2">
                    <a href="{{ route('orders.pending-orders.index') }}" class="btn btn-sm btn-soft btn-primary">
                        View All Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="dropdown dropdown-bottom sm:dropdown-end dropdown-center">
            <div
                tabindex="0"
                role="button"
                class="btn btn-circle btn-ghost btn-sm relative"
                aria-label="Notifications">
                <span
                    class="iconify lucide--bell motion-preset-seesaw size-4.5"></span>
                <div
                    class="status status-error status-sm absolute end-1 top-1"></div>
            </div>
            <div
                tabindex="0"
                class="dropdown-content bg-base-100 rounded-box mt-1 w-84 shadow-md duration-1000 hover:shadow-lg">
                <div
                    class="bg-base-200/30 rounded-t-box border-base-200 border-b ps-4 pe-2 pt-3">
                    <div class="flex items-center justify-between">
                        <p class="font-medium">Notification</p>
                        <button
                            class="btn btn-xs btn-circle btn-ghost"
                            aria-label="Close"
                            onclick="document.activeElement.blur()">
                            <span class="iconify lucide--x size-4"></span>
                        </button>
                    </div>
                    <div
                        class="-ms-2 mt-2 -mb-px flex items-center justify-between">
                        <div role="tablist" class="tabs tabs-sm tabs-border">
                            <div
                                role="tab"
                                class="tab tab-active gap-2 px-3 font-medium">
                                <span>All</span>
                                <div class="badge badge-sm">4</div>
                            </div>
                            <div role="tab" class="tab gap-2 px-3">
                                <span>Team</span>
                            </div>
                            <div role="tab" class="tab gap-2 px-3">
                                <span>AI</span>
                            </div>
                            <div role="tab" class="tab gap-2 px-3">
                                <span>@mention</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="hover:bg-base-200/20 relative flex items-start gap-3 p-4 transition-all">
                    <div class="avatar avatar-online size-12">
                        <img
                            src="{{ asset('assets/images/avatars/2.png') }}"
                            class="from-primary/80 to-primary/60 mask mask-squircle bg-linear-to-b px-1 pt-1"
                            alt="" />
                    </div>
                    <div class="grow">
                        <p class="text-sm leading-tight">
                            Lena submitted a draft for review.
                        </p>
                        <p class="text-base-content/60 text-xs">15 min ago</p>
                        <div class="mt-2 flex items-center gap-2">
                            <button class="btn btn-sm btn-primary">
                                Approve
                            </button>
                            <button
                                class="btn btn-sm btn-outline border-base-300">
                                Decline
                            </button>
                        </div>
                    </div>
                    <div
                        class="status status-primary absolute end-4 top-4 size-1.5"></div>
                </div>
                <hr class="border-base-300 border-dashed" />
                <div
                    class="hover:bg-base-200/20 flex items-start gap-3 p-4 transition-all">
                    <div class="avatar avatar-offline size-12">
                        <img
                            src="{{ asset('assets/images/avatars/4.png') }}"
                            class="from-secondary/80 to-secondary/60 mask mask-squircle bg-linear-to-b px-1 pt-1"
                            alt="" />
                    </div>
                    <div class="grow">
                        <p class="text-sm leading-tight">
                            Kai mentioned you in a project.
                        </p>
                        <p class="text-base-content/60 text-xs">22 min ago</p>
                        <div
                            class="from-base-200 via-base-200/80 rounded-box mt-2 flex items-center justify-between gap-2 bg-linear-to-r to-transparent py-1 ps-2.5">
                            <p class="text-sm">Check model inputs?</p>
                            <button class="btn btn-xs btn-ghost text-xs">
                                <span
                                    class="iconify lucide--reply size-3.5"></span>
                                Reply
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="border-base-300 border-dashed" />
                <div
                    class="hover:bg-base-200/20 flex items-start gap-3 p-4 transition-all">
                    <div class="avatar size-12">
                        <img
                            src="{{ asset('assets/images/avatars/5.png') }}"
                            class="mask mask-squircle bg-linear-to-b from-orange-500/80 to-orange-500/60 px-1 pt-1"
                            alt="" />
                    </div>
                    <div class="grow">
                        <p class="text-sm leading-tight">
                            Your latest results are ready
                        </p>
                        <div
                            class="border-base-200 rounded-box mt-2 flex items-center justify-between gap-2 border px-2.5 py-1.5">
                            <p class="text-sm">
                                Forecast Report
                                <span class="text-base-content/60 text-xs">
                                    (12 MB)
                                </span>
                            </p>
                            <button
                                class="btn btn-xs btn-square btn-ghost text-xs">
                                <span
                                    class="iconify lucide--arrow-down-to-line size-4"></span>
                            </button>
                        </div>
                        <div
                            class="border-base-200 rounded-box mt-2 flex items-center justify-between gap-2 border px-2.5 py-1.5">
                            <p class="text-sm">
                                Generated Summary
                                <span class="text-base-content/60 text-xs">
                                    (354 KB)
                                </span>
                            </p>
                            <button
                                class="btn btn-xs btn-square btn-ghost text-xs">
                                <span
                                    class="iconify lucide--arrow-down-to-line size-4"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="border-base-200" />
                <div class="flex items-center justify-between px-2 py-2">
                    <button class="btn btn-sm btn-soft btn-primary">
                        View All
                    </button>
                    <div class="flex items-center gap-1">
                        <button class="btn btn-sm btn-square btn-ghost">
                            <span
                                class="iconify lucide--check-check size-4"></span>
                        </button>
                        <button class="btn btn-sm btn-square btn-ghost">
                            <span
                                class="iconify lucide--bell-ring size-4"></span>
                        </button>
                        <button class="btn btn-sm btn-square btn-ghost">
                            <span
                                class="iconify lucide--settings size-4"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="dropdown dropdown-bottom dropdown-end">
            <div
                tabindex="0"
                role="button"
                class="btn btn-ghost flex items-center gap-2 px-1.5">
                <div class="avatar">
                    <div class="bg-base-200 mask mask-squircle w-8">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ currentUser('email', 'default') }}" alt="Avatar" />
                    </div>
                </div>
                <div class="text-start max-lg:hidden">
                    <p class="text-sm/none">{{ currentUser('name') }}</p>
                    <p class="text-base-content/50 mt-0.5 text-xs/none">
                        {{ userRole('Guest') }}
                    </p>
                </div>
            </div>
            <ul
                role="menu"
                tabindex="0"
                class="dropdown-content menu bg-base-100 rounded-box shadow-base-content/4 mt-1 w-48 p-1 shadow-[0px_10px_40px_0px]">
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
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('topbar-logout-form').submit();">
                        <span class="iconify lucide--log-out size-4"></span>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>

        <form id="topbar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>

<!--  End: Layout - Topbar -->
