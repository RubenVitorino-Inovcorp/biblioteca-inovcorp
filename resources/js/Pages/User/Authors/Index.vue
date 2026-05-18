<script setup>
import {Head, Link} from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue";
import InputSearch from "@/Components/InputSearch.vue";
import FilterDropdown from "@/Components/FilterDropdown.vue";
import { UserPen } from "@lucide/vue";

const props = defineProps({
    authors: Object,
    filters: Object,
})

const authorSortOptions = [
    { value: 'nome_az', label: 'Nome (A-Z)' },
    { value: 'nome_za', label: 'Nome (Z-A)' },
    { value: 'livros_desc', label: 'Mais Livros' },
    { value: 'livros_asc', label: 'Menos Livros' },
];
</script>

<template>
    <AppLayout title="Catálogo de Autores">
        <template #header>
            <div class="flex justify-between space-x-2 items-center">
                <h2 class="page-title flex items-center gap-2">
                    <UserPen :size="20"/> Autores
                </h2>
                <InputSearch :filters="filters" route="catalog.autores.index" placeholder="Pesquisar autores..." />
                <div class="header-actions">
                    <FilterDropdown
                        route-name="catalog.autores.index"
                        :filters="filters"
                        :sort-options="authorSortOptions"
                    />
                </div>
            </div>
        </template>

        <div v-if="authors.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 xl:mx-10 gap-6 p-4">
            <div v-for="author in authors.data" :key="author.id" class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-[0_8px_20px_-6px_rgba(6,81,237,0.15)] hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                <Link :href="route('catalog.autores.show', author.id)" class="block flex-grow">
                    <div class="pt-8 pb-4 flex justify-center bg-[#f8fafc] relative">
                        <div class="absolute inset-0 bg-[#006c49]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <img :src="author.photo_path" :alt="author.name" class="relative rounded-full w-28 h-28 object-cover shadow-sm ring-4 ring-white" />
                    </div>
                    <div class="p-5 flex flex-col items-center text-center">
                        <h2 class="font-bold text-lg text-[#191c1e] truncate w-full group-hover:text-[#006c49] transition-colors" :title="author.name">
                            {{ author.name }}
                        </h2>
                        
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#e8f3ee] text-[#006c49] mt-3">
                            {{ author.books_count }} {{ author.books_count === 1 ? 'obra' : 'obras' }}
                        </span>
                    </div>
                </Link>
                <div class="px-5 pb-5 mt-auto w-full">
                    <Link :href="route('catalog.autores.show', author.id)" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-white border border-[#e2e8f0] rounded-xl text-sm font-medium text-[#475569] hover:bg-[#006c49] hover:text-white hover:border-[#006c49] transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#006c49]">
                        Ver Perfil
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="authors.links && authors.links.length > 3" class="pagination mt-6">
            <div class="pagination-list">
                <Link
                    v-for="(link, index) in authors.links"
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

        <div v-if="authors.data.length === 0" class="text-center py-10">
            <p class="empty-state-text">Nenhum autor encontrado.</p>
        </div>
    </AppLayout>
</template>

<style scoped>
.page-title {
    font-family: 'Manrope', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: #191c1e;
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
    color: #6c7a71;
    font-style: italic;
}
</style>
