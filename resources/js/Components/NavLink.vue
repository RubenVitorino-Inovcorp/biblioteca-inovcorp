<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'nav-link nav-link--active'
        : 'nav-link';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

<style scoped>
.nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: #3c4a42;
    border-radius: 6px;
    transition: all 0.2s ease;
    text-decoration: none;
    width: 100%;
}

.nav-link:hover {
    background: rgba(0, 108, 73, 0.06);
    color: #006c49;
}

.nav-link--active {
    background: rgba(0, 108, 73, 0.08);
    color: #006c49;
    font-weight: 600;
    border-left: 3px solid #10b981;
}
</style>
