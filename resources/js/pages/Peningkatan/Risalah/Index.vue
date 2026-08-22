<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { route } from '@/lib/route';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    risalahs: Array,
    units: Array,
    stats: Object,
});

const toast = useToast();
const selectedPeriodeId = ref(props.selectedPeriodeId);
const showDialog = ref(false);
const editingRisalah = ref(null);

const form = ref({
    unit_kerja_id: null,
    tgl_rtm: null,
    pimpinan_rapat: '',
    isi_risalah: '',
    keputusan_peningkatan: '',
});

watch(selectedPeriodeId, (newId) => {
    router.get(route('peningkatan.risalah.index'), { periode_id: newId }, { preserveState: true });
});

function openCreateDialog() {
    editingRisalah.value = null;
    form.value = {
        unit_kerja_id: null,
        tgl_rtm: null,
        pimpinan_rapat: '',
        isi_risalah: '',
        keputusan_peningkatan: '',
    };
    showDialog.value = true;
}

function openEditDialog(risalah) {
    editingRisalah.value = risalah;
    form.value = {
        unit_kerja_id: risalah.unit_kerja_id,
        tgl_rtm: risalah.tgl_rtm ? new Date(risalah.tgl_rtm) : null,
        pimpinan_rapat: risalah.pimpinan_rapat ?? '',
        isi_risalah: risalah.isi_risalah ?? '',
        keputusan_peningkatan: risalah.keputusan_peningkatan ?? '',
    };
    showDialog.value = true;
}

function submitForm() {
    if (!form.value.unit_kerja_id || !form.value.isi_risalah) {
        toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Unit kerja dan isi risalah wajib diisi', life: 3000 });
        return;
    }
    const payload = {
        ...form.value,
        tgl_rtm: form.value.tgl_rtm?.toISOString().split('T')[0] ?? null,
    };

    if (editingRisalah.value) {
        router.put(route('peningkatan.risalah.update', { risalah: editingRisalah.value.id }), payload, {
            onSuccess: () => {
                showDialog.value = false;
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Risalah berhasil diperbarui', life: 3000 });
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    } else {
        router.post(route('peningkatan.risalah.store'), payload, {
            onSuccess: () => {
                showDialog.value = false;
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Risalah berhasil ditambahkan', life: 3000 });
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    }
}

function deleteRisalah(risalah) {
    if (confirm('Apakah Anda yakin ingin menghapus risalah ini?')) {
        router.delete(route('peningkatan.risalah.destroy', { risalah: risalah.id }), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Risalah berhasil dihapus', life: 3000 });
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0];
                toast.add({ severity: 'error', summary: 'Gagal', detail: msg || 'Terjadi kesalahan', life: 5000 });
            },
        });
    }
}

function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function getKategoriCount(kategori) {
    return props.stats?.[kategori] ?? 0;
}
</script>

<template>
    <AppLayout title="Risalah RTM">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="pi pi-file-check text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Risalah Rapat Tinjauan Manajemen</h3>
                    <p class="text-sm text-slate-500">Dokumen hasil rapat tinjauan manajemen dan keputusan peningkatan</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <Button label="Tambah Risalah" icon="pi pi-plus" @click="openCreateDialog" />
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="pi pi-check text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Sesuai / Melampaui</p>
                    <p class="text-lg font-bold text-slate-800">{{ getKategoriCount('Sesuai') + getKategoriCount('Melampaui') }}</p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center">
                    <i class="pi pi-exclamation-triangle text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Observasi (OB)</p>
                    <p class="text-lg font-bold text-slate-800">{{ getKategoriCount('Observasi (OB)') }}</p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                    <i class="pi pi-times-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">KTS Minor / Mayor</p>
                    <p class="text-lg font-bold text-slate-800">{{ getKategoriCount('KTS Minor') + getKategoriCount('KTS Mayor') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <DataTable :value="risalahs" stripedRows paginator :rows="10" :rowsPerPageOptions="[10, 25, 50]" dataKey="id" rowHover
                emptyMessage="Belum ada risalah untuk periode ini.">
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <i class="pi pi-inbox text-5xl mb-4"></i>
                        <p class="text-lg font-medium">Tidak ada data</p>
                        <p class="text-sm">Risalah RTM belum tersedia untuk periode ini</p>
                    </div>
                </template>
                <Column header="Unit Kerja" style="min-width: 16rem;">
                    <template #body="{ data }">
                        <span class="font-semibold text-slate-800">{{ data.unit_kerja?.nama_unit }}</span>
                    </template>
                </Column>
                <Column header="Tanggal RTM" style="width: 12rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <span class="text-sm text-slate-600">{{ formatDate(data.tgl_rtm) }}</span>
                    </template>
                </Column>
                <Column header="Pimpinan Rapat" style="width: 14rem;">
                    <template #body="{ data }">
                        <span class="text-sm text-slate-700">{{ data.pimpinan_rapat }}</span>
                    </template>
                </Column>
                <Column header="Isi Risalah" style="min-width: 20rem;">
                    <template #body="{ data }">
                        <span class="text-sm text-slate-600 line-clamp-2">{{ data.isi_risalah }}</span>
                    </template>
                </Column>
                <Column header="Keputusan" style="min-width: 18rem;">
                    <template #body="{ data }">
                        <span class="text-sm text-slate-600 line-clamp-2">{{ data.keputusan_peningkatan }}</span>
                    </template>
                </Column>
                <Column header="Aksi" style="width: 10rem;" bodyClass="text-center">
                    <template #body="{ data }">
                        <div class="flex items-center justify-center gap-1">
                            <Button icon="pi pi-pencil" rounded text severity="info" @click="openEditDialog(data)" />
                            <Button icon="pi pi-trash" rounded text severity="danger" @click="deleteRisalah(data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="showDialog" :modal="true" :header="editingRisalah ? 'Edit Risalah' : 'Tambah Risalah Baru'" :style="{ width: '42rem' }" :closable="true">
            <div class="flex flex-col gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Unit Kerja <span class="text-red-500">*</span></label>
                    <Select v-model="form.unit_kerja_id" :options="units" optionLabel="nama_unit" optionValue="id" placeholder="Pilih Unit Kerja" class="w-full" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal RTM</label>
                    <DatePicker v-model="form.tgl_rtm" dateFormat="dd/mm/yy" placeholder="Pilih tanggal" class="w-full" showIcon />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pimpinan Rapat</label>
                    <InputText v-model="form.pimpinan_rapat" class="w-full" placeholder="Nama pimpinan rapat" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Isi Risalah <span class="text-red-500">*</span></label>
                    <Textarea v-model="form.isi_risalah" rows="4" class="w-full" placeholder="Tuliskan isi risalah rapat..." autoResize />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keputusan Peningkatan</label>
                    <Textarea v-model="form.keputusan_peningkatan" rows="4" class="w-full" placeholder="Tuliskan keputusan peningkatan..." autoResize />
                </div>
            </div>
            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button label="Batal" text severity="secondary" @click="showDialog = false" />
                    <Button label="Simpan" icon="pi pi-check" @click="submitForm" :loading="$page.props.processing" />
                </div>
            </template>
        </Dialog>
    </AppLayout>
</template>
