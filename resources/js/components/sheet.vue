<template>
    <div>
        <h1>Items</h1>
        <ul>
            <div v-if="loading">Loading...</div>
            <div v-else>
                <li v-for="item in items" :key="item.id">
<!--                    {{index}}-->
                    {{ item.jurusan }} - {{ item.periode }} - {{ item.note }}
                </li>
            </div>
        </ul>
    </div>

</template>

<script setup>
import api from "../utils/api.js";
import { ref, onMounted } from 'vue';

const items = ref([]);
const loading = ref(true);

async function fetchSheet() {
    try {
        const response = await api.get('/api/sheet'); // Endpoint yang diakses
        items.value = response.data; // Simpan data yang diterima
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false; // Set loading menjadi false setelah data di-fetch
    }
}

onMounted(fetchSheet);

</script>

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
