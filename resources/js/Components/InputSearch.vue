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
    <label class="input-bordered flex items-center gap-2 w-1/2 bg-transparent focus-within:outline-none">
        <svg class="h-[1.2em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
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
        <input type="search" v-model="search" class="grow rounded-xl bg-transparent" :placeholder="placeholder" />
    </label>
</template>

<style scoped>

</style>
