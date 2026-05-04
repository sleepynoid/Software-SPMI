<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    targets: Array,
});

const toast = useToast();
const reportDialog = ref(false);
const activePeriodeId = ref(props.selectedPeriodeId);
const selectedTarget = ref(null);

const form = useForm({
    nilai_aktual: 0,
    evaluasi_diri: '',
    link_dokumen_bukti: '',
});

const openReport = (target) => {
    selectedTarget.value = target;
    form.nilai_aktual = target.capaian_pelaksanaan?.nilai_aktual || 0;
    form.evaluasi_diri = target.capaian_pelaksanaan?.evaluasi_diri || '';
    form.link_dokumen_bukti = target.capaian_pelaksanaan?.link_dokumen_bukti || '';
    reportDialog.value = true;
};

const submitReport = () => {
    // Validasi URL sederhana
    const urlPattern = /^(http|https):\/\/[^ "]+$/;
    if (!urlPattern.test(form.link_dokumen_bukti)) {
        toast.add({ 
            severity: 'error', 
            summary: 'Link Tidak Valid', 
            detail: 'Pastikan link dimulai dengan http:// atau https://', 
            life: 5000 
        });
        return;
    }

    form.post(route('pelaksanaan.evaluasi-diri.store', selectedTarget.value.id), {
        onSuccess: () => {
            reportDialog.value = false;
            toast.add({ severity: 'success', summary: 'Sukses', detail: 'Laporan tersimpan', life: 3000 });
        }
    });
};

const handlePeriodeChange = (id) => {
    router.get(route('pelaksanaan.evaluasi-diri.index'), { periode_id: id });
};

const getStatus = (target) => {
    if (!target.capaian_pelaksanaan) return { label: 'Belum Lapor', severity: 'danger' };
    return { label: 'Sudah Lapor', severity: 'success' };
};
</script>

<template>
    <AppLayout title="Evaluasi Diri Prodi">
        <div class="flex flex-col gap-6">
            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Laporan Capaian Mutu</h3>
                    <p class="text-sm text-slate-500">Laporkan realisasi capaian untuk setiap indikator yang ditargetkan</p>
                </div>
                <Select v-model="activePeriodeId" :options="periodes" optionLabel="tahun_akademik" optionValue="id" @change="handlePeriodeChange($event.value)" class="w-64" />
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div v-for="t in targets" :key="t.id" class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row gap-6 hover:border-primary-200 transition-all">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-bold uppercase tracking-wider">
                                {{ t.indikator_mutu?.standar?.kategori?.nama_kategori }}
                            </span>
                            <span class="text-xs font-bold text-primary-600">{{ t.indikator_mutu?.kode_indikator }}</span>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-2">{{ t.indikator_mutu?.standar?.nama_standar }}</h4>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">{{ t.indikator_mutu?.isi_standar }}</p>
                        
                        <div class="flex flex-wrap gap-6 pt-4 border-t border-slate-50">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Target</p>
                                <p class="text-lg font-black text-slate-700">{{ t.nilai_target }} <span class="text-xs font-medium">{{ t.satuan }}</span></p>
                            </div>
                             <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Realisasi</p>
                                <p class="text-lg font-black" :class="t.capaian_pelaksanaan ? 'text-green-600' : 'text-slate-300'">
                                    {{ t.capaian_pelaksanaan?.nilai_aktual || '0' }} <span class="text-xs font-medium">{{ t.satuan }}</span>
                                </p>
                            </div>
                            <div class="ml-auto flex items-center">
                                <Tag :severity="getStatus(t).severity" :value="getStatus(t).label" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:w-48 flex flex-col justify-center border-l-0 md:border-l border-slate-100 pl-0 md:pl-6">
                        <Button 
                            :label="t.capaian_pelaksanaan ? 'Edit Laporan' : 'Isi Capaian'" 
                            :icon="t.capaian_pelaksanaan ? 'pi pi-pencil' : 'pi pi-plus'" 
                            :severity="t.capaian_pelaksanaan ? 'secondary' : 'primary'"
                            class="w-full mb-2"
                            @click="openReport(t)"
                        />
                        <a v-if="t.capaian_pelaksanaan?.link_dokumen_bukti" :href="t.capaian_pelaksanaan.link_dokumen_bukti" target="_blank" class="w-full">
                            <Button label="Lihat Bukti" icon="pi pi-external-link" text class="w-full text-xs" />
                        </a>
                    </div>
                </div>
            </div>
            
            <div v-if="targets.length === 0" class="p-20 text-center card bg-white rounded-2xl border border-dashed border-slate-300">
                <i class="pi pi-inbox text-4xl text-slate-200 mb-4"></i>
                <p class="text-slate-400 italic">Belum ada target indikator yang dibebankan ke unit anda pada periode ini.</p>
            </div>
        </div>

        <Dialog v-model:visible="reportDialog" :style="{width: '550px'}" header="Laporan Capaian Mutu" :modal="true">
            <div v-if="selectedTarget" class="flex flex-col gap-6 mt-4">
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Indikator</p>
                    <p class="text-sm font-bold text-slate-700">{{ selectedTarget.indikator_mutu?.isi_standar }}</p>
                </div>

                <div class="field">
                    <label class="block font-medium mb-2">Nilai Realisasi Aktual ({{ selectedTarget.satuan }})</label>
                    <InputNumber v-model="form.nilai_aktual" :minFractionDigits="0" :maxFractionDigits="2" class="w-full" autofocus />
                </div>

                <div class="field">
                    <label class="block font-medium mb-2">Evaluasi Diri / Analisis Capaian</label>
                    <Textarea v-model="form.evaluasi_diri" rows="5" class="w-full" placeholder="Jelaskan kendala, faktor pendukung, atau analisis mengapa target tercapai/tidak tercapai..." />
                    <small class="text-slate-400 italic">Analisis ini akan direview oleh Auditor AMI.</small>
                </div>

                <div class="field">
                    <label class="block font-medium mb-2">Link Dokumen Bukti (URL)</label>
                    <InputText v-model="form.link_dokumen_bukti" class="w-full" placeholder="https://drive.google.com/..." />
                    <small class="text-slate-400">Pastikan link dapat diakses oleh Auditor.</small>
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="reportDialog = false"/>
                <Button label="Simpan Laporan" icon="pi pi-check" @click="submitReport" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
