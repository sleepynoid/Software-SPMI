<script setup>
import Modal from "@/components/sheets/modal.vue";
import {ref} from "vue";
const props = defineProps({
  data: Object, refresh: Function
});

const formData = ref([])
const dataEval = ref([])
const mode = ref(true);
const role = ref('pelaksanaan')

const save = (idIndikator, bukti, idP) => {
    const newData = {idIndikator: idIndikator, bukti: bukti, idPelaksanaan: idP };
    const index = formData.value.findIndex(item => item.id === idIndikator);
    if (index !== -1) {
        formData.value.splice(index, 1, newData);
    } else {
        formData.value.push(newData);
    }
};

const saveEval = (idBuktiPelaksanaan, komenEval, adjusment, idP) => {
    if (komenEval === ''){
        alert("komentar harap di isi🗿");
    }else {
    const newData = { idBuktiPelaksanaan: idBuktiPelaksanaan, komentarEvaluasi: komenEval, adjusment: adjusment, idPelaksanaan:idP };
    const index = dataEval.value.findIndex(item => item.id === idBuktiPelaksanaan);
    if (index !== -1) {
        dataEval.value.splice(index, 1, newData);
    } else {
        dataEval.value.push(newData);
    }

    console.log(dataEval)
    }
};

    const submitData = () => {
        axios.post('/api/submit', {data: formData.value})
            .then(response => {
                console.log('Data submitted successfully:', response.data);
                props.refresh();
            })
            .catch(error => {
                console.error('Error submitting data:', error.response.data);
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

const adjusment = ['Melampaui', 'Mencapai', 'Belum mencapai','Menyimpang'];

// document.addEventListener("contextmenu", function (event){
//     alert("gaboleh klik kanan ea");
//     event.preventDefault();
// })
</script>


<template>
    <br>
    <label for="mo">role: </label>
    <select id="mo" v-model="role" style="width: 10rem;">
        <option>pelaksanaan</option>
        <option>superUser</option>
    </select>
<!--    {{role}}-->
    <br>
    <br>
  <button v-if="role === 'pelaksanaan'" @click="submitData">Save</button>
  <table :class="role">
    <thead>
    <tr>
      <th colspan="3">Penetapan</th>
      <th colspan="2">Pelaksanaan</th>
      <th colspan="5" v-if="role === 'superUser'">Evaluasi</th>
    </tr>
    <tr>
      <th rowspan="">Standar</th>
      <th>Indicator</th>
      <th>Target</th>
      <th>Komentar</th>
      <th>Link Bukti</th>
    <template v-if="role === 'superUser'">
      <th  colspan="2">Komentar</th>
      <th colspan="2">Adjusment</th>
      <th>Link Bukti</th>
        </template>
    </tr>
    </thead>
    <tbody>
    <template v-for="(standar, index) in data" :key="index">
      <tr>
        <td :rowspan="standar.indicators.length+1">{{ standar.standar }}</td>
      </tr>
      <tr v-for="indicator in standar.indicators">
        <td>{{ indicator.indicator }}</td>
        <td>{{ indicator.target }}</td>

        <td>
            <p v-if="role === 'superUser'">{{indicator.bukti}}</p>
            <textarea v-else v-model="indicator.bukti" @input="save(indicator.id, indicator.bukti, indicator.idPelaksanaan)"></textarea>
        </td>
        <td>
          <button
              v-if="indicator.idBukti !== '' "
              class="pop"
              @click="openPopup(indicator.idBukti)">Link
          </button>
        </td>


      <template v-if="role === 'superUser'">
        <td colspan="2">
            <textarea v-model="indicator.evaluasi"></textarea>
        </td>
        <td colspan="2">
            <select v-model="indicator.adjusment" @change="saveEval(indicator.idBukti, indicator.evaluasi, indicator.adjusment, indicator.idPelaksanaan)" >
                <option>{{indicator.adjusment}}</option>
                <option v-for="a in adjusment">{{a}}</option>
            </select>
        </td>
        <td>
            <button
                v-if="indicator.idEvaluasi !== '' "
                class="pop"
                @click="openPopup(indicator.idEvaluasi)">Link
            </button>
        </td>
      </template>
      </tr>
    </template>

    </tbody>
  </table>

  <Modal v-if="popupTriggers"
         :idBukti="selectedIndicator"
         :togglePopup="togglePopup"
         :role="role"
  >
  </Modal>
</template>



<style scoped>
.superUser {
  width: 120vw;
  table-layout: fixed;
  border-collapse: collapse;
  margin-top: 1rem;
}

.pelaksanaan{
    width: 92vw;
}

th, td {
  padding: 8px;
  border: 1px solid #ccc;
  text-align: center;
  word-wrap: break-word;
}

thead th {
  background-color: #f5f5f5;
}

textarea {
  width: 100%;
  box-sizing: border-box;
}

.pop {
  width: 80%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}


</style>
