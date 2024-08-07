<script setup>
import XlsxRead from "./XlsxRead.vue";
import XlsxTable from "./XlsxTable.vue";
import XlsxSheets from "./XlsxSheets.vue";
import { useRouter } from "vue-router";
import { ref } from "vue";

const router = useRouter();
const file = ref(null);
const selectedSheet = ref(null);
const department = ref("");
const type = ref("");
const period = ref("");
const note = ref("");

const handleFileChange = (event) => {
    file.value = event.target.files ? event.target.files[0] : null;
};

const submitData = async () => {
    const formData = new FormData();
    formData.append("file", file.value);
    formData.append("jurusan", department.value);
    formData.append("tipe", type.value);
    formData.append("periode", period.value);
    formData.append("note", note.value);

    try {
        const response = await axios.post("api/penetapan/import", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        if (response.data.success) {
            alert(response.data.message);
            router.push(response.data.redirect);
        } else {
            alert("Error: " + response.data.message);
        }
    } catch (error) {
        console.error("Error mengirim file:", error);
    }
};
// const period = ref('');

function generateYearRange() {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = currentYear - 5; year <= currentYear; year++) {
        years.push(year);
    }
    return years;
}
</script>

<template>
    <div class="container">
        <div class="upload-section">
            <router-link to="/">Home</router-link>
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
                        >Jurusan <span class="required">*</span></label
                    >
                    <select id="department" v-model="department" required>
                        <option value="">Pilih Jurusan</option>
                        <option value="Teknik Informatika">
                            Teknik Informatika
                        </option>
                        <option value="Sistem Informasi">
                            Sistem Informasi
                        </option>
                        <option value="Teknik Elektro">Teknik Elektro</option>
                    </select>
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
                            <option :value="`${year}/${year + 1} Ganjil`">
                                {{ `${year}/${year + 1} Ganjil` }}
                            </option>
                            <option :value="`${year}/${year + 1} Genap`">
                                {{ `${year}/${year + 1} Genap` }}
                            </option>
                        </template>
                    </select>
                </div>
                <div class="form-group">
                    <label for="note">Catatan</label>
                    <textarea
                        id="note"
                        v-model="note"
                        rows="4"
                        placeholder="Masukkan catatan jika ada"
                    ></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </form>
        </div>
        <div class="preview-section">
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
                                            }"
                                            href="#"
                                            @click="selectedSheet = sheet"
                                            :style="{
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

<style>
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

button:hover {
    background-color: #0056b3;
}

.required {
    color: red;
}

.navbar {
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: white;
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
</style>
