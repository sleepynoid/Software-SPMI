<script setup>
import { ref, onMounted } from 'vue';
const sheets = ref([]);
const loading = ref(true);
async function fetchSheet() {
    try {
        let response = await fetch('/api/standar');
        sheets.value = await response.json();
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}
onMounted(fetchSheet);

console.log(sheets)

// const parsSheet = JSON.parse(sheets);
// const itemS = ref(parsSheet);
// const count1 = it.value.filter(item => item.periode === '2023-06-06').length


const items = ref([
    { messageA: 'asdsaaaaaaaaaaadas', messageB: 'Lulus 3.5 abad', messageC: '13' },
    { messageA: 'A1', messageB: 'item2', messageC: '10' },
    { messageA: 'A1', messageB: 'item3', messageC: '10' },
    { messageA: 'A3', messageB: 'item4', messageC: '10' },
    { messageA: 'A3', messageB: 'item5', messageC: '10' },
    { messageA: 'A3', messageB: 'item6', messageC: '10' },
]);

const user = 1;

const ina = ref(3);
const count = ref(0);

console.log(count)
</script>

<template>
<!--    <div>-->
<!--        <h1>Items</h1>-->
<!--        <ul>-->
<!--            <div v-if="loading">Loading...</div>-->
<!--            <div v-else>-->
<!--                <li v-for="sheet in sheets" :key="item.id">-->
<!--&lt;!&ndash;                    {{index}}&ndash;&gt;-->
<!--                    {{ sheets.jurusan }} - {{ sheets.periode }} - {{ sheets.note }}-->
<!--                </li>-->
<!--            </div>-->
<!--        </ul>-->
<!--    </div>-->

    <table class="dt-center">
        <thead>
        <tr>
            <th colspan="3">Penetapan</th>
            <th rowspan="2" colspan="2">Pelaksanaan</th>
            <th v-if="user === 0" rowspan="2">Evaluasi</th>
            <th v-if="user === 1" rowspan="2">Evolusi</th>
        </tr>

        <tr>
            <th class="w-25">Standard</th>
            <th>Indicator</th>
            <th>Target</th>
        </tr>
        </thead>
        <tbody>
        <td colspan="6">input</td>
        <tr v-for="(item, index) in items" :key="index">
            <!--      <td v-if="index === 0" :rowspan="items.length">{{itemA[index].message}}</td>-->
            <td v-show="index %ina === 0" :rowspan="ina" class="w-25"> <!-- Tampilkan kolom A setiap 3 baris dengan rowspan 3 -->
                <h3 class="wid">
                    {{item.messageA}}
                </h3>
            </td> <!-- Kolom A -->
            <td>{{ item.messageB }}</td> <!-- Kolom B -->
            <td>{{item.messageC}}</td> <!-- Kolom C -->
            <td>Komentar</td> <!-- Kolom Pelaksanaan -->
            <td>Link Bukti</td> <!-- Kolom Pelaksanaan -->
            <td v-if="user === 0"></td> <!-- Kolom Evaluasi -->
            <td v-if="user === 1"></td> <!-- Kolom Evaluasi -->
        </tr>

<!--        <td colspan="6" style="height: 1px;">prosess</td>-->
<!--        <tr v-for="(item, index) in items" :key="index">-->
<!--            &lt;!&ndash;      <td v-if="index === 0" :rowspan="items.length">{{itemA[index].message}}</td>&ndash;&gt;-->
<!--            <td v-if="index %3 === 0" :rowspan="3">{{item.messageA}}</td> &lt;!&ndash; Kolom A &ndash;&gt;-->
<!--            <td>{{ item.messageB }}</td> &lt;!&ndash; Kolom B &ndash;&gt;-->
<!--            <td>{{item.messageC}}</td> &lt;!&ndash; Kolom C &ndash;&gt;-->
<!--            <td>Komentar</td> &lt;!&ndash; Kolom Pelaksanaan &ndash;&gt;-->
<!--            <td>Link Bukti</td> &lt;!&ndash; Kolom Pelaksanaan &ndash;&gt;-->
<!--            <td v-if="user === 0"></td> &lt;!&ndash; Kolom Evaluasi &ndash;&gt;-->
<!--            <td v-if="user === 1"></td> &lt;!&ndash; Kolom Evaluasi &ndash;&gt;-->
<!--        </tr>-->

<!--        <td colspan="6">output</td>-->
<!--        <tr v-for="(item, index) in items" :key="index">-->
<!--            &lt;!&ndash;      <td v-if="index === 0" :rowspan="items.length">{{itemA[index].message}}</td>&ndash;&gt;-->
<!--            <td v-if="index %3 === 0" :rowspan="3">{{item.messageA}}</td> &lt;!&ndash; Kolom A &ndash;&gt;-->
<!--            <td>{{ item.messageB }}</td> &lt;!&ndash; Kolom B &ndash;&gt;-->
<!--            <td>{{item.messageC}}</td> &lt;!&ndash; Kolom C &ndash;&gt;-->
<!--            <td>Komentar</td> &lt;!&ndash; Kolom Pelaksanaan &ndash;&gt;-->
<!--            <td>Link Bukti</td> &lt;!&ndash; Kolom Pelaksanaan &ndash;&gt;-->
<!--            <td v-if="user === 0"></td> &lt;!&ndash; Kolom Evaluasi &ndash;&gt;-->
<!--            <td v-if="user === 1"></td> &lt;!&ndash; Kolom Evaluasi &ndash;&gt;-->
<!--        </tr>-->

        </tbody>
    </table>


</template>



<style scoped>
table, th, td {
    border: 1px solid black;
    text-align: center;
    padding: 5px;
    width: 2rem;
}

button{
    width: 5rem;
    height: 1rem;
}

.wid{
    width: 10rem;
    word-wrap: break-word;
}
</style>
