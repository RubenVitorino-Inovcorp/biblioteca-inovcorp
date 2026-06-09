<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue";
import InputSearch from "@/Components/InputSearch.vue";
import FilterDropdown from "@/Components/FilterDropdown.vue";
import { Book as BookIcon, LibraryBig } from "@lucide/vue";
import LoanCreateForm from "@/Components/LoanCreateForm.vue";
import { PaginatedData, Book, Author, Publisher } from '@/types';

interface Filters {
    search?: string;
    sort?: string;
    publisher?: string;
    author?: string;
}

const props = defineProps<{
    books: PaginatedData<Book>;
    authors: Author[];
    publishers: Publisher[];
    filters: Filters;
}>();

interface SortOption {
    value: string;
    label: string;
}

const bookSortOptions: SortOption[] = [
    { value: 'preco_asc', label: 'Preço: Baixo para Alto' },
    { value: 'preco_desc', label: 'Preço: Alto para Baixo' },
    { value: 'titulo_az', label: 'Título (A-Z)' },
    { value: 'titulo_za', label: 'Título (Z-A)'},
];
</script>

<template>
    <AppLayout title="Catálogo de Livros">
        <template #header>
            <div class="flex justify-between space-x-2 items-center">
                <h2 class="page-title flex items-center gap-2">
                    <BookIcon :size="20"/> Catálogo de Livros
                </h2>
                <InputSearch :filters="filters" route="catalog.livros.index" placeholder="Pesquisar livro..." />
                
                <div class="header-actions">
                    <FilterDropdown
                        route-name="catalog.livros.index"
                        :filters="filters"
                        :sort-options="bookSortOptions"
                        :publishers="publishers"
                        :authors="authors"
                    />
                </div>
            </div>
        </template>

        <div v-if="books.data.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:mx-64 gap-x-6 gap-y-10 mt-6">
            <div v-for="book in books.data" :key="book.id" class="flex flex-col group p-4 rounded-xl">
                
                <Link :href="route('catalog.livros.show', book.id)" class="relative aspect-[4/5] overflow-hidden rounded-xl w-full block">
                    <span v-if="book.is_available" class="absolute top-2 right-2 opacity-80 bg-primary text-base-100 px-2 py-1 rounded-lg text-xs font-bold">Disponível</span>
                    <span v-else class="absolute top-2 right-2 opacity-80 bg-gray-400 text-base-100 px-2 py-1 rounded-lg text-xs font-bold">Indisponível</span>

                    <img 
                        :src="book.image_url" 
                        :alt="book.title" 
                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" 
                    />
                </Link>
            
                <div class="mt-3 flex flex-col flex-grow">
                    <h2 class="text-sm font-medium text-base-content line-clamp-2 min-h-[2.5rem]">
                        <Link :href="route('catalog.livros.show', book.id)" class="hover:text-primary transition-colors">
                            {{ book.title }}
                        </Link>
                    </h2>

                    <p class="mt-1 text-xs text-gray-500 truncate">
                        de
                        <span v-for="(author, index) in book.authors" :key="author.id">
                        
                            <Link :href="route('catalog.autores.show', author.id)" class="hover:text-base-content transition-colors">
                                {{ author.name }}   
                            </Link>
                            <span v-if="index < book.authors.length - 1">, </span>
                        </span>
                    </p>

                    <div class="mt-auto pt-2 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-900">{{ book.price }}€</span>

                        <span v-if="book.is_available" class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <LoanCreateForm :book="book">
                                <button type="button" class="flex items-center gap-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-green-900 transition-colors font-['Manrope'] px-4 py-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <LibraryBig :size="14" />
                                Requisitar
                            </button>
                        </LoanCreateForm>
                        </span>
                        <Link :href="route('catalog.livros.show', book.id)" v-else class="text-[10px] uppercase tracking-wider font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                        Ver mais
                    </Link>
                </div>
            </div>
            </div>
        </div>

        <div v-if="books.links && books.links.length > 3" class="pagination mt-6">
            <div class="pagination-list">
                <Link
                    v-for="(link, index) in books.links"
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

        <div v-if="books.data.length === 0" class="text-center py-10">
            <p class="empty-state-text">Nenhum livro encontrado.</p>
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
