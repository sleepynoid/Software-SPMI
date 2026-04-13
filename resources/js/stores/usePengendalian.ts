import { reactive } from 'vue';
import axios from 'axios';

interface PengendalianPayload {
    idBuktiEvaluasi: string;
    temuan: string;
    akarMasalah: string;
    rtl: string;
    pelaksanaanRtl: string;
    userName: string;
}

export const usePengendalian = reactive({
    loading: false,
    list: [],
    model: reactive<PengendalianPayload>({
        idBuktiEvaluasi: '',
        temuan: '',
        akarMasalah: '',
        rtl: '',
        pelaksanaanRtl: '',
        userName: '',
    }),
    initial(data?: PengendalianPayload) {
        this.model.idBuktiEvaluasi = data?.idBuktiEvaluasi || '';
        this.model.temuan = data?.temuan || '';
        this.model.akarMasalah = data?.akarMasalah || '';
        this.model.rtl = data?.rtl || '';
        this.model.pelaksanaanRtl = data?.pelaksanaanRtl || '';
    },
    setUserName(userName: string) {
        this.model.userName = userName;
    }
});

export async function submitPengendalian() {
    // usePengendalian.loading = true;
    try {
        const token = localStorage.getItem('token');
        const response = await axios.post('/api/submitPengendalian', { data: usePengendalian.model }, {
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

export async function fetchPengendalian(jurusan: string, periode: string, currentSheet: string, current: string) {
    // usePengendalian.loading = true;
    try {
        const response = await fetch(`/api/getPengendalian/${jurusan}/${periode}/${currentSheet}/${current}`);
        // console.log(response)
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        usePengendalian.list = await response.json();

    } catch (error) {
        console.error('Error fetching data:', error.message);
    }
}

