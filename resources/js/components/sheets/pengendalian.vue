<script setup>
import {ref} from "vue";
import Modal from "@/components/sheets/modal.vue";
import {debounce} from "lodash";
import CustomButton from "@/components/comp/custom-button.vue";

const props = defineProps({
    data: Object,
});

const emit = defineEmits(['submit-data', 'update']);


const popupTriggers = ref(false);
const selectedIndicator = ref(null);
const tipeLink = ref(null);
const komentar = ref(null);

const dataPengendalian = ref([]);

const openPopup = (indicator, tipe, komen) => {
    selectedIndicator.value = indicator;
    tipeLink.value = tipe;
    komentar.value = komen;
    togglePopup();
};
const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value;
};

const savePengendalian = (idBEval, temuan, akarMas, rtl, pelakRtl) => {

    const newData = {
        id_bukti_evaluasi: idBEval,
        temuan: temuan,
        akar_masalah: akarMas,
        rtl: rtl,
        pelaksanaan_rtl: pelakRtl
    };
    // const index = dataPengendalian.value.findIndex(item => item.id_bukti_evaluasi === idBEval);
    // if (index !== -1){
    //     dataPengendalian.value.splice(index, 1, newData);
    // }else {
    //     dataPengendalian.value.push(newData);
    // }
    //
    // if (dataPengendalian.value.length > 0){
    //     emit('update', true);
    // } else {
    //     emit('update', false);
    // }

    console.log(newData)
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
            <th colspan="4"><h4 class="font-poppin">Pengendalian</h4></th>
            <th rowspan="2" class="link">save</th>
        </tr>
        <tr>
            <th><div class="th">Standar</div></th>
            <th><div class="th">Indicator</div></th>
            <th>Target</th>
            <th>Temuan</th>
            <th>Akar Masalah</th>
            <th>RTL</th>
            <th>Pelaksanaan RTL</th>
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
                        <textarea
                            :disabled="data.idBuktiEval === ''"
                            class="ta"
                            v-model="data.temuan"
                            @input="data.isUpdate = true"
                        ></textarea>
                    </td>
                    <td>
                        <textarea
                            :disabled="data.idBuktiEval === ''"
                            class="ta"
                            v-model="data.akar_masalah"
                            @input="data.isUpdate = true"
                        ></textarea>
                    </td>
                    <td>
                        <textarea
                            :disabled="data.idBuktiEval === ''"
                            class="ta"
                            v-model="data.rtl"
                            @input="data.isUpdate = true"
                        ></textarea>
                    </td>
                    <td>
                        <textarea
                            :disabled="data.idBuktiEval === ''"
                            class="ta"
                            v-model="data.pelaksanaan_rtl"
                            @input="data.isUpdate = true"
                        ></textarea>
                    </td>
                    <td>
                        <button v-if="data.isUpdate" class="btnn" @click="savePengendalian(data.idBuktiEval, data.temuan, data.akar_masalah, data.rtl, data.pelaksanaan_rtl)">save</button>
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
        :role=" 'Pengendalian' "
        :komentar="komentar"
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
    width: 15rem;
    height: 10rem;
    font-size: 1rem;
}



</style>
