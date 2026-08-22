<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { route } from '@/lib/route';

const props = defineProps<{
    categories: Array<any>;
}>();

const toast = useToast();
const dialogVisible = ref(false);
const deleteDialogVisible = ref(false);
const editingCategory = ref<any>(null);
const categoryToDelete = ref<any>(null);

const emptyForm = {
    nama_kategori: '',
    is_default: false,
};

const form = useForm({ ...emptyForm });

const isEditing = computed(() => !!editingCategory.value);

function openCreate() {
    editingCategory.value = null;
    form.defaults({ ...emptyForm });
    form.reset();
    dialogVisible.value = true;
}

function openEdit(category: any) {
    editingCategory.value = category;
    form.defaults({
        nama_kategori: category.nama_kategori,
        is_default: category.is_default,
    });
    form.reset();
    dialogVisible.value = true;
}

function submit() {
    if (isEditing.value) {
        form.put(route('master.kategori-standar.update', { id: editingCategory.value.id }), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Kategori standar diperbarui.', life: 3000 });
                dialogVisible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    } else {
        form.post(route('master.kategori-standar.store'), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Kategori standar ditambahkan.', life: 3000 });
                dialogVisible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    }
}

function confirmDelete(category: any) {
    categoryToDelete.value = category;
    deleteDialogVisible.value = true;
}

function deleteCategory() {
    router.delete(route('master.kategori-standar.destroy', { id: categoryToDelete.value.id }), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Kategori standar dihapus.', life: 3000 });
            deleteDialogVisible.value = false;
        },
        onError: (errors) => {
            const msg = Object.values(errors)[0];
            toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
        },
    });
}
</script>

<template>
    <AppLayout title="Manajemen Kategori Standar">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Daftar Kategori Standar</h2>
                    <p class="text-sm text-slate-500">Kelola kategori standar SPMI</p>
                </div>
                <Button label="Tambah Kategori" icon="pi pi-plus" @click="openCreate" class="bg-[#00479b] border-[#00479b] hover:bg-[#003577]" />
            </div>

            <DataTable :value="categories" :rows="10" :paginator="true" stripedRows responsiveLayout="scroll">
                <Column field="nama_kategori" header="Nama Kategori" sortable />
                <Column header="Default" sortable sortField="is_default">
                    <template #body="{ data }">
                        <Tag :value="data.is_default ? 'Ya' : 'Tidak'" :severity="data.is_default ? 'success' : 'secondary'" />
                    </template>
                </Column>
                <Column header="Aksi" style="width: 10rem">
                    <template #body="{ data }">
                        <div class="flex gap-2">
                            <Button icon="pi pi-pencil" severity="info" text rounded @click="openEdit(data)" />
                            <Button icon="pi pi-trash" severity="danger" text rounded @click="confirmDelete(data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Create / Edit Dialog -->
        <Dialog v-model:visible="dialogVisible" :header="isEditing ? 'Edit Kategori Standar' : 'Tambah Kategori Standar'" modal :style="{ width: '32rem' }" :closable="!form.processing">
            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div>
                    <label for="nama_kategori" class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori</label>
                    <InputText id="nama_kategori" v-model="form.nama_kategori" class="w-full" :invalid="!!form.errors.nama_kategori" />
                    <small v-if="form.errors.nama_kategori" class="text-red-500">{{ form.errors.nama_kategori }}</small>
                </div>
                <div class="flex items-center gap-3">
                    <Checkbox v-model="form.is_default" :binary="true" inputId="is_default" />
                    <label for="is_default" class="text-sm font-medium text-slate-700">Default Kategori</label>
                </div>
            </form>
            <template #footer>
                <Button label="Batal" severity="secondary" text @click="dialogVisible = false" :disabled="form.processing" />
                <Button :label="isEditing ? 'Perbarui' : 'Simpan'" :loading="form.processing" @click="submit" class="bg-[#00479b] border-[#00479b] hover:bg-[#003577]" />
            </template>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:visible="deleteDialogVisible" header="Konfirmasi Hapus" modal :style="{ width: '28rem' }">
            <p class="text-slate-600">Apakah Anda yakin ingin menghapus kategori standar <strong>{{ categoryToDelete?.nama_kategori }}</strong>?</p>
            <template #footer>
                <Button label="Batal" severity="secondary" text @click="deleteDialogVisible = false" />
                <Button label="Hapus" severity="danger" @click="deleteCategory" />
            </template>
        </Dialog>
    </AppLayout>
</template>
