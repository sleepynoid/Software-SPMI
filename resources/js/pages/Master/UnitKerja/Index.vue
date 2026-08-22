<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { route } from '@/lib/route';

const props = defineProps<{
    units: Array<any>;
    users: Array<any>;
}>();

const toast = useToast();
const dialogVisible = ref(false);
const deleteDialogVisible = ref(false);
const editingUnit = ref<any>(null);
const unitToDelete = ref<any>(null);

const emptyForm = {
    nama_unit: '',
    jenis_unit: '',
    kepala_unit_id: null as number | null,
};

const form = useForm({ ...emptyForm });

const isEditing = computed(() => !!editingUnit.value);

const jenisUnitOptions = [
    { label: 'Fakultas', value: 'Fakultas' },
    { label: 'Program Studi', value: 'Program Studi' },
    { label: 'Biro', value: 'Biro' },
    { label: 'Lembaga', value: 'Lembaga' },
];

const jenisUnitSeverity: Record<string, string> = {
    Fakultas: 'info',
    'Program Studi': 'success',
    Biro: 'warn',
    Lembaga: 'help',
};

function openCreate() {
    editingUnit.value = null;
    form.defaults({ ...emptyForm });
    form.reset();
    dialogVisible.value = true;
}

function openEdit(unit: any) {
    editingUnit.value = unit;
    form.defaults({
        nama_unit: unit.nama_unit,
        jenis_unit: unit.jenis_unit,
        kepala_unit_id: unit.kepala_unit_id,
    });
    form.reset();
    dialogVisible.value = true;
}

function submit() {
    if (isEditing.value) {
        form.put(route('master.unit-kerja.update', { id: editingUnit.value.id }), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Unit kerja diperbarui.', life: 3000 });
                dialogVisible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    } else {
        form.post(route('master.unit-kerja.store'), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Unit kerja ditambahkan.', life: 3000 });
                dialogVisible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    }
}

function confirmDelete(unit: any) {
    unitToDelete.value = unit;
    deleteDialogVisible.value = true;
}

function deleteUnit() {
    router.delete(route('master.unit-kerja.destroy', { id: unitToDelete.value.id }), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Unit kerja dihapus.', life: 3000 });
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
    <AppLayout title="Manajemen Unit Kerja">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Daftar Unit Kerja</h2>
                    <p class="text-sm text-slate-500">Kelola unit kerja institusi</p>
                </div>
                <Button label="Tambah Unit" icon="pi pi-plus" @click="openCreate" class="bg-[#00479b] border-[#00479b] hover:bg-[#003577]" />
            </div>

            <DataTable :value="units" :rows="10" :paginator="true" stripedRows responsiveLayout="scroll">
                <Column field="nama_unit" header="Nama Unit" sortable />
                <Column header="Jenis Unit" sortable sortField="jenis_unit">
                    <template #body="{ data }">
                        <Tag :value="data.jenis_unit" :severity="jenisUnitSeverity[data.jenis_unit] || 'secondary'" />
                    </template>
                </Column>
                <Column header="Kepala Unit" sortField="kepala.nama_lengkap">
                    <template #body="{ data }">
                        {{ data.kepala?.nama_lengkap || '-' }}
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
        <Dialog v-model:visible="dialogVisible" :header="isEditing ? 'Edit Unit Kerja' : 'Tambah Unit Kerja'" modal :style="{ width: '32rem' }" :closable="!form.processing">
            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div>
                    <label for="nama_unit" class="block text-sm font-medium text-slate-700 mb-1">Nama Unit</label>
                    <InputText id="nama_unit" v-model="form.nama_unit" class="w-full" :invalid="!!form.errors.nama_unit" />
                    <small v-if="form.errors.nama_unit" class="text-red-500">{{ form.errors.nama_unit }}</small>
                </div>
                <div>
                    <label for="jenis_unit" class="block text-sm font-medium text-slate-700 mb-1">Jenis Unit</label>
                    <Select id="jenis_unit" v-model="form.jenis_unit" :options="jenisUnitOptions" optionLabel="label" optionValue="value" placeholder="Pilih Jenis Unit" class="w-full" :invalid="!!form.errors.jenis_unit" />
                    <small v-if="form.errors.jenis_unit" class="text-red-500">{{ form.errors.jenis_unit }}</small>
                </div>
                <div>
                    <label for="kepala_unit_id" class="block text-sm font-medium text-slate-700 mb-1">Kepala Unit</label>
                    <Select id="kepala_unit_id" v-model="form.kepala_unit_id" :options="users" optionLabel="nama_lengkap" optionValue="id" placeholder="Pilih Kepala Unit" class="w-full" :invalid="!!form.errors.kepala_unit_id" showClear />
                    <small v-if="form.errors.kepala_unit_id" class="text-red-500">{{ form.errors.kepala_unit_id }}</small>
                </div>
            </form>
            <template #footer>
                <Button label="Batal" severity="secondary" text @click="dialogVisible = false" :disabled="form.processing" />
                <Button :label="isEditing ? 'Perbarui' : 'Simpan'" :loading="form.processing" @click="submit" class="bg-[#00479b] border-[#00479b] hover:bg-[#003577]" />
            </template>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:visible="deleteDialogVisible" header="Konfirmasi Hapus" modal :style="{ width: '28rem' }">
            <p class="text-slate-600">Apakah Anda yakin ingin menghapus unit kerja <strong>{{ unitToDelete?.nama_unit }}</strong>?</p>
            <template #footer>
                <Button label="Batal" severity="secondary" text @click="deleteDialogVisible = false" />
                <Button label="Hapus" severity="danger" @click="deleteUnit" />
            </template>
        </Dialog>
    </AppLayout>
</template>
