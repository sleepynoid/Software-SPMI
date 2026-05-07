<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import * as XLSX from "xlsx";
import Button from 'primevue/button';
import Message from 'primevue/message';

const props = defineProps({
    targetUrl: String,
    redirectTo: String,
    periodeId: [Number, String]
});

const headers = ref([]);
const rows = ref([]);
const groupedRows = ref([]);
const hiddenHeaders = ref([]);
const isProcessing = ref(false);
const rawJsonData = ref([]);

const page = usePage();
const errors = computed(() => page.props.errors);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            
            rawJsonData.value = XLSX.utils.sheet_to_json(worksheet);
            
            const jsonSheet = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
            const merges = worksheet['!merges'] || [];
            updateData(jsonSheet, merges);
        };
        reader.readAsArrayBuffer(file);
    }
};

const updateData = (jsonSheet, merges) => {
    headers.value = jsonSheet[0].map((header) => ({ value: header, rowspan: 1, colspan: 1 }));
    rows.value = jsonSheet.slice(1).map((row) => row.map((cell) => ({ value: cell, rowspan: 1, colspan: 1 })));

    merges.forEach((merge) => {
        const startRow = merge.s.r;
        const startCol = merge.s.c;
        const endRow = merge.e.r;
        const endCol = merge.e.c;
        const rowspan = endRow - startRow + 1;
        const colspan = endCol - startCol + 1;

        if (startRow === 0) {
            headers.value[startCol].colspan = colspan;
            for (let col = startCol + 1; col <= endCol; col++) {
                hiddenHeaders.value.push(col);
            }
        }

        for (let row = startRow; row <= endRow; row++) {
            if (row > 0) {
                for (let col = startCol; col <= endCol; col++) {
                    if (row === startRow) {
                        rows.value[row - 1][col].rowspan = rowspan;
                        rows.value[row - 1][col].colspan = colspan;
                    } else {
                        rows.value[row - 1][col] = { ...rows.value[row - 1][col], hidden: true };
                    }
                }
            }
        }
    });

    groupedRows.value = rows.value.map(row => row.filter(cell => !cell.hidden));
};

const submitData = () => {
    if (!props.targetUrl) return;
    
    isProcessing.value = true;
    router.post(props.targetUrl, {
        data: rawJsonData.value,
        periode_id: props.periodeId
    }, {
        onFinish: () => isProcessing.value = false
    });
};
</script>

<template>
    <div class="space-y-4">
        <Message v-if="errors.data" severity="error" variant="simple" class="mb-4">
            {{ errors.data }}
        </Message>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <input 
                        type="file" 
                        id="excel-upload"
                        class="hidden" 
                        @change="handleFileUpload" 
                        accept=".xlsx" 
                    />
                    <label 
                        for="excel-upload" 
                        class="cursor-pointer bg-white border border-slate-300 hover:border-primary-500 hover:text-primary-600 px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2"
                    >
                        <i class="pi pi-file-excel text-green-600"></i>
                        Pilih File Excel
                    </label>
                    <span v-if="groupedRows.length" class="text-sm text-slate-500">
                        {{ groupedRows.length }} baris terdeteksi
                    </span>
                </div>

                <div v-if="groupedRows.length" class="flex gap-2">
                    <Button 
                        label="Batalkan" 
                        severity="secondary" 
                        text 
                        @click="groupedRows = []" 
                    />
                    <Button 
                        label="Simpan ke Sistem" 
                        icon="pi pi-check" 
                        :loading="isProcessing"
                        @click="submitData" 
                    />
                </div>
            </div>

            <div v-if="groupedRows.length" class="overflow-x-auto p-6">
                <table class="w-full border-collapse border border-slate-200 text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th 
                                v-for="(header, index) in headers" 
                                :key="index"
                                :colspan="header.colspan" 
                                :rowspan="header.rowspan"
                                class="border border-slate-200 p-3 font-semibold text-slate-700"
                            >
                                {{ header.value }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, rIdx) in groupedRows" :key="rIdx" class="hover:bg-slate-50 transition-colors">
                            <td 
                                v-for="(cell, cIdx) in row" 
                                :key="cIdx"
                                :colspan="cell.colspan" 
                                :rowspan="cell.rowspan"
                                class="border border-slate-200 p-3 text-slate-600"
                            >
                                {{ cell.value }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="py-20 flex flex-col items-center justify-center text-slate-400">
                <i class="pi pi-cloud-upload text-5xl mb-4 opacity-20"></i>
                <p>Belum ada file yang dipilih untuk preview</p>
            </div>
        </div>
    </div>
</template>
