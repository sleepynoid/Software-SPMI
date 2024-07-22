<script setup>

</script>

<template>
    <div class="not-found">
        <h1>404</h1>
        <p>Oops! Halaman yang kamu cari tidak ditemukan.</p>
        <router-link to="/">Kembali ke Beranda</router-link>
    </div>
</template>

<style scoped>
.not-found {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
    background-color: #f8f9fa;
}

.not-found h1 {
    font-size: 10rem;
    margin: 0;
    color: #343a40;
}

.not-found p {
    font-size: 1.5rem;
    color: #6c757d;
}

.not-found a {
    margin-top: 20px;
    font-size: 1rem;
    color: #007bff;
    text-decoration: none;
}

.not-found a:hover {
    text-decoration: underline;
}
</style>
