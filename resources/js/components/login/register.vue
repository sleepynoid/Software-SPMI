<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const email = ref('');
const role = ref('');
const name = ref('');
const password = ref('');
const role = ref('');
const name = ref('');

const register = async () => {
    try {
        const response = await axios.post('/api/register', {
            name: name.value,
            email: email.value,
            password: password.value,
            role: role.value,
        });
        console.log(response);
        router.push('/');
    } catch (error) {
        console.error('Registration failed:', error);
    }
};
</script>

<template>
    <div class="c1">

        <div class="main">
            <div class="main1">
                <div class="ma">
                    <h1>POWERED BY <br> CREATORS AROUND <br> THE WORLD.</h1>
                </div>
                <div class="mi">
                    <img src="https://images.unsplash.com/photo-1722170529553-3d486ba8ffba?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHwyOXx8fGVufDB8fHx8fA%3D%3D" class="mii">
                </div>
            </div>
            <div class="main2">
                <div class="lojin">
                    <strong style="text-align:center;">Register</strong>
                    <div>
                        <h6>Name</h6>
                        <input type="text" v-model="name" required>
                    </div>
                    <div>
                        <h6>Email</h6>
                        <input type="text" v-model="email" required>
                    </div>

                    <div>
                        <h6>Password</h6>
                        <input type="password" v-model="password" required>
                    </div>
                    <div>
                        <label for="">Role</label>
                        <select v-model="role">
                            <option value="">Pilih Role</option>
                            <option value="penetapan">Penetapan</option>
                            <option value="pelaksanaan">Pelaksanaan</option>
                        </select>
                    </div>
                    <div class="log btn" @click="register">Register</div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>


img{
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transition: opacity 0.3s ease-in-out;
    object-fit: cover;
}

.c1{
    width: 100vw;
    /* //height: 200vh; */
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    /* //padding: 3%;
    //background: whitesmoke; */


    .main{
        /* //margin-top: 3%; */
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: row;
        justify-content: space-between;

        .main1{
            width: 48.5%;
            height: 40rem;
            border-radius: 2rem;

            .ma{
                width: 100%;
                height: 65%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .mi{
                width: 100%;
                height: 35%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .mii{
                width: 100%;
                height: 100%;
                background: black;
                border-radius: 2rem;
            }
        }

        .main2{
            width: 48.5%;
            height: 40rem;
            display: flex;
            border-radius: 2rem;
            background-image: url("https://png.pngtree.com/background/20230618/original/pngtree-abstract-3d-render-in-lustrous-blue-metallic-hues-with-reflective-sheen-picture-image_3710911.jpg");
            align-items: center;
            justify-content: center;

            .lojin{
                width: 60%;
                height: 70%;
                padding: 3%;
                flex-direction: column;
                background: white;
                border-radius: 2rem;
                display: flex;
                gap: 2rem;
            }

            .log{
                width: 100%;
                height: 2rem;
                background: black;
                color: white;
                border-radius: 10rem;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-top: 1.5rem;
            }

            input{
                border: none;
                border-bottom: 2px solid #000; /* Warna garis bawah */
                padding: 5px;
                outline: none;
                width: 100%;

                font-size: 1rem;
            }
        }
    }
}

</style>