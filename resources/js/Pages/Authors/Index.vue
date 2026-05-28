<script setup>
import {Head, Link, router} from '@inertiajs/vue3'
  import AppLayout from "@/Layouts/AppLayout.vue";
  import AuthorEditModal from "@/Components/AuthorEditModal.vue";
  import AuthorDeleteForm from "@/Components/AuthorDeleteForm.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import InputSearch from "@/Components/InputSearch.vue";
  import FilterDropdown from "@/Components/FilterDropdown.vue";
import { CirclePlus, DownloadIcon, UserPen } from "@lucide/vue";
import ExportButton from "@/Components/ExportButton.vue";

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
    <AppLayout title="Biblioteca - Autores">
        <template #header>
            <div class="flex justify-between space-x-2 items-center">
                <h2 class="page-title flex items-center gap-2">
                    <UserPen :size="20"/> Autores
                </h2>
                <InputSearch :filters="filters" route="autores.index" placeholder="Pesquisar autores..." />
                <div class="header-actions">
                    <Link :href="route('autores.create')" class="btn-add">
                        <CirclePlus :size="16" /> Adicionar Autor
                    </Link>
                    <ExportButton route-name="autores.export" :filters="filters">
                        <template #icon>
                            <DownloadIcon :size="16" />
                        </template>
                        Exportar Autores
                    </ExportButton>
                    <FilterDropdown
                        route-name="autores.index"
                        :filters="filters"
                        :sort-options="authorSortOptions"
                    />
                </div>
            </div>
        </template>

        <TableWrapper>
            <template #header>
                <th>
                    <label>
                        <input type="checkbox" class="checkbox" />
                    </label>
                </th>
                <th>Nome</th>
                <th>Total Livros</th>
                <th></th>
                <th></th>
            </template>
            <template #body>
                <tr v-for="author in authors.data" :key="author.id" class="hover:bg-base-300">
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox" />
                        </label>
                    </th>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="mask mask-squircle h-12 w-12">
                                    <img
                                        :src="author.photo_path"
                                        :alt="author.name" />
                                </div>
                            </div>
                            <div>
                                <Link class="hover:text-primary" :href="route('autores.show', author.id)">
                                    <div class="font-bold">{{ author.name }}</div>
                                </Link>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="badge badge-ghost">
                            {{ author.books_count }} {{ author.books_count === 1 ? 'livro' : 'livros' }}
                        </div>
                    </td>
                    <th>
                        <AuthorEditModal :author="author" />
                    </th>
                    <th>
                        <AuthorDeleteForm :author="author" />
                    </th>
                </tr>
            </template>
            <!-- foot -->
            <template #footer>
                <th>
                    <label>
                        <input type="checkbox" class="checkbox" />
                    </label>
                </th>
                <th>Nome</th>
                <th>Total Livros</th>
                <th></th>
                <th></th>
            </template>
        </TableWrapper>

        <div v-if="authors.links && authors.links.length > 3" class="pagination">
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
            <p class="empty-state-text">Ainda não há autores registados.</p>
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
