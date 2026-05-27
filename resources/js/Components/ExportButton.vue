<script setup>


const props = defineProps({
    filters: { type: Object, required: true },
    routeName: { type: String, required: true },
})
</script>

<template>
    <a :href="route(props.routeName, props.filters)" class="btn-export">
        <slot></slot>
    </a>
</template>

<style scoped>
 .btn-export {
     display: inline-flex;
     align-items: center;
     gap: 6px;
     padding: 6px 14px;
     font-family: 'Manrope', sans-serif;
     font-size: 13px;
     font-weight: 600;
     letter-spacing: 0.02em;
     color: var(--color-silk-subtle);
     background: var(--color-base-100);
     border: 1px solid var(--color-silk-300);
     border-radius: 6px;
     cursor: pointer;
     transition: all 0.2s ease;
     position: relative;
 }

 .btn-export:hover {
         background: var(--color-primary-light);
 }
</style>
