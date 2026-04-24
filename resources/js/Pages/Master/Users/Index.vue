<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    users: Array,
    roles: Array,
    units: Array,
});

const toast = useToast();
const userDialog = ref(false);
const deleteUserDialog = ref(false);
const selectedUser = ref(null);

const form = useForm({
    id: null,
    nama_lengkap: '',
    email: '',
    password: '',
    role_id: null,
    unit_kerja_id: null,
    jenis_user: 'Dosen',
    nidn: '',
});

const openNew = () => {
    form.reset();
    userDialog.value = true;
};

const editUser = (user) => {
    form.id = user.id;
    form.nama_lengkap = user.nama_lengkap;
    form.email = user.email;
    form.role_id = user.role_id;
    form.unit_kerja_id = user.unit_kerja_id;
    form.jenis_user = user.jenis_user;
    form.nidn = user.nidn;
    userDialog.value = true;
};

const saveUser = () => {
    if (form.id) {
        form.put(route('master.users.update', form.id), {
            onSuccess: () => {
                userDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'User diperbarui', life: 3000 });
            }
        });
    } else {
        form.post(route('master.users.store'), {
            onSuccess: () => {
                userDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'User dibuat', life: 3000 });
            }
        });
    }
};

const confirmDelete = (user) => {
    selectedUser.value = user;
    deleteUserDialog.value = true;
};

const deleteUser = () => {
    form.delete(route('master.users.destroy', selectedUser.value.id), {
        onSuccess: () => {
            deleteUserDialog.value = false;
            toast.add({ severity: 'success', summary: 'Sukses', detail: 'User dihapus', life: 3000 });
        }
    });
};
</script>

<template>
    <AppLayout title="Manajemen Pengguna">
        <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <Toolbar class="mb-6 bg-transparent border-none p-0">
                <template #start>
                    <Button label="Tambah User" icon="pi pi-plus" class="p-button-success mr-2" @click="openNew" />
                </template>
                <template #end>
                    <IconField iconPosition="left">
                        <InputIcon class="pi pi-search" />
                        <InputText placeholder="Cari user..." />
                    </IconField>
                </template>
            </Toolbar>

            <DataTable :value="users" dataKey="id" :paginator="true" :rows="10" 
                responsiveLayout="scroll" class="p-datatable-sm">
                <Column field="nidn" header="NIDN" sortable style="min-width:8rem"></Column>
                <Column field="nama_lengkap" header="Nama Lengkap" sortable style="min-width:14rem"></Column>
                <Column field="email" header="Email" sortable style="min-width:12rem"></Column>
                <Column field="role.nama_role" header="Role" sortable>
                    <template #body="slotProps">
                        <Badge :value="slotProps.data.role?.nama_role" severity="info" />
                    </template>
                </Column>
                <Column field="unit_kerja.nama_unit" header="Unit Kerja" sortable style="min-width:12rem">
                    <template #body="slotProps">
                        {{ slotProps.data.unit_kerja?.nama_unit || 'Institusi' }}
                    </template>
                </Column>
                <Column :exportable="false" style="min-width:8rem" header="Aksi">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-info mr-2" @click="editUser(slotProps.data)" />
                        <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-danger" @click="confirmDelete(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- User Form Dialog -->
        <Dialog v-model:visible="userDialog" :style="{width: '450px'}" header="Detail Pengguna" :modal="true">
            <div class="flex flex-col gap-4 mt-2">
                <div class="field">
                    <label for="nama" class="block font-medium mb-2">Nama Lengkap</label>
                    <InputText id="nama" v-model="form.nama_lengkap" required class="w-full" autofocus />
                    <small class="text-red-500" v-if="form.errors.nama_lengkap">{{ form.errors.nama_lengkap }}</small>
                </div>
                <div class="field">
                    <label for="email" class="block font-medium mb-2">Email</label>
                    <InputText id="email" v-model="form.email" required class="w-full" />
                    <small class="text-red-500" v-if="form.errors.email">{{ form.errors.email }}</small>
                </div>
                <div class="field" v-if="!form.id">
                    <label for="password" class="block font-medium mb-2">Password</label>
                    <Password id="password" v-model="form.password" class="w-full" toggleMask :feedback="false" />
                    <small class="text-red-500" v-if="form.errors.password">{{ form.errors.password }}</small>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="field">
                        <label class="block font-medium mb-2">Jenis User</label>
                        <Select v-model="form.jenis_user" :options="['Dosen', 'Tenaga Kependidikan']" class="w-full" />
                    </div>
                     <div class="field">
                        <label class="block font-medium mb-2">NIDN (Opsional)</label>
                        <InputText v-model="form.nidn" class="w-full" />
                    </div>
                </div>
                <div class="field">
                    <label class="block font-medium mb-2">Role</label>
                    <Select v-model="form.role_id" :options="roles" optionLabel="nama_role" optionValue="id" placeholder="Pilih Role" class="w-full" />
                    <small class="text-red-500" v-if="form.errors.role_id">{{ form.errors.role_id }}</small>
                </div>
                <div class="field">
                    <label class="block font-medium mb-2">Unit Kerja</label>
                    <Select v-model="form.unit_kerja_id" :options="units" optionLabel="nama_unit" optionValue="id" placeholder="Pilih Unit" class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="userDialog = false"/>
                <Button label="Simpan" icon="pi pi-check" class="p-button-primary" @click="saveUser" :loading="form.processing" />
            </template>
        </Dialog>

        <!-- Delete Confirm Dialog -->
        <Dialog v-model:visible="deleteUserDialog" :style="{width: '450px'}" header="Konfirmasi Hapus" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle text-4xl text-orange-500" />
                <span v-if="selectedUser">Apakah anda yakin ingin menghapus <b>{{selectedUser.nama_lengkap}}</b>?</span>
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" class="p-button-text" @click="deleteUserDialog = false"/>
                <Button label="Ya, Hapus" icon="pi pi-danger" class="p-button-danger" @click="deleteUser" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
