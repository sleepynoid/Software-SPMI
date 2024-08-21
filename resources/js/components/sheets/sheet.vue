<script setup>
import { ref, watch } from 'vue';
import { useRoute } from "vue-router";
import { dotStream } from 'ldrs';
import Sheets from "@/components/sheets/sheets.vue";
import CustomSelect from "@/components/comp/custom-select.vue";
import Pengendalian from "@/components/sheets/pengendalian.vue";

dotStream.register();

const standarData = ref([]);
const loading = ref(true);

const tipe = ['input', 'proses', 'output'];
const current = ref(tipe[0]);

const roleUser = ['Pelaksanaan', 'Evaluasi', 'Pengendalian'];
const role = ref(roleUser[0]);

const tipeSheet = ['pendidikan', 'penelitian', 'pengabdian'];
const currentSheet = ref(tipeSheet[0]);

const route = useRoute();
const periode = ref(route.params.periode);
const jurusan = ref(route.params.jurusan);

let re = ref(0);

watch([re, periode, jurusan, currentSheet, current], async () => {
    loading.value = true;
    let response = await fetch(`/api/getPenetapan/${jurusan.value}/${periode.value}/${currentSheet.value}/${current.value}`);
    standarData.value = await response.json();
    loading.value = false;
    // console.log(standarData.value);
}, { immediate: true });


const submitData = (formData) => {
    const apiEndpoint = role.value === 'Pelaksanaan' ? '/api/submitPelaksanaan' : '/api/submitEvaluasi';

    axios.post(apiEndpoint, { data: formData })
        .then(response => {
            console.log('Data submitted successfully:', response.data);
            re.value++;
        })
        .catch(error => {
            console.error('Error submitting data:', error.response.data);
        });
};
</script>

<template>
    <div class="bodi">
        <router-link class="pop" to="/">Home</router-link>

        <p>tipe:</p>
        <select v-model="currentSheet" class="tipe" required>
            <option v-for="t in tipeSheet" :key="t">{{ t }}</option>
        </select>

        <br><br>

        <template v-for="t in tipe">
            <input type="radio" :id="t" :value="t" v-model="current">
            <label :for="t" style="margin-right: 0.5rem;">{{ t }}</label>
        </template>

        <div v-if="loading">
            <l-dot-stream size="60" speed="2.5" color="black"></l-dot-stream>
        </div>

        <div v-else-if="standarData === 'Null'">
            Belum ada data :)
        </div>

        <div v-else class="dt">
            <h2 class="font-poppin" v-once>Role: </h2>
            <custom-select :data="roleUser" :wid="10" @response="data => role = data"></custom-select>
            <Sheets v-if="role!== 'Pengendalian'" :data="standarData" :role="role" @submit-data="submitData"></Sheets>
            <pengendalian :data="standarData"></pengendalian>
        </div>
    </div>
</template>

<style scoped>
.bodi {
    width: 100vw;
    height: 100vh;
    padding: 3%;
}

button {
    width: 5rem;
    height: 1rem;
}

.pop {
    padding: 3px;
    height: 2rem;
}

.dt {
    overflow-x: auto;
    padding-bottom: 3%;
}
</style>
