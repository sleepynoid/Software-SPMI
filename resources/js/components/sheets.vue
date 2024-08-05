<script setup>
import Modal from "@/components/modal.vue";
import {ref} from "vue";
const props = defineProps({
  data: Object
});

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
    <template v-for="(standar, index) in data">
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
</template>



<style scoped>

</style>
