<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    risalahs: Array,
    units: Array,
    stats: Object,
});

const toast = useToast();
const rtmDialog = ref(false);
const activePeriodeId = ref(props.selectedPeriodeId);

const form = useForm({
    id: null,
    periode_id: props.selectedPeriodeId,
    unit_kerja_id: null,
    tgl_rtm: new Date().toISOString().slice(0, 10),
    pimpinan_rapat: '',
    isi_risalah: '',
    keputusan_peningkatan: '',
});

const openNew = () => {
    form.reset();
    form.periode_id = activePeriodeId.value;
    rtmDialog.value = true;
};

const editRisalah = (r) => {
    form.id = r.id;
    form.periode_id = r.periode_id;
    form.unit_kerja_id = r.unit_kerja_id;
    form.tgl_rtm = r.tgl_rtm;
    form.pimpinan_rapat = r.pimpinan_rapat;
    form.isi_risalah = r.isi_risalah;
    form.keputusan_peningkatan = r.keputusan_peningkatan;
    rtmDialog.value = true;
};

const saveRisalah = () => {
    if (form.id) {
        form.put(route('peningkatan.risalah.update', form.id), {
            onSuccess: () => {
                rtmDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Risalah diperbarui', life: 3000 });
            }
        });
    } else {
        form.post(route('peningkatan.risalah.store'), {
            onSuccess: () => {
                rtmDialog.value = false;
                toast.add({ severity: 'success', summary: 'Sukses', detail: 'Risalah disimpan', life: 3000 });
            }
        });
    }
};

const handlePeriodeChange = (id) => {
    router.get(route('peningkatan.risalah.index'), { periode_id: id });
};
</script>

<template>
    <AppLayout title="Rapat Tinjauan Manajemen (RTM)">
        <div class="flex flex-col gap-6">
            <!-- Stats Bar -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total KTS Mayor</p>
                    <p class="text-3xl font-black text-red-600">{{ stats['KTS Mayor'] || 0 }}</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total KTS Minor</p>
                    <p class="text-3xl font-black text-orange-600">{{ stats['KTS Minor'] || 0 }}</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total Observasi</p>
                    <p class="text-3xl font-black text-blue-600">{{ stats['Observasi (OB)'] || 0 }}</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Sesuai/Melampaui</p>
                    <p class="text-3xl font-black text-green-600">{{ (stats['Sesuai'] || 0) + (stats['Melampaui'] || 0) }}</p>
                </div>
            </div>

            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Daftar Risalah RTM</h3>
                    <p class="text-sm text-slate-500">Hasil rapat koordinasi peningkatan mutu internal</p>
                </div>
                <div class="flex items-center gap-4">
                    <Select v-model="activePeriodeId" :options="periodes" optionLabel="tahun_akademik" optionValue="id" @change="handlePeriodeChange($event.value)" class="w-64" />
                    <Button label="Buat Risalah" icon="pi pi-plus" @click="openNew" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="r in risalahs" :key="r.id" class="card bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:border-primary-300 transition-all">
                    <div class="p-6 border-b border-slate-50 flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-slate-800 text-lg">{{ r.unit_kerja?.nama_unit }}</h4>
                            <p class="text-xs text-slate-400 font-medium">Tanggal: {{ r.tgl_rtm }}</p>
                        </div>
                        <div class="flex gap-2">
                             <Button icon="pi pi-pencil" class="p-button-text p-button-info p-button-sm" @click="editRisalah(r)" />
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1 italic">Hasil Pembahasan</p>
                            <p class="text-sm text-slate-600 line-clamp-3 italic">"{{ r.isi_risalah }}"</p>
                        </div>
                        <div class="p-4 bg-green-50/50 rounded-xl border border-green-100">
                             <p class="text-[10px] font-bold text-green-600 uppercase mb-1">Keputusan Peningkatan</p>
                             <p class="text-sm text-green-800 font-bold leading-tight">{{ r.keputusan_peningkatan }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="risalahs.length === 0" class="p-20 text-center card bg-white rounded-2xl border border-dashed border-slate-300">
                <i class="pi pi-comments text-4xl text-slate-200 mb-4"></i>
                <p class="text-slate-400 italic">Belum ada risalah RTM yang dicatat untuk periode ini.</p>
            </div>
        </div>

        <Dialog v-model:visible="rtmDialog" :style="{width: '700px'}" header="Detail Risalah RTM" :modal="true">
            <div class="flex flex-col gap-6 mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field">
                        <label class="block font-medium mb-2">Unit Kerja / Prodi</label>
                        <Select v-model="form.unit_kerja_id" :options="units" optionLabel="nama_unit" optionValue="id" class="w-full" :disabled="!!form.id" required />
                    </div>
                    <div class="field">
                        <label class="block font-medium mb-2">Tanggal Rapat</label>
                        <DatePicker v-model="form.tgl_rtm" dateFormat="yy-mm-dd" class="w-full" required />
                    </div>
                </div>

                <div class="field">
                    <label class="block font-medium mb-2">Pimpinan Rapat</label>
                    <InputText v-model="form.pimpinan_rapat" class="w-full" placeholder="Contoh: Rektor / Wakil Rektor 1" required />
                </div>

                <div class="field">
                    <label class="block font-medium mb-2">Risalah / Notulensi Pembahasan</label>
                    <Textarea v-model="form.isi_risalah" rows="6" class="w-full" placeholder="Ringkasan apa yang dibahas dalam rapat terkait unit ini..." required />
                </div>

                <div class="field">
                    <label class="block font-medium mb-2 text-primary-700">Keputusan / Instruksi Peningkatan Mutu</label>
                    <Textarea v-model="form.keputusan_peningkatan" rows="4" class="w-full border-primary-100 bg-primary-50/20" placeholder="Keputusan final pimpinan untuk peningkatan unit ini..." required />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="rtmDialog = false"/>
                <Button label="Simpan Risalah" icon="pi pi-check" @click="saveRisalah" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
