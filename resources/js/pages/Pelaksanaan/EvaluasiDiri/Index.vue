<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { route } from '@/lib/route';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    targets: Array,
});

const page = usePage();
const toast = useToast();

const selectedPeriodeId = ref(props.selectedPeriodeId);
const selectedTarget = ref(null);
const showDialog = ref(false);

const form = ref({
    nilai_aktual: null,
    evaluasi_diri: '',
    link_dokumen_bukti: '',
});

const units = computed(() => {
    const map = new Map();
    props.targets.forEach((t: any) => {
        if (t.unit_kerja && !map.has(t.unit_kerja.id)) {
            map.set(t.unit_kerja.id, t.unit_kerja);
        }
    });
    return Array.from(map.values());
});

watch(selectedPeriodeId, (newId) => {
    router.get(route('pelaksanaan.evaluasi-diri.index'), { periode_id: newId }, { preserveState: true });
});

function openDialog(target) {
    selectedTarget.value = target;
    form.value = {
        nilai_aktual: target.capaian_pelaksanaan?.nilai_aktual ?? null,
        evaluasi_diri: target.capaian_pelaksanaan?.evaluasi_diri ?? '',
        link_dokumen_bukti: target.capaian_pelaksanaan?.link_dokumen_bukti ?? '',
    };
    showDialog.value = true;
}

function submitForm() {
    if (!form.value.nilai_aktual && form.value.nilai_aktual !== 0) {
        toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Nilai aktual wajib diisi', life: 3000 });
        return;
    }
    router.post(route('pelaksanaan.evaluasi-diri.store', { targetUnit: selectedTarget.value.id }), form.value, {
        onSuccess: () => {
            showDialog.value = false;
            toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Evaluasi diri berhasil disimpan', life: 3000 });
        },
        onError: (errors) => {
            const msg = Object.values(errors)[0];
            toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
        },
    });
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
    <AppLayout title="Evaluasi Diri">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="pi pi-file-edit text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Evaluasi Diri Pelaksanaan</h3>
                    <p class="text-sm text-slate-500">Isi capaian pelaksanaan untuk indikator mutu yang telah ditetapkan</p>
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
            <DataTable :value="targets" stripedRows paginator :rows="10" :rowsPerPageOptions="[10, 25, 50]" dataKey="id" rowHover
                emptyMessage="Belum ada target indikator mutu untuk periode ini.">
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <i class="pi pi-inbox text-5xl mb-4"></i>
                        <p class="text-lg font-medium">Tidak ada data</p>
                        <p class="text-sm">Target indikator mutu belum tersedia untuk periode ini</p>
                    </div>
                </template>
                <Column field="indikator_mutu.kode_indikator" header="Kode Indikator" style="width: 12rem;" />
                <Column header="Isi Standar" style="min-width: 20rem;">
                    <template #body="{ data }">
                        <span class="text-sm text-slate-700">{{ data.indikator_mutu?.standar?.isi_standar }}</span>
                    </template>
                </Column>
                <Column header="Kategori" style="width: 14rem;">
                    <template #body="{ data }">
                        <span :class="[getCategoryColor(data.indikator_mutu?.standar?.kategori?.nama_kategori), 'inline-flex px-2.5 py-1 text-xs font-semibold rounded-full']">
                            {{ data.indikator_mutu?.standar?.kategori?.nama_kategori }}
                        </span>
                    </template>
                </Column>
                <Column header="Target" style="width: 10rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <span class="font-semibold text-slate-800">{{ data.nilai_target }}</span>
                        <span class="text-slate-500 text-sm ml-1">{{ data.satuan }}</span>
                    </template>
                </Column>
                <Column header="Capaian" style="width: 10rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <span v-if="data.capaian_pelaksanaan" class="font-semibold text-blue-600">
                            {{ data.capaian_pelaksanaan.nilai_aktual }}
                        </span>
                        <span v-else class="text-slate-400">-</span>
                    </template>
                </Column>
                <Column header="Status" style="width: 10rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <Tag v-if="data.capaian_pelaksanaan?.status === 'Submitted'" value="Submitted" severity="success" />
                        <Tag v-else value="Open" severity="warn" />
                    </template>
                </Column>
                <Column header="Aksi" style="width: 8rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" rounded text severity="info" @click="openDialog(data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="showDialog" modal header="Isi Evaluasi Diri" :style="{ width: '36rem' }" :closable="true" :modal="true">
            <template v-if="selectedTarget">
                <div class="mb-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1">Indikator</p>
                    <p class="text-sm font-semibold text-slate-800">{{ selectedTarget.indikator_mutu?.kode_indikator }} - {{ selectedTarget.indikator_mutu?.standar?.isi_standar }}</p>
                </div>
                <div class="flex flex-col gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nilai Aktual <span class="text-red-500">*</span></label>
                        <InputNumber v-model="form.nilai_aktual" :min="0" class="w-full" :class="{ 'p-invalid': !form.nilai_aktual && form.nilai_aktual !== 0 }" placeholder="Masukkan nilai aktual" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Evaluasi Diri</label>
                        <Textarea v-model="form.evaluasi_diri" rows="4" class="w-full" placeholder="Tuliskan evaluasi diri..." autoResize />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Link Dokumen Bukti</label>
                        <InputText v-model="form.link_dokumen_bukti" class="w-full" placeholder="https://..." />
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
