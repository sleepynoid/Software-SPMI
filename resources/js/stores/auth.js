import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isLoaded: false,
    }),
    actions: {
        async fetchUser() {
            try {
                const response = await axios.get('/api/user', {
                    withCredentials: true,
                });
                this.user = response.data;
            } catch (error) {
                this.user = null;
            } finally {
                this.isLoaded = true;
            }
        },
        async logout() {
            try {
                await axios.post('/api/logout');
                this.user = null;
            } catch (e) {
                console.error('Logout failed', e);
            }
        }
    }
});
