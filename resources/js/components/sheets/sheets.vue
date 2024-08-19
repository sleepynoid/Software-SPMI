<script setup>
import {defineAsyncComponent, ref} from "vue";
import CustomButton from "@/components/comp/custom-button.vue";
import CustomSelect from "@/components/comp/custom-select.vue";
const Modal = defineAsyncComponent(({
    loader: () => import('../sheets/modal.vue'),
}));
const props = defineProps({
    data: Object, refresh: Function
});

const formData = ref([])
const dataEval = ref([])
const roleUser = ['Pelaksanaan', 'Evaluasi'];
const role = ref(roleUser[0]);

const adjusment = ['melampaui', 'mencapai', 'belum mencapai','menyimpang'];

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
        const newData = { idBuktiPelaksanaan: idBuktiPelaksanaan, komentarEvaluasi: komenEval, adjusment: adjusment, idEvaluasi:idP };
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
    if (role.value === 'Pelaksanaan'){
        axios.post('/api/submitPelaksanaan', {data: formData.value})
            .then(response => {
                console.log('Data submitted successfully:', response.data);
                props.refresh();
            })
            .catch(error => {
                console.error('Error submitting data:', error.response.data);
            });
    } else {
        axios.post('/api/submitEvaluasi', {data: dataEval.value})
            .then(response => {
                console.log('Data submitted successfully:', response.data);
                // props.refresh();
            })
            .catch(error => {
                console.error('Error submitting data:', error.response.data);
            });
    }
}


const popupTriggers = ref(false)
const selectedIndicator = ref(null)
const tipeLink = ref(null);
const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value
}

const openPopup = (indicator, tipe) =>{
    selectedIndicator.value = indicator;
    tipeLink.value = tipe;
    togglePopup();
}


// document.addEventListener("contextmenu", function (event){
//     alert("gaboleh klik kanan ea");
//     event.preventDefault();
// })
</script>


<template>
    <br>
    <h2 class="font-poppin" v-once>Role: </h2>

    <custom-select :data="roleUser" :wid="10" @response="(data) => role = data"/>
    <br>
    <br>
    <custom-button v-once @click="submitData">Save</custom-button>
    <div class="table">
        <table :class="role">
            <thead>
            <tr>
                <th colspan="3"><h3 class="font-poppin">Penetapan</h3></th>
                <th colspan="2"><h3 class="font-poppin">Pelaksanaan</h3></th>
                <th colspan="5" v-if="role === 'Evaluasi'"><h3 class="font-poppin">Evaluasi</h3></th>
            </tr>
            <tr>
                <th rowspan=""><h3 class="font-poppin">Standar</h3></th>
                <th><h3 class="font-poppin">Indicator</h3></th>
                <th><h3 class="font-poppin">Target</h3></th>
                <th><h3 class="font-poppin">Komentar</h3></th>
                <th><h3 class="font-poppin">Link Bukti</h3></th>
                <template v-if="role === 'Evaluasi'">
                    <th colspan="2"><h3 class="font-poppin">Komentar</h3></th>
                    <th colspan="2"><h3 class="font-poppin">Adjusment</h3></th>
                    <th><h3 class="font-poppin">Link Bukti</h3></th>
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
                        <p v-if="role === 'Evaluasi'">{{indicator.bukti}}</p>
                        <textarea v-else v-model="indicator.bukti" @input="save(indicator.id, indicator.bukti, indicator.idPelaksanaan)"></textarea>
                    </td>
                    <td>
                        <button
                            v-if="indicator.idBukti !== '' "
                            class="pop"
                            @click="openPopup(indicator.idBukti, 'Pelaksanaan')">Link
                        </button>
                    </td>


                    <template v-if="role === 'Evaluasi'">
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
                                @click="openPopup(indicator.idEvaluasi, 'Evaluasi')">Link
                            </button>
                        </td>
                    </template>
                </tr>
            </template>

            </tbody>
        </table>
    </div>

    <Modal v-if="popupTriggers"
           :togglePopup="togglePopup"
           :idBukti="selectedIndicator"
           :tipe="tipeLink"
           :role="role">
    </Modal>
</template>



<style scoped>

.table{
    overflow-x: auto;
    padding-right: 2%;
}


.Evaluasi {
    width: 120vw;
    margin-top: 1rem;
}

.Pelaksanaan{
    width: 92vw;
    margin-top: 1rem;
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
