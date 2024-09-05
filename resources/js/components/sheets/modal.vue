<script setup>
import {ref, watch, watchEffect} from "vue";
import {onClickOutside, useMagicKeys} from "@vueuse/core";
import CustomLink from "@/components/comp/custom-link.vue";
const {escape} = useMagicKeys()

const props = defineProps({
    togglePopup: Function,
    idBukti: Number,
    tipe: String,
    role: String,
    komentar: String
});

watch(escape, (v) =>{
    if (v){
        props.togglePopup()
    }
})

const modal = ref(null);
onClickOutside(modal, ()=> (props.togglePopup()))

const link = ref('')
const judulLink = ref('')
const count = ref(0);
const loading = ref(true);
const savedLink = ref([]);
const token = localStorage.getItem('token');

watch(count, async () => {
    try {
        loading.value = true;
        let response = await fetch(`/api/getLink/${props.idBukti}/${props.tipe}`);
        savedLink.value = await response.json();
        loading.value = false;
        console.log(count.value);
    } catch (error){
        console.error('Error submitting data:', error.response.data);
    }
})

watchEffect(async ()=> {
    loading.value = true;
    let response = await fetch(`/api/getLink/${props.idBukti}/${props.tipe}`);
    savedLink.value = await response.json();
    loading.value = false;

    // console.log(count.value);
})

const addLink = () => {
    if (judulLink.value === '' || link.value === ''){
        alert("judul atau link bukti tidak boleh kosong :)")
        return;
    }
    axios.post('/api/submitLink',
        {
            idBukti: props.idBukti,
            judul_link: judulLink.value,
            link: link.value,
            tipeLink: props.tipe,
        },{
            headers: {
                "Authorization": `Bearer ${token}`
            }
        })
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
    if (confirm("Hapus Link??") === true) {
        axios.post('/api/deleteLink', {idLink: {idL: IdLink}}, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        })
            .then(response => {
                console.log('link terhapus:', response.data);
                count.value--;
            })
            .catch(err => {
                console.log('Error menghapus:', err.response.data);
            });
    }
}

const openLink = (link) => {
    window.open(link, "_blank")
}
</script>

<template>
    <div class="popup">
        <div class="popup-inner" :class="role" ref="modal">
            <slot/>
            <div class="pe" v-if="role === 'Pengendalian' || role === 'Peningkatan'">
                <h1>Bukti {{tipe}}</h1>
                <p class="peng">{{komentar}}</p>
            </div>
            <h2 v-else class="lb font-rubik">Link Bukti {{props.tipe}}</h2>

            <p v-if="loading">Loading...</p>
            <div>
                <p style="margin: 0 0 2% 0">Link Bukti:</p>
                <p v-if="savedLink.length < 1" :hidden="loading">Belum ada Link</p>
                <ol>
                    <li v-for="link in savedLink" :key="link.id">
                        <custom-link :link="link" :open="openLink" :remove="removeTodo" :role="props.role" :tipe="props.tipe "/>
                    </li>
                </ol>
                <div v-if="props.role === props.tipe" class="addLink">
                    <input v-model="judulLink" required placeholder="judul link">
                    <input v-model="link" required placeholder="link">
                    <button @click="addLink">add</button>
                </div>
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

.Pengendalian{
    justify-content: space-between;
    width: 50vw;
    height: 70vh;
}

.Peningkatan{
    justify-content: space-between;
    width: 50vw;
    height: 70vh;
}

.pe{
    height: 50%;
}
.peng{
    height: 70%;
    overflow-y: auto;
    padding-right: 2%;
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
