<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import ToggleSwitch from 'primevue/toggleswitch';

const props = defineProps({
    categories: Array,
});

const toast = useToast();
const categoryDialog = ref(false);
const form = useForm({
    id: null,
    nama_kategori: '',
    is_default: false,
});

const openNew = () => {
    form.reset();
    categoryDialog.value = true;
};

const editCategory = (cat) => {
    form.id = cat.id;
    form.nama_kategori = cat.nama_kategori;
    form.is_default = !!cat.is_default;
    categoryDialog.value = true;
};

const saveCategory = () => {
    if (form.id) {
        form.put(route('master.kategori-standar.update', form.id), {
            onSuccess: () => {
                categoryDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Kategori diperbarui', life: 3000 });
            }
        });
    } else {
        form.post(route('master.kategori-standar.store'), {
            onSuccess: () => {
                categoryDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Kategori ditambahkan', life: 3000 });
            }
        });
    }
};

const deleteCategory = (cat) => {
    if(confirm('Hapus kategori ini?')) {
        form.delete(route('master.kategori-standar.destroy', cat.id), {
            onSuccess: () => toast.add({ severity: 'success', summary: 'Sukses', detail: 'Kategori dihapus', life: 3000 })
        });
    }
};
</script>

<template>
    <AppLayout title="Manajemen Kategori Standar">
        <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <Toolbar class="mb-6 bg-transparent border-none p-0">
                <template #start>
                    <Button label="Tambah Kategori" icon="pi pi-plus" class="p-button-success" @click="openNew" />
                </template>
            </Toolbar>

            <DataTable :value="categories" dataKey="id" responsiveLayout="scroll">
                <Column field="nama_kategori" header="Nama Kategori" sortable></Column>
                <Column field="is_default" header="Wajib Nasional" sortable>
                    <template #body="slotProps">
                        <Tag :severity="slotProps.data.is_default ? 'success' : 'warning'" :value="slotProps.data.is_default ? 'YA' : 'TIDAK'" />
                    </template>
                </Column>
                <Column header="Aksi">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" class="p-button-text p-button-info mr-2" @click="editCategory(slotProps.data)" />
                        <Button icon="pi pi-trash" class="p-button-text p-button-danger" @click="deleteCategory(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="categoryDialog" :style="{width: '400px'}" header="Detail Kategori" :modal="true">
            <div class="flex flex-col gap-6 mt-4">
                <div class="field">
                    <label class="block font-medium mb-2">Nama Kategori</label>
                    <InputText v-model="form.nama_kategori" required class="w-full" />
                </div>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <p class="font-semibold text-slate-800">Wajib Nasional (SN-Dikti)</p>
                        <p class="text-xs text-slate-500">Standar default untuk semua prodi</p>
                    </div>
                    <ToggleSwitch v-model="form.is_default" />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="categoryDialog = false"/>
                <Button label="Simpan" icon="pi pi-check" @click="saveCategory" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
