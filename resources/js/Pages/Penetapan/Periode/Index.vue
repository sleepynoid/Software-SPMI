<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    periodes: Array,
});

const toast = useToast();
const periodeDialog = ref(false);
const statusOptions = ['Draft', 'Pelaksanaan EDOM', 'Audit Lapangan', 'RTM', 'Selesai'];

const form = useForm({
    id: null,
    tahun_akademik: '',
    tgl_mulai_audit: '',
    tgl_selesai_audit: '',
    status: 'Draft',
});

const openNew = () => {
    form.reset();
    periodeDialog.value = true;
};

const editPeriode = (p) => {
    form.id = p.id;
    form.tahun_akademik = p.tahun_akademik;
    form.tgl_mulai_audit = p.tgl_mulai_audit;
    form.tgl_selesai_audit = p.tgl_selesai_audit;
    form.status = p.status;
    periodeDialog.value = true;
};

const savePeriode = () => {
    if (form.id) {
        form.put(route('penetapan.periode.update', form.id), {
            onSuccess: () => {
                periodeDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Periode diperbarui', life: 3000 });
            }
        });
    } else {
        form.post(route('penetapan.periode.store'), {
            onSuccess: () => {
                periodeDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Periode dibuka', life: 3000 });
            }
        });
    }
};

const getStatusSeverity = (status) => {
    switch (status) {
        case 'Draft': return 'secondary';
        case 'Pelaksanaan EDOM': return 'info';
        case 'Audit Lapangan': return 'warning';
        case 'RTM': return 'help';
        case 'Selesai': return 'success';
        default: return null;
    }
};
</script>

<template>
    <AppLayout title="Manajemen Periode AMI">
        <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <Toolbar class="mb-6 bg-transparent border-none p-0">
                <template #start>
                    <Button label="Buka Periode AMI Baru" icon="pi pi-plus" class="p-button-success" @click="openNew" />
                </template>
            </Toolbar>

            <DataTable :value="periodes" dataKey="id" responsiveLayout="scroll">
                <Column field="tahun_akademik" header="Tahun Akademik" sortable style="min-width:12rem">
                    <template #body="slotProps">
                        <span class="font-bold text-slate-700">{{ slotProps.data.tahun_akademik }}</span>
                    </template>
                </Column>
                <Column header="Rentang Waktu" style="min-width:18rem">
                    <template #body="slotProps">
                        <span class="text-sm">
                            <i class="pi pi-calendar mr-2 text-slate-400"></i>
                            {{ slotProps.data.tgl_mulai_audit }} s/d {{ slotProps.data.tgl_selesai_audit }}
                        </span>
                    </template>
                </Column>
                <Column field="status" header="Status" sortable>
                    <template #body="slotProps">
                        <Tag :severity="getStatusSeverity(slotProps.data.status)" :value="slotProps.data.status" />
                    </template>
                </Column>
                <Column header="Aksi">
                    <template #body="slotProps">
                        <Button icon="pi pi-cog" label="Kelola" class="p-button-text p-button-info" @click="editPeriode(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="periodeDialog" :style="{width: '500px'}" header="Detail Periode AMI" :modal="true">
            <div class="flex flex-col gap-6 mt-4">
                <div class="field">
                    <label class="block font-medium mb-2">Tahun Akademik</label>
                    <InputText v-model="form.tahun_akademik" placeholder="Contoh: Ganjil 2024/2025" class="w-full" required />
                    <small class="text-red-500" v-if="form.errors.tahun_akademik">{{ form.errors.tahun_akademik }}</small>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="field">
                        <label class="block font-medium mb-2">Tgl Mulai Audit</label>
                        <DatePicker v-model="form.tgl_mulai_audit" dateFormat="yy-mm-dd" class="w-full" />
                    </div>
                    <div class="field">
                        <label class="block font-medium mb-2">Tgl Selesai Audit</label>
                        <DatePicker v-model="form.tgl_selesai_audit" dateFormat="yy-mm-dd" class="w-full" />
                    </div>
                </div>
                <div class="field">
                    <label class="block font-medium mb-2">Status Siklus PPEPP</label>
                    <Select v-model="form.status" :options="statusOptions" class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="periodeDialog = false"/>
                <Button label="Simpan Perubahan" icon="pi pi-check" @click="savePeriode" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
