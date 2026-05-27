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
    color: var(--color-base-content);
    border-radius: 6px;
    transition: all 0.2s ease;
    text-decoration: none;
    width: 100%;
}

.nav-link svg,
.nav-link :deep(svg) {
    color: var(--color-silk-subtle) !important;
}

.nav-link:hover {
    background: rgba(0, 108, 73, 0.06);
    color: var(--color-primary);
}

.nav-link--active {
    background: rgba(0, 108, 73, 0.08);
    color: var(--color-primary);
    font-weight: 600;
    border-left: 3px solid var(--color-primary);
}
</style>
