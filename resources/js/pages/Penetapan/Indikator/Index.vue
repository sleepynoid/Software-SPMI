<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { route } from '@/lib/route';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';

const props = defineProps({
    indikators: Array,
    standars: Array,
    periodes: Array,
    selectedPeriodeId: Number,
    selectedStandarId: Number,
});

const toast = useToast();
const visible = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    standar_id: null as number | null,
    kode_indikator: '',
    isi_standar: '',
    jenis: 'IKU',
});

const selectedPeriode = ref(props.selectedPeriodeId);
const selectedStandar = ref(props.selectedStandarId);

const jenisOptions = [
    { label: 'IKU - Indikator Kinerja Utama', value: 'IKU' },
    { label: 'IKT - Indikator Kinerja Tambahan', value: 'IKT' },
];

watch(selectedPeriode, (newId) => {
    router.get(route('penetapan.indikator.index'), { periode_id: newId }, { preserveState: true });
});

watch(selectedStandar, (newId) => {
    router.get(route('penetapan.indikator.index'), { periode_id: props.selectedPeriodeId, standar_id: newId }, { preserveState: true });
});

function openDialog(item: any = null) {
    if (item) {
        editingId.value = item.id;
        form.standar_id = item.standar_id;
        form.kode_indikator = item.kode_indikator;
        form.isi_standar = item.isi_standar;
        form.jenis = item.jenis;
    } else {
        editingId.value = null;
        form.reset();
        form.standar_id = props.selectedStandarId as number;
    }
    visible.value = true;
}

function save() {
    if (editingId.value) {
        form.put(route('penetapan.indikator.update', editingId.value), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Indikator diperbarui', life: 3000 });
                visible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    } else {
        form.post(route('penetapan.indikator.store'), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Indikator ditambahkan', life: 3000 });
                visible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    }
}

function destroy(id: number) {
    if (confirm('Yakin ingin menghapus indikator ini?')) {
        form.delete(route('penetapan.indikator.destroy', id), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Indikator dihapus', life: 3000 });
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    }
}
</script>

<template>
    <AppLayout title="Indikator Mutu">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#00479b] text-white rounded-xl flex items-center justify-center">
                        <i class="pi pi-list text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight">Indikator Mutu</h3>
                        <p class="text-sm text-slate-500">Kelola indikator kinerja standar</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <Select
                        v-model="selectedPeriode"
                        :options="periodes"
                        optionLabel="tahun_akademik"
                        optionValue="id"
                        placeholder="Pilih Periode"
                        class="w-52"
                    />
                    <Select
                        v-model="selectedStandar"
                        :options="standars"
                        optionLabel="nama_standar"
                        optionValue="id"
                        placeholder="Semua Standar"
                        class="w-64"
                        :disabled="!selectedPeriode"
                    />
                    <Button label="Tambah Indikator" icon="pi pi-plus" @click="openDialog()" class="bg-[#00479b] border-[#00479b]" />
                </div>
            </div>

            <DataTable :value="indikators" stripedRows responsiveLayout="scroll" emptyMessage="Belum ada data indikator">
                <Column field="kode_indikator" header="Kode Indikator" sortable />
                <Column field="isi_standar" header="Isi Standar" sortable />
                <Column header="Jenis">
                    <template #body="{ data }">
                        <Tag :value="data.jenis" :severity="data.jenis === 'IKU' ? 'info' : 'warn'" />
                    </template>
                </Column>
                <Column header="Standar">
                    <template #body="{ data }">
                        {{ data.standar?.nama_standar ?? '-' }}
                    </template>
                </Column>
                <Column header="Aksi" style="width: 10rem">
                    <template #body="{ data }">
                        <div class="flex gap-2">
                            <Button icon="pi pi-pencil" severity="info" text rounded @click="openDialog(data)" />
                            <Button icon="pi pi-trash" severity="danger" text rounded @click="destroy(data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="visible" :header="editingId ? 'Edit Indikator' : 'Tambah Indikator'" modal class="w-[550px]">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Standar</label>
                    <Select
                        v-model="form.standar_id"
                        :options="standars"
                        optionLabel="nama_standar"
                        optionValue="id"
                        placeholder="Pilih Standar"
                        class="w-full"
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Kode Indikator</label>
                    <InputText v-model="form.kode_indikator" placeholder="Contoh: IK.1.1" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Isi Standar</label>
                    <Textarea v-model="form.isi_standar" rows="4" placeholder="Deskripsi isi standar" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Jenis</label>
                    <Select
                        v-model="form.jenis"
                        :options="jenisOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Pilih Jenis"
                        class="w-full"
                    />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" severity="secondary" text @click="visible = false" />
                <Button label="Simpan" icon="pi pi-check" @click="save" :loading="form.processing" class="bg-[#00479b] border-[#00479b]" />
            </template>
        </Dialog>
    </AppLayout>
</template>
