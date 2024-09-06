<script setup>
import {ref} from "vue";
import {debounce} from "lodash";
import CustomButton from "@/components/comp/custom-button.vue";
import ModalPeningkatan from "@/components/sheets/modalPeningkatan.vue";
import Modal from "@/components/sheets/modal.vue";

const props = defineProps({
    data: Object,
});

const emit = defineEmits(['submit-data', 'update']);


const popupTriggers = ref(false);
const popupTriggers2 = ref(false);
const selectedIndicator = ref(null);
const tipeLink = ref(null);
const komentar = ref(null);

const data = ref([]);
const dataPeningkatan = ref([]);

const openPopup = (indicator, tipe, komen) => {
    selectedIndicator.value = indicator;
    tipeLink.value = tipe;
    komentar.value = komen;
    togglePopup();
};

const openPopup2 = (indicator, t, am, rtl, prtl ) => {
    selectedIndicator.value = indicator;
    data.value = {temuan: t, akarMasala: am, rtl: rtl, pelakRtl: prtl}
    togglePopup2();
};

const togglePopup2 = () => {
    popupTriggers2.value = !popupTriggers2.value;
};
const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value;
};

const savePeningkatan = (id, komen) => {

    const newData = {idBuktiPengendalian: id, komenPeningkatan: komen};
    // const index = dataPeningkatan.value.findIndex(item => item.idBP === id);
    // if (index !== -1){
    //     dataPeningkatan.value.splice(index, 1, newData);
    // }else {
    //     dataPeningkatan.value.push(newData);
    // }
    //
    // if (dataPeningkatan.value.length > 0){
    //     emit('update', true);
    // } else {
    //     emit('update', false);
    // }

    // console.log(dataPeningkatan.value)

    emit('submit-data', newData)
};

function submit(){
}

</script>

<template>
    <br>
    <div class="bodi">
        <table class="tb">
            <thead>
            <tr>
                <th colspan="3"><h4 class="font-poppin">Penetapan</h4></th>
                <th rowspan="2"><h4 class="font-poppin">Pelaksanaan</h4></th>
                <th rowspan="2"><h4 class="font-poppin">Evaluasi</h4></th>
                <th rowspan="2"><h4 class="font-poppin">Pengendalian</h4></th>
                <th><h4 class="font-poppin" style="width: 27rem;">Peningkatan</h4></th>
                <th rowspan="2" class="link">save</th>
            </tr>
            <tr>
                <th><div class="th">Standar</div></th>
                <th><div class="th">Indicator</div></th>
                <th>Target</th>
                <th>Komentar</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(standar, index) in props.data" :key="index">
                <tr>
                    <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>
                </tr>
                <tr v-for="data in standar.indicators" :key="data.id">
                    <td>{{ data.indicator }}</td>
                    <td>{{ data.target }}</td>
                    <!--             Pelaksanaan            -->
                    <td>
                        <button
                            v-if="data.idBukti !== ''"
                            @click="openPopup(data.idBukti, 'Pelaksanaan', data.bukti)"
                            :title="data.bukti">
                            Link
                        </button>
                    </td>
                    <!--             Evaluasi            -->
                    <td>
                        <button
                            v-if="data.evaluasi !== ''"
                            @click="openPopup(data.idBuktiEval, 'Evaluasi', data.evaluasi)"
                            :title="data.evaluasi">
                            Link
                        </button>
                    </td>
                    <td>
                        <button
                            v-if="data.idBPengendalian !== ''"
                            @click="openPopup2(data.idBPengendalian, data.temuan, data.akar_masalah, data.rtl, data.pelaksanaan_rtl)"
                            :title="data.temuan">
                            Link
                        </button>
                    </td>
                    <td>
                        <textarea
                            :disabled="data.idBPengendalian === ''"
                            class="ta"
                            v-model="data.komenPeningkatan"
                            @input="data.isUpdate=true"
                        ></textarea>
                    </td>
                    <td>
                        <button v-if="data.isUpdate" class="btnn" @click="savePeningkatan(data.idBPengendalian, data.komenPeningkatan)">save</button>
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
        :role=" 'Peningkatan' "
        :komentar="komentar"
    />

    <ModalPeningkatan
        v-if="popupTriggers2"
        :togglePopup2="togglePopup2"
        :idBukti="selectedIndicator"
        :tipe="'Pengendalian'"
        :data="data"
    />
</template>

<style scoped>

.btnn{
    background: yellow;
}

.bodi{
    overflow-x: auto;
    padding-right: 2%;
    margin-top: 1rem;
    padding-bottom: 2%;
}

.ta{
    width: 100%;
    height: 10rem;
    font-size: 1rem;
}

</style>
