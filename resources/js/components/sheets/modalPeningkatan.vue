<script setup>
import {ref, watch} from "vue";
import {onClickOutside, useMagicKeys} from "@vueuse/core";
import CustomLink from "@/components/comp/custom-link.vue";
const {escape} = useMagicKeys()

const props = defineProps({
    togglePopup2: Function,
    idBukti: Number,
    tipe: String,
    data: Object
});

watch(escape, (v) =>{
    if (v){
        props.togglePopup2()
    }
})

const modal = ref(null);
onClickOutside(modal, ()=> (props.togglePopup2()))

const count = ref(0);
// const loading = ref(true);
</script>

<template>
    <div class="popup">
        <div class="popup-inner" ref="modal">
            <slot/>
            <div class="pe">
                <h1>Bukti {{tipe}}</h1>
                <br>
                <table class="pening">
                    <thead>
                        <tr>
                            <th>Temuan</th>
                            <th>Akar Masalah</th>
                            <th>RTL</th>
                            <th>Pelaksanaan RTL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ data.temuan }}</td>
                            <td>{{data.akarMasala}}</td>
                            <td>{{data.rtl}}</td>
                            <td>{{data.pelakRtl}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button class="popup-close" @click="props.togglePopup" :title="'or press esc to close'">Close</button>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.popup{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99;
    background-color: rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.popup-inner{
    width: 70%;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    text-align: justify;
    position: relative;
    margin-bottom: 10%;
    background: #FFF;
    padding: 32px;
    border-radius: 1rem;
    box-shadow: 0 10px 5px 2px rgba(0,0,0,0.1);
}

.pe{
    width: 100%;
    height: 100%;
}
.peng{
    height: 80%;
    overflow-y: auto;
    padding-right: 2%;
}

.pening{
    width: 100%;
    height: 10rem;
}

.lb{
    margin-bottom: 1rem;
}

.addLink{
    display: flex;
    width: 100%;
    margin-top: 5%;
}
</style>
