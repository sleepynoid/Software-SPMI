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

console.log(data);

onMounted(fetchStandar)

const user = ref(1);
</script>


<template>
    <router-link to="/">Sheet</router-link>
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
