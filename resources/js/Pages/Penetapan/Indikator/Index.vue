<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    indikators: Array,
    standars: Array,
    periodes: Array,
    selectedPeriodeId: Number,
    selectedStandarId: Number,
});

const toast = useToast();
const indDialog = ref(false);
const activePeriodeId = ref(props.selectedPeriodeId);
const activeStandarId = ref(props.selectedStandarId);

const form = useForm({
    id: null,
    standar_id: props.selectedStandarId,
    kode_indikator: '',
    isi_standar: '',
    jenis: 'IKU',
});

watch([activePeriodeId, activeStandarId], ([pId, sId], [oldPId, oldSId]) => {
    let params = { periode_id: pId };
    if (pId === oldPId) { // Only update standar filter if periode didn't change
        params.standar_id = sId;
    } else {
        activeStandarId.value = null; // Reset standar if periode changes
    }
    router.get(route('penetapan.indikator.index'), params, { preserveState: true });
});

const openNew = () => {
    form.reset();
    form.standar_id = activeStandarId.value;
    indDialog.value = true;
};

const editInd = (ind) => {
    form.id = ind.id;
    form.standar_id = ind.standar_id;
    form.kode_indikator = ind.kode_indikator;
    form.isi_standar = ind.isi_standar;
    form.jenis = ind.jenis;
    indDialog.value = true;
};

const saveInd = () => {
    if (form.id) {
        form.put(route('penetapan.indikator.update', form.id), {
            onSuccess: () => {
                indDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Indikator diperbarui', life: 3000 });
            }
        });
    } else {
        form.post(route('penetapan.indikator.store'), {
            onSuccess: () => {
                indDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Indikator ditambahkan', life: 3000 });
            }
        });
    }
};

const deleteInd = (ind) => {
    if(confirm('Hapus indikator ini?')) {
        form.delete(route('penetapan.indikator.destroy', ind.id), {
            onSuccess: () => toast.add({ severity: 'success', summary: 'Sukses', detail: 'Indikator dihapus', life: 3000 })
        });
    }
};
</script>

<template>
    <AppLayout title="Manajemen Indikator Mutu">
        <div class="flex flex-col gap-6">
            <!-- Filter Bar -->
            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase">Periode AMI</label>
                    <Select v-model="activePeriodeId" :options="periodes" optionLabel="tahun_akademik" optionValue="id" placeholder="Pilih Periode" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase">Standar Dikti</label>
                    <Select v-model="activeStandarId" :options="standars" optionLabel="nama_standar" optionValue="id" placeholder="Semua Standar" class="w-full" showClear />
                </div>
            </div>

            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <Toolbar class="mb-6 bg-transparent border-none p-0">
                    <template #start>
                        <Button label="Tambah Indikator" icon="pi pi-plus" class="p-button-success" @click="openNew" :disabled="!activeStandarId" />
                    </template>
                </Toolbar>

                <DataTable :value="indikators" dataKey="id" responsiveLayout="scroll">
                    <Column field="kode_indikator" header="Kode" sortable style="width:10rem"></Column>
                    <Column field="standar.nama_standar" header="Standar" sortable style="width:15rem" v-if="!activeStandarId"></Column>
                    <Column field="isi_standar" header="Isi Indikator"></Column>
                    <Column field="jenis" header="Jenis" sortable style="width:8rem">
                        <template #body="slotProps">
                            <Badge :value="slotProps.data.jenis" :severity="slotProps.data.jenis === 'IKU' ? 'info' : 'warning'" />
                        </template>
                    </Column>
                    <Column header="Aksi" style="width:8rem">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="p-button-text p-button-info mr-2" @click="editInd(slotProps.data)" />
                            <Button icon="pi pi-trash" class="p-button-text p-button-danger" @click="deleteInd(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <Dialog v-model:visible="indDialog" :style="{width: '600px'}" header="Detail Indikator Mutu" :modal="true">
            <div class="flex flex-col gap-6 mt-4">
                <div class="field">
                    <label class="block font-medium mb-2">Standar Dikti</label>
                    <Select v-model="form.standar_id" :options="standars" optionLabel="nama_standar" optionValue="id" class="w-full" disabled />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field">
                        <label class="block font-medium mb-2">Kode Indikator</label>
                        <InputText v-model="form.kode_indikator" placeholder="STD-PND-01" class="w-full" required />
                    </div>
                    <div class="field">
                        <label class="block font-medium mb-2">Jenis Indikator</label>
                        <Select v-model="form.jenis" :options="['IKU', 'IKT']" class="w-full" />
                    </div>
                </div>
                <div class="field">
                    <label class="block font-medium mb-2">Isi Standar / Indikator</label>
                    <Textarea v-model="form.isi_standar" rows="5" class="w-full" placeholder="Deskripsi detail apa yang harus dicapai..." required />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="indDialog = false"/>
                <Button label="Simpan Indikator" icon="pi pi-check" @click="saveInd" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
