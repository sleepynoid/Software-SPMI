<script setup>
import XlsxRead from './XlsxRead.vue';
import XlsxTable from "./XlsxTable.vue";
import XlsxSheets from "./XlsxSheets.vue";
// import XlsxJson from "./components/XlsxJson.vue";
// import XlsxWorkbook from "./components/XlsxWorkbook.vue";
// import XlsxSheet from "./components/XlsxSheet.vue";
// import XlsxDownload from "./components/XlsxDownload.vue";

import { ref } from 'vue';

const file = ref(null);
const selectedSheet = ref(null);
const sheetName = ref(null);
const sheets = ref([{ name: "SheetOne", data: [{ c: 2 }] }]);
const collection = ref([{ a: 1, b: 2 }]);

const onChange = (event) => {
    file.value = event.target.files ? event.target.files[0] : null;
};

const addSheet = () => {
    sheets.value.push({ name: sheetName.value, data: [...collection.value] });
    sheetName.value = null;
};
</script>

<template>
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
                                        <a class="nav-link" :class="{ 'active': selectedSheet === sheet }" href="#" @click="selectedSheet = sheet" :style="{ backgroundColor: selectedSheet === sheet ? '#343a40' : '' }">{{ sheet }}</a>
                                    </li>
                                </ul>
                            </nav>
                        </template>
                    </xlsx-sheets>
                    <xlsx-table :sheet="selectedSheet" />
                    <!-- <xlsx-json :sheet="selectedSheet">
            <template #default="{collection}">
              <div>
                {{ collection }}
              </div>
            </template>
          </xlsx-json> -->
                </template>
            </xlsx-read>
        </section>
    </div>
</template>
