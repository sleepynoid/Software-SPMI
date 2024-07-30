<script setup>
import { ref, onMounted } from 'vue';

// Definisikan variabel reaktif
const standarData = ref([]);
const loading = ref(true);
const Data = ref([]);

// const fromPhp = ref(window.items);

async function fetchStandar() {
    try {
        let response = await fetch('/api/nyobak');
        standarData.value = await response.json();
        // standarData.value = JSON.parse(Data.value);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}


console.log(standarData);

onMounted(fetchStandar)

const user = ref(1);
</script>


<template>
    <router-link to="/">Home</router-link>

    <div v-if="loading">Loading...</div>
    <div v-else>
        <table border="1">
            <thead>
                <tr>
                    <th colspan="3">Penetapan</th>
                    <th rowspan="1" colspan="2">Pelaksanaan</th>

                </tr>
                <tr>
                    <th rowspan="">Standar</th>
                    <th>Indicator</th>
                    <th>Target</th>
                    <th>Komentar</th>
                    <th>Link Bukti</th>
                </tr>
            </thead>
            <td colspan=6>input</td>
            <tbody>
                <template v-for="(standar, index) in standarData">
                    <tr>
                        <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>
                    </tr>
                    <tr v-for="(indicator, index) in standar.indicators">
                        <td>{{ indicator.indicator }}</td>
                        <td>{{ indicator.target }}</td>
                        <td><input type="text"></td>
                        <td><input type="text"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

</template>


<style scoped>
table,
th,
td {
    border: 1px solid black;
    text-align: center;
    padding: 5px;
    width: 2rem;
}

button {
    width: 5rem;
    height: 1rem;
}

.wid {
    width: 10rem;
    word-wrap: break-word;
}
</style>
