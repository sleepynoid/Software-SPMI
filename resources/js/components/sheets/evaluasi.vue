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

const adjusmentOptions = ['melampaui', 'mencapai', 'belum mencapai', 'menyimpang'];

const saveEval = debounce((idBuktiPelaksanaan, komenEval, adjusment, idE, idBE) => {

    const newData = {
        idBuktiPelaksanaan: idBuktiPelaksanaan,
        komentarEvaluasi: komenEval,
        adjusment,
        idEvaluasi: idE,
    };
    const index = dataEval.value.findIndex(item => item.idBuktiPelaksanaan === idBuktiPelaksanaan);
    if (index !== -1) {
        if (komenEval !== '' || adjusment !== ''){
            dataEval.value.splice(index, 1, newData);
            return;
        }
        if (komenEval !== '' || adjusment !== '' || idBE !== ''){
            dataEval.value.splice(index, 1, newData);
            return;
        }
        dataEval.value.splice(index, 1);
    } else {
        dataEval.value.push(newData);
    }

    if (dataEval.value.length > 0){
        emit('update', true);
    } else {
        emit('update', false);
    }

    console.log(dataEval.value);

}, 500);

function submit(){
    emit('submit-data', dataEval.value)
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
    <custom-button v-once @click="submit">Save</custom-button>
    <div class="table">
        <table :class="props.role">
            <thead>
            <tr>
                <th colspan="3"><h3 class="font-poppin">Penetapan</h3></th>
                <th colspan="2"><h3 class="font-poppin">Pelaksanaan</h3></th>
                <th colspan="5"><h3 class="font-poppin">Evaluasi</h3></th>
            </tr>
            <tr>
                <th><h3 class="font-poppin" style="width: 10rem">Standar</h3></th>
                <th><h3 class="font-poppin" style="width: 10rem">Indikator</h3></th>
                <th><h3 class="font-poppin">Target</h3></th>
                <th><h3 class="xd">Komentar</h3></th>
                <th><h3 class="font-poppin">Link Bukti</h3></th>
                <th colspan="2" class="xd"><h3 class="font-poppin">Komentar Evaluasi</h3></th>
                <th colspan="2"><h3 class="font-poppin">Adjusment</h3></th>
                <th><h3 class="font-poppin">Link Bukti Evaluasi</h3></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(standar, index) in props.data" :key="index">
                <tr>
                    <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>
                </tr>
                <tr v-for="indicator in standar.indicators" :key="indicator.id">
                    <td>{{ indicator.indicator }}</td>
                    <td>{{ indicator.target }}</td>

                    <td>
                        <p>{{ indicator.bukti }}</p>
                    </td>
                    <td>
                        <button
                            v-if="indicator.idBukti !== ''"
                            class="pop"
                            @click="openPopup(indicator.idBukti, 'Pelaksanaan')">
                            Link
                        </button>
                    </td>

                        <td colspan="2">
                            <textarea class="ta" v-model="indicator.evaluasi" @input="saveEval(indicator.idBukti, indicator.evaluasi, indicator.adjusment, indicator.idPelaksanaan, indicator.idBuktiEval)"></textarea>
                        </td>
                        <td colspan="2">
                            <select v-model="indicator.adjusment" @change="saveEval(indicator.idBukti, indicator.evaluasi, indicator.adjusment, indicator.idPelaksanaan, indicator.idBuktiEval)">
                                <option value=""></option>
<!--                                <option>{{ indicator.adjusment }}</option>-->
                                <option v-for="a in adjusmentOptions" :key="a">{{ a }}</option>
                            </select>
                        </td>
                        <td>
                            <button
                                v-if="indicator.idBuktiEval !== ''"
                                class="pop"
                                @click="openPopup(indicator.idBuktiEval, 'Evaluasi')"
                            >
                                Link
                            </button>
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
.table {
    overflow-x: auto;
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
