<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { route } from '@/lib/route';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    findings: Array,
});

const toast = useToast();
const selectedPeriodeId = ref(props.selectedPeriodeId);
const selectedFinding = ref(null);
const showDialog = ref(false);

const form = ref({
    rencana_tindak_lanjut: '',
    jadwal_penyelesaian: null,
    akar_masalah: '',
});

watch(selectedPeriodeId, (newId) => {
    router.get(route('pengendalian.isi-rtl.index'), { periode_id: newId }, { preserveState: true });
});

function openDialog(finding) {
    selectedFinding.value = finding;
    form.value = {
        rencana_tindak_lanjut: finding.tindak_lanjut?.rencana_tindak_lanjut ?? '',
        jadwal_penyelesaian: finding.tindak_lanjut?.jadwal_penyelesaian ? new Date(finding.tindak_lanjut.jadwal_penyelesaian) : null,
        akar_masalah: finding.tindak_lanjut?.akar_masalah ?? '',
    };
    showDialog.value = true;
}

function submitForm() {
    if (!form.value.rencana_tindak_lanjut) {
        toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Rencana tindak lanjut wajib diisi', life: 3000 });
        return;
    }
    router.post(route('pengendalian.isi-rtl.store', { kka: selectedFinding.value.id }), {
        rencana_tindak_lanjut: form.value.rencana_tindak_lanjut,
        jadwal_penyelesaian: form.value.jadwal_penyelesaian?.toISOString().split('T')[0] ?? null,
        akar_masalah: form.value.akar_masalah,
    }, {
        onSuccess: () => {
            showDialog.value = false;
            toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Rencana tindak lanjut berhasil disimpan', life: 3000 });
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

function getRtlStatus(finding) {
    if (finding.tindak_lanjut?.rencana_tindak_lanjut) {
        return { label: 'Sudah Diisi', severity: 'success' };
    }
    return { label: 'Belum Diisi', severity: 'warn' };
}
</script>

<template>
    <AppLayout title="Isi RTL">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="pi pi-check-circle text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Isi Rencana Tindak Lanjut</h3>
                    <p class="text-sm text-slate-500">Isi rencana tindak lanjut untuk temuan audit yang memerlukan perbaikan</p>
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
            <DataTable :value="findings" stripedRows paginator :rows="10" :rowsPerPageOptions="[10, 25, 50]" dataKey="id" rowHover
                emptyMessage="Belum ada temuan audit untuk periode ini.">
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <i class="pi pi-inbox text-5xl mb-4"></i>
                        <p class="text-lg font-medium">Tidak ada data</p>
                        <p class="text-sm">Temuan audit belum tersedia untuk periode ini</p>
                    </div>
                </template>
                <Column header="Kode Indikator" style="width: 12rem;">
                    <template #body="{ data }">
                        <span class="font-mono text-sm font-semibold text-slate-700">{{ data.capaian_pelaksanaan?.target_unit?.indikator_mutu?.kode_indikator }}</span>
                    </template>
                </Column>
                <Column header="Isi Standar" style="min-width: 18rem;">
                    <template #body="{ data }">
                        <span class="text-sm text-slate-700">{{ data.capaian_pelaksanaan?.target_unit?.indikator_mutu?.standar?.isi_standar }}</span>
                    </template>
                </Column>
                <Column header="Kategori Temuan" style="width: 14rem;">
                    <template #body="{ data }">
                        <Tag :value="data.kategori_temuan" :severity="getTemuanSeverity(data.kategori_temuan)" />
                    </template>
                </Column>
                <Column header="Deskripsi Temuan" style="min-width: 16rem;">
                    <template #body="{ data }">
                        <span class="text-sm text-slate-600 line-clamp-2">{{ data.deskripsi_temuan }}</span>
                    </template>
                </Column>
                <Column header="Status RTL" style="width: 12rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <Tag :value="getRtlStatus(data).label" :severity="getRtlStatus(data).severity" />
                    </template>
                </Column>
                <Column header="Aksi" style="width: 8rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" rounded text severity="info" @click="openDialog(data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="showDialog" modal header="Isi Rencana Tindak Lanjut" :style="{ width: '40rem' }" :closable="true" :modal="true">
            <template v-if="selectedFinding">
                <div class="mb-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1">Temuan</p>
                    <p class="text-sm font-semibold text-slate-800">
                        {{ selectedFinding.capaian_pelaksanaan?.target_unit?.indikator_mutu?.kode_indikator }} -
                        {{ selectedFinding.kategori_temuan }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1">{{ selectedFinding.deskripsi_temuan }}</p>
                </div>
                <div class="flex flex-col gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Akar Masalah</label>
                        <Textarea v-model="form.akar_masalah" rows="3" class="w-full" placeholder="Tuliskan akar masalah..." autoResize />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rencana Tindak Lanjut <span class="text-red-500">*</span></label>
                        <Textarea v-model="form.rencana_tindak_lanjut" rows="4" class="w-full" placeholder="Tuliskan rencana tindak lanjut..." autoResize />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jadwal Penyelesaian</label>
                        <DatePicker v-model="form.jadwal_penyelesaian" dateFormat="dd/mm/yy" placeholder="Pilih tanggal" class="w-full" showIcon />
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
