<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { SlidersHorizontal } from "@lucide/vue";

const props = defineProps({
    filters: { type: Object, required: true },
    routeName: { type: String, required: true },
    sortOptions: { type: Array, required: true },
    publishers: { type: Array, default: () => [] },
    authors: { type: Array, default: () => [] },
});

const sort = ref(props.filters.sort || '');
const publisher = ref(props.filters.publisher || '');
const author = ref(props.filters.author || '');

watch([sort, publisher, author], () => {
    const params = { search: props.filters.search };

    if (sort.value) params.sort = sort.value;
    if (publisher.value) params.publisher = publisher.value;
    if (author.value) params.author = author.value;

    router.get(route(props.routeName), params, {
        preserveState: true,
        replace: true
    });
});
</script>

<template>
    <div class="dropdown dropdown-end">
        <div tabindex="0" role="button" class="btn btn-outline btn-sm m-1">
            <SlidersHorizontal />
        </div>
        <ul tabindex="0" class="dropdown-content z- menu p-4 shadow bg-base-100 rounded-box w-64 space-y-4">
            <li>
                <label class="label-text font-bold">Ordenar por</label>
                <select v-model="sort" class="select select-primary select-sm w-full">
                    <option value="">Mais recentes</option>
                    <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </li>

            <li v-if="publishers.length > 0">
                <span class="label-text font-bold">Editora</span>
                <select v-model="publisher" class="select select-bordered select-sm w-full">
                    <option value="">Todas as Editoras</option>
                    <option v-for="p in publishers" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
            </li>

            <li v-if="authors.length > 0">
                <span class="label-text font-bold">Autor</span>
                <select v-model="author" class="select select-bordered select-sm w-full">
                    <option value="">Todos os Autores</option>
                    <option v-for="a in authors" :key="a.id" :value="a.id">{{ a.name }}</option>
                </select>
            </li>

        </ul>
    </div>
</template>
