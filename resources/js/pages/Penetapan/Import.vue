<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { route } from '@/lib/route';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import * as XLSX from 'xlsx';

const props = defineProps({
    periodes: Array,
    active_periode: Object,
});

const toast = useToast();
const selectedPeriodeId = ref(props.active_periode?.id ?? null);
const uploadedData = ref<any[]>([]);
const fileName = ref('');
const isLoading = ref(false);

const form = useForm({
    periode_id: null as number | null,
    data: [] as any[],
});

function onFileSelect(event: any) {
    const file = event.files?.[0];
    if (!file) return;

    fileName.value = file.name;
    isLoading.value = true;

    const reader = new FileReader();
    reader.onload = (e: any) => {
        try {
            const wb = XLSX.read(e.target.result, { type: 'array' });
            const ws = wb.Sheets[wb.SheetNames[0]];
            const jsonData = readXlsx.utils.sheet_to_json(ws);

            uploadedData.value = jsonData.map((row: any) => ({
                kategori: row['Kategori'] ?? '',
                nama_standar: row['Nama Standar'] ?? '',
                kode_indikator: row['Kode Indikator'] ?? '',
                isi_indikator: row['Isi Indikator'] ?? '',
                jenis: row['Jenis'] ?? '',
                target: row['Target'] ?? '',
                satuan: row['Satuan'] ?? '',
                unit_kerja: row['Unit Kerja'] ?? '',
            }));

            toast.add({ severity: 'info', summary: 'File dibaca', detail: `${uploadedData.value.length} baris data ditemukan`, life: 3000 });
        } catch (err) {
            toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal membaca file Excel', life: 3000 });
        } finally {
            isLoading.value = false;
        }
    };
    reader.readAsArrayBuffer(file);
}

function submitImport() {
    if (!selectedPeriodeId.value) {
        toast.add({ severity: 'warn', summary: 'Perhatian', detail: 'Pilih periode terlebih dahulu', life: 3000 });
        return;
    }

    if (uploadedData.value.length === 0) {
        toast.add({ severity: 'warn', summary: 'Perhatian', detail: 'Upload file terlebih dahulu', life: 3000 });
        return;
    }

    form.periode_id = selectedPeriodeId.value;
    form.data = uploadedData.value;

    form.post(route('penetapan.standar.import.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data standar berhasil diimpor', life: 3000 });
            uploadedData.value = [];
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
                    <span class="ml-2 text-green-600 font-medium">({{ uploadedData.length }} baris)</span>
                </p>
                <p v-else class="mt-3 text-sm text-slate-400">Format: Kategori | Nama Standar | Kode Indikator | Isi Indikator | Jenis | Target | Satuan | Unit Kerja</p>
            </div>

            <DataTable v-if="uploadedData.length > 0" :value="uploadedData" stripedRows responsiveLayout="scroll" class="mb-6" emptyMessage="Tidak ada data">
                <Column field="kategori" header="Kategori" />
                <Column field="nama_standar" header="Nama Standar" />
                <Column field="kode_indikator" header="Kode Indikator" />
                <Column field="isi_indikator" header="Isi Indikator" />
                <Column field="jenis" header="Jenis" />
                <Column field="target" header="Target" />
                <Column field="satuan" header="Satuan" />
                <Column field="unit_kerja" header="Unit Kerja" />
            </DataTable>

            <div v-if="uploadedData.length > 0" class="flex justify-end">
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
