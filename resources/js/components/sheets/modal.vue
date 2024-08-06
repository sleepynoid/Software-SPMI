<script setup>
import {ref, watch} from "vue";
import {useMagicKeys} from "@vueuse/core";
const {escape} = useMagicKeys()



const props = defineProps({
    togglePopup: Function,
    idS: String
});

watch(escape, (v) =>{
    if (v){
        props.togglePopup()
    }
})


const link = ref('')
const judulLink = ref('')
const links = ref([])
let id = 0;

function addLink(){
    links.value.push({id: id++, judul: judulLink.value, link: link.value})
    judulLink.value = '';
    link.value = '';
}

function removeTodo(link) {
    links.value = links.value.filter((t) => t !== link)
}

const openLink = (link) => {
    window.open(link, "_blank")
}
</script>

<template>
    <div class="popup">
        <div class="popup-inner">
            <slot/>
            <h2>Link Bukti Pelaksanaan</h2>
            <h2>{{idS}}</h2>
<!--            <input type="text" name="" id="">-->

            <ul>
                <li v-for="link in links" :key="link.id">
                    <div class="link">
                        <p>Judul: {{link.judul}}</p>
                        <button @click="openLink(link.link)">link</button>
                    <button @click="removeTodo(link)">X</button>
                    </div>
                    <!--      <p>Link: {{link.link}}</p>-->
                </li>
            </ul>

            <input v-model="judulLink" required placeholder="judul link">
            <input v-model="link" required placeholder="judul link">

            <button @click="addLink">add</button>
            <br>
            <br>
            <button class="popup-close" @click="props.togglePopup">CLose</button>
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
    position: relative;
    margin-bottom: 10rem;
    background: #FFF;
    padding: 32px;
}

.link{
    display: flex;
    gap: 1rem;
}
</style>
