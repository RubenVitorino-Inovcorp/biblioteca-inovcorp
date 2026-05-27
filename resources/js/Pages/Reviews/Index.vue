<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import {Head, Link} from '@inertiajs/vue3'
  import {MessageCircle} from "@lucide/vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import { computed } from 'vue';
  import { usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

  const props = defineProps({
      reviews: Object,
      pending_reviews: Array,
  })

  const isAdmin = computed(() => {
      return usePage().props.auth.user?.role?.id === usePage().props.roles.ADMIN;
  });

</script>

<template>
  <AppLayout>
    <Head title="Opiniões" />
    <template #header>

        <div class="flex justify-between space-x-2 items-center">
            <h2 class="page-title flex items-center gap-2">
               <MessageCircle :size="20" /> Opiniões
            </h2>
        </div>
    </template>

    <!-- Pending Loans Cards -->
    <div v-if="pending_reviews && pending_reviews.length > 0" class="pending-section">
        <h3 class="section-title">
            Opiniões Pendentes
            <span class="section-count">{{ pending_reviews.length }}</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- <PendingReviewCard
                v-for="review in pending_reviews"
                :key="review.id"
                :review="review"
                :is-admin="isAdmin"
            /> -->
        </div>
    </div>
      <TableWrapper>
          <template #header>
              <th>#</th>
              <th>Livro</th>
              <th>Avaliador</th>
              <th>Data da Avaliação</th>
              <th>Estado</th>
              <th></th>
          </template>

          <template #body>
              <tr v-for="review in reviews.data" :key="review.id" class="hover:bg-base-300">
                  <Link class="hover:text-primary" :href="route(isAdmin ? 'requisicoes.show' : 'catalog.requisicoes.show', review.id)">
                  <td>
                      {{ review.review_number }}
                    </td>
                </Link>

                  <td>
                      <div class="flex items-center gap-3">
                          <div>
                              <div class="mask mask-square h-12 w-12">
                                  <img
                                      :src="review.book.image_path"
                                      :alt="review.book.title" />
                              </div>
                          </div>
                          <div>
                              <Link :href="route(isAdmin ? 'livros.show' : 'catalog.livros.show', review.book.id)">
                                  {{ review.book.title }}
                              </Link>
                          </div>
                      </div>
                  </td>

                  <td>
                      <div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="w-12 h-12 rounded-full border border-gray-100 overflow-hidden shadow-sm">
                                <img v-if="review.user.profile_photo_url" :src="review.user.profile_photo_url" :alt="review.user.name" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200 text-[#3c4a42] text-2xl font-bold font-['Manrope']">
                                    {{ review.user.name?.charAt(0)?.toUpperCase() }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <Link class="hover:text-primary" :href="route('utilizadores.show', review.user.id)">
                                {{ review.user.name }}
                            </Link>
                        </div>
                      </div>
                  </td>
                  <td>
                      {{ review.created_at }}
                  </td>
                  <td>
                    <span class="badge badge-sm whitespace-nowrap" :class="review.status_color">
                      {{ review.status_label }}
                    </span>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                        <!-- <ApproveReviewModal v-if="isAdmin && review.status === 'pending'" :review="review">
                            <PrimaryButton class="px-4">
                                Confirmar Avaliação
                            </PrimaryButton>
                        </ApproveReviewModal>
                        <RejectReviewModal v-if="isAdmin && review.status === 'pending'" :review="review">
                            <PrimaryButton class="px-4">
                                Rejeitar Avaliação
                            </PrimaryButton>
                        </RejectReviewModal> -->
                    </div>
                  </td>
              </tr>
          </template>

          <template #footer>
              <th>#</th>
              <th>Livro</th>
              <th>Avaliador</th>
              <th>Data da Avaliação</th>
              <th>Estado</th>
              <th></th>
          </template>
      </TableWrapper>

      <div v-if="reviews.links && reviews.links.length > 3" class="pagination">
          <div class="pagination-list">
              <Link
                  v-for="(link, index) in reviews.links"
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

      <div v-if="reviews.data.length === 0" class="text-center py-10">
          <p class="empty-state-text">Ainda não há avaliações registadas.</p>
      </div>
  </AppLayout>
</template>

<style scoped>
    .page-title {
        font-family: 'Manrope', sans-serif;
        font-size: 17px;
        font-weight: 600;
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
        color: #191c1e;
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
        border: 1px solid #E2E8F0;
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
        color: #6c7a71;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-family: 'Manrope', sans-serif;
        font-size: 24px;
        font-weight: 800;
        color: #191c1e;
        line-height: 1.2;
    }
</style>
