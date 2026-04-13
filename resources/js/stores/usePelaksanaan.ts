import { reactive } from 'vue';
import axios from 'axios';

interface PelaksanaanPayload {
    idPelaksanaan: string;
    idIndikator: string;
    komentarPelaksanaan: string;
    userName: string;
}

export const usePelaksanaan = reactive({
    loading: false,
    list: [],
    model: reactive<PelaksanaanPayload>({
        idPelaksanaan: '',
        idIndikator: '',
        komentarPelaksanaan: '',
        userName: '',
    }),
    initial(data: PelaksanaanPayload) {
        this.model.idPelaksanaan = data.idPelaksanaan;
        this.model.idIndikator = data.idIndikator;
        this.model.komentarPelaksanaan = data.komentarPelaksanaan;
    },
    setUserName(userName: string) {
        this.model.userName = userName;
    }
});

export async function submitPelaksanaan() {
    try {
        const token = localStorage.getItem('token');
        const response = await axios.post('/api/submitPelaksanaan', { data: usePelaksanaan.model }, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        console.log('Data submitted successfully:', response.status);

        return response.status
    } catch (error) {
        console.error('Error submitting data:', error.response?.data || error.message);
    }
}

export async function fetchPelaksanaan(jurusan: string, periode: string, currentSheet: string, current: string) {
    try {
        const response = await fetch(`/api/getPelaksanaan/${jurusan}/${periode}/${currentSheet}/${current}`);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        usePelaksanaan.list = await response.json();
    } catch (error) {
        console.error('Error fetching data:', error.message);
    }
}

