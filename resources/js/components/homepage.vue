<script setup>
import {ref, watchEffect} from "vue";
import { dotStream } from 'ldrs'
dotStream.register()

const message = ref('upload new sheet')
const jurusan = ref('a')
const idS = ref('0')
const periode = ref('0')
const per = ref([])
const loading = ref(true);


function notify(){
    alert('Pilih jurusan 😈')
}

watchEffect(async ()=> {
    loading.value = true;
    let response = await fetch(`/api/getPeriode/${jurusan.value}`);
    per.value = await response.json();
    loading.value = false;

    console.log(per)
})

watchEffect(async ()=> {
    // loading.value = true;
    let response = await fetch(`/api/getSheet/${jurusan.value}/${periode.value}`);
    idS.value = await response.json();
    // loading.value = false;

    console.log(idS)
})



</script>

<template>

    <div class="c1">
        <h1>Mode Super User</h1>
        <button>
              <router-link
                  to="/import"
                  class="custom-router-link"
                  :title="message"
              ><h4>+</h4></router-link>
        </button>
    </div>

    <div class="c2">
        <h5>Pilih Jurusan:</h5>
        <div>
            <select v-model="jurusan" class="pilihSheet" required>
                <option disabled value="">select sheet</option>
                <option>Teknik Informatika</option>
                <option>Teknik Elektro</option>
                <option>Teknik Lingkungan</option>
                <option>Sistem Informasi</option>
            </select>

            <button v-if="jurusan == 'a'" @click.prevent="notify"><h5>go</h5></button>
            <div v-if="loading">
                <l-dot-stream
                    size="60"
                    speed="2.5"
                    color="black"
                ></l-dot-stream>
            </div>
            <div v-else-if="per.length == 0">
                <p>Sheet dengan jurusan {{ jurusan }} belum ada</p>
            </div>
            <div v-else class="periode">
                <h5>Pilih Periode:</h5>
                <select v-model="periode" class="pilihSheet" required>
                    <option v-for="p in per">{{ p.periode }}</option>
                </select>
<!--                {{idS}}-->

                <button>
                    <router-link
                        :to="{ name: 'Sheet', params: {idsheet: idS}}"
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

.c1{
    display: flex;
    gap: 1rem;
    align-items: center;
}

.c2{
    //display: flex;
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

</style>
