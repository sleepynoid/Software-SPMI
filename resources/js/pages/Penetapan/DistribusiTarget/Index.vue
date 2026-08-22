<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { route } from '@/lib/route';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    indikators: Array,
    units: Array,
    existingTargets: Object,
});

const toast = useToast();
const selectedPeriode = ref(props.selectedPeriodeId);

const form = useForm({
    periode_id: null as number | null,
    targets: [] as { indikator_id: number; unit_kerja_id: number; nilai_target: string; satuan: string }[],
});

watch(selectedPeriode, (newId) => {
    router.get(route('penetapan.distribusi-target.index'), { periode_id: newId }, { preserveState: true });
});

function getTargetValue(indikatorId: number, unitId: number): string {
    return (props.existingTargets as any)?.[indikatorId]?.[unitId]?.nilai_target ?? '';
}

function getTargetSatuan(indikatorId: number, unitId: number): string {
    return (props.existingTargets as any)?.[indikatorId]?.[unitId]?.satuan ?? '';
}

function onInputChange(indikatorId: number, unitId: number, field: 'nilai_target' | 'satuan', value: string) {
    const existing = form.targets.find((t) => t.indikator_id === indikatorId && t.unit_kerja_id === unitId);
    if (existing) {
        (existing as any)[field] = value;
    } else {
        const newTarget: any = {
            indikator_id: indikatorId,
            unit_kerja_id: unitId,
            nilai_target: '',
            satuan: '',
        };
        newTarget[field] = value;
        form.targets.push(newTarget);
    }
}

function save() {
    form.periode_id = selectedPeriode.value as number;
    form.post(route('penetapan.distribusi-target.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Target berhasil disimpan', life: 3000 });
        },
        onError: (errors) => {
            const msg = Object.values(errors)[0];
            toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
        },
    });
}

const programStudiUnits = computed(() => (props.units as any[]).filter((u: any) => u.nama_unit?.toLowerCase().includes('prodi')));
</script>

<template>
    <AppLayout title="Distribusi Target">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#00479b] text-white rounded-xl flex items-center justify-center">
                        <i class="pi pi-map text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight">Distribusi Target</h3>
                        <p class="text-sm text-slate-500">Distribusi target indikator ke unit kerja (Program Studi)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <Select
                        v-model="selectedPeriode"
                        :options="periodes"
                        optionLabel="tahun_akademik"
                        optionValue="id"
                        placeholder="Pilih Periode"
                        class="w-64"
                    />
                    <Button label="Simpan Semua Target" icon="pi pi-save" @click="save" :loading="form.processing" class="bg-[#00479b] border-[#00479b]" />
                </div>
            </div>

            <DataTable :value="indikators" stripedRows responsiveLayout="scroll" class="text-sm" emptyMessage="Belum ada data indikator">
                <Column field="kode_indikator" header="Kode" style="width: 100px" />
                <Column field="isi_standar" header="Isi Indikator" style="min-width: 200px" />

                <Column v-for="unit in programStudiUnits" :key="unit.id" :header="unit.nama_unit" style="min-width: 180px">
                    <template #body="{ data }">
                        <div class="flex flex-col gap-1">
                            <InputText
                                :modelValue="getTargetValue(data.id, unit.id)"
                                @input="(e: any) => onInputChange(data.id, unit.id, 'nilai_target', e.target.value)"
                                placeholder="Nilai"
                                class="w-full text-xs"
                            />
                            <InputText
                                :modelValue="getTargetSatuan(data.id, unit.id)"
                                @input="(e: any) => onInputChange(data.id, unit.id, 'satuan', e.target.value)"
                                placeholder="Satuan"
                                class="w-full text-xs"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>
</template>
