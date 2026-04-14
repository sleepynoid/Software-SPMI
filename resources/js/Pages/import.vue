<script setup>
import XlsxRead from "../components/upload/XlsxRead.vue";
import XlsxTable from "../components/upload/XlsxTable.vue";
import XlsxSheets from "../components/upload/XlsxSheets.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import { Button } from "primevue";
import axios from "axios";

const file = ref(null);
const selectedSheet = ref(null);
const department = ref("");
const selectedMajor = ref("");
const type = ref("");
const period = ref("");
const note = ref("");
const loading = ref(false);
const showMajorError = ref(false);

const departments = [
    {
        name: "F. Teknologi Industri",
        majors: [
            "Teknik Mesin",
            "Teknik Industri",
            "Teknik Kimia",
            "Teknik Pertambangan",
            "Teknik Perkapalan",
        ],
    },
    {
        name: "F. Teknik Sipil & Perencanaan",
        majors: ["Teknik Sipil", "Arsitektur", "Teknik Lingkungan"],
    },
    {
        name: "F. Teknik Elektro dan Teknologi Informasi",
        majors: ["Teknik Elektro", "Teknik Informatika", "Sistem Informasi"],
    },
];

const validateMajorSelection = () => {
    if (!department.value) {
        showMajorError.value = true;
        selectedMajor.value = "";
    } else {
        showMajorError.value = false;
    }
};

const handleFileChange = (event) => {
    const selected = event.target.files?.[0] ?? null;
    file.value = selected;
};

const submitData = async () => {
    if (!file.value) {
        alert('Pilih file terlebih dahulu.');
        return;
    }
    loading.value = true;
    const formData = new FormData();
    formData.append("file", file.value);
    formData.append("jurusan", selectedMajor.value);
    formData.append("tipe", type.value);
    formData.append("periode", period.value);
    formData.append("note", note.value);

    try {
        const response = await axios.post("/import", formData);
        const data = response.data;
        if (data.success) {
            alert(data.message);
            router.visit("/");
        } else {
            const errMsg = data.errors
                ? data.errors.join('\n')
                : (data.message ?? 'Terjadi kesalahan.');
            alert("Error: " + errMsg);
        }
    } catch (error) {
        console.error("Error mengirim file:", error);
        alert("Gagal mengirim file. Periksa koneksi atau coba lagi.");
    } finally {
        loading.value = false;
    }
};

function generateYearRange() {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = currentYear - 5; year <= currentYear; year++) {
        years.push(year);
    }
    return years;
}

const downloadFile = async () => {
    try {
        const response = await axios.get("/downloadSheet", {
            responseType: 'blob'
        });
        const blob = response.data;
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "TemplatePenetapan.xlsx");
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error("Download failed:", error);
        alert("Gagal mengunduh template.");
    }
};
</script>

<template>
    <div class="container">
        <div class="upload-section bg-white">
            <div>
                <Button
                    label="Template"
                    severity="secondary"
                    type="button"
                    class="bt p-button-outlined"
                    icon="pi pi-download"
                    style="width: auto"
                    @click="downloadFile"
                />
                <i class=""></i>
            </div>
            <h2>Tambah/ Unggah Berkas</h2>
            <form @submit.prevent="submitData">
                <div class="form-group">
                    <label for="file-upload"
                        >Pilih berkas <span class="required">*</span></label
                    >
                    <input
                        class="form-control"
                        type="file"
                        id="file-upload"
                        @change="handleFileChange"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="department"
                        >Fakultas <span class="required">*</span></label
                    >
                    <select
                        id="department"
                        v-model="department"
                        @change="validateMajorSelection"
                        required
                    >
                        <option value="">Pilih Fakultas</option>
                        <option
                            v-for="dept in departments"
                            :key="dept.name"
                            :value="dept.name"
                        >
                            {{ dept.name }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="major"
                        >Jurusan <span class="required">*</span></label
                    >
                    <select
                        id="major"
                        v-model="selectedMajor"
                        :disabled="!department"
                        required
                    >
                        <option value="">Pilih Jurusan</option>
                        <option
                            v-for="major in departments.find(
                                (d) => d.name === department
                            )?.majors"
                            :key="major"
                            :value="major"
                        >
                            {{ major }}
                        </option>
                    </select>
                    <div v-if="showMajorError" class="error-message">
                        <span style="color: red"
                            >Silakan pilih fakultas terlebih dahulu.</span
                        >
                    </div>
                </div>
                <div class="form-group">
                    <label for="type"
                        >Tipe <span class="required">*</span></label
                    >
                    <select id="type" v-model="type" required>
                        <option value="">Pilih Tipe</option>
                        <option value="pendidikan">Pendidikan</option>
                        <option value="penelitian">Penelitian</option>
                        <option value="pengabdian">Pengabdian</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="period"
                        >Periode <span class="required">*</span></label
                    >
                    <select id="period" v-model="period" required>
                        <option value="">Pilih Periode</option>
                        <template
                            v-for="year in generateYearRange()"
                            :key="year"
                        >
                            <option :value="`${year}-${year + 1} Ganjil`">
                                {{ `${year}-${year + 1} Ganjil` }}
                            </option>
                            <option :value="`${year}-${year + 1} Genap`">
                                {{ `${year}-${year + 1} Genap` }}
                            </option>
                        </template>
                    </select>
                </div>
                <div class="form-group">
                    <label for="note"
                        >Catatan</label
                    >
                    <textarea
                        id="note"
                        v-model="note"
                        rows="4"
                        placeholder="Masukkan catatan jika ada"
                    ></textarea>
                </div>
                <div class="text-right">
                    <Button
                        type="submit"
                        label="Upload"
                        severity="contrast"
                        style="width: auto"
                    />
                </div>
            </form>
        </div>
        <div class="preview-section bg-white">
            <h3>Pratinjau Berkas</h3>
            <xlsx-read :file="file">
                <template #default="{ loading }">
                    <span v-if="loading">Memuat...</span>
                    <xlsx-sheets>
                        <template #default="{ sheets }">
                            <nav class="navbar">
                                <ul class="navbar-nav">
                                    <li
                                        class="nav-item"
                                        v-for="sheet in sheets"
                                        :key="sheet"
                                    >
                                        <a
                                            class="nav-link"
                                            :class="{
                                                active: selectedSheet === sheet,
                                            }" href="#" @click="selectedSheet = sheet" :style="{
                                                backgroundColor:
                                                    selectedSheet === sheet
                                                        ? '#94b6ff'
                                                        : '',
                                            }"
                                            >{{ sheet }}</a
                                        >
                                    </li>
                                </ul>
                            </nav>
                        </template>
                    </xlsx-sheets>
                    <div class="xlsx-table">
                        <xlsx-table :sheet="selectedSheet" />
                    </div>
                </template>
            </xlsx-read>
        </div>
    </div>
</template>

<style scoped>
.bt {
    width: 10rem;
    margin-bottom: 1rem;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: rgba(197, 197, 197, 0.1);
    backdrop-filter: blur(2px);
    z-index: 999;
}

.container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
}

.upload-section,
.preview-section {
    height: fit-content;
    width: 48%;
    margin: 0;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="file"],
input[type="text"],
select,
textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.required {
    color: red;
}

.preview-section {
    overflow: hidden;
    max-height: 600px;
}

.xlsx-table {
    overflow-x: auto;
    max-height: 450px;
}

::-webkit-scrollbar {
    width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px grey;
    border-radius: 10px;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #94b6ff;
    border-radius: 50px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #94b6ff;
}

.error-message {
    margin-top: 5px;
    color: red;
}
</style>
