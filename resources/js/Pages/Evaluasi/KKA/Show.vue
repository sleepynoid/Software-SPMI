<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    unit: Object,
    periodes: Array,
    selectedPeriodeId: Number,
    auditData: Array,
});

const toast = useToast();
const auditDialog = ref(false);
const activePeriodeId = ref(props.selectedPeriodeId);
const currentCapaian = ref(null);

const form = useForm({
    kategori_temuan: 'Sesuai',
    deskripsi_temuan: '',
});

const openAudit = (target) => {
    if (!target.capaian_pelaksanaan) {
        toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Auditee belum mengisi capaian untuk indikator ini.', life: 3000 });
        return;
    }
    currentCapaian.value = target.capaian_pelaksanaan;
    form.kategori_temuan = target.capaian_pelaksanaan.kertas_kerja_audit?.kategori_temuan || 'Sesuai';
    form.deskripsi_temuan = target.capaian_pelaksanaan.kertas_kerja_audit?.deskripsi_temuan || '';
    auditDialog.value = true;
};

const submitAudit = () => {
    form.post(route('evaluasi.kka.store', currentCapaian.value.id), {
        onSuccess: () => {
            auditDialog.value = false;
            toast.add({ severity: 'success', summary: 'Sukses', detail: 'Hasil audit disimpan', life: 3000 });
        }
    });
};

const handlePeriodeChange = (id) => {
    router.get(route('evaluasi.kka.show', props.unit.id), { periode_id: id });
};

const getTemuanSeverity = (cat) => {
    switch (cat) {
        case 'Sesuai': return 'success';
        case 'Melampaui': return 'info';
        case 'Observasi (OB)': return 'warn';
        case 'KTS Minor': return 'warn';
        case 'KTS Mayor': return 'danger';
        default: return 'secondary';
    }
};
</script>

