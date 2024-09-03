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
        adjusment: adjusment,
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
                <th colspan="3"><h4 class="font-poppin">Penetapan</h4></th>
                <th colspan="2"><h4 class="font-poppin">Pelaksanaan</h4></th>
                <th colspan="5"><h4 class="font-poppin">Evaluasi</h4></th>
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
                    <textarea v-model="data.indicator" ></textarea>
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
                            <textarea
                                class="ta"
                                :disabled="data.bukti === ''"
                                v-model="data.evaluasi"
                                @input="saveEval(data.idBukti, data.evaluasi, data.adjusment, data.idPelaksanaan, data.idBuktiEval)"
                            ></textarea>
                        </td>
                        <td colspan="2">
                            <select
                                v-model="data.adjusment"
                                :disabled="data.bukti === ''"
                                @change="saveEval(data.idBukti, data.evaluasi, data.adjusment, data.idPelaksanaan, data.idBuktiEval)">
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
