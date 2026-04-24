<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    user: Object,
});

const isAdmin = computed(() => props.user.role?.nama_role === 'Admin/LPM');
const isAuditor = computed(() => props.user.role?.nama_role === 'Auditor');
const isAuditee = computed(() => props.user.role?.nama_role === 'Auditee');
const isPimpinan = computed(() => props.user.role?.nama_role === 'Pimpinan');
</script>

<template>
    <AppLayout title="Dashboard Overview">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Summary Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="pi pi-calendar text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Periode Aktif</p>
                    <p class="text-lg font-bold text-slate-800">Ganjil 2024/2025</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                    <i class="pi pi-info-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Status Siklus</p>
                    <p class="text-lg font-bold text-slate-800">Audit Lapangan</p>
                </div>
            </div>
            
            <div v-if="isAdmin || isPimpinan" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="pi pi-check-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Pencapaian Rata2</p>
                    <p class="text-lg font-bold text-slate-800">82.5%</p>
                </div>
            </div>

            <div v-if="isAuditor || isAuditee" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="pi pi-list text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">KKA Tersisa</p>
                    <p class="text-lg font-bold text-slate-800">12 Indikator</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <h3 class="text-xl font-bold text-slate-800 mb-6">Informasi Sesi</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-bold">
                        {{ user.nama_lengkap.charAt(0) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ user.nama_lengkap }}</p>
                        <p class="text-xs text-slate-500">{{ user.email }}</p>
                    </div>
                    <div class="ml-auto flex gap-2">
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 text-xs font-bold rounded-full">
                            {{ user.role?.nama_role }}
                        </span>
                        <span class="px-3 py-1 bg-slate-200 text-slate-700 text-xs font-bold rounded-full">
                            {{ user.unit_kerja?.nama_unit || 'Institusi' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
