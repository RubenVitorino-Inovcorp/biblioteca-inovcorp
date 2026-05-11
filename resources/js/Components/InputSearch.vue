<script setup>
import {ref, watch} from "vue";
import { debounce } from 'lodash';
import {router} from "@inertiajs/vue3";

const props = defineProps({
    filters: { type: Object, required: true },
    placeholder: { type: String, default: '' },
    route: { type: String, required: true },
})

const search = ref(props.filters?.search || '')

const updateSearch = debounce((value) => {
    router.get(
        route(props.route),
        {
            search: value,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true
        }
    );
}, 300);

watch(search, (newValue) => {
    updateSearch(newValue);
});
</script>

<template>
    <label class="search-wrapper">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <g
                stroke-linejoin="round"
                stroke-linecap="round"
                stroke-width="2.5"
                fill="none"
                stroke="currentColor"
            >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </g>
        </svg>
        <input type="search" v-model="search" class="search-input" :placeholder="placeholder" />
    </label>
</template>

<style scoped>
.search-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    max-width: 400px;
    padding: 0 14px;
    background: #F1F5F9;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.search-wrapper:focus-within {
    border-color: #006c49;
    box-shadow: 0 0 0 2px rgba(0, 108, 73, 0.1);
    background: #ffffff;
}

.search-icon {
    width: 16px;
    height: 16px;
    color: #6c7a71;
    flex-shrink: 0;
}

.search-input {
    flex: 1;
    padding: 8px 0;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 400;
    color: #191c1e;
    background: transparent;
    border: none;
    outline: none;
}

.search-input::placeholder {
    color: #6c7a71;
    font-style: normal;
}

/* Remove default search input styling */
.search-input::-webkit-search-cancel-button,
.search-input::-webkit-search-decoration {
    -webkit-appearance: none;
    appearance: none;
}
</style>
