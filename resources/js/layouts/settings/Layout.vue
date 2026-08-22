<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { route } from '@/lib/route';

const page = usePage();

const sidebarNavItems = [
    { title: 'Profile', href: '/settings/profile' },
    { title: 'Security', href: '/settings/security' },
    { title: 'Appearance', href: '/settings/appearance' },
];

const isActive = (href: string) => page.url.startsWith(href);
</script>

<template>
    <div class="px-4 py-6">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Settings</h1>
        <p class="text-slate-500 mb-6">Manage your profile and account settings</p>

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1" aria-label="Settings">
                    <Link
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        :href="item.href"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                        :class="isActive(item.href) ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50'"
                    >
                        {{ item.title }}
                    </Link>
                </nav>
            </aside>

            <div class="flex-1 md:max-w-2xl mt-6 lg:mt-0">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
