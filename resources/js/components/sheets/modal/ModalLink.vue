<script setup lang="ts">
import {ref, toRefs, watch, watchEffect} from "vue";
import {onClickOutside, useMagicKeys} from "@vueuse/core";
import {deleteLink, fetchLink, LinkPayload, submitLink, useLink} from "../../../stores/useLink";
import {useToast} from "primevue";
import {useConfirm} from "primevue/useconfirm";
import ConfirmPopup from "primevue/confirmpopup";
const {escape} = useMagicKeys()

const props = defineProps<{
    role: string,
    idBukti: string,
    tipeLink: string,
}>();
const toast = useToast();
const confirm = useConfirm();

const { role, idBukti, tipeLink } = toRefs(props);
const isModal = ref<boolean>(false);
const loading = ref<boolean>(true);
const modal = ref<any>(null);
const list = ref<any[]>([]);
const payload = ref<LinkPayload>(
    {
        id: '',
        idBukti: '',
        judul_link: '',
        link: '',
        tipeLink: '',
    }
)

watch(escape, (v) => {
    if (v) {
        isModal.value = false;
        handleInitial()
    }
})
watch(isModal, async ()=> {
    if (isModal){
        list.value = await fetchLink(props.idBukti, props.tipeLink)
    }
})

const handleSubmitLink = async () => {
    payload.value.idBukti = props.idBukti.toString()
    payload.value.tipeLink = props.tipeLink

    if (
        !payload.value.link.toLowerCase().startsWith('https://') ||
        !payload.value.link.toLowerCase().startsWith('http://')
    ) {
        payload.value.link = 'https://' + payload.value.link;
    }

    const response = await submitLink(payload.value);
    list.value = await fetchLink(props.idBukti, props.tipeLink)
    handleInitial()
    if (response == 200){
        toast.add({ severity: 'success', summary: 'Success saving link', detail: 'Link saved', life: 3000 });
    } else {
        toast.add({ severity: 'danger', summary: 'Something went wrong', detail: 'Error submiting link', life: 3000 });
    }
}

const handleDeleteLink = async (event, idLink: string) => {
    confirm.require({
        target: event.currentTarget,
        group: 'headless',
        message: 'Delete link?',
        accept: async () => {
            await deleteLink(idLink)
            list.value = await fetchLink(props.idBukti, props.tipeLink)
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Link deleted!', life: 3000 });
        },
        reject: () => {
            // toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    });

}

const handleInitial = (data?: any) => {
    if (data){
        payload.value = {
            id: data.id.toString(),
            idBukti: data.id_bukti,
            judul_link: data.judul_link,
            link: data.link,
            tipeLink: data.tipe_link,
        }
    }else {
        id: '',
        payload.value = {
            idBukti: '',
            judul_link: '',
            link: '',
            tipeLink: '',
        }
    }

}

const openLink = (link) => {
    window.open(link, "_blank")
}

onClickOutside(modal, () => {
    isModal.value = false;
    handleInitial();
});

</script>

<template>
    <Toast />
    <ConfirmPopup group="headless">
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="rounded p-4">
                <span>{{ message?.message! }}</span>
                <div class="flex items-center gap-2 mt-4">
                    <Button label="Yes" severity="danger" @click="acceptCallback" size="small"></Button>
                    <Button label="Cancel" outlined @click="rejectCallback" severity="secondary" size="small" text></Button>
                </div>
            </div>
        </template>
    </ConfirmPopup>
    <Button
        @click="isModal = true"
        severity="contrast"
        variant="outlined"
        raised
    >Link</Button>

    <Teleport to="#modal">
        <Transition name="modal">
            <div class="modal-bg" v-show="isModal">
                <div class="Modal" ref="modal">
                    <button @click="isModal = false" class="close-btn">x</button>
                    <div class="flex flex-col gap-5">
                        <h1 class="text-2xl font-bold">Bukti {{tipeLink}}</h1>
                        <div class="w-full flex flex-row gap-3 items-center" v-if="role === props.tipeLink">
                            <InputGroup class="max-w-[50%]">
                                <InputGroupAddon>https://</InputGroupAddon>
                                <InputText
                                    v-model="payload.link"
                                    :invalid="!payload.link"
                                    placeholder="Website"
                                />
                            </InputGroup>
                            <InputText
                                v-model="payload.judul_link"
                                :invalid="!payload.judul_link"
                                placeholder="Keterangan Link"
                                class="w-[50%]"
                            />
                            <Button
                                @click="handleSubmitLink"
                                icon="pi pi-save"
                                severity="info"
                                :disabled="payload.judul_link && payload.link && useLink.loading"
                                class="w-[20rem]"
                                raised
                            />
                        </div>

                        <Fieldset legend="Link Bukti">
                            <DataTable
                                :value="list"
                                scrollable showGridlines stripedRows
                                scrollHeight="300px"
                                tableStyle="min-width: 100%"
                                class="tablecolor"
                            >
                                <Column field="judul_link" header="Keterangan Link" class="w-[80%]">
                                    <template #body="{ data }">
                                        <span>{{data?.judul_link!}}</span>
                                    </template>
                                </Column>
                                <Column field="name" header="Link">
                                    <template #body="{ data }">
                                        <Button
                                            v-tooltip.top="{ value: `${data?.link!}`, showDelay: 500, hideDelay: 300 }"
                                            @click="openLink(data?.link!)"
                                        >
                                            Visit
                                        </Button>
                                    </template>
                                </Column>
                                <Column field="category" header="Action">
                                    <template #body="{ data }">
                                        <ButtonGroup >
                                            <Button
                                                v-tooltip.top="{ value: `edit`, showDelay: 500, hideDelay: 300 }"
                                                icon="pi pi-pen-to-square"
                                                severity="info"
                                                raised
                                                :disabled="useLink.loading || !props.role == props.tipeLink"
                                                @click="handleInitial(data)"
                                            />
                                            <Button
                                                v-tooltip.top="{ value: `delete`, showDelay: 500, hideDelay: 300 }"
                                                icon="pi pi-trash"
                                                severity="danger"
                                                raised
                                                :disabled="useLink.loading || !props.role == props.tipeLink"
                                                @click="handleDeleteLink($event, data?.id!)"
                                            />
                                        </ButtonGroup>
                                    </template>
                                </Column>
                            </DataTable>
                        </Fieldset>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>

.tablecolor{
    --p-datatable-header-cell-background: rgb(233, 196, 106);
}

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
    width: 70vw;
    height: 80vh;
    max-height: 80vh;
    overflow-y: auto;
    padding: 32px;
    border-radius: 5px;
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
