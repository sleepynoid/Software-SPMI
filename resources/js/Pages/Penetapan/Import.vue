<script setup>
import AppLayout from '@/components/AppLayout.vue';
import ImportExcel from '@/components/upload/importexcel.vue';
import { Head, Link } from '@inertiajs/vue3';
import * as XLSX from 'xlsx';

const props = defineProps({
    active_periode: Object
});

const downloadTemplate = () => {
    const data = [
        ["Kategori", "Nama Standar", "Kode Indikator", "Isi Indikator", "Jenis", "Target", "Satuan"],
        ["Standar Pendidikan", "Standar Kompetensi Lulusan", "SN.01", "Rumusan kualifikasi kemampuan lulusan...", "IKU", "90", "%"],
        ["Standar Pendidikan", "Standar Isi Pembelajaran", "SN.02", "Kedalaman dan keluasan materi...", "IKT", "100", "Dokumen"],
    ];
    const worksheet = XLSX.utils.aoa_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Template");
    XLSX.writeFile(workbook, "template_import_standar.xlsx");
};
</script>

<template>
    <AppLayout title="Import Standar & Indikator">
        <Head title="Import Excel" />

        <div class="max-w-5xl mx-auto flex flex-col gap-8">
            <!-- Header & Info -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 mb-1">Import Massal Standar</h1>
                    <p class="text-slate-500">Gunakan file Excel untuk memasukkan data SN-Dikti dalam jumlah besar.</p>
                </div>
                <Link :href="route('penetapan.standar.index')" class="flex items-center gap-2 text-primary-600 font-medium hover:underline">
                    <i class="pi pi-arrow-left"></i>
                    Kembali ke Daftar
                </Link>
            </div>

            <!-- Periode Info -->
            <div v-if="active_periode" class="bg-primary-600 rounded-2xl p-6 text-white flex items-center justify-between shadow-lg shadow-primary-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="pi pi-calendar text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-primary-100 text-sm font-medium">Periode Aktif Target Import</p>
                        <h3 class="text-xl font-bold uppercase tracking-tight">{{ active_periode.tahun_akademik }}</h3>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-bold uppercase tracking-widest">{{ active_periode.status }}</span>
                </div>
            </div>

            <!-- Instruction Card -->
            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="pi pi-info-circle text-primary-500"></i>
                            Instruksi Format
                        </h3>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li class="flex gap-2">
                                <i class="pi pi-check-circle text-green-500 shrink-0 mt-0.5"></i>
                                <span>Baris pertama adalah <strong>Header</strong>.</span>
                            </li>
                            <li class="flex gap-2">
                                <i class="pi pi-check-circle text-green-500 shrink-0 mt-0.5"></i>
                                <span>Kolom: <strong>Kategori, Nama Standar, Kode Indikator, Isi Indikator, Jenis</strong>.</span>
                            </li>
                            <li class="flex gap-2">
                                <i class="pi pi-check-circle text-green-500 shrink-0 mt-0.5"></i>
                                <span>Kolom <strong>Jenis</strong> diisi: <code class="bg-slate-100 px-1 rounded">IKU</code> atau <code class="bg-slate-100 px-1 rounded">IKT</code>.</span>
                            </li>
                        </ul>
                        
                        <div class="mt-6 pt-6 border-t border-slate-50">
                            <button 
                                @click="downloadTemplate"
                                class="w-full text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg text-sm font-bold transition-colors"
                            >
                                <i class="pi pi-download mr-1"></i>
                                Unduh Template Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Import Component -->
                <div class="md:col-span-2">
                    <ImportExcel 
                        :targetUrl="route('penetapan.standar.import.store')"
                        :periodeId="active_periode?.id"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
