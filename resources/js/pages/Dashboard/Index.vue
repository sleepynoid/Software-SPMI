<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Select from 'primevue/select';
import { route } from '@/lib/route';

const props = defineProps({
    user: Object,
    periodes: Array,
    active_periode: Object,
    stats: Object,
});

const selectedPeriodeId = ref(props.active_periode?.id);

watch(selectedPeriodeId, (newId) => {
    router.get(route('dashboard'), { periode_id: newId }, { preserveState: true });
});

const isAdmin = computed(() => props.user?.role?.nama_role === 'Admin/LPM');
const isPimpinan = computed(() => props.user?.role?.nama_role === 'Pimpinan');
</script>

<template>
    <AppLayout title="Dashboard Overview">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="pi pi-chart-bar text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Ringkasan Capaian Mutu</h3>
                    <p class="text-sm text-slate-500">Melihat data statistik berdasarkan periode yang dipilih</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Tampilkan Periode:</span>
                <Select
                    v-model="selectedPeriodeId"
                    :options="periodes"
                    optionLabel="tahun_akademik"
                    optionValue="id"
                    placeholder="Pilih Periode"
                    class="w-64"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 hover:border-blue-200 transition-colors">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="pi pi-calendar text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Periode Aktif</p>
                    <p class="text-lg font-bold text-slate-800">{{ active_periode?.tahun_akademik || '-' }}</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                    <i class="pi pi-info-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Status Siklus</p>
                    <p class="text-lg font-bold text-slate-800">{{ active_periode?.status || '-' }}</p>
                </div>
            </div>

            <div v-if="isAdmin || isPimpinan" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="pi pi-book text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Total Standar</p>
                    <p class="text-lg font-bold text-slate-800">{{ stats?.total_standar }} Item</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                    <i class="pi pi-exclamation-triangle text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Temuan KTS/OB</p>
                    <p class="text-lg font-bold text-slate-800">{{ stats?.total_temuan }} Temuan</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <h3 class="text-xl font-bold text-slate-800 mb-6">Informasi Sesi Login</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl">
                        {{ user?.nama_lengkap?.charAt(0) }}
                    </div>
                    <div>
                        <p class="text-base font-bold text-slate-800 leading-tight">{{ user?.nama_lengkap }}</p>
                        <p class="text-sm text-slate-500">{{ user?.email }}</p>
                    </div>
                    <div class="ml-auto flex flex-col items-end gap-1">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full uppercase tracking-wider">
                            {{ user?.role?.nama_role }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium italic">
                            {{ user?.unit_kerja?.nama_unit || 'Institusi (LPM)' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
