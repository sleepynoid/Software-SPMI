<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { route } from '@/lib/route';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';

const props = defineProps({
    periodes: Array,
    active_periode: Object,
});

const toast = useToast();
const selectedPeriodeId = ref(props.active_periode?.id ?? null);
const fileName = ref('');

const form = useForm({
    file: null as File | null,
    periode_id: null as number | null,
});

function onFileSelect(event: any) {
    const file = event.files?.[0];
    if (!file) return;
    fileName.value = file.name;
    form.file = file;
}

function submitImport() {
    if (!selectedPeriodeId.value) {
        toast.add({ severity: 'warn', summary: 'Perhatian', detail: 'Pilih periode terlebih dahulu', life: 3000 });
        return;
    }

    if (!form.file) {
        toast.add({ severity: 'warn', summary: 'Perhatian', detail: 'Upload file terlebih dahulu', life: 3000 });
        return;
    }

    form.periode_id = selectedPeriodeId.value;

    form.post(route('penetapan.standar.import.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data standar berhasil diimpor', life: 3000 });
            form.reset();
            fileName.value = '';
        },
        onError: (errors) => {
            const msg = Object.values(errors)[0];
            toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
        },
    });
}
</script>

<template>
    <AppLayout title="Import Standar">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#00479b] text-white rounded-xl flex items-center justify-center">
                        <i class="pi pi-file-import text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight">Import Standar dari Excel</h3>
                        <p class="text-sm text-slate-500">Unggah file Excel (.xlsx) untuk impor data standar dan indikator</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a :href="route('penetapan.standar.import.template')">
                        <Button label="Unduh Template" icon="pi pi-download" severity="secondary" outlined />
                    </a>
                    <Select
                        v-model="selectedPeriodeId"
                        :options="periodes"
                        optionLabel="tahun_akademik"
                        optionValue="id"
                        placeholder="Pilih Periode"
                        class="w-64"
                    />
                </div>
            </div>

            <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center mb-6 hover:border-[#00479b] transition-colors">
                <FileUpload
                    mode="basic"
                    accept=".xlsx,.xls"
                    :maxFileSize="10000000"
                    @select="onFileSelect"
                    chooseLabel="Pilih File Excel"
                    class="w-full"
                />
                <p v-if="fileName" class="mt-3 text-sm text-slate-600">
                    <i class="pi pi-file mr-1"></i> {{ fileName }}
                </p>
                <p v-else class="mt-3 text-sm text-slate-400">Format: Kategori | Nama Standar | Kode Indikator | Isi Indikator | Jenis | Target | Satuan | Unit Kerja</p>
            </div>

            <div v-if="form.file" class="flex justify-end">
                <Button
                    label="Impor Data"
                    icon="pi pi-upload"
                    @click="submitImport"
                    :loading="form.processing"
                    class="bg-[#00479b] border-[#00479b]"
                />
            </div>
        </div>
    </AppLayout>
</template>
