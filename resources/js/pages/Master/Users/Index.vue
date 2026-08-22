<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { route } from '@/lib/route';

const props = defineProps<{
    users: Array<any>;
    roles: Array<any>;
    units: Array<any>;
}>();

const toast = useToast();
const dialogVisible = ref(false);
const deleteDialogVisible = ref(false);
const editingUser = ref<any>(null);
const userToDelete = ref<any>(null);

const emptyForm = {
    nama_lengkap: '',
    email: '',
    password: '',
    jenis_user: '',
    nidn: '',
    role_id: null,
    unit_kerja_id: null,
};

const form = useForm({ ...emptyForm });

const isEditing = computed(() => !!editingUser.value);

const jenisUserOptions = [
    { label: 'Dosen', value: 'Dosen' },
    { label: 'Tendik', value: 'Tendik' },
];

function openCreate() {
    editingUser.value = null;
    form.defaults({ ...emptyForm });
    form.reset();
    dialogVisible.value = true;
}

function openEdit(user: any) {
    editingUser.value = user;
    form.defaults({
        nama_lengkap: user.nama_lengkap,
        email: user.email,
        password: '',
        jenis_user: user.jenis_user,
        nidn: user.nidn,
        role_id: user.role_id,
        unit_kerja_id: user.unit_kerja_id,
    });
    form.reset();
    dialogVisible.value = true;
}

function submit() {
    if (isEditing.value) {
        form.put(route('master.users.update', { id: editingUser.value.id }), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'User diperbarui.', life: 3000 });
                dialogVisible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    } else {
        form.post(route('master.users.store'), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'User ditambahkan.', life: 3000 });
                dialogVisible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    }
}

function confirmDelete(user: any) {
    userToDelete.value = user;
    deleteDialogVisible.value = true;
}

function deleteUser() {
    router.delete(route('master.users.destroy', { id: userToDelete.value.id }), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: 'User dihapus.', life: 3000 });
            deleteDialogVisible.value = false;
        },
        onError: (errors) => {
            const msg = Object.values(errors)[0];
            toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
        },
    });
}

function getRoleBadgeSeverity(roleName: string) {
    const map: Record<string, string> = {
        'Admin/LPM': 'info',
        'Auditor': 'warn',
        'Auditee': 'success',
        'Pimpinan': 'danger',
    };
    return map[roleName] || 'secondary';
}
</script>

<template>
    <AppLayout title="Manajemen Users">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Daftar Users</h2>
                    <p class="text-sm text-slate-500">Kelola data pengguna sistem SPMI</p>
                </div>
                <Button label="Tambah User" icon="pi pi-plus" @click="openCreate" class="bg-[#00479b] border-[#00479b] hover:bg-[#003577]" />
            </div>

            <DataTable :value="users" :rows="10" :paginator="true" stripedRows responsiveLayout="scroll">
                <Column field="nidn" header="NIDN" sortable />
                <Column field="nama_lengkap" header="Nama Lengkap" sortable />
                <Column field="email" header="Email" sortable />
                <Column header="Role" sortable sortField="role.nama_role">
                    <template #body="{ data }">
                        <Badge :value="data.role?.nama_role" :severity="getRoleBadgeSeverity(data.role?.nama_role)" />
                    </template>
                </Column>
                <Column header="Unit Kerja" sortField="unit_kerja.nama_unit">
                    <template #body="{ data }">
                        {{ data.unit_kerja?.nama_unit || '-' }}
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
        <Dialog v-model:visible="dialogVisible" :header="isEditing ? 'Edit User' : 'Tambah User'" modal :style="{ width: '32rem' }" :closable="!form.processing">
            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <InputText id="nama_lengkap" v-model="form.nama_lengkap" class="w-full" :invalid="!!form.errors.nama_lengkap" />
                    <small v-if="form.errors.nama_lengkap" class="text-red-500">{{ form.errors.nama_lengkap }}</small>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <InputText id="email" v-model="form.email" type="email" class="w-full" :invalid="!!form.errors.email" />
                    <small v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</small>
                </div>
                <div v-if="!isEditing">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <Password id="password" v-model="form.password" :feedback="false" toggleMask class="w-full" inputClass="w-full" :invalid="!!form.errors.password" />
                    <small v-if="form.errors.password" class="text-red-500">{{ form.errors.password }}</small>
                </div>
                <div>
                    <label for="nidn" class="block text-sm font-medium text-slate-700 mb-1">NIDN</label>
                    <InputText id="nidn" v-model="form.nidn" class="w-full" :invalid="!!form.errors.nidn" />
                    <small v-if="form.errors.nidn" class="text-red-500">{{ form.errors.nidn }}</small>
                </div>
                <div>
                    <label for="jenis_user" class="block text-sm font-medium text-slate-700 mb-1">Jenis User</label>
                    <Select id="jenis_user" v-model="form.jenis_user" :options="jenisUserOptions" optionLabel="label" optionValue="value" placeholder="Pilih Jenis User" class="w-full" :invalid="!!form.errors.jenis_user" />
                    <small v-if="form.errors.jenis_user" class="text-red-500">{{ form.errors.jenis_user }}</small>
                </div>
                <div>
                    <label for="role_id" class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                    <Select id="role_id" v-model="form.role_id" :options="roles" optionLabel="nama_role" optionValue="id" placeholder="Pilih Role" class="w-full" :invalid="!!form.errors.role_id" />
                    <small v-if="form.errors.role_id" class="text-red-500">{{ form.errors.role_id }}</small>
                </div>
                <div>
                    <label for="unit_kerja_id" class="block text-sm font-medium text-slate-700 mb-1">Unit Kerja</label>
                    <Select id="unit_kerja_id" v-model="form.unit_kerja_id" :options="units" optionLabel="nama_unit" optionValue="id" placeholder="Pilih Unit Kerja" class="w-full" :invalid="!!form.errors.unit_kerja_id" />
                    <small v-if="form.errors.unit_kerja_id" class="text-red-500">{{ form.errors.unit_kerja_id }}</small>
                </div>
            </form>
            <template #footer>
                <Button label="Batal" severity="secondary" text @click="dialogVisible = false" :disabled="form.processing" />
                <Button :label="isEditing ? 'Perbarui' : 'Simpan'" :loading="form.processing" @click="submit" class="bg-[#00479b] border-[#00479b] hover:bg-[#003577]" />
            </template>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:visible="deleteDialogVisible" header="Konfirmasi Hapus" modal :style="{ width: '28rem' }">
            <p class="text-slate-600">Apakah Anda yakin ingin menghapus user <strong>{{ userToDelete?.nama_lengkap }}</strong>?</p>
            <template #footer>
                <Button label="Batal" severity="secondary" text @click="deleteDialogVisible = false" />
                <Button label="Hapus" severity="danger" @click="deleteUser" />
            </template>
        </Dialog>
    </AppLayout>
</template>
