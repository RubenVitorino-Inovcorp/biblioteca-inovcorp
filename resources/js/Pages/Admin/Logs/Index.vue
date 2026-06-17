<script setup lang="ts">
  import AppLayout from '@/Layouts/AppLayout.vue'
  import {Head, Link} from '@inertiajs/vue3'
  import {Activity} from "@lucide/vue";
  import InputSearch from "@/Components/InputSearch.vue";
  import FilterDropdown from "@/Components/FilterDropdown.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import { usePage } from '@inertiajs/vue3';
  import { PaginatedData, User, ActivityLog } from '@/types';

  interface Filters {
      search?: string;
      sort?: string;
      module?: string;
  }

  const moduleTranslations: Record<string, string> = {
      'books': 'Livro',
      'authors': 'Autor',
      'publishers': 'Editora',
      'users': 'Utilizador',
      'loans': 'Requisição',
      'reviews': 'Avaliação',
      'orders': 'Encomenda'
  }

  const props = defineProps<{
      logs: PaginatedData<ActivityLog>;
      filters: Filters;
  }>();

  const page = usePage<{
      auth: {
          user?: User | null;
      };
  }>();

  interface SortOption {
      value: string;
      label: string;
  }

  const logSortOptions: SortOption[] = [
      {value: 'data_desc', label: 'Data: Mais recente'},
      {value: 'data_asc', label: 'Data: Mais antiga'},
      {value: 'acao_az', label: 'Ação: A-Z'},
      {value: 'acao_za', label: 'Ação: Z-A'},
  ]
</script>

<template>
  <AppLayout>
    <Head title="Atividade" />
    <template #header>

        <div class="flex justify-between space-x-2 items-center">
            <h2 class="page-title flex items-center gap-2">
               <Activity :size="20" /> Atividade
            </h2>
            <InputSearch :filters="filters" route="admin.logs.index" placeholder="Pesquisar logs..." />

            <div class="header-actions">
                <FilterDropdown
                    route-name="admin.logs.index"
                    :filters="filters"
                    :sort-options="logSortOptions"
                />
            </div>

        </div>
    </template>
      <TableWrapper>
          <template #header>
              <th>#</th>
              <th>Utilizador</th>
              <th>Módulo</th>
              <th>Ação</th>
              <th>ID Objeto</th>
              <th>Endereço IP</th>
              <th>Navegador</th>
              <th>Data</th>
          </template>

          <template #body>
              <tr v-for="log in logs.data" :key="log.id" class="hover:bg-base-300">
                  <td>
                      {{ log.id }}
                  </td>

                  <td>
                      <div v-if="log.user" class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="w-10 h-10 rounded-full border border-gray-100 overflow-hidden shadow-sm">
                                <img v-if="log.user.profile_photo_url" :src="log.user.profile_photo_url" :alt="log.user.name" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200 text-base-content/70 text-lg font-bold font-['Manrope']">
                                    {{ log.user.name?.charAt(0)?.toUpperCase() }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <Link class="hover:text-primary" :href="route('utilizadores.show', log.user.id)">
                                <div class="font-bold">{{ log.user.name }}</div>
                                <div class="text-xs opacity-50">{{ log.user.email }}</div>
                            </Link>
                        </div>
                      </div>
                      <div v-else class="text-gray-500 italic">
                          Sistema
                      </div>
                  </td>

                  <td>
                      <span class="badge badge-outline">{{ moduleTranslations[log.module] || log.module }}</span>
                  </td>

                  <td>
                      {{ log.action }}
                  </td>

                  <td>
                      <span v-if="log.object_id" class="font-mono text-sm text-gray-500">#{{ log.object_id }}</span>
                      <span v-else class="text-gray-400">-</span>
                  </td>

                  <td>
                      <span v-if="log.ip_address" class="font-mono text-sm text-gray-500">{{ log.ip_address }}</span>
                      <span v-else class="text-gray-400">-</span>
                  </td>

                  <td>
                      <span v-if="log.user_agent" class="font-mono text-sm text-gray-500">{{ log.user_agent }}</span>
                      <span v-else class="text-gray-400">-</span>
                  </td>

                  <td>
                      <div class="text-sm">
                        {{ new Date(log.created_at).toLocaleDateString('pt-PT') }}
                        <span class="opacity-50 ml-1">{{ new Date(log.created_at).toLocaleTimeString('pt-PT', {hour: '2-digit', minute: '2-digit'}) }}</span>
                      </div>
                  </td>
              </tr>
          </template>

          <template #footer>
              <th>#</th>
              <th>Utilizador</th>
              <th>Módulo</th>
              <th>Ação</th>
              <th>ID Objeto</th>
              <th>Endereço IP</th>
              <th>Navegador</th>
              <th>Data</th>
          </template>
      </TableWrapper>

      <div v-if="logs.links && logs.links.length > 3" class="pagination">
          <div class="pagination-list">
              <Link
                  v-for="(link, index) in logs.links"
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

      <div v-if="logs.data.length === 0" class="text-center py-10">
          <p class="empty-state-text">Ainda não há atividade registada.</p>
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

    /* ─── Pagination Section ─── */
    .pagination {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    .pagination-list {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .pagination-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 6px;
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: var(--color-silk-content);
        background: white;
        border: 1px solid var(--color-silk-300);
        transition: all 0.2s ease;
    }

    .pagination-item:hover:not(.pagination-item--disabled) {
        background: var(--color-silk-100);
        border-color: var(--color-silk-400);
    }

    .pagination-item--active {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }

    .pagination-item--active:hover {
        background: var(--color-primary);
        border-color: var(--color-primary);
    }

    .pagination-item--disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: var(--color-silk-100);
    }
</style>
