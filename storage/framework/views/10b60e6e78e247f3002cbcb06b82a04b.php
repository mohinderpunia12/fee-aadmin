<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FeeAdmin - Multi-School Fee, Salary & Attendance Management</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        :root {
            --primary-blue: #2D32B8;
            --primary-green: #00C29E;
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">
    <?php
        $settings = \App\Models\SystemSetting::query()->first();
        $trialDays = (int) ($settings?->trial_days ?? 7);
        $supportEmail = $settings?->support_email;
        $supportPhone = $settings?->support_phone;
        $tutorialUrl = $settings?->tutorial_video_url;
    ?>

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-gray-900 focus:px-4 focus:py-2 focus:text-white">
        Skip to content
    </a>

    <div class="min-h-screen">
        <!-- Top bar -->
        <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/80 backdrop-blur">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Primary">
                <div class="flex items-center gap-3">
                    <img src="<?php echo e(asset('logo.png')); ?>" alt="FeeAdmin Logo" class="h-10 w-auto">
                    <div>
                        <p class="text-sm font-semibold leading-5" style="color: var(--primary-blue);">FeeAdmin</p>
                        <p class="text-xs text-gray-600">Multi-school management SaaS</p>
                    </div>
                </div>

                <div class="hidden items-center gap-8 md:flex">
                    <a href="#features" class="text-sm text-gray-700 hover:text-gray-900">Features</a>
                    <a href="#roles" class="text-sm text-gray-700 hover:text-gray-900">Dashboards</a>
                    <a href="#pricing" class="text-sm text-gray-700 hover:text-gray-900">Trial & Pricing</a>
                    <a href="#faq" class="text-sm text-gray-700 hover:text-gray-900">FAQ</a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="/app/login" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Login
                    </a>
                    <a href="/register-school" class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-lg hover:opacity-90" style="background-color: var(--primary-blue);">
                        Start Free Trial
                    </a>
                </div>
            </nav>
        </header>

        <main id="main">
            <!-- Hero -->
            <section class="relative overflow-hidden bg-gradient-to-b from-white to-gray-50">
                <div class="pointer-events-none absolute inset-0 opacity-40">
                    <div class="absolute -left-24 top-16 h-80 w-80 rounded-full blur-3xl" style="background-color: var(--primary-blue); opacity: 0.2;"></div>
                    <div class="absolute -right-24 top-40 h-80 w-80 rounded-full blur-3xl" style="background-color: var(--primary-green); opacity: 0.2;"></div>
                </div>

                <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-24">
                    <div class="grid items-center gap-12 lg:grid-cols-2">
                        <div>
                            <p class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-700">
                                <span class="h-2 w-2 rounded-full" style="background-color: var(--primary-green);"></span>
                                Single-database multi-school platform
                            </p>

                            <h1 class="mt-5 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                                Fees, salaries, attendance, and role-based dashboards—
                                <span style="color: var(--primary-blue);">all in one place</span>.
                            </h1>

                            <p class="mt-5 max-w-xl text-base leading-7 text-gray-600 sm:text-lg">
                                FeeAdmin helps schools run operations smoothly: collect fees, generate receipts, manage staff salaries & slips,
                                track attendance, and give each role a focused dashboard.
                            </p>

                            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                                <a href="/register-school" class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-semibold text-white shadow-lg hover:opacity-90" style="background-color: var(--primary-blue);">
                                    Start <?php echo e($trialDays); ?>-day free trial
                                </a>
                                <a href="/app/login" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                    Go to login
                                </a>
                            </div>

                            <dl class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                    <dt class="text-xs text-gray-600">Setup time</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">Minutes</dd>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                    <dt class="text-xs text-gray-600">Multi-school</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">Built-in</dd>
                                </div>
                                <div class="col-span-2 rounded-xl border border-gray-200 bg-white p-4 sm:col-span-1">
                                    <dt class="text-xs text-gray-600">Receipts & slips</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">PDF download</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="relative">
                            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900">What you get</p>
                                    <span class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs text-gray-700">Secure & tenant-aware</span>
                                </div>

                                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="rounded-lg p-2" style="background-color: rgba(45, 50, 184, 0.1);">
                                                <svg class="h-5 w-5" style="color: var(--primary-blue);" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M7 7h10M7 11h10M7 15h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    <path d="M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Fees & receipts</p>
                                                <p class="mt-1 text-sm text-gray-600">Structures, payments, and PDF receipts.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="rounded-lg p-2" style="background-color: rgba(0, 194, 158, 0.1);">
                                                <svg class="h-5 w-5" style="color: var(--primary-green);" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 7h16M7 7V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    <path d="M6 7v14h12V7" stroke="currentColor" stroke-width="1.8"/>
                                                    <path d="M9 11h6M9 15h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Salaries & slips</p>
                                                <p class="mt-1 text-sm text-gray-600">Breakdowns, advances, and PDF slips.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="rounded-lg p-2" style="background-color: rgba(45, 50, 184, 0.1);">
                                                <svg class="h-5 w-5" style="color: var(--primary-blue);" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M8 3v3M16 3v3M4 8h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    <path d="M6 5h12a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8"/>
                                                    <path d="M8 12l2 2 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Attendance</p>
                                                <p class="mt-1 text-sm text-gray-600">Staff & student attendance records.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="rounded-lg p-2" style="background-color: rgba(0, 194, 158, 0.1);">
                                                <svg class="h-5 w-5" style="color: var(--primary-green);" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="1.8"/>
                                                    <path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Role dashboards</p>
                                                <p class="mt-1 text-sm text-gray-600">Admin, staff, and student portals.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-sm font-semibold text-gray-900">Designed for real workflows</p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Tenant-aware data, simple navigation, and export-ready documents.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section id="features" class="border-t border-gray-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Everything your school needs</h2>
                        <p class="mt-3 text-gray-600">
                            Manage daily operations, generate printable PDFs, and keep everyone informed with dashboards tailored to their role.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <p class="text-sm font-semibold text-gray-900">Monthly fee collections</p>
                            <p class="mt-2 text-sm text-gray-600">Track payments, filter by student/class, and download receipts.</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <p class="text-sm font-semibold text-gray-900">Salary management</p>
                            <p class="mt-2 text-sm text-gray-600">Structures, payments, advances, and salary slip PDFs.</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <p class="text-sm font-semibold text-gray-900">Bulk attendance</p>
                            <p class="mt-2 text-sm text-gray-600">Record attendance and review history with filters.</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <p class="text-sm font-semibold text-gray-900">Classrooms</p>
                            <p class="mt-2 text-sm text-gray-600">Create classrooms, assign teachers, and link students.</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <p class="text-sm font-semibold text-gray-900">ID cards</p>
                            <p class="mt-2 text-sm text-gray-600">Generate student & staff ID cards as PDFs.</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <p class="text-sm font-semibold text-gray-900">Multi-school SaaS</p>
                            <p class="mt-2 text-sm text-gray-600">Single database, tenant-aware panels and data isolation.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Roles -->
            <section id="roles" class="border-t border-gray-200 bg-gray-50">
                <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Dashboards for every role</h2>
                        <p class="mt-3 text-gray-600">Each dashboard is focused, simple, and built around what that role needs daily.</p>
                    </div>

                    <div class="mt-10 grid gap-6 lg:grid-cols-3">
                        <div class="rounded-2xl border border-gray-200 bg-white p-6">
                            <p class="text-sm font-semibold text-gray-900">Superuser</p>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li>Manage schools and subscriptions</li>
                                <li>System settings (pricing, trial days, support)</li>
                                <li>Global visibility & control</li>
                            </ul>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-6">
                            <p class="text-sm font-semibold text-gray-900">School Admin</p>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li>Students, staff, classrooms</li>
                                <li>Fees, salaries, attendance</li>
                                <li>Metrics dashboard & exports</li>
                            </ul>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-6">
                            <p class="text-sm font-semibold text-gray-900">Staff & Student</p>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li>Staff: salary slips & attendance history</li>
                                <li>Student: fee dues, receipts, class info</li>
                                <li>Download PDFs anytime</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing / Trial -->
            <section id="pricing" class="border-t border-gray-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Start with a free trial</h2>
                            <p class="mt-3 text-gray-600">
                                Get full access for <span class="font-semibold text-gray-900"><?php echo e($trialDays); ?> days</span>. No credit card required.
                            </p>
                            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                <a href="/register-school" class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-semibold text-white shadow-lg hover:opacity-90" style="background-color: var(--primary-blue);">
                                    Create your school
                                </a>
                                <a href="/app/login" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                    Login to existing school
                                </a>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <p class="text-sm font-semibold text-gray-900">Included in trial</p>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700">Fee receipts (PDF)</div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700">Salary slips (PDF)</div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700">Student & staff ID cards</div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-700">Attendance tracking</div>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tutorialUrl): ?>
                                <div class="mt-6 rounded-xl border border-gray-200 bg-white p-4">
                                    <p class="text-sm font-semibold text-gray-900">Tutorial</p>
                                    <a href="<?php echo e($tutorialUrl); ?>" class="mt-2 inline-flex items-center text-sm font-semibold hover:opacity-80" style="color: var(--primary-blue);">
                                        Watch setup video
                                        <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"/>
                                        </svg>
                                    </a>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section id="faq" class="border-t border-gray-200 bg-gray-50">
                <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">FAQ</h2>
                        <p class="mt-3 text-gray-600">Quick answers to common questions.</p>
                    </div>

                    <div class="mt-10 grid gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl border border-gray-200 bg-white p-6">
                            <p class="text-sm font-semibold text-gray-900">Is this multi-school?</p>
                            <p class="mt-2 text-sm text-gray-600">Yes. Each school runs as a tenant with data isolation.</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-6">
                            <p class="text-sm font-semibold text-gray-900">Can we download receipts and slips?</p>
                            <p class="mt-2 text-sm text-gray-600">Yes. Fee receipts, salary slips, and ID cards are available as PDFs.</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-6">
                            <p class="text-sm font-semibold text-gray-900">Do staff and students get dashboards?</p>
                            <p class="mt-2 text-sm text-gray-600">Yes. Each role has a dedicated panel with the right data only.</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-6">
                            <p class="text-sm font-semibold text-gray-900">What about support?</p>
                            <p class="mt-2 text-sm text-gray-600">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supportEmail): ?>
                                    Email: <span class="font-semibold text-gray-900"><?php echo e($supportEmail); ?></span>
                                <?php else: ?>
                                    Support contact can be configured by the superuser.
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($supportPhone): ?>
                                    <span class="ml-2">Phone: <span class="font-semibold text-gray-900"><?php echo e($supportPhone); ?></span></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold" style="color: var(--primary-blue);">FeeAdmin</p>
                        <p class="mt-1 text-sm text-gray-600">Multi-school fee, salary & attendance management.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="/admin/login" class="text-sm text-gray-600 hover:text-gray-900">Superuser Login</a>
                        <a href="/app/login" class="text-sm text-gray-600 hover:text-gray-900">School Login</a>
                        <a href="/register-school" class="text-sm font-semibold hover:opacity-80" style="color: var(--primary-blue);">Create School</a>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-600">
                    <p>&copy; <?php echo e(date('Y')); ?> FeeAdmin. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
<?php /**PATH /Users/wiredtechie/Desktop/fee-admin/resources/views/landing.blade.php ENDPATH**/ ?>