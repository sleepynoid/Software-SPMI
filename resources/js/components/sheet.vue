<script setup>
import { ref, onMounted } from 'vue';
import Modal from "@/components/modal.vue";

// Definisikan variabel reaktif
const standarData = ref([]);
const loading = ref(true);
const data = ref([]);
const output = ref([]);

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

async function fetchProses() {
    try {
        let response = await fetch('/api/nyo');
        data.value = await response.json();
        // standarData.value = JSON.parse(Data.value);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchOutput() {
    try {
        let response = await fetch('/api/bak');
        output.value = await response.json();
        // standarData.value = JSON.parse(Data.value);
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}

console.log(data);

const popupTriggers = ref(false)
const selectedIndicator = ref(null)
const asu = ref("jamban")
const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value
}

const openPopup = (indicator) =>{
    selectedIndicator.value = indicator;
    togglePopup();
}

onMounted(fetchStandar)
onMounted(fetchOutput)
onMounted(fetchProses)

const user = ref(1);
</script>


<template>
    <router-link class="pop" to="/">Home</router-link>

    <button class="pop">Save</button>
    <br>
    <br>

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



          <td colspan=6>proses</td>
          <template v-for="(standar, index) in data">
              <tr>
                  <td :rowspan="standar.indicators.length+1">{{ standar.standar }}</td>
              </tr>
              <tr v-for="(indicator, index) in standar.indicators">
                  <td>{{ indicator.indicator }}</td>
                  <td>{{ indicator.target }}</td>
                  <td><input type="text"></td>
                  <td>
                      <input type="text">
                      <!--                      <div class="col kom">-->
                      <!--                          <input type="text">-->
                      <!--                          <input type="text">-->
                      <!--                      </div>-->
                  </td>
              </tr>
          </template>
          <td colspan=6>output</td>
          <template v-for="(standar, index) in output">
              <tr>
                  <td :rowspan="standar.indicators.length+1">{{ standar.standar }}</td>
              </tr>
              <tr v-for="(indicator, index) in standar.indicators">
                  <td>{{ indicator.indicator }}</td>
                  <td>{{ indicator.target }}</td>
                  <td><input type="text"></td>
                  <td>
                      <input type="text">
                      <!--                      <div class="col kom">-->
                      <!--                          <input type="text">-->
                      <!--                          <input type="text">-->
                      <!--                      </div>-->
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
