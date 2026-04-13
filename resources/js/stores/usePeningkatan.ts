import { reactive } from 'vue';
import axios from 'axios';

interface PeningkatanPayload {
    idBuktiPengendalian: string;
    komenPeningkatan: string;
    userName: string;
}

export const usePeningkatan = reactive({
    loading: false,
    list: [],
    model: reactive<PeningkatanPayload>({
        idBuktiPengendalian: '',
        komenPeningkatan: '',
        userName: '',
    }),
    initial(data: PeningkatanPayload) {
        this.model.idBuktiPengendalian = data.idBuktiPengendalian;
        this.model.komenPeningkatan = data.komenPeningkatan;
    },
    setUserName(userName: string) {
        this.model.userName = userName;
    }
});

export async function submitPeningkatan() {
    try {
        const token = localStorage.getItem('token');
        const response = await axios.post('/api/submitPeningkatan', { data: usePeningkatan.model }, {
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

export async function fetchPeningkatan(jurusan: string, periode: string, currentSheet: string, current: string) {
    try {
        const response = await fetch(`/api/getPeningkatan/${jurusan}/${periode}/${currentSheet}/${current}`);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        usePeningkatan.list = await response.json();

    } catch (error) {
        console.error('Error fetching data:', error.message);
    }
}

