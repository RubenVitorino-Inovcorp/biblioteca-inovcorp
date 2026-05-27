<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import {Head, Link} from '@inertiajs/vue3'
  import {DownloadIcon, LibraryBig, Activity, CalendarDays, BookOpenCheck} from "@lucide/vue";
  import InputSearch from "@/Components/InputSearch.vue";
  import FilterDropdown from "@/Components/FilterDropdown.vue";
  import ExportButton from "@/Components/ExportButton.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import PendingLoanCard from "@/Components/PendingLoanCard.vue";
  import ReceiveBookModal from "@/Components/ReceiveBookModal.vue";
  import { computed } from 'vue';
  import { usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

  const props = defineProps({
      loans: Object,
      users: Array,
      books: Array,
      pending_loans: Array,
      filters: Object,
      stats: Object,
  })

  const isAdmin = computed(() => {
      return usePage().props.auth.user?.role?.id === usePage().props.roles.ADMIN;
  });

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
      {value: 'return_pending', label: 'Devolução Pendente'},
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
               <LibraryBig :size="20" /> {{ isAdmin ? 'Requisições' : 'As minhas Requisições' }}
            </h2>
            <InputSearch :filters="filters" route="requisicoes.index" placeholder="Pesquisar requisições..." />

            <div class="header-actions">
<!--                <ExportButton route-name="requisicoes.export" :filters="filters">-->
<!--                    <DownloadIcon :size="16" /> Exportar Requisições-->
<!--                </ExportButton>-->
                <FilterDropdown
                    route-name="requisicoes.index"
                    :filters="filters"
                    :sort-options="loanSortOptions"
                    :statuses="loanStatusOptions"
                />
            </div>

        </div>
    </template>

    <!-- Indicators / Stats Section -->
    <div v-if="isAdmin && stats" class="stats-section mx-20 mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="stat-card">
            <div class="stat-icon-wrapper bg-blue-100 text-blue-600">
                <Activity :size="24" />
            </div>
            <div class="stat-content">
                <p class="stat-title">Requisições Ativas</p>
                <h4 class="stat-value">{{ stats.active_loans }}</h4>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrapper bg-orange-100 text-orange-600">
                <CalendarDays :size="24" />
            </div>
            <div class="stat-content">
                <p class="stat-title">Últimos 30 dias</p>
                <h4 class="stat-value">{{ stats.last_30_days }}</h4>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrapper bg-green-100 text-green-600">
                <BookOpenCheck :size="24" />
            </div>
            <div class="stat-content">
                <p class="stat-title">Entregues Hoje</p>
                <h4 class="stat-value">{{ stats.returned_today }}</h4>
            </div>
        </div>
    </div>

    <!-- Pending Loans Cards -->
    <div v-if="pending_loans && pending_loans.length > 0" class="pending-section">
        <h3 class="section-title">
            Requisições Pendentes
            <span class="section-count">{{ pending_loans.length }}</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <PendingLoanCard
                v-for="loan in pending_loans"
                :key="loan.id"
                :loan="loan"
                :is-admin="isAdmin"
            />
        </div>
    </div>
      <TableWrapper>
          <template #header>
              <th>#</th>
              <th>Livro</th>
              <th>Requisitado por</th>
              <th>Requisitado em</th>
              <th>Data de Devolução</th>
              <th>Dias Decorridos</th>
              <th>Estado</th>
              <th></th>
          </template>

          <template #body>
              <tr v-for="loan in loans.data" :key="loan.id" class="hover:bg-base-300">
                  <Link class="hover:text-primary" :href="route(isAdmin ? 'requisicoes.show' : 'catalog.requisicoes.show', loan.id)">
                  <td>
                      {{ loan.loan_number }}
                    </td>
                </Link>

                  <td>
                      <div class="flex items-center gap-3">
                          <div>
                              <div class="mask mask-square h-12 w-12">
                                  <img
                                      :src="loan.book.image_path"
                                      :alt="loan.book.title" />
                              </div>
                          </div>
                          <div>
                              <Link :href="route(isAdmin ? 'livros.show' : 'catalog.livros.show', loan.book.id)">
                                  {{ loan.book.title }}
                              </Link>
                          </div>
                      </div>
                  </td>

                  <td>
                      <div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="w-12 h-12 rounded-full border border-gray-100 overflow-hidden shadow-sm">
                                <img v-if="loan.user_photo_snapshot" :src="loan.user_photo_snapshot" :alt="loan.user.name" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200 text-base-content/70 text-2xl font-bold font-['Manrope']">
                                    {{ loan.user.name?.charAt(0)?.toUpperCase() }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <Link class="hover:text-primary" :href="route('utilizadores.show', loan.user.id)">
                                {{ loan.user.name }}
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
                  <td>
                    <div class="flex items-center gap-2">
                        <ReceiveBookModal v-if="isAdmin && (loan.status === 'active' || loan.status === 'overdue' || loan.status === 'return_pending')" :loan="loan">
                            <PrimaryButton class="px-4">
                                Confirmar Devolução
                            </PrimaryButton>
                        </ReceiveBookModal>
                    </div>
                  </td>
              </tr>
          </template>

          <template #footer>
              <th>#</th>
              <th>Livro</th>
              <th>Requisitado por</th>
              <th>Requisitado em</th>
              <th>Data de Devolução</th>
              <th>Dias Decorridos</th>
              <th>Estado</th>
              <th></th>
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

    /* ─── Pending Section ─── */
    .pending-section {
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
        background: #d97706;
        border-radius: 9999px;
    }

    .pending-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* ─── Stats Section ─── */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        border: 1px solid var(--color-silk-300);
        box-shadow: 0px 4px 20px rgba(15, 23, 42, 0.03);
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        flex-shrink: 0;
    }

    .stat-content {
        display: flex;
        flex-direction: column;
    }

    .stat-title {
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        color: var(--color-silk-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-family: 'Manrope', sans-serif;
        font-size: 24px;
        font-weight: 800;
        color: var(--color-silk-content);
        line-height: 1.2;
    }
</style>
