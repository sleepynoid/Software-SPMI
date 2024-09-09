<script setup>
import {ref, watch, watchEffect} from "vue";
import {onClickOutside, useMagicKeys} from "@vueuse/core";
import CustomLink from "@/components/comp/custom-link.vue";
const {escape} = useMagicKeys()

const props = defineProps({
    role: String,
    idBukti: Number,
    tipe: String,
})

const isModal = ref(false);
const modal = ref(null);
const count = ref(0);
const loading = ref(true);
const token = localStorage.getItem('token');

const savedLink = ref([]);
const judulLink = ref('')
const link = ref('')

watch(escape, (v) => {
    if (v) {
        isModal.value = false;
    }
})

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

onClickOutside(modal, () => (isModal.value = false));
</script>

<template>
    <button @click="isModal = true">Link</button>

    <Teleport to="#modal">
        <Transition name="modal">
            <div class="modal-bg" v-show="isModal">
                <div class="Modal" ref="modal">
                    <button @click="isModal = false" class="close-btn">x</button>
                    <h1></h1>
                    <div>
                        <h2 class="lb font-rubik">Link Bukti {{tipe}}</h2>
                        <p style="margin: 0 0 2% 0">Bukti :</p>
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
                    <br>
                    <button class="but" @click="">
                        <p>Submit</p>
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-bg {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100;
    background-color: rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.Modal {
    position: relative;
    background: #FFF;
    padding: 32px;
    border-radius: 1rem;
    box-shadow: 0 10px 5px 2px rgba(0, 0, 0, 0.1);

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        cursor: pointer;
    }
}

.modal-enter-active,
.modal-leave-active {
    transition: all 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.1);
}
</style>
