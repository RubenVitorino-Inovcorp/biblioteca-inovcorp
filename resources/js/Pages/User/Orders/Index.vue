<script setup lang="ts">
  import AppLayout from '@/Layouts/AppLayout.vue'
  import {Head, Link} from '@inertiajs/vue3'
  import {ShoppingBag} from "@lucide/vue";
  import InputSearch from "@/Components/InputSearch.vue";
  import FilterDropdown from "@/Components/FilterDropdown.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import { computed } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import { PaginatedData, Order, User, Book } from '@/types';

  interface Filters {
      search?: string;
      sort?: string;
      status?: string;
  }

  interface Stats {
      active_orders: number;
  }

  const props = defineProps<{
      orders: PaginatedData<Order>;
      users?: User[];
      books?: Book[];
      pending_orders?: Order[];
      filters: Filters;
      stats?: Stats;
  }>();

  const page = usePage<{
      auth: {
          user?: User | null;
      };
  }>();

  const isAdmin = computed<boolean>(() => {
      return !!page.props.auth.user?.is_admin;
  });

  interface SortOption {
      value: string;
      label: string;
  }

  interface StatusOption {
      value: string;
      label: string;
  }

  const orderSortOptions: SortOption[] = [
      {value: 'data_recente', label: 'Data: Mais recente'},
      {value: 'data_antiga', label: 'Data: Mais antiga'},
      {value: 'numero_asc', label: 'Nº Requisição: Crescente'},
      {value: 'numero_desc', label: 'Nº Requisição: Decrescente'},
  ]

  const orderStatusOptions: StatusOption[] = [
      {value: 'pending', label: 'Pendente'},
      {value: 'paid', label: 'Pago'},
      {value: 'processing', label: 'Em Processamento'},
      {value: 'shipped', label: 'Enviado'},
      {value: 'delivered', label: 'Entregue'},
      {value: 'cancelled', label: 'Cancelado'},
      {value: 'returned', label: 'Devolvido'},
  ]
</script>

<template>
  <AppLayout>
    <Head title="Encomendas" />
    <template #header>

        <div class="flex justify-between space-x-2 items-center">
            <h2 class="page-title flex items-center gap-2">
               <ShoppingBag :size="20" /> Encomendas
            </h2>
            <InputSearch :filters="filters" route="encomendas.index" placeholder="Pesquisar encomendas..." />

            <div class="header-actions">
                <FilterDropdown
                    route-name="encomendas.index"
                    :filters="filters"
                    :sort-options="orderSortOptions"
                    :statuses="orderStatusOptions"
                />
            </div>

        </div>
    </template>
        <!-- Mensagem de sucesso vindo do checkout -->
        <div v-if="$page.props.flash?.success" class="alert bg-primary border-none max-w-3xl mx-auto mb-4">
            <img src="/storage/imagens/success.webp" alt="Sucesso" class="max-w-16" /> <span class="font-semibold text-xl text-base-300"> {{ $page.props.flash.success }}</span>
        </div>
        <!-- Erro vindo do checkout (quando o payment foi rejeitado, por exemplo) -->
        <div v-if="$page.props.flash?.error" class="alert alert-error border-none max-w-3xl mx-auto mb-4">
           <img src="/storage/imagens/error.webp" alt="Erro" class="max-w-16" /> <span class="font-semibold text-xl text-base-100"> {{ $page.props.flash.error }}</span>
        </div>
        
      <TableWrapper>
          <template #header>
              <th>#</th>
              <th>Encomenda</th>
              <th>Morada de Entrega</th>
              <th>Data da Encomenda</th>
              <th>Total</th>
              <th>Estado</th>
          </template>

          <template #body>
              <tr v-for="order in orders.data" :key="order.id" class="hover:bg-base-300">
                  <td>
                      <Link class="hover:text-primary" :href="route('encomendas.show', order.id)">
                          {{ order.order_number }}
                      </Link>
                  </td>

                  <td class="align-middle">
                    <div v-for="item in order.items" class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="mask mask-squircle h-12 w-12">
                                <img
                                    :src="item.book?.image_url"
                                    :alt="item.book?.title" />
                            </div>
                        </div>
                        <div>
                            <Link class="hover:text-primary" :href="route('catalog.livros.show', item.book?.id)">
                                <div class="font-bold">{{ item.book?.title }}</div>
                                <div class="text-sm opacity-50">{{ item.book?.isbn ?? 'N/A' }}</div>
                            </Link>
                        </div>
                    </div>
                  </td>
                  <td>
                      {{ order.delivery_address }}
                  </td>

                  <td>
                      {{ new Date(order.created_at).toLocaleDateString('pt-PT') }}
                  </td>
                  <td>
                      <span v-if="order.total_price">{{ order.total_price.toFixed(2) }} €</span>
                      <span v-else class="text-gray-400">-</span>
                  </td>
                  <td>
                    <span class="badge badge-sm whitespace-nowrap" :class="order.status_color">
                      {{ order.status_label }}
                    </span>
                  </td>
              </tr>
          </template>

          <template #footer>
              <th>#</th>
              <th>Encomenda</th>
              <th>Morada de Entrega</th>
              <th>Data da Encomenda</th>
              <th>Total</th>
              <th>Estado</th>
          </template>
      </TableWrapper>

      <div v-if="orders.links && orders.links.length > 3" class="pagination">
          <div class="pagination-list">
              <Link
                  v-for="(link, index) in orders.links"
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

      <div v-if="orders.data.length === 0" class="text-center py-10">
          <p class="empty-state-text">Ainda não há encomendas registadas.</p>
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
