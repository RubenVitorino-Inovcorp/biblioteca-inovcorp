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
     color: #3c4a42;
     background: #ffffff;
     border: 1px solid #E2E8F0;
     border-radius: 6px;
     cursor: pointer;
     transition: all 0.2s ease;
     position: relative;
 }

 .btn-export:hover {
         background: #f0fdf4;
 }
</style>
