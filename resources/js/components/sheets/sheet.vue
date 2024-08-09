<script setup>
import {ref, watchEffect} from 'vue';
import {useRoute} from "vue-router";
import { dotStream } from 'ldrs'
import Sheets from "@/components/sheets/sheets.vue"

dotStream.register()

const standarData = ref([]);
const loading = ref(true);
const tipe = ['input', 'proses', 'output'];
const current = ref(tipe[0]);

const tipeSheet = ['pendidikan', 'penelitian', 'pengabdian'];
const currentSheet = ref(tipeSheet[0]);

const route = useRoute();
const periode = ref(route.params.periode);
const jurusan = ref(route.params.jurusan);


watchEffect(async ()=> {
    loading.value = true;
    let response = await fetch(`/api/getPenetapan/${jurusan.value}/${periode.value}/${currentSheet.value}/${current.value}`);
    standarData.value = await response.json();

    loading.value = false;
    // console.log(standarData)
})

const refreshPage = () => {
    location.reload();
};

</script>


<template>
    <div class="bodi">

    <router-link class="pop" to="/">Home</router-link>
<!--    <button class="pop">Save</button>-->
    <p>tipe:</p>
    <select v-model="currentSheet" class="tipe" required>
        <option v-for="t in tipeSheet">{{t}}</option>
    </select>
<!--    {{currentSheet}}-->
    <br>
    <br>
    <template v-for="t in tipe">
        <input type="radio"
        :id="t"
        :value="t"
        v-model="current">
        <label :for="t" style="margin-right: 0.5rem;">{{t}}</label>
    </template>
    <div v-if="loading">
        <l-dot-stream
            size="60"
            speed="2.5"
            color="black"
        ></l-dot-stream>
    </div>
    <div v-else-if="standarData ==='Null'">
        Belum ada data :)
    </div>
    <div v-else class="dt">
    <Sheets
        :data="standarData"
        :refresh="refreshPage"/>
    </div>
    </div>

</template>


<style scoped>
.bodi{
    width: 100vw;
    height: 100vh;
    padding: 3%;
}

button {
    width: 5rem;
    height: 1rem;
}

.pop{
    padding: 3px;
    height: 2rem;
}

.dt{
  padding-bottom: 3%;
}
</style>
