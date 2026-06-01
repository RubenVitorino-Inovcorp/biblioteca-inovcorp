<script setup>
import { Link } from "@inertiajs/vue3";
import { LibraryBig, CalendarClock, Clock } from "@lucide/vue";
import ReturnBookButton from "./ReturnBookButton.vue";

const props = defineProps({
    loan: { type: Object, required: true },
    showRoute: { type: String, default: 'catalog.requisicoes.show' },
    bookRoute: { type: String, default: 'catalog.livros.show' },
    returnRouteName: { type: String, default: 'catalog.requisicoes.devolver' },
});

const formattedDate = (date) => {
    if (!date) return '—';
    return date;
};

const isReturnable = (status) => {
    return status === 'active' || status === 'overdue';
};
</script>

<template>
    <div class="active-card" :class="{ 'active-card--overdue': loan.status === 'overdue' }">
        <div class="active-card-inner">
            <!-- Book Cover -->
            <div class="active-card-cover">
                <img
                    v-if="loan.book?.image_url"
                    :src="loan.book.image_url"
                    :alt="loan.book?.title"
                    class="active-card-cover-img"
                />
                <div v-else class="active-card-cover-placeholder">
                    <LibraryBig :size="28" />
                </div>
            </div>

            <!-- Content -->
            <div class="active-card-content">
                <div class="active-card-header">
                    <Link :href="route(showRoute, loan.id)" class="active-card-number">
                        {{ loan.loan_number }}
                    </Link>
                    <span class="badge badge-sm" :class="loan.status_color">
                        {{ loan.status_label }}
                    </span>
                </div>

                <Link :href="route(bookRoute, loan.book?.id)" class="active-card-title">
                    {{ loan.book?.title }}
                </Link>

                <!-- Date info -->
                <div class="active-card-meta">
                    <div class="active-card-meta-item" v-if="loan.start_date">
                        <CalendarClock :size="14" />
                        <span>Início: {{ formattedDate(loan.start_date) }}</span>
                    </div>
                    <div class="active-card-meta-item" v-if="loan.estimated_return_date">
                        <Clock :size="14" />
                        <span :class="{ 'active-card-meta-overdue': loan.status === 'overdue' }">
                            Devolução: {{ formattedDate(loan.estimated_return_date) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Return Action -->
            <div v-if="isReturnable(loan.status)" class="active-card-actions">
                <ReturnBookButton
                    :loan="loan"
                    :route-name="returnRouteName"
                    :compact="true"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
.active-card {
    background: var(--color-base-100);
    border: 1px solid var(--color-silk-300);
    border-radius: 12px;
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.active-card:hover {
    border-color: var(--color-silk-muted);
    box-shadow: 0px 4px 20px rgba(0, 108, 73, 0.06);
}

.active-card--overdue {
    border-color: #fbbf24;
    background: linear-gradient(135deg, #fffbeb 0%, var(--color-base-100) 40%);
}

.active-card--overdue:hover {
    border-color: #f59e0b;
    box-shadow: 0px 4px 20px rgba(245, 158, 11, 0.12);
}

.active-card-inner {
    display: flex;
    align-items: stretch;
    gap: 0;
}

/* ─── Book Cover ─── */
.active-card-cover {
    width: 100px;
    min-height: 140px;
    flex-shrink: 0;
    overflow: hidden;
    background: var(--color-silk-200);
    display: flex;
    align-items: center;
    justify-content: center;
}

.active-card-cover-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.active-card-cover-placeholder {
    color: var(--color-silk-muted);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ─── Content ─── */
.active-card-content {
    flex: 1;
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
}

.active-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.active-card-number {
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    font-weight: 700;
    color: var(--color-silk-muted);
    letter-spacing: 0.03em;
    text-decoration: none;
    transition: color 0.15s ease;
}

.active-card-number:hover {
    color: var(--color-primary);
}

.active-card-title {
    font-family: 'Manrope', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: var(--color-silk-content);
    line-height: 1.3;
    text-decoration: none;
    transition: color 0.15s ease;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.active-card-title:hover {
    color: var(--color-primary);
}

/* ─── Meta ─── */
.active-card-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-top: 2px;
}

.active-card-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    color: var(--color-silk-muted);
}

.active-card-meta-overdue {
    color: #d97706;
    font-weight: 700;
}

/* ─── Actions ─── */
.active-card-actions {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 8px;
    padding: 16px 20px;
    border-left: 1px solid var(--color-silk-300);
    flex-shrink: 0;
}

/* ─── Responsive ─── */
@media (max-width: 640px) {
    .active-card-inner {
        flex-direction: column;
    }

    .active-card-cover {
        width: 100%;
        min-height: 120px;
        max-height: 160px;
    }

    .active-card-actions {
        flex-direction: row;
        border-left: none;
        border-top: 1px solid var(--color-silk-300);
        padding: 12px 16px;
    }
}
</style>
