<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import {Head, Link} from '@inertiajs/vue3'
  import {MessageCircle, Star} from "@lucide/vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import { computed } from 'vue';
  import { usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ReviewPendingButton from '@/Components/ReviewPendingButton.vue';

  const props = defineProps({
      reviews: Object,
  })
</script>

<template>
  <AppLayout>
    <Head title="As Minhas Opiniões" />
    <template #header>
        <div class="flex justify-between space-x-2 items-center">
            <h2 class="page-title flex items-center gap-2">
               <MessageSquareText :size="20" /> As minhas Opiniões
            </h2>
        </div>
    </template>

    <TableWrapper>
        <template #header>
            <th>Livro</th>
            <th>Opinião</th>
            <th>Data da Avaliação</th>
            <th>Estado</th>
            <th>Ações</th>
        </template>

        <template #body>
            <tr v-for="review in reviews.data" :key="review.id" class="hover:bg-base-300">
                <td>
                    <div class="flex items-center gap-3">
                        <div>
                            <div class="mask mask-square h-12 w-12">
                                <img
                                    :src="review.book.image_url"
                                    :alt="review.book.title" />
                            </div>
                        </div>
                        <div>
                            <Link :href="route('catalog.livros.show', review.book.id)" class="hover:text-primary font-semibold">
                                {{ review.book.title }}
                            </Link>
                        </div>
                    </div>
                </td>

                <td>
                    <Link :href="route('opinioes.edit', review.id)" class="flex flex-col max-w-sm group">
                        <span class="font-bold text-sm truncate text-base-content group-hover:text-primary transition-colors duration-200">{{ review.review_title }}</span>
                        <span class="text-xs text-gray-500 line-clamp-2 mt-0.5 whitespace-normal group-hover:text-primary/80 transition-colors duration-200">{{ review.review_text }}</span>
                        <div class="flex items-center gap-1 mt-1.5">
                            <Star :size="12" fill="#EFBF04" color="#EFBF04"/> <span class="font-bold text-xs text-base-content">{{ review.rating }}/10</span>
                        </div>
                    </Link>
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
                    <Link :href="route('opinioes.edit', review.id)" class="btn btn-sm btn-outline btn-primary">
                        Editar
                    </Link>
                </td>
            </tr>
        </template>

        <template #footer>
            <th>Livro</th>
            <th>Opinião</th>
            <th>Data da Avaliação</th>
            <th>Estado</th>
            <th>Ações</th>
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
        <p class="empty-state-text">Ainda não tem opiniões registadas.</p>
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
