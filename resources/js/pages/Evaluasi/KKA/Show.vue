<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { route } from '@/lib/route';

const props = defineProps({
    unit: Object,
    periodes: Array,
    selectedPeriodeId: Number,
    auditData: Object,
});

const toast = useToast();
const selectedPeriodeId = ref(props.selectedPeriodeId);
const selectedCapaian = ref(null);
const showDialog = ref(false);

const form = ref({
    kategori_temuan: '',
    deskripsi_temuan: '',
});

const temuanOptions = ref([
    { label: 'Sesuai', value: 'Sesuai' },
    { label: 'Melampaui', value: 'Melampaui' },
    { label: 'Observasi (OB)', value: 'Observasi (OB)' },
    { label: 'KTS Minor', value: 'KTS Minor' },
    { label: 'KTS Mayor', value: 'KTS Mayor' },
]);

watch(selectedPeriodeId, (newId) => {
    router.get(route('evaluasi.kka.show', { unit: props.unit.id }), { periode_id: newId }, { preserveState: true });
});

function openDialog(capaian) {
    selectedCapaian.value = capaian;
    form.value = {
        kategori_temuan: capaian.audit?.kategori_temuan ?? '',
        deskripsi_temuan: capaian.audit?.deskripsi_temuan ?? '',
    };
    showDialog.value = true;
}

function submitForm() {
    if (!form.value.kategori_temuan) {
        toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Kategori temuan wajib dipilih', life: 3000 });
        return;
    }
    router.post(route('evaluasi.kka.store', { capaian: selectedCapaian.value.id }), {
        unit_kerja_id: props.unit.id,
        kategori_temuan: form.value.kategori_temuan,
        deskripsi_temuan: form.value.deskripsi_temuan,
    }, {
        onSuccess: () => {
            showDialog.value = false;
            toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data audit berhasil disimpan', life: 3000 });
        },
        onError: (errors) => {
            const msg = Object.values(errors)[0];
            toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
        },
    });
}

function getTemuanSeverity(kategori) {
    const map = {
        'Sesuai': 'success',
        'Melampaui': 'info',
        'Observasi (OB)': 'warn',
        'KTS Minor': 'danger',
        'KTS Mayor': 'danger',
    };
    return map[kategori] || 'secondary';
}

function getCategoryColor(kategori) {
    const colors = {
        'Pendidikan Teaching Learning': 'bg-blue-100 text-blue-700',
        'Penelitian Pengabdian': 'bg-green-100 text-green-700',
        'Kemahasiswaan': 'bg-purple-100 text-purple-700',
        'Sumber Daya': 'bg-orange-100 text-orange-700',
        'Lainnya': 'bg-slate-100 text-slate-700',
    };
    return colors[kategori] || 'bg-slate-100 text-slate-700';
}
</script>

<template>
    <AppLayout title="Kartu Kerja Audit">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="pi pi-search text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Kartu Kerja Audit (KKA)</h3>
                    <p class="text-sm text-slate-500">Audit capaian untuk unit: <strong class="text-slate-700">{{ unit?.nama_unit }}</strong></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Periode:</span>
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

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <DataTable :value="auditData?.data || []" stripedRows paginator :rows="10" :rowsPerPageOptions="[10, 25, 50]" dataKey="id" rowHover
                emptyMessage="Belum ada data capaian untuk unit ini.">
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <i class="pi pi-inbox text-5xl mb-4"></i>
                        <p class="text-lg font-medium">Tidak ada data</p>
                        <p class="text-sm">Data capaian pelaksanaan belum tersedia</p>
                    </div>
                </template>
                <Column header="Kode Indikator" style="width: 12rem;">
                    <template #body="{ data }">
                        <span class="font-mono text-sm font-semibold text-slate-700">{{ data.target_unit?.indikator_mutu?.kode_indikator }}</span>
                    </template>
                </Column>
                <Column header="Isi Standar" style="min-width: 20rem;">
                    <template #body="{ data }">
                        <span class="text-sm text-slate-700">{{ data.target_unit?.indikator_mutu?.standar?.isi_standar }}</span>
                    </template>
                </Column>
                <Column header="Kategori" style="width: 14rem;">
                    <template #body="{ data }">
                        <span :class="[getCategoryColor(data.target_unit?.indikator_mutu?.standar?.kategori?.nama_kategori), 'inline-flex px-2.5 py-1 text-xs font-semibold rounded-full']">
                            {{ data.target_unit?.indikator_mutu?.standar?.kategori?.nama_kategori }}
                        </span>
                    </template>
                </Column>
                <Column header="Capaian Aktual" style="width: 10rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <span class="font-semibold text-blue-600">{{ data.target_unit?.capaian_pelaksanaan?.nilai_aktual ?? '-' }}</span>
                    </template>
                </Column>
                <Column header="Temuan" style="width: 14rem;">
                    <template #body="{ data }">
                        <Tag v-if="data.audit?.kategori_temuan" :value="data.audit.kategori_temuan" :severity="getTemuanSeverity(data.audit.kategori_temuan)" />
                        <span v-else class="text-slate-400">-</span>
                    </template>
                </Column>
                <Column header="Aksi" style="width: 8rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" rounded text severity="info" @click="openDialog(data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="showDialog" modal header="Isi Audit KKA" :style="{ width: '36rem' }" :closable="true" :modal="true">
            <template v-if="selectedCapaian">
                <div class="mb-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1">Indikator</p>
                    <p class="text-sm font-semibold text-slate-800">{{ selectedCapaian.target_unit?.indikator_mutu?.kode_indikator }} - {{ selectedCapaian.target_unit?.indikator_mutu?.standar?.isi_standar }}</p>
                </div>
                <div class="flex flex-col gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori Temuan <span class="text-red-500">*</span></label>
                        <Select v-model="form.kategori_temuan" :options="temuanOptions" optionLabel="label" optionValue="value" placeholder="Pilih Kategori Temuan" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Temuan</label>
                        <Textarea v-model="form.deskripsi_temuan" rows="4" class="w-full" placeholder="Tuliskan deskripsi temuan..." autoResize />
                    </div>
                </div>
            </template>
            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button label="Batal" text severity="secondary" @click="showDialog = false" />
                    <Button label="Simpan" icon="pi pi-check" @click="submitForm" :loading="$page.props.processing" />
                </div>
            </template>
        </Dialog>
    </AppLayout>
</template>
