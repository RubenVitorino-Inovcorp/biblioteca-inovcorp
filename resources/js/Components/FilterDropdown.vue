<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { SlidersHorizontal } from "@lucide/vue";

const props = defineProps({
    filters: { type: Object, required: true },
    routeName: { type: String, required: true },
    sortOptions: { type: Array, required: true },
    publishers: { type: Array, default: () => [] },
    authors: { type: Array, default: () => [] },
});

const open = ref(false);
const dropdownRef = ref(null);

const sort = ref(props.filters.sort ?? '');
const publisher = ref(props.filters.publisher || '');
const author = ref(props.filters.author || '');

const toggle = () => {
    open.value = !open.value;
};

const closeOnClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        open.value = false;
    }
};

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeOnClickOutside);
    document.addEventListener('keydown', closeOnEscape);
});

onUnmounted(() => {
    document.removeEventListener('click', closeOnClickOutside);
    document.removeEventListener('keydown', closeOnEscape);
});

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

const clearFilters = () => {
    sort.value = '';
    publisher.value = '';
    author.value = '';
};

const hasActiveFilters = () => {
    return sort.value || publisher.value || author.value;
};
</script>

<template>
    <div ref="dropdownRef" class="filter-dropdown">
        <button
            @click="toggle"
            class="filter-trigger"
            :class="{ 'has-filters': hasActiveFilters() }"
            type="button"
        >
            <SlidersHorizontal :size="16" />
            <span class="filter-label">Filtros</span>
            <span v-if="hasActiveFilters()" class="filter-badge"></span>
        </button>

        <transition
            enter-active-class="filter-enter-active"
            enter-from-class="filter-enter-from"
            enter-to-class="filter-enter-to"
            leave-active-class="filter-leave-active"
            leave-from-class="filter-leave-from"
            leave-to-class="filter-leave-to"
        >
            <div v-show="open" class="filter-panel">
                <div class="filter-panel-header">
                    <span class="filter-panel-title">Filtros</span>
                    <button
                        v-if="hasActiveFilters()"
                        @click="clearFilters"
                        class="filter-clear-btn"
                        type="button"
                    >
                        Limpar
                    </button>
                </div>

                <div class="filter-group">
                    <label class="filter-group-label">Ordenar por</label>
                    <select v-model="sort" class="filter-select">
                        <option value="">Mais recentes</option>
                        <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <div v-if="publishers.length > 0" class="filter-group">
                    <label class="filter-group-label">Editora</label>
                    <select v-model="publisher" class="filter-select">
                        <option value="">Todas as Editoras</option>
                        <option v-for="p in publishers" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>

                <div v-if="authors.length > 0" class="filter-group">
                    <label class="filter-group-label">Autor</label>
                    <select v-model="author" class="filter-select">
                        <option value="">Todos os Autores</option>
                        <option v-for="a in authors" :key="a.id" :value="a.id">{{ a.name }}</option>
                    </select>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.filter-dropdown {
    position: relative;
    display: inline-flex;
}

.filter-trigger {
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

.filter-trigger:hover {
    border-color: #006c49;
    color: #006c49;
}

.filter-trigger.has-filters {
    border-color: #006c49;
    color: #006c49;
    background: #f0fdf4;
}

.filter-badge {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    position: absolute;
    top: 5px;
    right: 5px;
}

.filter-label {
    display: none;
}

@media (min-width: 640px) {
    .filter-label {
        display: inline;
    }
}

.filter-panel {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 280px;
    background: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    box-shadow: 0px 4px 20px rgba(15, 23, 42, 0.08);
    padding: 16px;
    z-index: 50;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.filter-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 10px;
    border-bottom: 1px solid #E2E8F0;
}

.filter-panel-title {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #191c1e;
}

.filter-clear-btn {
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: #006c49;
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 4px;
    transition: background 0.15s ease;
}

.filter-clear-btn:hover {
    background: #f0fdf4;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-group-label {
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.03em;
    color: #6c7a71;
    text-transform: uppercase;
}

.filter-select {
    width: 100%;
    padding: 8px 12px;
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    font-weight: 400;
    color: #191c1e;
    background-color: #F1F5F9;
    border: 1px solid #E2E8F0;
    border-radius: 6px;
    cursor: pointer;
    transition: border-color 0.2s ease;
    appearance: auto;
    -webkit-appearance: auto;
    -moz-appearance: auto;
    outline: none;
}

.filter-select:focus {
    border-color: #006c49;
    box-shadow: 0 0 0 2px rgba(0, 108, 73, 0.1);
}

/* Transition classes */
.filter-enter-active,
.filter-leave-active {
    transition: all 0.2s ease;
}
.filter-enter-from,
.filter-leave-to {
    opacity: 0;
    transform: translateY(-4px) scale(0.98);
}
.filter-enter-to,
.filter-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
}
</style>
