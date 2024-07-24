<script setup>
import { ref, onMounted } from 'vue';

// Definisikan variabel reaktif
const data = ref([]);
const indikators = ref([]);
const targets = ref([]);
const loading = ref(true);

// Fungsi asinkron untuk fetch data
async function fetchStandar() {
    try {
        let response = await fetch('/api/sheet');
        data.value = await response.json();
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

onMounted(fetchStandar)

const user = ref(1);
</script>


<template>
    <router-link to="/">Sheet</router-link>

    <table border="1">
        <thead>
        <tr>
            <th rowspan="2">Standar</th>
            <th colspan="2">Penetapan</th>
            <th rowspan="2">Pelaksanaan</th>
        </tr>
        <tr>
            <th>Indicator</th>
            <th>Target</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(standar, index) in data" :key="index">
            <td :rowspan="standar.indicators.length">{{ standar.standar }}</td>
            <td v-for="(indicator, i) in standar.indicators" :key="i">
                <template v-if="i === 0">
                    {{ indicator.indicator }}
                </template>
                <template v-else>
                    <tr>
                        <td>{{ indicator.indicator }}</td>
                        <td>{{ indicator.target }}</td>
                        <td>Komentar</td>
                        <td>Link Bukti</td>
                    </tr>
                </template>
            </td>
        </tr>
        </tbody>
    </table>
</template>


<!--<template>-->
<!--    <table border="1">-->
<!--    <thead>-->
<!--    <tr>-->
<!--        <th colspan="3">Penetapan</th>-->
<!--        <th rowspan="2" colspan="2">Pelaksanaan</th>-->
<!--        <th v-if="user === 0" rowspan="2">Evaluasi</th>-->
<!--        <th v-if="user === 1" rowspan="2">Evolusi</th>-->
<!--    </tr>-->

<!--    <tr>-->
<!--        <th class="w-25">Standard</th>-->
<!--        <th>Indicator</th>-->
<!--        <th>Target</th>-->
<!--    </tr>-->
<!--    </thead>-->
<!--    <tbody>-->
<!--    <td colspan="6">input</td>-->
<!--        <tr v-for="(item, index) in data" :key="index">-->
<!--        <td ><h3 class="wid">{{item.standar}}</h3></td>-->
<!--        <td>{{ item.standar }}</td> &lt;!&ndash; Kolom indikator &ndash;&gt;-->
<!--        <td>{{item.standar}}</td> &lt;!&ndash; Kolom target &ndash;&gt;-->
<!--        <td>Komentar</td> &lt;!&ndash; Kolom Pelaksanaan &ndash;&gt;-->
<!--        <td>Link Bukti</td> &lt;!&ndash; Kolom Pelaksanaan &ndash;&gt;-->
<!--        <td v-if="user === 0"></td> &lt;!&ndash; Kolom Evaluasi &ndash;&gt;-->
<!--        <td v-if="user === 1"></td> &lt;!&ndash; Kolom Evaluasi &ndash;&gt;-->
<!--    </tr>-->
<!--    </tbody>-->
<!--    </table>-->
<!--</template>-->


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
