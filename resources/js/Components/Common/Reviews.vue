<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { MessageSquareText, CircleX, CircleCheck, ClockAlert, Star } from '@lucide/vue';
import ReviewForm from '@/Components/ReviewForm.vue';

const props = defineProps({
    reviews: Array,
    book: Object,
    userReview: Object,
    reviewableLoanId: Number,
});

const auth = computed(() => usePage().props.auth.user);

const isAdmin = computed(() => {
    return usePage().props.auth.user?.is_admin;
});

const hasAlreadyReviewed = computed(() => !!props.userReview);
const canReview = computed(() => !hasAlreadyReviewed.value && !!props.reviewableLoanId && !isAdmin.value);




const getUserInitial = (name) => {
    return name?.charAt(0)?.toUpperCase() || '?';
};
</script>

<template>
    <div>
        <div class="max-w-5xl mx-auto p-4 md:p-8">
            <div class="reviews-container">
                <!-- Cabeçalho das opiniões -->
                <div class="reviews-header">
                    <div class="reviews-header-icon">
                        <MessageSquareText :size="20" />
                    </div>
                    <h2 class="reviews-header-title">Opiniões</h2>
                    <span v-if="reviews.length" class="reviews-count">{{ reviews.length }}</span>
                </div>

                <!-- Formulário de opinião -->
                <ReviewForm v-if="canReview" :bookId="book.id" :loanId="reviewableLoanId" />

                <!-- User já avaliou (pendente/rejeitada/aprovada) -->
                <div v-else-if="hasAlreadyReviewed" class="review-status-card">
                    <div v-if="userReview.status === 'pending'" class="review-status-pending">
                        <ClockAlert />
                        <div>
                            <h4 class="review-status-title">A sua opinião está a ser analisada</h4>
                            <p class="review-status-text">Título: <strong>{{ userReview.review_title }}</strong></p>
                        </div>
                    </div>
                    <div v-else-if="userReview.status === 'rejected'" class="review-status-rejected">
                        <CircleX />
                        <div>
                            <h4 class="review-status-title">A sua opinião foi rejeitada</h4>
                            <p v-if="userReview.rejection_reason" class="review-status-text">Motivo: {{ userReview.rejection_reason }}</p>
                            <Link :href="route('opinioes.edit', userReview.id)" class="review-edit-link">Editar e reenviar</Link>
                        </div>
                    </div>
                    <div v-else-if="userReview.status === 'approved'" class="review-status-approved">
                        <CircleCheck />
                        <div>
                            <h4 class="review-status-title">Já partilhou a sua opinião sobre este livro</h4>
                        </div>
                    </div>
                </div>

                <!-- Opiniões -->
                <div v-if="reviews.length" class="reviews-list">
                    <div v-for="review in reviews" :key="review.id" class="chat chat-start">
                        <div class="chat-image avatar">
                            <div class="review-avatar">
                                <img
                                    v-if="review.user?.profile_photo_url"
                                    :src="review.user.profile_photo_url"
                                    :alt="review.user.name"
                                    class="review-avatar-img"
                                />
                                <span v-else class="review-avatar-initials">
                                    {{ getUserInitial(review.user?.name) }}
                                </span>
                            </div>
                        </div>
                        <div class="chat-header">
                            <span class="review-author">{{ review.user?.name }}</span>
                            <time class="review-date">{{ review.created_at }}</time>
                        </div>
                        <div class="chat-bubble review-bubble group">
                            <div class="review-bubble-title">{{ review.review_title }}</div>
                            <p class="review-bubble-text">{{ review.review_text }}</p>
                            <div class="review-bubble-footer">
                                <Link
                                    v-if="review.user?.id === auth?.id"
                                    :href="route('opinioes.edit', review.id)"
                                    class="text-xs font-semibold hover:text-primary transition-opacity opacity-0 group-hover:opacity-100"
                                >
                                    Editar
                                </Link>
                                <div v-else></div>

                                <div class="review-bubble-rating">
                                    <Star :size="14" class="review-bubble-star" />
                                    <span>{{ review.rating }}/10</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="!canReview" class="reviews-empty">
                    <div class="reviews-empty-icon">
                        <MessageSquareText :size="32" class="text-gray-300" />
                    </div>
                    <h3 class="reviews-empty-title">Sem opiniões</h3>
                    <p class="reviews-empty-text">Este livro ainda não tem opiniões aprovadas.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ───────────────────────────────────────────
   Container
   ─────────────────────────────────────────── */
.reviews-container {
    background: var(--color-base-100);
    border-radius: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--color-silk-300, #e5e7eb);
    overflow: hidden;
    padding: 1.5rem 2rem 2rem;
}

/* ───────────────────────────────────────────
   Header
   ─────────────────────────────────────────── */
.reviews-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--color-silk-300, #e5e7eb);
}

.reviews-header-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background-color: var(--color-primary);
    color: #fff;
    flex-shrink: 0;
}

.reviews-header-title {
    font-family: 'Manrope', sans-serif;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--color-base-content);
}

.reviews-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 8px;
    border-radius: 999px;
    background: var(--color-primary);
    color: #fff;
    font-family: 'Manrope', sans-serif;
    font-size: 0.75rem;
    font-weight: 700;
}

/* ───────────────────────────────────────────
   Review Form
   ─────────────────────────────────────────── */
.review-form-wrapper {
    margin-bottom: 2rem;
}

