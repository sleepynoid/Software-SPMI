<script setup>
import Modal from "@/components/sheets/modal.vue";
import {ref} from "vue";
const props = defineProps({
  data: Object, refresh: Function
});

const formData = ref([])
const mode = ref(true);
const role = ref('penetapan')

const save = (id, bukti) => {
    const newData = { id: id, bukti: bukti };
    const index = formData.value.findIndex(item => item.id === id);
    if (index !== -1) {
        formData.value.splice(index, 1, newData);
    } else {
        formData.value.push(newData);
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
</script>


<template>
    <label for="mo">mode: </label>
    <select id="mo" v-model="mode" style="width: 10rem;">
        <option :value="true">read</option>
        <option :value="false">edit</option>
    </select>
    <br>
    <br>
    <label for="mo">role: </label>
    <select id="mo" v-model="role" style="width: 10rem;">
        <option>penetapan</option>
        <option>super user</option>
    </select>
<!--    {{role}}-->
    <br>
    <br>
  <button v-if="!mode" @click="submitData">Save</button>
  <table>
    <thead>
    <tr>
      <th colspan="3">Penetapan</th>
      <th colspan="2">Pelaksanaan</th>
      <th colspan="3" v-if="role === 'super user'">Evaluasi</th>

    </tr>
    <tr>
      <th rowspan="">Standar</th>
      <th>Indicator</th>
      <th>Target</th>
      <th>Komentar</th>
      <th>Link Bukti</th>
      <th v-if="role === 'super user'">Komentar</th>
      <th v-if="role === 'super user'">Adjusment</th>
      <th v-if="role === 'super user'">Link Bukti</th>
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
            <p v-if="mode">{{indicator.bukti}}</p>
            <textarea v-else v-model="indicator.bukti" @input="save(indicator.id, indicator.bukti)"></textarea>
        </td>
        <td>
          <button v-if="indicator.idBukti !== '' " class="pop" @click="openPopup(indicator.idBukti)">Link</button>
        </td>
        <td v-if="role === 'super user'">daasd</td>
        <td v-if="role === 'super user'">daasd</td>
        <td v-if="role === 'super user'">daasd</td>
      </tr>
    </template>

    </tbody>
  </table>

  <Modal v-if="popupTriggers"
         :idBukti="selectedIndicator"
         :togglePopup="togglePopup"
         :mode="mode"
  >
  </Modal>
</template>



<style scoped>
table {
  width: 90vw;
  table-layout: fixed;
  border-collapse: collapse;
  margin-top: 1rem;
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
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}


</style>
