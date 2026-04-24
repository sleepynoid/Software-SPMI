<script setup>
import AppLayout from '@/components/AppLayout.vue';
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    periodes: Array,
    selectedPeriodeId: Number,
    findings: Array,
});

const toast = useToast();
const rtlDialog = ref(false);
const activePeriodeId = ref(props.selectedPeriodeId);
const selectedKka = ref(null);

const form = useForm({
    rencana_tindak_lanjut: '',
    jadwal_penyelesaian: '',
    penanggung_jawab: '',
});

const openRtl = (kka) => {
    selectedKka.value = kka;
    form.rencana_tindak_lanjut = kka.tindak_lanjut?.rencana_tindak_lanjut || '';
    form.jadwal_penyelesaian = kka.tindak_lanjut?.jadwal_penyelesaian || '';
    form.penanggung_jawab = kka.tindak_lanjut?.penanggung_jawab || '';
    rtlDialog.value = true;
};

const submitRtl = () => {
    form.post(route('pengendalian.isi-rtl.store', selectedKka.value.id), {
        onSuccess: () => {
            rtlDialog.value = false;
            toast.add({ severity: 'success', summary: 'Sukses', detail: 'RTL berhasil disimpan', life: 3000 });
        }
    });
};

const handlePeriodeChange = (id) => {
    router.get(route('pengendalian.isi-rtl.index'), { periode_id: id });
};

const getTemuanSeverity = (cat) => {
    switch (cat) {
        case 'Observasi (OB)': return 'warn';
        case 'KTS Minor': return 'danger';
        case 'KTS Mayor': return 'danger';
        default: return 'secondary';
    }
};
</script>

<template>
    <AppLayout title="Rencana Tindak Lanjut (RTL)">
        <div class="flex flex-col gap-6">
            <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Daftar Temuan Audit</h3>
                    <p class="text-sm text-slate-500">Isi rencana perbaikan untuk temuan KTS dan Observasi</p>
                </div>
                <Select v-model="activePeriodeId" :options="periodes" optionLabel="tahun_akademik" optionValue="id" @change="handlePeriodeChange($event.value)" class="w-64" />
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div v-for="kka in findings" :key="kka.id" class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row gap-6 hover:border-orange-200 transition-all border-l-4" :class="kka.kategori_temuan.includes('KTS') ? 'border-l-red-500' : 'border-l-orange-400'">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                             <Tag :severity="getTemuanSeverity(kka.kategori_temuan)" :value="kka.kategori_temuan" />
                             <span class="text-xs font-bold text-slate-400">{{ kka.capaian_pelaksanaan?.target_unit?.indikator_mutu?.kode_indikator }}</span>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-2">{{ kka.capaian_pelaksanaan?.target_unit?.indikator_mutu?.standar?.nama_standar }}</h4>
                        
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 mb-4">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Deskripsi Temuan Auditor</p>
                            <p class="text-sm text-slate-700 italic">"{{ kka.deskripsi_temuan || 'Tidak ada deskripsi temuan.' }}"</p>
                        </div>

                        <div v-if="kka.tindak_lanjut" class="flex flex-wrap gap-6 pt-4 border-t border-slate-50">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Rencana Perbaikan</p>
                                <p class="text-sm text-slate-800 font-medium line-clamp-1">{{ kka.tindak_lanjut.rencana_tindak_lanjut }}</p>
                            </div>
                             <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Target Selesai</p>
                                <p class="text-sm text-slate-800 font-bold">{{ kka.tindak_lanjut.jadwal_penyelesaian }}</p>
                            </div>
                            <div class="ml-auto">
                                <Tag :value="kka.tindak_lanjut.status_tl" :severity="kka.tindak_lanjut.status_tl === 'Open' ? 'info' : 'success'" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:w-48 flex flex-col justify-center border-l-0 md:border-l border-slate-100 pl-0 md:pl-6">
                        <Button 
                            :label="kka.tindak_lanjut ? 'Edit RTL' : 'Isi RTL'" 
                            :icon="kka.tindak_lanjut ? 'pi pi-pencil' : 'pi pi-plus'" 
                            :severity="kka.tindak_lanjut ? 'secondary' : 'warning'"
                            class="w-full"
                            @click="openRtl(kka)"
                        />
                    </div>
                </div>
            </div>
            
            <div v-if="findings.length === 0" class="p-20 text-center card bg-white rounded-2xl border border-dashed border-slate-300">
                <i class="pi pi-check-circle text-4xl text-green-200 mb-4"></i>
                <p class="text-slate-400 italic">Selamat! Tidak ada temuan audit yang memerlukan tindak lanjut pada periode ini.</p>
            </div>
        </div>

        <Dialog v-model:visible="rtlDialog" :style="{width: '550px'}" header="Rencana Tindak Lanjut" :modal="true">
            <div v-if="selectedKka" class="flex flex-col gap-6 mt-4">
                <div class="field">
                    <label class="block font-medium mb-2">Rencana Tindak Lanjut / Perbaikan</label>
                    <Textarea v-model="form.rencana_tindak_lanjut" rows="5" class="w-full" placeholder="Jelaskan langkah konkret yang akan diambil untuk memperbaiki temuan ini..." required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field">
                        <label class="block font-medium mb-2">Jadwal Penyelesaian</label>
                        <DatePicker v-model="form.jadwal_penyelesaian" dateFormat="yy-mm-dd" class="w-full" required />
                    </div>
                    <div class="field">
                        <label class="block font-medium mb-2">Penanggung Jawab</label>
                        <InputText v-model="form.penanggung_jawab" class="w-full" placeholder="Contoh: Kaprodi / Sekprodi" required />
                    </div>
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="rtlDialog = false"/>
                <Button label="Simpan RTL" icon="pi pi-check" severity="warning" @click="submitRtl" :loading="form.processing" />
            </template>
        </Dialog>
    </AppLayout>
</template>
