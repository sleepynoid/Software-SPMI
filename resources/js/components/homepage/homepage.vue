<script setup>
import {ref, watchEffect} from "vue";
import { dotStream } from 'ldrs'
dotStream.register()
const role = localStorage.getItem("userRole");

const message = ref('upload new sheet')
const jurusan = ref('a')
const periode = ref('0')
const per = ref([])
const loading = ref(false);
const token = localStorage.getItem("token");
const faculties = [
    {
        name: "F. Teknologi Industri",
        majors: ["Teknik Mesin", "Teknik Industri", "Teknik Kimia", "Teknik Pertambangan", "Teknik Perkapalan"]
    },
    {
        name: "F. Teknik Sipil & Perencanaan",
        majors: ["Teknik Sipil", "Arsitektur", "Teknik Lingkungan"]
    },
    {
        name: "F. Teknik Elektro dan Teknologi Informasi",
        majors: ["Teknik Elektro", "Teknik Informatika", "Sistem Informasi"]
    }
];
const selectedMajor = ref("");
const showMajorError = ref(false);

function notify(){
    alert('Pilih jurusan 😈')
}

const validateMajorSelection = () => {
    if (!jurusan.value) {
        showMajorError.value = true;
        selectedMajor.value = "";
    } else {
        showMajorError.value = false;
    }
};

watchEffect(async ()=> {
    loading.value = true;
    let response = await fetch(`/api/getPeriode/${selectedMajor.value}`);
    per.value = await response.json();
    loading.value = false;
})

</script>

<template>

    <div class="bodi">
<!--    {{user}}-->
    <div class="c1">
        <div class="c1-1">

            <h1>Software Penjamin Mutu Internal</h1>


            <button v-if="role === 'Evaluasi'">
                  <router-link
                      to="/import"
                      class="custom-router-link"
                      :title="message"
                  ><h4>+</h4></router-link>
            </button>
        </div>
    </div>
    <p v-if="token === null">Anda Belum <router-link  to="/login">Login</router-link> 🫨</p>
    <div class="c2" v-else>
        <h5>Pilih Fakultas:</h5>
        <select v-model="jurusan" class="pilihSheet" required>
            <option disabled value="">Pilih Fakultas</option>
            <option v-for="faculty in faculties" :key="faculty.name" :value="faculty.name">
                {{ faculty.name }}
            </option>
        </select>

        <div v-if="showMajorError" class="error-message">
            <span style="color: red;">Silakan pilih fakultas terlebih dahulu.</span>
        </div>

        <h5>Pilih Jurusan:</h5>
        <select v-model="selectedMajor" class="pilihSheet" @change="validateMajorSelection" :disabled="false" required>
            <option value="">Pilih Jurusan</option>
            <option v-for="major in faculties.find(f => f.name === jurusan)?.majors || []" :key="major" :value="major">
                {{ major }}
            </option>
        </select>

<!--        <button v-if="selectedMajor" @click.prevent="notify"><h5>go</h5></button>-->
        <div v-if="loading">
            <l-dot-stream
                size="60"
                speed="2.5"
                color="black"
            ></l-dot-stream>
        </div>
        <div v-else-if="per.length === 0">
            <p>Sheet dengan jurusan {{ selectedMajor }} belum ada</p>
        </div>
        <div v-else class="periode">
            <h5>Pilih Periode:</h5>
            <select v-model="periode" class="pilihSheet" required>
                <option v-for="p in per" :value="p.periode">{{ p.periode }}</option>
            </select>
<!--                {{idS}}-->

            <button>
                <router-link
                    :to="{ name: 'Sheet', params: {jurusan: selectedMajor, periode: periode}}"
                    class="custom-router-link"
                ><h5>go</h5></router-link>
            </button>
        </div>
    </div>
    </div>


<!--   <router-link :to="{ name: 'Sheet', params: {idSheet: 27}}">Sheet</router-link>-->


<!--  <router-link to="/upload">Upload</router-link>-->

</template>

<style scoped>
.bodi{
    width: 100vw;
    height: 100vh;
    padding: 3%;
}

.c1{
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.c1-1{
    display: flex;
    gap: 1rem;
    align-items: center;
}

.c2{
    /* //display: flex; */
    margin-top: 3rem;
    gap: 1rem;
}

.custom-router-link {
    text-decoration: none;
    color: inherit;
}

.pilihSheet{
    width: 60%;
}

.periode{
    margin-top: 3rem;
    margin-bottom: 2rem;

    > select{
        width: 40%;
    }
}

.error-message {
    margin-top: 5px;
    color: red;
}
</style>
