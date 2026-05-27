<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="text-input"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>

<style scoped>
.text-input {
    width: 100%;
    padding: 8px 12px;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    color: var(--color-silk-content);
    background: var(--color-silk-200);
    border: 1px solid var(--color-silk-300);
    border-radius: 6px;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.text-input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 2px rgba(0, 108, 73, 0.1);
    background: var(--color-base-100);
}
</style>
