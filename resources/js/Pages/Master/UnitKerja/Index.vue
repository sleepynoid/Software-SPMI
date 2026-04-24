<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    units: Array,
    users: Array,
});

const toast = useToast();
const unitDialog = ref(false);
const deleteUnitDialog = ref(false);
const selectedUnit = ref(null);

const form = useForm({
    id: null,
    nama_unit: '',
    jenis_unit: 'Program Studi',
    kepala_unit_id: null,
});

const openNew = () => {
    form.reset();
    unitDialog.value = true;
};

const editUnit = (unit) => {
    form.id = unit.id;
    form.nama_unit = unit.nama_unit;
    form.jenis_unit = unit.jenis_unit;
    form.kepala_unit_id = unit.kepala_unit_id;
    unitDialog.value = true;
};

const saveUnit = () => {
    if (form.id) {
        form.put(route('master.unit-kerja.update', form.id), {
            onSuccess: () => {
                unitDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Unit Kerja diperbarui', life: 3000 });
            }
        });
    } else {
        form.post(route('master.unit-kerja.store'), {
            onSuccess: () => {
                unitDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Unit Kerja ditambahkan', life: 3000 });
            }
        });
    }
};

const confirmDelete = (unit) => {
    selectedUnit.value = unit;
    deleteUnitDialog.value = true;
};

const deleteUnit = () => {
    form.delete(route('master.unit-kerja.destroy', selectedUnit.value.id), {
        onSuccess: () => {
            deleteUnitDialog.value = false;
            toast.add({ severity: 'success', summary: 'Sukses', detail: 'Unit Kerja dihapus', life: 3000 });
        }
    });
};
</script>

<template>
    <AppLayout title="Manajemen Unit Kerja">
        <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <Toolbar class="mb-6 bg-transparent border-none p-0">
                <template #start>
                    <Button label="Tambah Unit" icon="pi pi-plus" class="p-button-success mr-2" @click="openNew" />
                </template>
            </Toolbar>

            <DataTable :value="units" dataKey="id" :paginator="true" :rows="10" 
                responsiveLayout="scroll" class="p-datatable-sm">
                <Column field="nama_unit" header="Nama Unit" sortable style="min-width:14rem"></Column>
                <Column field="jenis_unit" header="Jenis" sortable>
                    <template #body="slotProps">
                        <Badge :value="slotProps.data.jenis_unit" severity="info" />
                    </template>
                </Column>
                <Column field="kepala_unit.nama_lengkap" header="Kepala Unit" sortable style="min-width:12rem">
                    <template #body="slotProps">
                        {{ slotProps.data.kepala_unit?.nama_lengkap || '-' }}
                    </template>
                </Column>
                <Column :exportable="false" style="min-width:8rem" header="Aksi">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-info mr-2" @click="editUnit(slotProps.data)" />
                        <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-danger" @click="confirmDelete(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="unitDialog" :style="{width: '450px'}" header="Detail Unit Kerja" :modal="true">
            <div class="flex flex-col gap-4 mt-2">
                <div class="field">
                    <label class="block font-medium mb-2">Nama Unit</label>
                    <InputText v-model="form.nama_unit" required class="w-full" autofocus />
                </div>
                <div class="field">
                    <label class="block font-medium mb-2">Jenis Unit</label>
                    <Select v-model="form.jenis_unit" :options="['Fakultas', 'Program Studi', 'Biro', 'Lembaga']" class="w-full" />
                </div>
                <div class="field">
                    <label class="block font-medium mb-2">Kepala Unit (Dosen/Tendik)</label>
                    <Select v-model="form.kepala_unit_id" :options="users" filter optionLabel="nama_lengkap" optionValue="id" placeholder="Pilih User" class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="unitDialog = false"/>
                <Button label="Simpan" icon="pi pi-check" class="p-button-primary" @click="saveUnit" :loading="form.processing" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteUnitDialog" :style="{width: '450px'}" header="Konfirmasi" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle text-4xl text-orange-500" />
                <span v-if="selectedUnit">Hapus <b>{{selectedUnit.nama_unit}}</b>?</span>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="deleteUnitDialog = false"/>
                <Button label="Hapus" class="p-button-danger" @click="deleteUnit" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
