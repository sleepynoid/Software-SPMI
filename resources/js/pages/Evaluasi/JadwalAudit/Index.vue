<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { route } from '@/lib/route';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    units: Object,
    reportingStats: Object,
});

const selectedPeriodeId = ref(props.selectedPeriodeId);

watch(selectedPeriodeId, (newId) => {
    router.get(route('evaluasi.jadwal-audit.index'), { periode_id: newId }, { preserveState: true });
});

function getJumlahLaporan(unitId) {
    return props.reportingStats?.[unitId] ?? 0;
}
</script>

<template>
    <AppLayout title="Jadwal Audit">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="pi pi-calendar text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Jadwal Audit Internal</h3>
                    <p class="text-sm text-slate-500">Daftar unit kerja yang akan diaudit beserta laporan</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Periode:</span>
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

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <DataTable :value="units?.data || []" stripedRows paginator :rows="10" :rowsPerPageOptions="[10, 25, 50]" dataKey="id" rowHover
                emptyMessage="Belum ada unit kerja untuk periode ini.">
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <i class="pi pi-inbox text-5xl mb-4"></i>
                        <p class="text-lg font-medium">Tidak ada data</p>
                        <p class="text-sm">Unit kerja belum tersedia untuk periode ini</p>
                    </div>
                </template>
                <Column field="nama_unit" header="Nama Unit" style="min-width: 20rem;">
                    <template #body="{ data }">
                        <span class="font-semibold text-slate-800">{{ data.nama_unit }}</span>
                    </template>
                </Column>
                <Column field="jenis_unit" header="Jenis Unit" style="width: 14rem;">
                    <template #body="{ data }">
                        <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                            {{ data.jenis_unit }}
                        </span>
                    </template>
                </Column>
                <Column header="Jumlah Laporan" style="width: 12rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <span class="font-semibold text-slate-800">{{ getJumlahLaporan(data.id) }}</span>
                        <span class="text-slate-500 text-sm ml-1">laporan</span>
                    </template>
                </Column>
                <Column header="Aksi" style="width: 10rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <Link :href="route('evaluasi.kka.show', { unit: data.id })"
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="pi pi-eye"></i>
                            Lihat KKA
                        </Link>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>
</template>
