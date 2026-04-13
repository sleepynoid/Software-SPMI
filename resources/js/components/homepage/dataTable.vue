<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { FilterMatchMode } from "@primevue/core";

// Data sheets dikirim dari HomeController sebagai Inertia prop
const page = usePage();
const availableSheets = computed(() => page.props.sheets ?? []);
const loading = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    jurusan: { value: null, matchMode: FilterMatchMode.IN },
    tipe: { value: null, matchMode: FilterMatchMode.IN },
    periode: { value: null, matchMode: FilterMatchMode.IN },
});
// Selected Row and Fields
const selectedRow = ref(null);
const selectedMajor = ref(null); // Selected major
const selectedType = ref(null); // Selected major
const periode = ref(null); // Selected period


// Variabel for filter options
const jurusanOptions = computed(() =>
    Array.from(new Set(availableSheets.value.map((sheet) => sheet.jurusan)))
);
const tipeOptions = computed(() =>
    Array.from(new Set(availableSheets.value.map((sheet) => sheet.tipe)))
);
const periodeOptions = computed(() =>
    Array.from(new Set(availableSheets.value.map((sheet) => sheet.periode)))
);

// Filtered Data
const filteredData = computed(() => {
    let data = availableSheets.value;

    // Apply global search filter
    if (filters.value.global.value) {
        const searchText = filters.value.global.value.toLowerCase();
        data = data.filter((row) => {
            return (
                row.jurusan.toLowerCase().includes(searchText) ||
                row.tipe.toLowerCase().includes(searchText) ||
                row.periode.toLowerCase().includes(searchText)
            );
        });
    }

    // Apply column-specific filters
    if (filters.value.jurusan.value && filters.value.jurusan.value.length > 0) {
        data = data.filter((row) =>
            filters.value.jurusan.value.includes(row.jurusan)
        );
    }
    if (filters.value.tipe.value && filters.value.tipe.value.length > 0) {
        data = data.filter((row) =>
            filters.value.tipe.value.includes(row.tipe)
        );
    }
    if (filters.value.periode.value && filters.value.periode.value.length > 0) {
        data = data.filter((row) =>
            filters.value.periode.value.includes(row.periode)
        );
    }

    return data;
});

// Clear Filter
const clearFilter = () => {
    filters.value.global.value = null;
    filters.value.jurusan.value = null;
    filters.value.tipe.value = null;
    filters.value.periode.value = null;
};

// Row Select Event
const onRowSelect = (event) => {
    selectedRow.value = event.data; // Set the selected row
    selectedMajor.value = event.data.jurusan; // Set the selected major
    selectedType.value = event.data.tipe; // Set the selected major
    periode.value = event.data.periode;
    console.log(selectedMajor.value);
    console.log(periode.value);

    // Directly navigate to the sheet route
    router.get(`/sheet/${encodeURIComponent(selectedMajor.value)}/${encodeURIComponent(periode.value)}/${encodeURIComponent(selectedType.value)}`);
    console.log("Navigating to Sheet:", event.data);
};

// Navigate to Sheet
const navigateToSheet = () => {
    if (selectedMajor.value && periode.value && selectedType.value) {
        router.get(`/sheet/${encodeURIComponent(selectedMajor.value)}/${encodeURIComponent(periode.value)}/${encodeURIComponent(selectedType.value)}`);
    } else {
        console.error("No major, period, or type selected.");
    }
};

</script>

<template>
    <main
        class="min-w-full w-full flex flex-col items-center justify-center bg-white rounded-lg shadow-lg py-4"
    >
        <!-- Your content here -->
        <div class="flex flex-col gap-4 w-[90%]">
            <div class="card">
                <!-- Table (only rendered after data is fetched) -->
                <div class="top-0 bg-white w-full">
                    <DataTable
                        class="w-full"
                        :value="loading ? Array(10).fill(null) : filteredData"
                        v-model:selection="selectedRow"
                        v-model:filters="filters"
                        paginator
                        :rows="10"
                        stripedRows
                        datakey="id"
                        selectionMode="single"
                        filterDisplay="row"
                        :globalFilterFields="['jurusan', 'periode', 'tipe']"
                        @row-select="onRowSelect"
                    >
                        <template #header>
                            <div class="flex justify-between">
                                <div>
                                    <Button
                                        style="width: auto"
                                        type="button"
                                        icon="pi pi-filter-slash"
                                        label="Clear"
                                        outlined
                                        @click="clearFilter"
                                    />
                                </div>
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText
                                        v-model="filters['global'].value"
                                        placeholder="Keyword Search"
                                    />
                                </IconField>
                            </div>
                        </template>
                        <template #empty><div class="flex items-center justify-center h-full"> No sheets found. </div></template>
                        <!-- Column Jurusan -->
                        <Column
                            field="jurusan"
                            header="Jurusan"
                            :showFilterMenu="false"
                            style="width: 25%"
                        >
                            <template #body="{ data }">
                                <span v-if="loading">
                                    <Skeleton width="150px" height="16px" />
                                </span>
                                <span v-else>{{ data.jurusan }}</span>
                            </template>
                            <template #filter="{ filterModel, filterCallback }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    @change="filterCallback()"
                                    :options="jurusanOptions"
                                    placeholder="Select Jurusan"
                                    style="min-width: 14rem"
                                />
                            </template>
                        </Column>

                        <!-- Column Tipe -->
                        <Column
                            field="tipe"
                            header="Tipe"
                            :showFilterMenu="false"
                            style="width: 25%"
                        >
                            <template #body="{ data }">
                                <span v-if="loading">
                                    <Skeleton width="150px" height="16px" />
                                </span>
                                <span v-else>{{ data.tipe }}</span>
                                <!-- {{ data.tipe }} -->
                            </template>
                            <template #filter="{ filterModel, filterCallback }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    @change="filterCallback()"
                                    :options="tipeOptions"
                                    placeholder="Select Tipe"
                                    style="min-width: 14rem"
                                />
                            </template>
                        </Column>

                        <!-- Column Periode -->
                        <Column
                            field="periode"
                            header="Period"
                            :showFilterMenu="false"
                            style="width: 25%"
                        >
                            <template #body="{ data }">
                                <span v-if="loading">
                                    <Skeleton width="150px" height="16px" />
                                </span>
                                <span v-else>{{ data.periode }}</span>
                                <!-- {{ data.periode }} -->
                            </template>
                            <template #filter="{ filterModel, filterCallback }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    @change="filterCallback()"
                                    :options="periodeOptions"
                                    placeholder="Select Period"
                                    style="min-width: 14rem"
                                />
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <!-- Display selected row data -->
                <div v-if="selectedRow" class="mt-4">
                    <h3>Selected Row:</h3>
                    <p><strong>Faculty:</strong> {{ selectedRow.faculty }}</p>
                    <p><strong>Major:</strong> {{ selectedRow.major }}</p>
                    <p><strong>Period:</strong> {{ periode }}</p>
                    <Button
                        v-if="role === 'SuperUser'"
                        label="Go"
                        class="p-button-success w-full"
                        @click="navigateToSuperUser"
                    />
                    <Button
                        v-else
                        label="Go"
                        class="p-button-success w-full"
                        @click="navigateToSheet"
                    />
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
/* Add your custom styles here */
</style>
