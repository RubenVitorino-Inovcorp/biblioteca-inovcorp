<script setup lang="ts">

defineSlots<{
    icon?: (props: Record<string, never>) => any;
    default?: (props: Record<string, never>) => any;
}>();

const props = defineProps({
    filters: { type: Object, required: true },
    routeName: { type: String, required: true },
})
</script>

<template>
    <a :href="route(props.routeName, props.filters)" class="btn-export">
        <slot name="icon"></slot>
        
        <span class="truncate">
            <slot></slot>
        </span>
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
     color: var(--color-silk-subtle);
     background: var(--color-base-100);
     border: 1px solid var(--color-silk-300);
     border-radius: 6px;
     max-width: 100%;
 }

.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0; 
}

.btn-export:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
}
</style>
