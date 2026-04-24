<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    units: Array,
    reportingStats: Object,
});

const activePeriodeId = ref(props.selectedPeriodeId);

const handlePeriodeChange = (id) => {
    router.get(route('evaluasi.jadwal-audit.index'), { periode_id: id });
};

const getProgress = (unitId) => {
    const reported = props.reportingStats[unitId] || 0;
    // For now assume each unit should have some indicators. 
    // This is just a visual hint.
    return reported;
};
</script>

<template>
    <AppLayout title="Audit Mutu Internal (AMI)">
        <div class="flex flex-col gap-6">
            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Daftar Unit Audit</h3>
                    <p class="text-sm text-slate-500">Pilih Program Studi untuk mulai melakukan audit lapangan</p>
                </div>
                <Select v-model="activePeriodeId" :options="periodes" optionLabel="tahun_akademik" optionValue="id" @change="handlePeriodeChange($event.value)" class="w-64" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="unit in units" :key="unit.id" class="card bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-md transition-all">
                    <div class="p-6 flex-1">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="pi pi-building text-slate-400"></i>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ unit.jenis_unit }}</span>
                        </div>
                        <h4 class="text-xl font-bold text-slate-800 mb-2">{{ unit.nama_unit }}</h4>
                        
                        <div class="flex items-center gap-2 text-sm text-slate-500 mb-6">
                            <i class="pi pi-user text-xs"></i>
                            <span>Ka. Unit: {{ unit.kepala_unit?.nama_lengkap || '-' }}</span>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Indikator Terisi</p>
                                <p class="text-lg font-black text-primary-600">{{ getProgress(unit.id) }}</p>
                            </div>
                            <i class="pi pi-file-edit text-2xl text-slate-200"></i>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-slate-50 border-t border-slate-100">
                        <Link :href="route('evaluasi.kka.show', unit.id)">
                            <Button label="Mulai Audit (KKA)" icon="pi pi-search" class="w-full" severity="primary" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
