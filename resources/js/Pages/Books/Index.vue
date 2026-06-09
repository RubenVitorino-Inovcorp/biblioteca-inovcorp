<script setup lang="ts">

  import {Head, Link, router} from '@inertiajs/vue3'
  import AppLayout from "@/Layouts/AppLayout.vue";
  import BookEditModal from "@/Components/BookEditModal.vue";
  import BookDeleteForm from "@/Components/BookDeleteForm.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import InputSearch from "@/Components/InputSearch.vue";
  import FilterDropdown from "@/Components/FilterDropdown.vue";
  import ExportButton from "@/Components/ExportButton.vue";
  import { Book as BookIcon, CirclePlus, DownloadIcon } from "@lucide/vue";
  import { PaginatedData, Book, Author, Publisher, Tag } from '@/types';

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
      tags?: Tag[];
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
    <AppLayout title="Biblioteca - Livros">
        <template #header>
            <div class="flex justify-between space-x-2 items-center">
                <h2 class="page-title flex items-center gap-2">
                    <BookIcon :size="20"/> Livros
                </h2>
               <InputSearch :filters="filters" route="livros.index" placeholder="Pesquisar livro..." />

                <div class="header-actions">
                    <Link :href="route('livros.create')" class="btn-add">
                        <CirclePlus :size="16" /> Adicionar Livro
                    </Link>
                    <ExportButton route-name="livros.export" :filters="filters">
                        <template #icon>
                            <DownloadIcon :size="16" />
                        </template>
                        Exportar Livros
                    </ExportButton>
                    <FilterDropdown
                        route-name="livros.index"
                        :filters="filters"
                        :sort-options="bookSortOptions"
                        :publishers="publishers"
                        :authors="authors"
                    />
                </div>

            </div>
        </template>

        <TableWrapper>
                <template #header>
                    <th></th>
                    <th>Livro</th>
                    <th>Preço (€)</th>
                    <th>Autor</th>
                    <th>Editora</th>
                    <th></th>
                    <th></th>
                </template>

                <template #body>
                    <tr v-for="book in books.data" :key="book.id" class="hover:bg-base-300">
                        <th></th>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="mask mask-squircle h-12 w-12">
                                        <img
                                            :src="book.image_url"
                                            :alt="book.title" />
                                    </div>
                                </div>
                                <div>
                                    <Link class="hover:text-primary" :href="route('livros.show', book.id)">
                                        <div class="font-bold">{{ book.title }}</div>
                                        <div class="text-sm opacity-50">{{ book.isbn }}</div>
                                    </Link>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ book.price }}€
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                <div class="badge-author" v-for="author in book.authors" :key="author.id">
                                    <Link :href="route('autores.show', author.id)">
                                        {{ author.name }}
                                    </Link>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                <div class="badge-publisher" v-if="book.publisher">
                                    <Link :href="route('editoras.show', book.publisher?.id)">
                                        {{ book.publisher?.name }}
                                    </Link>
                                </div>
                            </div>
                        </td>
                        <th>
                            <BookEditModal :book="book" :publishers="publishers" :authors="authors" :tags="tags" />
                        </th>
                        <th>
                            <BookDeleteForm :book="book" />
                        </th>
                    </tr>
                </template>

                <template #footer>
                    <th></th>
                    <th>Livro</th>
                    <th>Preço (€)</th>
                    <th>Autor</th>
                    <th>Editora</th>
                    <th></th>
                    <th></th>
                </template>
        </TableWrapper>

        <div v-if="books.links && books.links.length > 3" class="pagination">
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
            <p class="empty-state-text">Ainda não há livros registados.</p>
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
