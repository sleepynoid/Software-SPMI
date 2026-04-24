<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref, reactive } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    indikators: Array,
    units: Array,
    existingTargets: Object,
});

const toast = useToast();
const activePeriodeId = ref(props.selectedPeriodeId);
const editMode = ref(false);

const localTargets = reactive({});

// Initialize local targets from props
props.indikators.forEach(ind => {
    localTargets[ind.id] = {};
    props.units.forEach(unit => {
        const target = props.existingTargets[ind.id]?.[unit.id];
        localTargets[ind.id][unit.id] = {
            nilai_target: target ? target.nilai_target : 0,
            satuan: target ? target.satuan : '%',
        };
    });
});

const saveForm = useForm({
    targets: [],
});

const saveTargets = () => {
    const targetArray = [];
    Object.keys(localTargets).forEach(indId => {
        Object.keys(localTargets[indId]).forEach(unitId => {
            targetArray.push({
                indikator_id: indId,
                unit_kerja_id: unitId,
                nilai_target: localTargets[indId][unitId].nilai_target,
                satuan: localTargets[indId][unitId].satuan,
            });
        });
    });

    saveForm.targets = targetArray;
    saveForm.post(route('penetapan.distribusi-target.store'), {
        onSuccess: () => {
            editMode.value = false;
            toast.add({ severity: 'success', summary: 'Sukses', detail: 'Target berhasil disimpan', life: 3000 });
        }
    });
};

const handlePeriodeChange = (id) => {
    router.get(route('penetapan.distribusi-target.index'), { periode_id: id });
};
</script>

<template>
    <AppLayout title="Distribusi Target Mutu (Matriks)">
        <div class="flex flex-col gap-6">
            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Matriks Target Unit Kerja</h3>
                    <p class="text-sm text-slate-500">Tetapkan target angka untuk masing-masing Program Studi</p>
                </div>
                <div class="flex items-center gap-4">
                    <Select v-model="activePeriodeId" :options="periodes" optionLabel="tahun_akademik" optionValue="id" @change="handlePeriodeChange($event.value)" class="w-64" />
                    <Button v-if="!editMode" label="Edit Matriks" icon="pi pi-pencil" @click="editMode = true" :disabled="indikators.length === 0" />
                    <div v-else class="flex gap-2">
                        <Button label="Batal" icon="pi pi-times" severity="secondary" @click="editMode = false" />
                        <Button label="Simpan Semua" icon="pi pi-check" @click="saveTargets" :loading="saveForm.processing" />
                    </div>
                </div>
            </div>

            <div class="card bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="p-4 border-b border-slate-200 text-left text-xs font-bold text-slate-500 uppercase sticky left-0 bg-slate-50 z-10 w-80">Indikator Mutu</th>
                                <th v-for="unit in units" :key="unit.id" class="p-4 border-b border-slate-200 text-center text-xs font-bold text-slate-500 uppercase min-w-32">
                                    {{ unit.nama_unit }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="ind in indikators" :key="ind.id" class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 border-b border-slate-100 sticky left-0 bg-white z-10">
                                    <p class="text-xs font-bold text-primary-600 mb-1">{{ ind.kode_indikator }}</p>
                                    <p class="text-sm text-slate-700 leading-tight line-clamp-2">{{ ind.isi_standar }}</p>
                                </td>
                                <td v-for="unit in units" :key="unit.id" class="p-4 border-b border-slate-100 text-center">
                                    <div v-if="editMode" class="flex flex-col gap-1">
                                        <InputNumber v-model="localTargets[ind.id][unit.id].nilai_target" size="small" class="w-full" :minFractionDigits="0" :maxFractionDigits="2" />
                                        <InputText v-model="localTargets[ind.id][unit.id].satuan" size="small" unstyled class="w-full text-xs bg-slate-100 px-2 rounded h-6 text-center" />
                                    </div>
                                    <div v-else class="flex flex-col items-center">
                                        <span class="text-lg font-bold text-slate-800">{{ localTargets[ind.id][unit.id].nilai_target }}</span>
                                        <span class="text-xs text-slate-400 font-medium">{{ localTargets[ind.id][unit.id].satuan }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="indikators.length === 0" class="p-20 text-center italic text-slate-400">
                    Belum ada indikator mutu untuk periode ini.
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Ensure the sticky col stays on top of other cells */
th.sticky, td.sticky {
    box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
}
</style>