.review-form-card {
    background: var(--color-base-200, #f9fafb);
    border: 1px solid var(--color-silk-300, #e5e7eb);
    border-radius: 1rem;
    padding: 1.5rem;
}

.review-form-title {
    font-family: 'Manrope', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-base-content);
    margin-bottom: 0.25rem;
}

.review-form-subtitle {
    font-family: 'Manrope', sans-serif;
    font-size: 0.8rem;
    color: var(--color-base-content);
    opacity: 0.5;
    margin-bottom: 1.25rem;
}

.review-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.form-label {
    font-family: 'Manrope', sans-serif;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--color-base-content);
}

.form-input,
.form-textarea {
    font-family: 'Manrope', sans-serif;
    font-size: 0.875rem;
    padding: 0.625rem 0.875rem;
    border: 1px solid var(--color-silk-300, #d1d5db);
    border-radius: 0.625rem;
    background: var(--color-base-100);
    color: var(--color-base-content);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    outline: none;
    width: 100%;
}

.form-input:focus,
.form-textarea:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(0, 128, 0, 0.08);
}

.form-input--error {
    border-color: #ef4444;
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-error {
    font-family: 'Manrope', sans-serif;
    font-size: 0.75rem;
    color: #ef4444;
    margin: 0;
}

/* ───────────────────────────────────────────
   Classificação
   ─────────────────────────────────────────── */
.rating-section {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.rating-slider-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.rating-star-icon {
    color: #f59e0b;
    flex-shrink: 0;
}

.rating-value-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 48px;
    padding: 0.25rem 0.625rem;
    border-radius: 999px;
    background: var(--color-primary);
    color: #fff;
    font-family: 'Manrope', sans-serif;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}

.rating-ticks {
    display: flex;
    justify-content: space-between;
    padding: 0 30px 0 26px;
}

.rating-tick {
    font-size: 0.5rem;
    color: var(--color-silk-300, #d1d5db);
    line-height: 1;
}

.rating-tick--active {
    color: var(--color-primary);
}

.rating-label {
    font-family: 'Manrope', sans-serif;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--color-primary);
    text-align: center;
}

/* ───────────────────────────────────────────
   Submit Button
   ─────────────────────────────────────────── */
.review-form-actions {
    display: flex;
    justify-content: flex-end;
    padding-top: 0.5rem;
}

.review-submit-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    border-radius: 0.625rem;
    background: var(--color-primary);
    color: #fff;
    font-family: 'Manrope', sans-serif;
    font-size: 0.8125rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.1s ease;
}

.review-submit-btn:hover {
    background: #005a30;
}

.review-submit-btn:active {
    transform: scale(0.97);
}

.review-submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* ───────────────────────────────────────────
   Cards de status (já avaliado)
   ─────────────────────────────────────────── */
.review-status-card {
    margin-bottom: 1.5rem;
}

.review-status-pending,
.review-status-rejected,
.review-status-approved {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-radius: 0.75rem;
    border: 1px solid;
}

.review-status-pending {
    background: #fffbeb;
    border-color: #fde68a;
}

.review-status-rejected {
    background: #fef2f2;
    border-color: #fecaca;
}

.review-status-approved {
    background: #f0fdf4;
    border-color: #bbf7d0;
}

.review-status-title {
    font-family: 'Manrope', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--color-base-content);
    margin-bottom: 0.25rem;
}

.review-status-text {
    font-family: 'Manrope', sans-serif;
    font-size: 0.8125rem;
    color: var(--color-base-content);
    opacity: 0.7;
    margin: 0;
}

.review-edit-link {
    font-family: 'Manrope', sans-serif;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--color-primary);
    text-decoration: underline;
    margin-top: 0.25rem;
    display: inline-block;
}

/* ───────────────────────────────────────────
   Lista de opiniões (balões de conversa)
   ─────────────────────────────────────────── */
.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* Avatar */
.review-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary), #00a86b);
    color: #fff;
    flex-shrink: 0;
}

.review-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.review-avatar-initials {
    font-family: 'Manrope', sans-serif;
    font-size: 1rem;
    font-weight: 700;
}

/* Chat header */
.review-author {
    font-family: 'Manrope', sans-serif;
    font-weight: 600;
    font-size: 0.8125rem;
}

.review-date {
    font-family: 'Manrope', sans-serif;
    font-size: 0.6875rem;
    opacity: 0.5;
}

/* Chat bubble */
.review-bubble {
    background: var(--color-base-200, #f3f4f6) !important;
    color: var(--color-base-content) !important;
    border-radius: 1rem 1rem 1rem 0.25rem !important;
    padding: 0.875rem 1rem !important;
    max-width: 600px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.review-bubble-title {
    font-family: 'Manrope', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    margin-bottom: 0.375rem;
    color: var(--color-base-content);
}

.review-bubble-text {
    font-family: 'Manrope', sans-serif;
    font-size: 0.8125rem;
    line-height: 1.6;
    margin: 0;
    color: var(--color-base-content);
    opacity: 0.85;
}

.review-bubble-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.06);
}

.review-bubble-rating {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-family: 'Manrope', sans-serif;
    font-size: 0.75rem;
    font-weight: 700;
    color: #f59e0b;
}

.review-bubble-star {
    fill: #f59e0b;
    color: #f59e0b;
}

/* ───────────────────────────────────────────
   Empty State
   ─────────────────────────────────────────── */
.reviews-empty {
    text-align: center;
    padding: 3rem 2rem;
}

.reviews-empty-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--color-base-200, #f3f4f6);
    margin-bottom: 1rem;
}

.reviews-empty-title {
    font-family: 'Manrope', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    color: var(--color-base-content);
    opacity: 0.4;
    margin-bottom: 0.25rem;
}

.reviews-empty-text {
    font-family: 'Manrope', sans-serif;
    font-size: 0.8125rem;
    color: var(--color-base-content);
    opacity: 0.35;
    margin: 0;
}
</style>
