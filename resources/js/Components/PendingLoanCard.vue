<script setup>
import { Link } from "@inertiajs/vue3";
import { LibraryBig, CalendarClock } from "@lucide/vue";
import PendingButton from "./PendingButton.vue";

const props = defineProps({
    loan: { type: Object, required: true },
    isAdmin: { type: Boolean, default: false },
    showRoute: { type: String, default: 'requisicoes.show' },
    bookRoute: { type: String, default: 'livros.show' },
    userRoute: { type: String, default: 'utilizadores.show' },
});

const formattedDate = (date) => {
    if (!date) return '—';
    return date;
};
</script>

<template>
    <div class="pending-card">
        <div class="pending-card-inner">
            <!-- Book Cover -->
            <div class="pending-card-cover">
                <img
                    v-if="loan.book?.image_url"
                    :src="loan.book.image_url"
                    :alt="loan.book?.title"
                    class="pending-card-cover-img"
                />
                <div v-else class="pending-card-cover-placeholder">
                    <LibraryBig :size="28" />
                </div>
            </div>

            <!-- Content -->
            <div class="pending-card-content">
                <div class="pending-card-header">
                    <Link :href="route(showRoute, loan.id)" class="pending-card-number">
                        {{ loan.loan_number }}
                    </Link>
                    <span class="badge badge-sm" :class="loan.status_color">
                        {{ loan.status_label }}
                    </span>
                </div>

                <Link :href="route(bookRoute, loan.book?.id)" class="pending-card-title">
                    {{ loan.book?.title }}
                </Link>

                <!-- User Info -->
                <div class="pending-card-user">
                    <div class="pending-card-user-avatar">
                        <img
                            v-if="loan.user_photo_snapshot"
                            :src="loan.user_photo_snapshot"
                            :alt="loan.user?.name"
                            class="pending-card-user-img"
                        />
                        <div v-else class="pending-card-user-initials">
                            {{ loan.user?.name?.charAt(0)?.toUpperCase() }}
                        </div>
                    </div>
                    <div class="pending-card-user-info">
                        <Link v-if="isAdmin && userRoute" :href="route(userRoute, loan.user?.id)" class="pending-card-user-name">
                            {{ loan.user?.name }}
                        </Link>
                        <span v-else class="pending-card-user-name-static">
                            {{ loan.user?.name }}
                        </span>
                        <span class="pending-card-user-email">{{ loan.user?.email }}</span>
                    </div>
                </div>

                <!-- Date info -->
                <div class="pending-card-meta">
                    <div class="pending-card-meta-item" v-if="loan.start_date">
                        <CalendarClock :size="14" />
                        <span>{{ formattedDate(loan.start_date) }}</span>
                    </div>
                    <div class="pending-card-meta-item" v-else>
                        <CalendarClock :size="14" />
                        <span class="pending-card-meta-pending">A aguardar aprovação</span>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div v-if="isAdmin" class="pending-card-actions">
                <PendingButton :loan="loan" action="approve" />
                <PendingButton :loan="loan" action="reject" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.pending-card {
    background: var(--color-base-100);
    border: 1px solid var(--color-silk-300);
    border-radius: 12px;
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.pending-card:hover {
    border-color: var(--color-silk-muted);
    box-shadow: 0px 4px 20px rgba(0, 108, 73, 0.06);
}

.pending-card-inner {
    display: flex;
    align-items: stretch;
    gap: 0;
}

/* ─── Book Cover ─── */
.pending-card-cover {
    width: 100px;
    min-height: 140px;
    flex-shrink: 0;
    overflow: hidden;
    background: var(--color-silk-200);
    display: flex;
    align-items: center;
    justify-content: center;
}

.pending-card-cover-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pending-card-cover-placeholder {
    color: var(--color-silk-muted);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ─── Content ─── */
.pending-card-content {
    flex: 1;
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
}

.pending-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.pending-card-number {
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    font-weight: 700;
    color: var(--color-silk-muted);
    letter-spacing: 0.03em;
    text-decoration: none;
    transition: color 0.15s ease;
}

.pending-card-number:hover {
    color: var(--color-primary);
}

.pending-card-title {
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

.pending-card-title:hover {
    color: var(--color-primary);
}

/* ─── User ─── */
.pending-card-user {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pending-card-user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary), #00a86b);
    color: #fff;
}

.pending-card-user-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pending-card-user-initials {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
}

.pending-card-user-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.pending-card-user-name {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-silk-content);
    text-decoration: none;
    transition: color 0.15s ease;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.pending-card-user-name:hover {
    color: var(--color-primary);
}

.pending-card-user-name-static {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-silk-content);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.pending-card-user-email {
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    color: var(--color-silk-muted);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ─── Meta ─── */
.pending-card-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 2px;
}

.pending-card-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-family: 'Manrope', sans-serif;
    font-size: 12px;
    color: var(--color-silk-muted);
}

.pending-card-meta-pending {
    color: #d97706;
    font-weight: 600;
    font-style: italic;
}

/* ─── Actions ─── */
.pending-card-actions {
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
    .pending-card-inner {
        flex-direction: column;
    }

    .pending-card-cover {
        width: 100%;
        min-height: 120px;
        max-height: 160px;
    }

    .pending-card-actions {
        flex-direction: row;
        border-left: none;
        border-top: 1px solid var(--color-silk-300);
        padding: 12px 16px;
    }

    .pending-card-btn {
        flex: 1;
    }
}
</style>
