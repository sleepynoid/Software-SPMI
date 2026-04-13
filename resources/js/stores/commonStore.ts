import { reactive } from 'vue';
import CryptoJS from "crypto-js";

export function getUserName(): string {
    const encryptedName = localStorage.getItem("name");
    const key = localStorage.getItem("date");
    if (encryptedName) {
        const nameBytes = CryptoJS.AES.decrypt(encryptedName, `asx${key}`);

        return nameBytes.toString(CryptoJS.enc.Utf8);
    }
}

export function getUserRole(): string {
    const encryptedRole = localStorage.getItem("userRole");
    const key = localStorage.getItem("date");
    if (encryptedRole) {
        const roleBytes = CryptoJS.AES.decrypt(encryptedRole, `ddx${key}`);

        return roleBytes.toString(CryptoJS.enc.Utf8);
    }
}
