<script setup>
import {ref, onMounted, watchEffect} from 'vue';
import Modal from "@/components/modal.vue";
import {useRoute} from "vue-router";
import { infinity } from 'ldrs'

infinity.register()

const standarData = ref([]);
const loading = ref(true);
const tipe = ['input', 'proses', 'output'];
const current = ref(tipe[0]);

const route = useRoute();
const idSheet = ref(route.params.idSheet);
console.log(idSheet);

watchEffect(async ()=> {
    loading.value = true;
    let response = await fetch(`/api/${current.value}`);
    standarData.value = await response.json();
    loading.value = false;
})

const popupTriggers = ref(false)
const selectedIndicator = ref(null)
const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value
}

const openPopup = (indicator) =>{
    selectedIndicator.value = indicator;
    togglePopup();
}

</script>


<template>
    <router-link class="pop" to="/">Home</router-link>
    <h1>{{idSheet}}</h1>

    <button class="pop">Save</button>
    <br>
    <br>
    <template v-for="t in tipe">
        <input type="radio"
        :id="t"
        :value="t"
        v-model="current">
        <label :for="t">{{t}}</label>
    </template>
    <div v-if="loading">
        <l-infinity
            size="55"
            stroke="4"
            stroke-length="0.15"
            bg-opacity="0.1"
            speed="1.3"
            color="black"
        ></l-infinity>
    </div>
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
                  <td :rowspan="standar.indicators.length+1">{{ standar.standar }}</td>
              </tr>
              <tr v-for="(indicator, index) in standar.indicators">
                  <td>{{ indicator.indicator }}</td>
                  <td>{{ indicator.target }}</td>
                  <td><input type="text" ></td>
                  <td>
                      <button class="pop" @click="openPopup(indicator.indicator)">Link</button>
                  </td>
              </tr>
          </template>

          </tbody>
      </table>

        <Modal v-if="popupTriggers"
               :idS="selectedIndicator"
               :togglePopup="togglePopup">
        </Modal>

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

.pop{
    padding: 3px;
    height: 2rem;
}
</style>
