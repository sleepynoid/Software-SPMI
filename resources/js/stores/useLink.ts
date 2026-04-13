import {reactive} from "vue";
import axios from 'axios';

export interface LinkPayload {
    id?: string;
    idBukti: string;
    judul_link: string;
    link: string;
    tipeLink: string;
}

export const useLink = reactive({
    loading: false,
    list: [],
})

export async function fetchLink(idBukti: string, tipeLink: string) {
    try {
        useLink.loading = true;
        const response = await fetch(`/api/getLink/${idBukti}/${tipeLink}`);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        useLink.loading = false;
        return await response.json()
    } catch (error) {
        console.error('Error fetching data:', error.response?.data || error.message);
    }
}

export async function submitLink(payload: LinkPayload){
    try {
        useLink.loading = true;
        const token = localStorage.getItem('token');
        const response = await axios.post('/api/submitLink', { data: payload }, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        console.log('Data submitted successfully:', response.status);
        useLink.loading = false;
        return response.status
    } catch (error) {
        console.error('Error submitting data:', error.response?.data || error.message);
    }
}

export async function deleteLink(idLink: string){
    try {
        useLink.loading = true;
        const token = localStorage.getItem('token');
        const response = await axios.post('/api/deleteLink', {idLink: idLink}, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        console.log('Data deleted successfully:', response.status);
        useLink.loading = false;
        return response.status
    } catch (error) {
        console.error('Error submitting data:', error.response?.data || error.message);
    }
}
