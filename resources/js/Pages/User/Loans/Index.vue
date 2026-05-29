<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import {Head, Link} from '@inertiajs/vue3'
  import {LibraryBig} from "@lucide/vue";
  import InputSearch from "@/Components/InputSearch.vue";
  import FilterDropdown from "@/Components/FilterDropdown.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import ActiveLoanCard from "@/Components/ActiveLoanCard.vue";

  const props = defineProps({
      loans: Object,
      active_loans: Array,
      filters: Object,
  })



  const loanSortOptions = [
      {value: 'inicio_recente', label: 'Data de Início: Mais recente'},
      {value: 'inicio_antigo', label: 'Data de Início: Mais antigo'},
      {value: 'devolucao_proxima', label: 'Data de Devolução: Mais próxima'},
      {value: 'devolucao_distante', label: 'Data de Devolução: Mais distante'},
      {value: 'numero_asc', label: 'Nº Requisição: Crescente'},
      {value: 'numero_desc', label: 'Nº Requisição: Decrescente'},
      {value: 'dias_asc', label: 'Dias Decorridos: Menos a Mais'},
      {value: 'dias_desc', label: 'Dias Decorridos: Mais a Menos'},
  ]

  const loanStatusOptions = [
      {value: 'pending', label: 'Pendente'},
      {value: 'active', label: 'Ativo'},
      {value: 'returned', label: 'Devolvido'},
      {value: 'overdue', label: 'Em Atraso'},
      {value: 'rejected', label: 'Rejeitado'},
  ]
</script>

<template>
  <AppLayout>
    <Head title="Requisições" />
    <template #header>

        <div class="flex justify-between space-x-2 items-center">
            <h2 class="page-title flex items-center gap-2">
               <LibraryBig :size="20" /> As minhas Requisições
            </h2>
            <InputSearch :filters="filters" route="catalog.requisicoes.index" placeholder="Pesquisar requisições..." />

            <div class="header-actions">
                <FilterDropdown
                    route-name="catalog.requisicoes.index"
                    :filters="filters"
                    :sort-options="loanSortOptions"
                    :statuses="loanStatusOptions"
                />
            </div>

        </div>
    </template>

    <!-- Card de requisições ativas -->
    <div v-if="active_loans && active_loans.length > 0" class="active-section">
        <h3 class="section-title">
            Requisições Ativas
            <span class="section-count">{{ active_loans.length }}</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <ActiveLoanCard
                v-for="loan in active_loans"
                :key="loan.id"
                :loan="loan"
            />
        </div>
    </div>

      <TableWrapper>
          <template #header>
              <th>#</th>
              <th>Livro</th>
              <th>Requisitado em</th>
              <th>Data de Devolução</th>
              <th>Dias Decorridos</th>
              <th>Estado</th>
          </template>

          <template #body>
              <tr v-for="loan in loans.data" :key="loan.id" class="hover:bg-base-300">
                  <Link class="hover:text-primary" :href="route('catalog.requisicoes.show', loan.id)">
                  <td>
                      {{ loan.loan_number }}
                    </td>
                </Link>

                  <td>
                      <div class="flex items-center gap-3">
                          <div>
                              <div class="mask mask-square h-12 w-12">
                                  <img
                                      :src="loan.book.image_url"
                                      :alt="loan.book.title" />
                              </div>
                          </div>
                          <div>
                              <Link :href="route('catalog.livros.show', loan.book.id)">
                                  {{ loan.book.title }}
                              </Link>
                          </div>
                      </div>
                  </td>
                  <td>
                      {{ loan.start_date }}
                  </td>
                  <td>
                      <span v-if="loan.end_date">{{ loan.end_date }}</span>
                      <span v-else class="text-gray-400">-</span>
                  </td>
                  <td>
                      <span v-if="loan.start_date">{{ loan.elapsed_days }} dias</span>
                      <span v-else class="text-gray-400">-</span>
                  </td>
                  <td>
                    <span class="badge badge-sm whitespace-nowrap" :class="loan.status_color">
                      {{ loan.status_label }}
                    </span>
                  </td>
              </tr>
          </template>

          <template #footer>
              <th>#</th>
              <th>Livro</th>
              <th>Requisitado em</th>
              <th>Data de Devolução</th>
              <th>Dias Decorridos</th>
              <th>Estado</th>
          </template>
      </TableWrapper>

      <div v-if="loans.links && loans.links.length > 3" class="pagination">
          <div class="pagination-list">
              <Link
                  v-for="(link, index) in loans.links"
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

      <div v-if="loans.data.length === 0" class="text-center py-10">
          <p class="empty-state-text">Ainda não há requisitações registadas.</p>
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

    /* ─── Active Section ─── */
    .active-section {
        margin-bottom: 32px;
        margin-left: 5rem;
        margin-right: 5rem;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: 'Manrope', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: var(--color-silk-content);
        margin-bottom: 16px;
    }

    .section-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 6px;
        font-size: 12px;
        font-weight: 700;
        color: #ffffff;
        background: var(--color-primary);
        border-radius: 9999px;
    }
</style>
