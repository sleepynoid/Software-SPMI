<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    standars: Array,
    periodes: Array,
    categories: Array,
    selectedPeriodeId: Number,
});

const toast = useToast();
const standarDialog = ref(false);
const activePeriodeId = ref(props.selectedPeriodeId);

const form = useForm({
    id: null,
    periode_id: props.selectedPeriodeId,
    kategori_id: null,
    nama_standar: '',
});

watch(activePeriodeId, (newId) => {
    router.get(route('penetapan.standar.index'), { periode_id: newId }, { preserveState: true });
});

const openNew = () => {
    form.reset();
    form.periode_id = activePeriodeId.value;
    standarDialog.value = true;
};

const editStandar = (s) => {
    form.id = s.id;
    form.periode_id = s.periode_id;
    form.kategori_id = s.kategori_id;
    form.nama_standar = s.nama_standar;
    standarDialog.value = true;
};

const saveStandar = () => {
    if (form.id) {
        form.put(route('penetapan.standar.update', form.id), {
            onSuccess: () => {
                standarDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Standar diperbarui', life: 3000 });
            }
        });
    } else {
        form.post(route('penetapan.standar.store'), {
            onSuccess: () => {
                standarDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Standar ditambahkan', life: 3000 });
            }
        });
    }
};

const deleteStandar = (s) => {
    if(confirm('Hapus standar ini?')) {
        form.delete(route('penetapan.standar.destroy', s.id), {
            onSuccess: () => toast.add({ severity: 'success', summary: 'Sukses', detail: 'Standar dihapus', life: 3000 })
        });
    }
};
</script>

<template>
    <AppLayout title="Penetapan Standar Dikti">
        <div class="flex flex-col gap-6">
            <!-- Periode Selector -->
            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Filter Periode AMI</h3>
                    <p class="text-sm text-slate-500">Pilih periode untuk mengelola standar yang berlaku</p>
                </div>
                <Select v-model="activePeriodeId" :options="periodes" optionLabel="tahun_akademik" optionValue="id" placeholder="Pilih Periode" class="w-64" />
            </div>

            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <Toolbar class="mb-6 bg-transparent border-none p-0">
                    <template #start>
                        <Button label="Tambah Standar" icon="pi pi-plus" class="p-button-success" @click="openNew" :disabled="!activePeriodeId" />
                    </template>
                </Toolbar>

                <DataTable :value="standars" dataKey="id" responsiveLayout="scroll">
                    <Column field="kategori.nama_kategori" header="Kategori" sortable>
                        <template #body="slotProps">
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-semibold uppercase">
                                {{ slotProps.data.kategori?.nama_kategori }}
                            </span>
                        </template>
                    </Column>
                    <Column field="nama_standar" header="Nama Standar" sortable></Column>
                    <Column header="Aksi">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="p-button-text p-button-info mr-2" @click="editStandar(slotProps.data)" />
                            <Button icon="pi pi-trash" class="p-button-text p-button-danger" @click="deleteStandar(slotProps.data)" />
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center p-8">
                            <p class="text-slate-500 mb-4">Belum ada standar ditetapkan untuk periode ini.</p>
                            <Button label="Buat Standar Pertama" icon="pi pi-plus" text @click="openNew" />
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>

        <Dialog v-model:visible="standarDialog" :style="{width: '500px'}" header="Detail Standar Dikti" :modal="true">
            <div class="flex flex-col gap-6 mt-4">
                <div class="field text-sm p-4 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 italic">
                    Standar ini akan didistribusikan ke unit kerja terkait untuk diisi indikator mutunya.
                </div>
                <div class="field">
                    <label class="block font-medium mb-2">Pilih Kategori</label>
                    <Select v-model="form.kategori_id" :options="categories" optionLabel="nama_kategori" optionValue="id" placeholder="-- Pilih Kategori --" class="w-full" required />
                </div>
                <div class="field">
                    <label class="block font-medium mb-2">Nama Standar Dikti</label>
                    <Textarea v-model="form.nama_standar" rows="3" class="w-full" placeholder="Contoh: Standar Kompetensi Lulusan" required />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-text" class="p-button-text" @click="standarDialog = false"/>
                <Button label="Simpan Standar" icon="pi pi-check" @click="saveStandar" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
