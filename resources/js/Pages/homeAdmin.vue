<template>
    <Toast />
    <main class="max-h-full h-full w-full">
        <div class="card">
            <Toolbar class="mb-4">
                <template #start>
                    <h1>Admin Dashboard</h1>
                </template>
                <template #end>
                    <div>
                        <Button style="width: auto" label="Register User" icon="pi pi-plus" @click="showModal = true" />
                    </div>
                </template>
            </Toolbar>

            <DataTable :value="users" :paginator="true" :rows="10" :rowsPerPageOptions="[5, 10, 20]"
                responsiveLayout="scroll">
                <Column field="email" header="Email" sortable></Column>
                <Column field="name" header="Name" sortable></Column>
                <Column field="role" header="Role" sortable></Column>
                <Column header="Actions">
                    <template #body="slotProps">
                        <Button icon="pi pi-ellipsis-v" @click="toggleMenu($event, slotProps.data, menu)"
                            aria-haspopup="true" aria-controls="overlay_menu" class="p-button-text" />
                        <Menu id="overlay_menu" ref="menu" :model="menuItems" :popup="true"
                            :instance="slotProps.data" />
                    </template>
                </Column>
            </DataTable>

            <!-- Registration Dialog -->
            <Dialog v-model:visible="showModal" modal header="Register User" :style="{ width: '450px' }"
                class="p-fluid">
                <div class="flex items-center gap-4 mb-4">
                    <label for="email" class="font-semibold w-24">Email</label>
                    <div class="flex-auto flex flex-col">
                        <InputText id="email" v-model="newUser.email" required type="email" :class="{
                            'p-invalid': submitted && !newUser.email,
                        }" autocomplete="off" />
                        <small v-if="submitted && !newUser.email" class="text-red-500 flex-1">Email is required.</small>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="name" class="font-semibold w-24">Name</label>
                    <div class="flex-auto flex flex-col">
                        <InputText id="name" v-model="newUser.name" required :class="{
                            'p-invalid': submitted && !newUser.name,
                        }" autocomplete="off" />
                        <small v-if="submitted && !newUser.name" class="text-red-500 flex-1">Name is required.</small>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="role" class="font-semibold w-24">Role</label>
                    <div class="flex-auto flex flex-col">
                        <Select id="role" v-model="newUser.role" :options="roles" optionLabel="name" optionValue="value"
                            placeholder="Select a Role" :class="{
                                'p-invalid': submitted && !newUser.role,
                            }" />
                        <small v-if="submitted && !newUser.role" class="text-red-500 flex-1">Role is required.</small>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-8">
                    <label for="password" class="font-semibold w-24">Password</label>
                    <div class="flex-auto flex flex-col">
                        <div class="flex items-center gap-2 w-full">
                            <Password id="password" v-model="newUser.password" required toggleMask :class="{
                                'p-invalid': submitted && !newUser.password,
                            }" />
                        </div>
                        <small v-if="submitted && !newUser.password" class="text-red-500 flex-1">Password is
                            required.</small>
                    </div>
                </div>
                <template #footer>
                    <div class="flex justify-end gap-2">
                        <Button label="Cancel" icon="pi pi-times" @click="closeModal" class="p-button-text"
                            style="width: auto" />
                        <Button label="Save" icon="pi pi-check" @click="registerUser" autofocus style="width: auto" />
                    </div>
                </template>
            </Dialog>

            <!-- Edit Role Dialog -->
            <Dialog v-model:visible="showEditModal" modal header="Edit User Role" :style="{ width: '450px' }"
                class="p-fluid">
                <div class="flex items-center gap-4 mb-4">
                    <label class="font-semibold w-24">User</label>
                    <div class="flex-auto">
                        <span class="font-medium">{{
                            selectedUser?.name
                            }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <label for="editRole" class="font-semibold w-24">Role</label>
                    <div class="flex-auto flex flex-col">
                        <Select id="editRole" v-model="selectedUser.role" :options="roles" optionLabel="name"
                            optionValue="value" placeholder="Select a Role" :class="{
                                'p-invalid':
                                    editSubmitted && !selectedUser?.role,
                            }" />
                        <small v-if="editSubmitted && !selectedUser?.role" class="text-red-500 flex-1">Role is
                            required.</small>
                    </div>
                </div>

                <template #footer>
                    <div class="flex justify-end gap-2">
                        <Button label="Cancel" icon="pi pi-times" @click="showEditModal = false" class="p-button-text"
                            style="width: auto" />
                        <Button label="Save" icon="pi pi-check" @click="updateUserRole" autofocus style="width: auto" />
                    </div>
                </template>
            </Dialog>

            <!-- Reset Password Dialog -->
            <Dialog v-model:visible="showResetModal" modal header="Reset User Password" :style="{ width: '450px' }"
                class="p-fluid">
                <div class="flex items-center gap-4 mb-4">
                    <label class="font-semibold w-24">User</label>
                    <div class="flex-auto">
                        <span class="font-medium">{{ selectedUser?.name }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <label for="newPassword" class="font-semibold w-24">New Password</label>
                    <div class="flex-auto flex flex-col">
                        <Password id="newPassword" v-model="newPassword" required toggleMask
                            :class="{ 'p-invalid': resetSubmitted && !newPassword }" />
                        <small v-if="resetSubmitted && !newPassword" class="text-red-500 flex-1">Password is
                            required.</small>
                    </div>
                </div>

                <template #footer>
                    <div class="flex justify-end gap-2">
                        <Button label="Cancel" icon="pi pi-times" @click="showResetModal = false" class="p-button-text"
                            style="width: auto" />
                        <Button label="Reset Password" icon="pi pi-check" @click="resetUserPassword" autofocus
                            style="width: auto" />
                    </div>
                </template>
            </Dialog>
            <!-- User History Dialog -->
            <Dialog v-model:visible="showHistoryModal" header="User History" :style="{ width: '70vw' }" :modal="true">
                <div v-if="isLoadingHistory" class="p-d-flex p-jc-center p-ai-center p-p-4">
                    <ProgressSpinner />
                </div>
                <div v-else-if="historyError" class="p-error p-p-4">{{ historyError }}</div>
                <div v-else>
                    <div class="flex items-center gap-4 mb-4">
                        <label class="font-semibold w-24">User</label>
                        <div class="flex-auto">
                            <span class="font-medium">{{ selectedUser?.name }}</span>
                        </div>
                    </div>

                    <DataTable :value="userHistory" :paginator="true" :rows="10" :rowsPerPageOptions="[5, 10, 25]"
                        responsiveLayout="scroll">
                        <Column field="created_at" header="Timestamp" :sortable="true">
                            <template #body="{ data }">
                                {{ formatDate(data.created_at) }}
                            </template>
                        </Column>
                        <Column field="url" header="Endpoint" :sortable="true">
                            <template #body="{ data }">
                                {{ extractPath(data.url) }}
                            </template>
                        </Column>
                        <Column field="method" header="Method" :sortable="true"></Column>
                        <Column field="status_code" header="Status" :sortable="true"></Column>
                    </DataTable>
                </div>
                <template #footer>
                    <Button label="Close" icon="pi pi-times" @click="showHistoryModal = false" class="p-button-text" />
                </template>
            </Dialog>
        </div>
    </main>
</template>

<script setup>
import { onMounted, watch } from "vue";
import { useUserManagement } from "@/composables/useUserManagement";
import { useUserActions } from "@/composables/useUserActions";
import { Toast, useToast } from "primevue";
import { usePage } from "@inertiajs/vue3";

const toast = useToast();
const {
    users,
    showModal,
    showEditModal,
    showHistoryModal,
    submitted,
    editSubmitted,
    menu,
    newUser,
    selectedUser,
    roles,
    fetchUsers,
    registerUser,
    closeModal,
    updateUserRole,
    showResetModal,
    newPassword,
    resetSubmitted,
    resetUserPassword,
    userHistory,
    isLoadingHistory,
    historyError,
    viewUserHistory,
    fetchUserHistory,
} = useUserManagement();

const { menuItems, toggleMenu } = useUserActions(
    (value) => (showEditModal.value = value),
    (value) => (selectedUser.value = value),
    (value) => (showResetModal.value = value),
    (value) => {
        showHistoryModal.value = value;
        console.log(showHistoryModal.value);
        fetchUserHistory();
    }
);


onMounted(() => {
    const flash = usePage().props.flash;
    if (flash && flash.success) {
        toast.add({ severity: 'success', summary: 'Success', detail: flash.success, life: 3000 });
    }
    fetchUsers();
});

const formatDate = (timestamp) => {
    if (!timestamp) return "-";
    return new Date(timestamp).toISOString().split("T")[0];
};
const extractPath = (url) => {
    try {
        return new URL(url).pathname;
    } catch (error) {
        return url;
    }
};
</script>

<style scoped>


/* Remove other styles as they will be handled by PrimeVue */
</style>
