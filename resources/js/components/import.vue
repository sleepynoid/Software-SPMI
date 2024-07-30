<script setup>
import XlsxRead from './XlsxRead.vue';
import XlsxTable from "./XlsxTable.vue";
import XlsxSheets from "./XlsxSheets.vue";
import XlsxJson from "./XlsxJson.vue";
// import XlsxWorkbook from "./components/XlsxWorkbook.vue";
// import XlsxSheet from "./components/XlsxSheet.vue";
// import XlsxDownload from "./components/XlsxDownload.vue";

import { ref } from 'vue';


const userRole = ref('admin');
const comment = ref('');
const proofLink = ref('');

const file = ref(null);
const selectedSheet = ref(null);
const sheets = ref([]);
const sheetName = ref(sheets.value[0]);
console.log('sheets', sheets);
const collection = ref([]);
const onChange = (event) => {
    file.value = event.target.files ? event.target.files[0] : null;
};

// const inputIndex = computed(() => {
//     const rows = collection.value.map(row => Object.entries(row));
//     return rows.map(entries => {
//         return entries.findIndex(([key, value]) => value === 'INPUT');
//     });
// });

const addSheet = () => {
    sheets.value.push({ name: sheetName.value, data: [...collection.value] });
    sheetName.value = null;
};
</script>

<template>
    <router-link to="/">Home</router-link>

    <div class="VueXlsx">
        <section>
            <h3>Import XLSX</h3>
            <input type="file" @change="onChange" />
            <xlsx-read :file="file">
                <template #default="{ loading }">
                    <span v-if="loading">Loading...</span>
                    <xlsx-sheets>
                        <template #default="{ sheets }">
                            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                                <ul class="navbar-nav">
                                    <li class="nav-item" v-for="sheet in sheets" :key="sheet">
                                        <a class="nav-link" :class="{ 'active': selectedSheet === sheet }" href="#"
                                            @click="selectedSheet = sheet"
                                            :style="{ backgroundColor: selectedSheet === sheet ? '#343a40' : '' }">{{
                                                sheet }}</a>
                                    </li>
                                </ul>
                            </nav>
                        </template>
                    </xlsx-sheets>
                    <xlsx-table :sheet="selectedSheet" />
                    <!-- <xlsx-json :sheet="selectedSheet">
                        <template #default="{ collection }">
                            <h3>Komentar dan Bukti Pelaksanaan</h3>
                            <section v-for="(row, index) in collection" :key="row" v-if="userRole === 'admin'">
                                <pre>{{ index }}</pre>
                                <div v-for="(value, key) in row" :key="key" v-if="index > 0">
                                    <pre>{{ index }}</pre>
                                    <h2 v-if="['INPUT', 'PROCESS', 'OUTPUT'].includes(value)">{{ value }}</h2>
                                    <div v-if="!['INPUT', 'PROCESS', 'OUTPUT'].includes(value) && value">
                                        <div>
                                            <label>Komentar:</label>
                                            <textarea class="form-control" aria-label="With textarea" type="text"
                                                v-model="comment" placeholder="Masukkan komentar"></textarea>
                                        </div>
                                        <div>
                                            <label>Link Bukti Pelaksanaan:</label>
                                            <input type="url" v-model="proofLink" placeholder="Masukkan URL">
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <pre>{{ JSON.parse(collection, null, 2) }}</pre>
                            <pre>{{J}}</pre>
                        </template>
                    </xlsx-json> -->
                </template>
            </xlsx-read>
        </section>


    </div>
</template>
