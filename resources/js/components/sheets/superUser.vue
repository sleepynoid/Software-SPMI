<script setup>
import {computed, onBeforeMount, ref, watch} from 'vue';
import {useRoute, useRouter} from "vue-router";
import { dotStream } from 'ldrs';
import Pelaksanaan from "@/components/sheets/pelaksanaan.vue";
import CustomSelect from "@/components/comp/custom-select.vue";
import Pengendalian from "@/components/sheets/pengendalian.vue";
import data from "bootstrap/js/src/dom/data.js";
import Evaluasi from "@/components/sheets/evaluasi.vue";

dotStream.register();

const standarData = ref([]);
const loading = ref(false);

const tipe = ['input', 'proses', 'output'];
const current = ref(tipe[0]);

const roleUser = ['Evaluasi', 'Pelaksanaan', 'Pengendalian'];
const role = ref(roleUser[0]);

const tipeSheet = ['pendidikan', 'penelitian', 'pengabdian'];
const currentSheet = ref(tipeSheet[0]);

const route = useRoute();
const routes = useRouter();
const update = ref(false);
const periode = ref(route.params.periode);
const jurusan = ref(route.params.jurusan);
const search = ref('');

let re = ref(0);

if (role !== null){
    watch([re, periode, jurusan, currentSheet, current], async () => {
        loading.value = true;
        let response = await fetch(`/api/getPenetapan/${jurusan.value}/${periode.value}/${currentSheet.value}/${current.value}`);
        standarData.value = await response.json();
        loading.value = false;
        // console.log(standarData.value);
    }, { immediate: true });
}

const submitData = (formData) => {
    let apiEndpoint = '';

    if (role.value === 'Pelaksanaan'){
        apiEndpoint = '/api/submitPelaksanaan';
    } else if (role.value === 'Evaluasi'){
        apiEndpoint = '/api/submitEvaluasi';
    } else if (role.value === 'Pengendalian'){
        apiEndpoint = '/api/submitPengendalian';
    }
    axios.post(apiEndpoint, { data: formData })
        .then(response => {
            console.log('Data submitted successfully:', response.data);
            re.value++;
            update.value = false;
        })
        .catch(error => {
            console.error('Error submitting data:', error.response.data);
        });
};
const checkFormDataBeforeLeave = (to, from, next) => {
    if (update.value) {
        const answer = confirm('Perubahan anda belum disave. Apakah anda yakin ingin meninggalkan halaman ini?');
        if (answer) {
            update.value = false;
            next();
        } else {
            next(false);
        }
    } else {
        next();
    }
};

const filtered = computed(()=>{
    return standarData.value.filter(stand =>
        stand.standar.toLowerCase().includes(search.value.toLowerCase())
    )
});


onBeforeMount(() => {
    routes.beforeEach((to, from, next) => {
        checkFormDataBeforeLeave(to, from, next);
    });
});
</script>

<template>
    <div class="c1">
        <router-link class="pop" to="/">Home</router-link>

        <p>tipe:</p>
        <select v-model="currentSheet" class="tipe" required>
            <option v-for="t in tipeSheet" :key="t">{{ t }}</option>
        </select>

        <custom-select :data="roleUser" :wid="10" @response="(data) => role = data"/>

        <br><br>

        <template v-for="t in tipe">
            <input type="radio" :id="t" :value="t" v-model="current">
            <label :for="t" style="margin-right: 0.5rem;">{{ t }}</label>
        </template>
        <!--        <h2 v-if="role !== null" class="font-poppin" v-once>{{role}}</h2>-->
        <!--        <p v-else>Anda Belum <router-link  to="/login">Login</router-link> 🫨</p>-->
        <!--        <custom-select :data="roleUser" :wid="10" @response="data => role = data"></custom-select>-->
        <div v-if="loading">
            <l-dot-stream size="60" speed="2.5" color="black"></l-dot-stream>
        </div>

        <div v-else-if="standarData === 'Null'">
            Belum ada data :)
        </div>

        <div v-else class="dt">
            <br>
            <input v-if="role !== null" v-model="search" placeholder="Search Standars">

            <Pelaksanaan
                v-if="role=== 'Pelaksanaan'"
                :data="filtered"
                :role="role"
                @submit-data="submitData"
                @update="(data) => update = data">
            </Pelaksanaan>
            <evaluasi
                v-else-if="role=== 'Evaluasi'"
                :data="filtered"
                :role="role"
                @submit-data="submitData"
                @update="(data) => update = data">
            </evaluasi>
            <pengendalian
                    v-else-if="role=== 'Pengendalian'"
                    :data="filtered"
                    @submit-data="submitData"
                    @update="(data) => update = data">
            </pengendalian>
        </div>
    </div>
</template>

<style scoped>
.c1 {
    position: absolute;
    width: 100vw;
    padding: 3%;
}

button {
    width: 5rem;
    height: 1rem;
}

.dt{
    padding-bottom: 3%;
}

.pop {
    padding: 3px;
    height: 2rem;
}

</style>
