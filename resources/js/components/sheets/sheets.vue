<script setup>
import Modal from "@/components/sheets/modal.vue";
import {ref} from "vue";
import router from "@/router.js";
const props = defineProps({
  data: Object
});

const formData = ref([])

const save = (idIndikator, bukti) => {
    const newData = { id: idIndikator, bukti: bukti };
    const index = formData.value.findIndex(item => item.id === idIndikator);
    if (index !== -1) {
        formData.value.splice(index, 1, newData);
    } else {
        formData.value.push(newData);
    }
};

    const submitData = () => {
        // Kirim data ke backend menggunakan Axios
        axios.post('/api/submit', formData.value)
            .then(response => {
                console.log('Data submitted successfully:', response.data);
            })
            .catch(error => {
                console.error('Error submitting data:', error);
            });
    }


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
      <button @click="submitData"></button>
  <table>
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
    <template v-for="(standar, index) in data" :key="index">
      <tr>
        <td :rowspan="standar.indicators.length+1">{{ standar.standar }}</td>
      </tr>
      <tr v-for="(indicator, index) in standar.indicators">
        <td>{{ indicator.indicator }}</td>
        <td>{{ indicator.target }}</td>
        <td><input type="text" v-model="indicator.bukti" @input="save(indicator.id, indicator.bukti)"></td>
<!--        <td>{{ indicator.bukti }}</td>-->
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
</template>



<style scoped>

</style>
