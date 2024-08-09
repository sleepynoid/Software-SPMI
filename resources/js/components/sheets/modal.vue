<script setup>
import {ref, watch, watchEffect} from "vue";
import {useMagicKeys} from "@vueuse/core";
const {escape} = useMagicKeys()

const props = defineProps({
    togglePopup: Function,
    idBukti: String,
    mode: Boolean,
});

watch(escape, (v) =>{
    if (v){
        props.togglePopup()
    }
})

const link = ref('')
const judulLink = ref('')
const count = ref(0);

const savedLink = ref([]);

watch(count, async () => {
    try {
        let response = await fetch(`/api/getLink/${props.idBukti}`);
        savedLink.value = await response.json();
        console.log(count.value);
    } catch (error){
        console.error('Error submitting data:', error.response.data);
    }
})

watchEffect(async ()=> {
    let response = await fetch(`/api/getLink/${props.idBukti}`);
    savedLink.value = await response.json();

    console.log(count.value);
})

const addLink = () => {
    if (judulLink.value === '' || link.value === ''){
        alert("tidak boleh kosong :)")
        return;
    }
    axios.post('/api/submitLink', {data: {idBukti: props.idBukti, judul_link: judulLink.value, link: link.value}})
        .then(response => {
            console.log('Data submitted successfully:', response.data);
            judulLink.value = '';
            link.value = '';
            count.value++;
        })
        .catch(error => {
            console.error('Error submitting data:', error.response.data);
        });
}
function removeTodo(IdLink) {
    axios.post('/api/deleteLink', {idLink: {idL: IdLink}})
        .then(response => {
            console.log('link terhapus:', response.data);
            count.value--;
        })
        .catch(err => {
            console.log('Error menghapus:', err.response.data);
        });
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
<!--            <h2>{{idBukti}}</h2>-->
<!--            <input type="text" name="" id="">-->

            <p v-if="savedLink.length < 1">Belum ada Link</p>
            <ul>
                <li v-for="link in savedLink" :key="link.id">
                    <div class="link">
                        <p>Judul: {{link.judul_link}}</p>
                        <button @click="openLink(link.link)">link</button>
                        <button @click="removeTodo(link.id)">X</button>
                    </div>
                    <!--      <p>Link: {{link.link}}</p>-->
                </li>
            </ul>

            <div v-if="!props.mode">
                <input v-model="judulLink" required placeholder="judul link">
                <input v-model="link" required placeholder="link">
                <button @click="addLink">add</button>
            </div>

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
