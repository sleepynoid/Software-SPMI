<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    title: String,
});

const page = usePage();
const user = page.props.auth.user;
const sidebarVisible = ref(true);
const toast = useToast();

const logout = () => {
    router.post(route('logout'));
};

const menuItems = [
    { label: 'Dashboard', icon: 'pi pi-home', route: 'dashboard' },
    { 
        label: 'Master Data', 
        icon: 'pi pi-database', 
        roles: ['Admin/LPM'],
        items: [
            { label: 'Users', icon: 'pi pi-users', route: 'master.users.index' },
            { label: 'Unit Kerja', icon: 'pi pi-building', route: 'master.unit-kerja.index' },
            { label: 'Kategori Standar', icon: 'pi pi-tag', route: 'master.kategori-standar.index' },
        ]
    },
    { 
        label: 'Penetapan Standar', 
        icon: 'pi pi-pencil', 
        roles: ['Admin/LPM'],
        items: [
            { label: 'Periode AMI', icon: 'pi pi-calendar-plus', route: 'penetapan.periode.index' },
            { label: 'Standar Dikti', icon: 'pi pi-book', route: 'penetapan.standar.index' },
            { label: 'Indikator Mutu', icon: 'pi pi-list', route: 'penetapan.indikator.index' },
            { label: 'Distribusi Target', icon: 'pi pi-map', route: 'penetapan.distribusi-target.index' },
        ]
    },
    { 
        label: 'Pelaksanaan', 
        icon: 'pi pi-play', 
        roles: ['Auditee'],
        items: [
            { label: 'Evaluasi Diri', icon: 'pi pi-file-edit', route: 'pelaksanaan.evaluasi-diri.index' },
        ]
    },
    { 
        label: 'Evaluasi / AMI', 
        icon: 'pi pi-search', 
        roles: ['Auditor'],
        items: [
            { label: 'Jadwal Audit', icon: 'pi pi-calendar', route: 'evaluasi.jadwal-audit.index' },
        ]
    },
    { 
        label: 'Pengendalian / RTL', 
        icon: 'pi pi-check-circle', 
        roles: ['Auditee'],
        items: [
            { label: 'Isi RTL', icon: 'pi pi-file-edit', route: 'pengendalian.isi-rtl.index' },
        ]
    },
    { 
        label: 'Peningkatan / RTM', 
        icon: 'pi pi-chart-line', 
        roles: ['Pimpinan'],
        items: [
            { label: 'Risalah RTM', icon: 'pi pi-file-check', route: 'peningkatan.risalah.index' },
        ]
    },
];

const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value;
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <Head :title="title" />
        <Toast />

        <!-- Sidebar -->
        <aside 
            class="bg-white border-r border-slate-200 transition-all duration-300 flex flex-col"
            :class="sidebarVisible ? 'w-64' : 'w-20'"
        >
            <div class="h-16 flex items-center px-6 border-b border-slate-200 shrink-0">
                <span class="text-primary-600 font-bold text-xl" v-if="sidebarVisible">SPMI System</span>
                <span class="text-primary-600 font-bold text-xl" v-else>S</span>
            </div>

            <div class="flex-1 overflow-y-auto pt-4 flex flex-col gap-1 px-3">
                <template v-for="item in menuItems" :key="item.label">
                    <div v-if="!item.roles || item.roles.includes(user.role?.nama_role)">
                        <Link 
                            v-if="!item.items"
                            :href="route(item.route)"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                            :class="$page.url.startsWith('/' + item.route.split('.')[0]) ? 'bg-primary-50 text-shared-600' : 'text-slate-600 hover:bg-slate-100'"
                        >
                            <i :class="[item.icon, 'text-lg']"></i>
                            <span v-if="sidebarVisible">{{ item.label }}</span>
                        </Link>
                        
                        <div v-else class="mb-2">
                             <div v-if="sidebarVisible" class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                {{ item.label }}
                             </div>
                             <div v-for="sub in item.items" :key="sub.label">
                                <Link 
                                    :href="route(sub.route)"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                                    :class="route().current(sub.route) ? 'bg-primary-50 text-primary-600 font-medium' : 'text-slate-600 hover:bg-slate-100'"
                                >
                                    <i :class="[sub.icon, 'text-lg']"></i>
                                    <span v-if="sidebarVisible">{{ sub.label }}</span>
                                </Link>
                             </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-4 border-t border-slate-200 shrink-0">
                <button 
                    @click="logout"
                    class="flex items-center gap-3 px-3 py-2.5 w-full rounded-lg text-red-600 hover:bg-red-50 transition-colors"
                >
                    <i class="pi pi-power-off text-lg"></i>
                    <span v-if="sidebarVisible">Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <button @click="sidebarVisible = !sidebarVisible" class="p-2 hover:bg-slate-100 rounded-lg text-slate-500 transition-colors">
                        <i class="pi pi-bars text-xl"></i>
                    </button>
                    <h2 class="text-lg font-semibold text-slate-800">{{ title }}</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-800 leading-none mb-1">{{ user.nama_lengkap }}</p>
                        <p class="text-xs text-slate-500 leading-none">{{ user.role?.nama_role }} - {{ user.unit_kerja?.nama_unit || 'Institusi' }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">
                        {{ user.nama_lengkap.charAt(0) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-8">
                <slot />
            </div>
        </main>
    </div>
</template>
