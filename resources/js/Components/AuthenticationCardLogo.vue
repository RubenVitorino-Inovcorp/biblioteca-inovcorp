<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'" class="flex flex-col items-center justify-center">
        <img class="h-24 w-auto drop-shadow-sm mb-2" src="/logo.webp" alt="Biblioteca Logo">
        <h1 class="text-center font-bold text-3xl text-[#191c1e] font-['Manrope']">Biblioteca</h1>
    </Link>
</template>
