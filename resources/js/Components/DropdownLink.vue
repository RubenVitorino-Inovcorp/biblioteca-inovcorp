<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    as: String,
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" type="submit" class="dropdown-link-item">
            <slot />
        </button>

        <a v-else-if="as =='a'" :href="href" class="dropdown-link-item">
            <slot />
        </a>

        <Link v-else :href="href" class="dropdown-link-item">
            <slot />
        </Link>
    </div>
</template>

<style scoped>
.dropdown-link-item {
    display: block;
    width: 100%;
    padding: 8px 16px;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 500;
    line-height: 1.4;
    color: #3c4a42;
    text-align: left;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
}

.dropdown-link-item:hover {
    background: #f0fdf4;
    color: #006c49;
}

.dropdown-link-item:focus {
    outline: none;
    background: #f0fdf4;
    color: #006c49;
}
</style>