<template>
    <AppLayout :title="'KKA: ' + unit.nama_unit">
        <div class="flex flex-col gap-6">
            <!-- Header Info -->
            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <Link :href="route('evaluasi.jadwal-audit.index')" class="p-2 hover:bg-slate-50 rounded-lg text-slate-400">
                        <i class="pi pi-arrow-left text-xl"></i>
                    </Link>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Kertas Kerja Audit (KKA)</h3>
                        <p class="text-sm text-slate-500">{{ unit.nama_unit }} - Periode AMI {{ periodes.find(p => p.id === activePeriodeId)?.tahun_akademik }}</p>
                    </div>
                </div>
                <Select v-model="activePeriodeId" :options="periodes" optionLabel="tahun_akademik" optionValue="id" @change="handlePeriodeChange($event.value)" class="w-64" />
            </div>

            <!-- Audit Table -->
            <div class="card bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <DataTable :value="auditData" responsiveLayout="scroll" class="p-datatable-sm">
                    <Column field="indikator_mutu.kode_indikator" header="Kode" style="width:6rem">
                        <template #body="slotProps">
                            <span class="font-bold text-primary-600 text-xs">{{ slotProps.data.indikator_mutu?.kode_indikator }}</span>
                        </template>
                    </Column>
                    <Column field="indikator_mutu.isi_standar" header="Indikator Mutu" style="min-width:15rem">
                         <template #body="slotProps">
                            <p class="text-sm leading-tight line-clamp-3">{{ slotProps.data.indikator_mutu?.isi_standar }}</p>
                            <span class="text-[10px] text-slate-400 font-bold uppercase">{{ slotProps.data.indikator_mutu?.standar?.kategori?.nama_kategori }}</span>
                        </template>
                    </Column>
                    <Column header="Target vs Aktual" style="width:12rem">
                        <template #body="slotProps">
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-400 font-bold uppercase">Target: {{ slotProps.data.nilai_target }} {{ slotProps.data.satuan }}</span>
                                <span class="text-sm font-black" :class="slotProps.data.capaian_pelaksanaan ? 'text-slate-800' : 'text-slate-300 italic'">
                                    Aktual: {{ slotProps.data.capaian_pelaksanaan?.nilai_aktual || 'Belum Isi' }}
                                </span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Bukti" style="width:5rem">
                        <template #body="slotProps">
                            <a v-if="slotProps.data.capaian_pelaksanaan?.link_dokumen_bukti" :href="slotProps.data.capaian_pelaksanaan.link_dokumen_bukti" target="_blank">
                                <Button icon="pi pi-external-link" text severity="info" />
                            </a>
                            <span v-else class="text-slate-300">-</span>
                        </template>
                    </Column>
                    <Column header="Hasil Audit" style="width:12rem">
                         <template #body="slotProps">
                            <div v-if="slotProps.data.capaian_pelaksanaan?.kertas_kerja_audit">
                                <Tag :severity="getTemuanSeverity(slotProps.data.capaian_pelaksanaan.kertas_kerja_audit.kategori_temuan)" 
                                    :value="slotProps.data.capaian_pelaksanaan.kertas_kerja_audit.kategori_temuan" />
                            </div>
                            <span v-else class="text-slate-300 italic text-xs">Belum Audit</span>
                        </template>
                    </Column>
                    <Column header="Pilih" style="width:4rem">
                        <template #body="slotProps">
                            <Button icon="pi pi-check-square" severity="primary" text @click="openAudit(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Audit Dialog -->
        <Dialog v-model:visible="auditDialog" :style="{width: '600px'}" header="Evaluasi Indikator AMI" :modal="true">
            <div v-if="currentCapaian" class="flex flex-col gap-6 mt-4">
                 <div class="p-6 bg-slate-50 rounded-xl border border-slate-100 flex gap-6">
                    <div class="flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Evaluasi Diri Auditee</p>
                        <p class="text-sm italic text-slate-700 leading-relaxed">"{{ currentCapaian.evaluasi_diri || 'Tidak ada analisis.' }}"</p>
                    </div>
                </div>

                <div class="field">
                    <label class="block font-medium mb-3">Kategori Temuan Audit</label>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                        <div v-for="cat in [
                            { label: 'Sesuai', active: 'bg-green-600 text-white border-green-600 shadow-green-100', inactive: 'bg-green-50 text-green-700 border-green-100 hover:border-green-300' },
                            { label: 'Melampaui', active: 'bg-blue-600 text-white border-blue-600 shadow-blue-100', inactive: 'bg-blue-50 text-blue-700 border-blue-100 hover:border-blue-300' },
                            { label: 'Observasi (OB)', active: 'bg-amber-500 text-white border-amber-500 shadow-amber-100', inactive: 'bg-amber-50 text-amber-700 border-amber-100 hover:border-amber-300' },
                            { label: 'KTS Minor', active: 'bg-orange-600 text-white border-orange-600 shadow-orange-100', inactive: 'bg-orange-50 text-orange-700 border-orange-100 hover:border-orange-300' },
                            { label: 'KTS Mayor', active: 'bg-red-600 text-white border-red-600 shadow-red-100', inactive: 'bg-red-50 text-red-700 border-red-100 hover:border-red-300' }
                        ]" :key="cat.label"
                            @click="form.kategori_temuan = cat.label"
                            class="p-3 border rounded-xl cursor-pointer text-center text-xs font-bold transition-all shadow-sm"
                            :class="form.kategori_temuan === cat.label ? cat.active + ' shadow-lg scale-[1.02]' : cat.inactive"
                        >
                            {{ cat.label }}
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="block font-medium mb-2">Deskripsi Temuan / Rekomendasi Auditor</label>
                    <Textarea v-model="form.deskripsi_temuan" rows="5" class="w-full" placeholder="Tuliskan detail ketidaksesuaian atau poin observasi di sini..." />
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="auditDialog = false"/>
                <Button label="Simpan Hasil Audit" icon="pi pi-check" @click="submitAudit" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
