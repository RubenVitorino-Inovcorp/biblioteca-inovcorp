<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue";
import InputSearch from "@/Components/InputSearch.vue";
import ExportButton from "@/Components/ExportButton.vue";
import { Book, Plus, DownloadIcon } from "@lucide/vue";
import { toast } from 'vue-sonner'
import { PaginatedData } from '@/types';

interface Filters {
    search?: string;
}

const props = defineProps<{
    books: PaginatedData<any>;
    filters: Filters;
}>();

const quickAdd = (book: any) => {
    router.post(route('livros.store'), {
        title: book.title,
        isbn: book.isbn,
        bibliography: book.description,
        price: 0,            
        total_stock: 1,      
        publisher_id: book.publisher_name || 'Desconhecida', 
        author_ids: Array.isArray(book.autores) ? book.autores : (book.autores ? book.autores.split(',').map((a: string) => a.trim()).filter(Boolean) : []),                          
        image_path: book.image_path
    }, {
        onSuccess: () => toast.success('Livro importado e guardado localmente!'),
        onError: () => toast.error('Erro ao importar o livro.')
    });
};

</script>

<template>
    <AppLayout title="Livros Google API">
        <template #header>
            <div class="flex justify-between space-x-2 items-center">
                <h2 class="page-title flex items-center gap-2">
                    <svg role="img" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Google</title><path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/></svg> 
                    Livros Google API
                </h2>
                <InputSearch :filters="filters" route="livros.google-index" placeholder="Pesquisar livros..." />
                <div class="header-actions">
                    <ExportButton route-name="livros.google-export" :filters="filters">
                        <template #icon>
                            <DownloadIcon :size="16" />
                        </template>
                        Exportar Google
                    </ExportButton>
                </div>
            </div>
        </template>

        <div v-if="books.data.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:mx-64 gap-x-6 gap-y-10 mt-6">
            <div v-for="book in books.data" :key="book.id" class="flex flex-col group p-4 rounded-xl">
                
                <Link :href="route('livros.google-show', book.google_id)" class="relative aspect-[4/5] overflow-hidden rounded-xl w-full block">
                    <img v-if="book.image_path !== '/storage/imagens/default.webp'"
                        :src="book.image_path" 
                        :alt="book.title" 
                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" 
                    />
                    <img v-else src="/storage/imagens/default.webp" alt="Capa" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                </Link>
            
                <div class="mt-3 flex flex-col flex-grow">
                    <Link :href="route('livros.google-show', book.google_id)">
                        <h2 class="text-sm font-medium text-base-content line-clamp-2 min-h-[2.5rem] hover:text-primary">
                            {{ book.title }}
                        </h2>
                    </Link>

                    <p class="mt-1 text-xs text-gray-500 truncate">
                        de {{ book.autores }}
                    </p>

                    <div class="mt-auto pt-2 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click="quickAdd(book)" class="flex items-center gap-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-green-900 transition-colors font-['Manrope'] px-4 py-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <Plus :size="14" />
                            Adicionar
                        </button>
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
