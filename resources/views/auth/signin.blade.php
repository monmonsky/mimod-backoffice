<!doctype html>
<html lang="en" class="group/html">
    <head>
        <title>Nexus (HTML - DaisyUI)</title>

        @include('partials.head')
    </head>

    <body>
        <!--  Start: Layout - Main -->

        <div class="grid grid-cols-12 overflow-auto sm:h-screen">
            <div
                class="relative hidden bg-[#FFE9D1] lg:col-span-7 lg:block xl:col-span-8 2xl:col-span-9 dark:bg-[#14181c]">
                <div class="absolute inset-0 flex items-center justify-center">
                    <img class="object-cover" alt="Auth Image" src="{{ asset('assets/images/auth/auth-hero.png') }}" />
                </div>
                <div class="animate-bounce-2 absolute right-[20%] bottom-[15%]">
                    <div class="card bg-base-100/80 w-64 backdrop-blur-lg">
                        <div class="card-body p-5">
                            <div class="flex flex-col items-center justify-center">
                                <div class="mask mask-squircle overflow-hidden">
                                    <img
                                        class="bg-base-200 size-14"
                                        alt=""
                                        src="{{ asset('assets/images/landing/testimonial-avatar-1.jpg') }}" />
                                </div>
                                <div class="mt-3 flex items-center justify-center gap-0.5">
                                    <span
                                        class="iconify lucide--star size-4 text-orange-600"></span>
                                    <span
                                        class="iconify lucide--star size-4 text-orange-600"></span>
                                    <span
                                        class="iconify lucide--star size-4 text-orange-600"></span>
                                    <span
                                        class="iconify lucide--star size-4 text-orange-600"></span>
                                    <span
                                        class="iconify lucide--star size-4 text-orange-600"></span>
                                </div>
                                <p class="mt-1 text-lg font-medium">Pouya Saadeghi</p>
                                <p class="text-base-content/60 text-sm">Creator of daisyUI</p>
                            </div>
                            <p class="mt-2 text-center text-sm">
                                This is the ultimate admin dashboard for any React project
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 lg:col-span-5 xl:col-span-4 2xl:col-span-3">
                <div class="flex flex-col items-stretch p-6 md:p-8 lg:p-16">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('login') }}">
                            @include('partials.logo')
                        </a>

                        @include('partials.theme-toggle', ['class' => 'btn btn-circle btn-outline border-base-300 relative overflow-hidden'])
                    </div>
                    <h3 class="mt-8 text-center text-xl font-semibold md:mt-12 lg:mt-24">Login</h3>
                    <h3 class="text-base-content/70 mt-2 text-center text-sm">
                        Seamless Access, Secure Connection: Your Gateway to a Personalized
                        Experience.
                    </h3>
                    <div class="mt-6 md:mt-10">
                        <form id="submit-form">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Email Address</legend>
                                <label class="input w-full focus:outline-0">
                                    <span
                                        class="iconify lucide--mail text-base-content/80 size-5"></span>
                                    <input name="email"
                                        class="grow focus:outline-0"
                                        placeholder="Email Address"
                                        type="email" />
                                </label>
                            </fieldset>
                            <fieldset class="fieldset" x-data="{ show: false }">
                                <legend class="fieldset-legend">Password</legend>
                                <label class="input w-full focus:outline-0">
                                    <span
                                        class="iconify lucide--key-round text-base-content/80 size-5"></span>
                                    <input name="password"
                                        class="grow focus:outline-0"
                                        placeholder="Password"
                                        id="password"
                                        :type="show ? 'text' : 'password'" />
                                    <label
                                        class="swap btn btn-xs btn-ghost btn-circle text-base-content/60">
                                        <input
                                            type="checkbox"
                                            x-model="show"
                                            aria-label="Show password" />
                                        <span class="iconify lucide--eye swap-off size-4"></span>
                                        <span class="iconify lucide--eye-off swap-on size-4"></span>
                                    </label>
                                </label>
                            </fieldset>

                            <button type="submit" class="btn btn-primary btn-wide mt-4 max-w-full gap-3 md:mt-6"><span class="iconify lucide--log-in size-4"></span> Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--  End: Layout - Main -->
        @include('partials.footer-scripts')

        @vite(['resources/js/modules/auth/signin.js'])
    </body>
</html>
