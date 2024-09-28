<script setup>
import { defineAsyncComponent, ref } from "vue";
import CustomButton from "@/components/comp/custom-button.vue";
import {debounce} from "lodash";

const Modal = defineAsyncComponent({
    loader: () => import('../sheets/modal.vue'),
});

const props = defineProps({
    data: Object,
    role: String,
});

const emit = defineEmits(['submit-data', 'update']);

const dataEval = ref([]);
const username = localStorage.getItem('name');

const adjusmentOptions = ['melampaui', 'mencapai', 'belum mencapai', 'menyimpang'];

const saveEval =(idBuktiPelaksanaan, komenEval, adjusment, idE, idInd, indicator) => {

    const newData = {
        idBuktiPelaksanaan: idBuktiPelaksanaan,
        komentarEvaluasi: komenEval,
        adjusment: adjusment,
        idEvaluasi: idE,
        userName: username,
        idInd: idInd,
        indicator: indicator,
    };
    // const index = dataEval.value.findIndex(item => item.idBuktiPelaksanaan === idBuktiPelaksanaan);
    // if (index !== -1) {
    //     if (komenEval !== '' || adjusment !== ''){
    //         dataEval.value.splice(index, 1, newData);
    //         return;
    //     }
    //     if (komenEval !== '' || adjusment !== '' || idBE !== ''){
    //         dataEval.value.splice(index, 1, newData);
    //         return;
    //     }
    //     dataEval.value.splice(index, 1);
    // } else {
    //     dataEval.value.push(newData);
    // }
    //
    // if (dataEval.value.length > 0){
    //     emit('update', true);
    // } else {
    //     emit('update', false);
    // }

    // console.log(dataEval.value);
    emit('submit-data', newData)
};

function submit(){
}

const popupTriggers = ref(false);
const selectedIndicator = ref(null);
const tipeLink = ref(null);

const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value;
};

const openPopup = (indicator, tipe) => {
    selectedIndicator.value = indicator;
    tipeLink.value = tipe;
    togglePopup();
};
</script>

<template>
    <br />
    <div class="table">
        <table :class="props.role">
            <thead>
            <tr>
                <th colspan="3"><h4 class="font-poppin">Penetapan</h4></th>
                <th colspan="2"><h4 class="font-poppin">Pelaksanaan</h4></th>
                <th colspan="5"><h4 class="font-poppin">Evaluasi</h4></th>
                <th rowspan="2" class="link">save</th>
            </tr>
            <tr>
                <th><div class="th">Standar</div></th>
                <th><div class="th">Indikator</div></th>
                <th>Target</th>
                <th><div class="xd">Komentar</div></th>
                <th><div class="font-poppin link">Link Bukti</div></th>
                <th colspan="2"><div class="font-poppin th">Komentar Evaluasi</div></th>
                <th colspan="2"><div class="font-poppin">Adjusment</div></th>
                <th><div class="font-poppin link">Link Bukti Evaluasi</div></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(standar, index) in props.data" :key="index">
                <tr>
                    <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>
                </tr>
                <tr v-for="data in standar.indicators" :key="data.id">
<!--                    <td>{{ data.indicator }}</td>-->
                    <td>
                        <textarea v-model="data.indicator" class="ta" ></textarea>
                    </td>
                    <td>{{ data.target }}</td>

                    <td>
                        <p>{{ data.bukti }}</p>
                    </td>
                    <td>
                        <button
                            v-if="data.idBukti !== ''"
                            class="pop"
                            @click="openPopup(data.idBukti, 'Pelaksanaan')">
                            Link
                        </button>
                    </td>

                        <td colspan="2">
                            <div class="edited">
                            <p>Last edited by: {{data.editorEval}}</p>
                            <textarea
                                class="ta"
                                :disabled="data.bukti === ''"
                                v-model="data.evaluasi"
                                @input="data.isUpdate = true"
                            ></textarea>
                            </div>
                        </td>
                        <td colspan="2">
                            <select
                                v-model="data.adjusment"
                                :disabled="data.bukti === ''"
                                @change="data.isUpdate = true">
                                <option value=""></option>
                                <option v-for="a in adjusmentOptions" :key="a">{{ a }}</option>
                            </select>
                        </td>
                        <td>
                            <button
                                v-if="data.idBuktiEval !== ''"
                                class="pop"
                                @click="openPopup(data.idBuktiEval, 'Evaluasi')"
                            >
                                Link
                            </button>
                        </td>
                        <td>
                            <button v-if="data.isUpdate" class="btnn" @click="saveEval(data.idBukti, data.evaluasi, data.adjusment, data.idPelaksanaan, data.id, data.indicator)">save</button>
                            <button v-else >save</button>
                        </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>

    <Modal
        v-if="popupTriggers"
        :togglePopup="togglePopup"
        :idBukti="selectedIndicator"
        :tipe="tipeLink"
        :role="role"
    />
</template>

<style scoped>

.btnn{
    background: yellow;
}

.table {
    overflow-x: auto;
    padding-bottom: 2%;
    padding-right: 2%;
}

.ta{
    height: 10rem;
}

.Evaluasi {
    width: 120vw;
    margin-top: 1rem;
}

.xd{
    width: 30rem;
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
