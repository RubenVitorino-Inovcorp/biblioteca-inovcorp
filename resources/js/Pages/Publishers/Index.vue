<script setup>
  import {Link} from '@inertiajs/vue3'
  import AppLayout from "@/Layouts/AppLayout.vue";
  import PublisherDeleteForm from "@/Components/PublisherDeleteForm.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import PublisherEditModal from "@/Components/PublisherEditModal.vue";
  import InputSearch from "@/Components/InputSearch.vue";
  import FilterDropdown from "@/Components/FilterDropdown.vue";
  import { Building, CirclePlus, DownloadIcon } from "@lucide/vue";
  import ExportButton from "@/Components/ExportButton.vue";

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
    <AppLayout title="Biblioteca - Editoras">
        <template #header>
            <div class="flex justify-between space-x-2 items-center">
                <h2 class="page-title flex items-center gap-2">
                 <Building :size="20"/> Editoras
                </h2>
                <InputSearch :filters="filters" route="editoras.index" placeholder="Pesquisar editora..." />
                <div class="header-actions">
                    <Link :href="route('editoras.create')" class="btn-add">
                        <CirclePlus :size="16" /> Adicionar Editora
                    </Link>
                    <ExportButton route-name="editoras.export" :filters="filters">
                        <DownloadIcon :size="16" /> Exportar Editoras
                    </ExportButton>
                    <FilterDropdown
                        route-name="editoras.index"
                        :filters="filters"
                        :sort-options="publisherSortOptions"
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
                <tr v-for="publisher in publishers.data" :key="publisher.id" class="hover:bg-base-300">
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
                                        :src="publisher.logo_path"
                                        :alt="publisher.name" />
                                </div>
                            </div>
                            <div>
                                <Link class="hover:text-primary" :href="route('editoras.show', publisher.id)">
                                    <div class="font-bold">{{ publisher.name }}</div>
                                </Link>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="badge badge-ghost">
                            {{ publisher.books_count }} {{ publisher.books_count === 1 ? 'livro' : 'livros' }}
                        </div>
                    </td>
                    <th>
                        <PublisherEditModal :publisher="publisher" />
                    </th>
                    <th>
                        <PublisherDeleteForm :publisher="publisher" />
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

        <div v-if="publishers.links && publishers.links.length > 3" class="pagination">
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
            <p class="empty-state-text">Ainda não há editoras registadas.</p>
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
