<script setup>
import {Head, Link} from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue";
import InputSearch from "@/Components/InputSearch.vue";
import FilterDropdown from "@/Components/FilterDropdown.vue";
import { Building } from "@lucide/vue";

const props = defineProps({
    publishers: Object,
    filters: Object,
})

const publisherSortOptions = [
    { value: 'nome_az', label: 'Nome (A-Z)' },
    { value: 'nome_za', label: 'Nome (Z-A)' },
    { value: 'livros_desc', label: 'Mais Livros' },
    { value: 'livros_asc', label: 'Menos Livros' },
];
</script>

<template>
    <AppLayout title="Catálogo de Editoras">
        <template #header>
            <div class="flex justify-between space-x-2 items-center">
                <h2 class="page-title flex items-center gap-2">
                 <Building :size="20"/> Catálogo de Editoras
                </h2>
                <InputSearch :filters="filters" route="catalog.editoras.index" placeholder="Pesquisar editora..." />
                <div class="header-actions">
                    <FilterDropdown
                        route-name="catalog.editoras.index"
                        :filters="filters"
                        :sort-options="publisherSortOptions"
                    />
                </div>
            </div>
        </template>

        <div v-if="publishers.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 xl:mx-10 gap-6 p-4">
            <div v-for="publisher in publishers.data" :key="publisher.id" class="bg-base-100 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-[0_8px_20px_-6px_rgba(6,81,237,0.15)] hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                <Link :href="route('catalog.editoras.show', publisher.id)" class="block flex-grow">
                    <div class="pt-6 pb-4 flex justify-center items-center bg-[#f8fafc] h-36 relative">
                        <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <img :src="publisher.logo_url" :alt="publisher.name" class="relative max-w-[80%] max-h-24 object-contain mix-blend-multiply" />
                    </div>
                    <div class="p-5 flex flex-col items-center text-center border-t border-gray-50">
                        <h2 class="font-bold text-lg text-base-content truncate w-full group-hover:text-primary transition-colors" :title="publisher.name">
                            {{ publisher.name }}
                        </h2>
                        
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 mt-3">
                            {{ publisher.books_count }} {{ publisher.books_count === 1 ? 'título' : 'títulos' }}
                        </span>
                    </div>
                </Link>
                <div class="px-5 pb-5 mt-auto w-full">
                    <Link :href="route('catalog.editoras.show', publisher.id)" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-base-100 border border-base-300 rounded-xl text-sm font-medium text-base-content/70 hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Ver Editora
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="publishers.links && publishers.links.length > 3" class="pagination mt-6">
            <div class="pagination-list">
                <Link
                    v-for="(link, index) in publishers.links"
                    :key="index"
                    :href="link.url ?? ''"
                    class="pagination-item"
                    :class="{
                    'pagination-item--active': link.active,
                    'pagination-item--disabled': !link.url
                }"
                    v-html="link.label"
                />
            </div>
        </div>

        <div v-if="publishers.data.length === 0" class="text-center py-10">
            <p class="empty-state-text">Nenhuma editora encontrada.</p>
        </div>
    </AppLayout>
</template>

<style scoped>
.page-title {
    font-family: 'Manrope', sans-serif;
    font-size: 17px;
    font-weight: 600;
    color: var(--color-silk-content);
    line-height: 1.4;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.empty-state-text {
    font-family: 'Manrope', sans-serif;
    font-size: 14px;
    color: var(--color-silk-muted);
    font-style: italic;
}
</style>
