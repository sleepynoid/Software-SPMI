<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { route } from '@/lib/route';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';

const props = defineProps({
    standars: Array,
    periodes: Array,
    categories: Array,
    selectedPeriodeId: Number,
});

const toast = useToast();
const visible = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    periode_id: null as number | null,
    kategori_id: null as number | null,
    nama_standar: '',
});

const selectedPeriode = ref(props.selectedPeriodeId);

watch(selectedPeriode, (newId) => {
    router.get(route('penetapan.standar.index'), { periode_id: newId }, { preserveState: true });
});

function openDialog(item: any = null) {
    if (item) {
        editingId.value = item.id;
        form.periode_id = item.periode_id;
        form.kategori_id = item.kategori_id;
        form.nama_standar = item.nama_standar;
    } else {
        editingId.value = null;
        form.reset();
        form.periode_id = props.selectedPeriodeId as number;
    }
    visible.value = true;
}

function save() {
    if (editingId.value) {
        form.put(route('penetapan.standar.update', editingId.value), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Standar diperbarui', life: 3000 });
                visible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    } else {
        form.post(route('penetapan.standar.store'), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Standar ditambahkan', life: 3000 });
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
    if (confirm('Yakin ingin menghapus standar ini?')) {
        form.delete(route('penetapan.standar.destroy', id), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Standar dihapus', life: 3000 });
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
    <AppLayout title="Standar Dikti">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#00479b] text-white rounded-xl flex items-center justify-center">
                        <i class="pi pi-book text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight">Standar Dikti</h3>
                        <p class="text-sm text-slate-500">Kelola standar pendidikan tinggi</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <Select
                        v-model="selectedPeriode"
                        :options="periodes"
                        optionLabel="tahun_akademik"
                        optionValue="id"
                        placeholder="Semua Periode"
                        class="w-64"
                    />
                    <Button label="Tambah Standar" icon="pi pi-plus" @click="openDialog()" class="bg-[#00479b] border-[#00479b]" />
                </div>
            </div>

            <DataTable :value="standars" stripedRows responsiveLayout="scroll" emptyMessage="Belum ada data standar">
                <Column field="nama_standar" header="Nama Standar" sortable />
                <Column header="Kategori">
                    <template #body="{ data }">
                        {{ data.kategori?.nama_kategori ?? '-' }}
                    </template>
                </Column>
                <Column header="Periode">
                    <template #body="{ data }">
                        {{ data.periode?.tahun_akademik ?? '-' }}
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

        <Dialog v-model:visible="visible" :header="editingId ? 'Edit Standar' : 'Tambah Standar'" modal class="w-[500px]">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Periode</label>
                    <Select
                        v-model="form.periode_id"
                        :options="periodes"
                        optionLabel="tahun_akademik"
                        optionValue="id"
                        placeholder="Pilih Periode"
                        class="w-full"
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Kategori</label>
                    <Select
                        v-model="form.kategori_id"
                        :options="categories"
                        optionLabel="nama_kategori"
                        optionValue="id"
                        placeholder="Pilih Kategori"
                        class="w-full"
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Nama Standar</label>
                    <InputText v-model="form.nama_standar" placeholder="Masukkan nama standar" class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" severity="secondary" text @click="visible = false" />
                <Button label="Simpan" icon="pi pi-check" @click="save" :loading="form.processing" class="bg-[#00479b] border-[#00479b]" />
            </template>
        </Dialog>
    </AppLayout>
</template>
