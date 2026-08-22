<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { route } from '@/lib/route';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';

const props = defineProps({
    periodes: Array,
});

const toast = useToast();
const visible = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    tahun_akademik: '',
    tgl_mulai_audit: null as Date | null,
    tgl_selesai_audit: null as Date | null,
    status: 'Draft',
});

const statusOptions = [
    { label: 'Draft', value: 'Draft' },
    { label: 'Pelaksanaan EDOM', value: 'Pelaksanaan EDOM' },
    { label: 'Audit Lapangan', value: 'Audit Lapangan' },
    { label: 'RTM', value: 'RTM' },
    { label: 'Selesai', value: 'Selesai' },
];

const statusSeverity = (status: string) => {
    const map: Record<string, string> = {
        'Draft': 'secondary',
        'Pelaksanaan EDOM': 'info',
        'Audit Lapangan': 'warn',
        'RTM': 'danger',
        'Selesai': 'success',
    };
    return map[status] ?? 'secondary';
};

function openDialog(item: any = null) {
    if (item) {
        editingId.value = item.id;
        form.tahun_akademik = item.tahun_akademik;
        form.tgl_mulai_audit = new Date(item.tgl_mulai_audit);
        form.tgl_selesai_audit = new Date(item.tgl_selesai_audit);
        form.status = item.status;
    } else {
        editingId.value = null;
        form.reset();
    }
    visible.value = true;
}

function save() {
    if (editingId.value) {
        form.put(route('penetapan.periode.update', editingId.value), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Periode diperbarui', life: 3000 });
                visible.value = false;
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    } else {
        form.post(route('penetapan.periode.store'), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Periode ditambahkan', life: 3000 });
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
    if (confirm('Yakin ingin menghapus periode ini?')) {
        form.delete(route('penetapan.periode.destroy', id), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Periode dihapus', life: 3000 });
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    }
}

function formatDate(date: string) {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout title="Periode AMI">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#00479b] text-white rounded-xl flex items-center justify-center">
                        <i class="pi pi-calendar-plus text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight">Periode AMI</h3>
                        <p class="text-sm text-slate-500">Kelola periode Audit Mutu Internal</p>
                    </div>
                </div>
                <Button label="Tambah Periode" icon="pi pi-plus" @click="openDialog()" class="bg-[#00479b] border-[#00479b]" />
            </div>

            <DataTable :value="periodes" stripedRows responsiveLayout="scroll" emptyMessage="Belum ada data periode">
                <Column field="tahun_akademik" header="Tahun Akademik" sortable />
                <Column header="Rentang Waktu Audit">
                    <template #body="{ data }">
                        {{ formatDate(data.tgl_mulai_audit) }} - {{ formatDate(data.tgl_selesai_audit) }}
                    </template>
                </Column>
                <Column header="Status">
                    <template #body="{ data }">
                        <Tag :value="data.status" :severity="statusSeverity(data.status)" />
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

        <Dialog v-model:visible="visible" :header="editingId ? 'Edit Periode' : 'Tambah Periode'" modal class="w-[450px]">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Tahun Akademik</label>
                    <InputText v-model="form.tahun_akademik" placeholder="Contoh: 2025/2026" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Tanggal Mulai Audit</label>
                    <DatePicker v-model="form.tgl_mulai_audit" dateFormat="dd/mm/yy" placeholder="Pilih tanggal mulai" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Tanggal Selesai Audit</label>
                    <DatePicker v-model="form.tgl_selesai_audit" dateFormat="dd/mm/yy" placeholder="Pilih tanggal selesai" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-slate-700">Status</label>
                    <Select v-model="form.status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Pilih Status" class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" severity="secondary" text @click="visible = false" />
                <Button label="Simpan" icon="pi pi-check" @click="save" :loading="form.processing" class="bg-[#00479b] border-[#00479b]" />
            </template>
        </Dialog>
    </AppLayout>
</template>
