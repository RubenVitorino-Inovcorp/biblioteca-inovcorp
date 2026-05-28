<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import { Star, Send, CircleCheck, CircleX, ClockAlert } from '@lucide/vue';

const props = defineProps({
    bookId: { type: Number, required: true },
    loanId: { type: Number, required: false },
    review: { type: Object, default: null },
});

const isEdit = computed(() => !!props.review);

const form = useForm({
    book_id: props.bookId,
    loan_id: props.loanId,
    review_title: props.review?.review_title || '',
    review_text: props.review?.review_text || '',
    rating: props.review?.rating || 5,
});

const emit = defineEmits(['success']);

const submitReview = () => {
    if (isEdit.value) {
        form.put(route('opinioes.update', props.review.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Opinião atualizada com sucesso!');
                emit('success');
            },
            onError: (errors) => {
                const firstError = Object.values(errors)[0];
                toast.error(firstError || 'Erro ao atualizar a opinião.');
            },
        });
    } else {
        form.post(route('opinioes.store'), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Opinião enviada para moderação!');
                form.reset('review_title', 'review_text', 'rating');
                emit('success');
            },
            onError: (errors) => {
                const firstError = Object.values(errors)[0];
                toast.error(firstError || 'Erro ao enviar a opinião.');
            },
        });
    }
};

const getRatingLabel = (rating) => {
    if (!rating) return 'Sem avaliação';
    const LABELS = {
        1: 'Péssimo', 2: 'Muito Mau', 3: 'Mau', 4: 'Abaixo da Média',
        5: 'Razoável', 6: 'Bom', 7: 'Muito Bom', 8: 'Ótimo',
        9: 'Excelente', 10: 'Obra-Prima',
    };
    const key = Math.floor(rating);
    return LABELS[key] || 'Sem classificação';
};
</script>

<template>
    <div class="review-form-wrapper">
        <div class="review-form-card">
            <div v-if="isEdit" class="review-status-card">
                <div v-if="review.status === 'pending'" class="review-status-pending">
                    <ClockAlert />
                    <div>
                        <h4 class="review-status-title">A sua opinião está a ser analisada</h4>
                        <p class="review-status-text">Título: <strong>{{ review.review_title }}</strong></p>
                    </div>
                </div>
                <div v-else-if="review.status === 'rejected'" class="review-status-rejected">
                    <CircleX />
                    <div>
                        <h4 class="review-status-title">A sua opinião foi rejeitada</h4>
                        <p v-if="review.rejection_reason" class="review-status-text">Motivo: {{ review.rejection_reason }}</p>                    </div>
                </div>
                <div v-else-if="review.status === 'approved'" class="review-status-approved">
                    <CircleCheck />
                    <div>
                        <h4 class="review-status-title">Já partilhou a sua opinião sobre este livro</h4>
                    </div>
                </div>
            </div>

            <h3 class="review-form-title">{{ isEdit ? 'Editar Opinião' : 'Partilhe a sua opinião' }}</h3>
            <p class="review-form-subtitle">{{ isEdit ? 'A sua opinião editada será reenviada para moderação.' : 'A sua opinião será enviada para aprovação antes de ser publicada.' }}</p>

            <form @submit.prevent="submitReview" class="review-form">
                <!-- Título da opinião -->
                <div class="form-group">
                    <label for="review_title" class="form-label">Título</label>
                    <input
                        id="review_title"
                        v-model="form.review_title"
                        type="text"
                        class="form-input"
                        placeholder="Dê um título à sua opinião..."
                        :class="{ 'form-input--error': form.errors.review_title }"
                    />
                    <p v-if="form.errors.review_title" class="form-error">{{ form.errors.review_title }}</p>
                </div>

                <!-- Opinião -->
                <div class="form-group">
                    <label for="review_text" class="form-label">A sua opinião</label>
                    <textarea
                        id="review_text"
                        v-model="form.review_text"
                        rows="4"
                        class="form-textarea"
                        placeholder="O que achou deste livro?"
                        :class="{ 'form-input--error': form.errors.review_text }"
                    />
                    <p v-if="form.errors.review_text" class="form-error">{{ form.errors.review_text }}</p>
                </div>

                <!-- Classificação -->
                <div class="form-group">
                    <label class="form-label">Classificação</label>
                    <div class="rating-section">
                        <div class="rating-slider-row">
                            <Star :size="18" class="rating-star-icon" />
                            <input
                                type="range"
                                v-model.number="form.rating"
                                min="1"
                                max="10"
                                step="0.5"
                                class="range range-primary range-sm"
                            />
                            <div class="rating-value-badge">
                                {{ form.rating }}/10
                            </div>
                        </div>
                        <div class="rating-ticks">
                            <span v-for="n in 10" :key="n" class="rating-tick" :class="{ 'rating-tick--active': n <= form.rating }">|</span>
                        </div>
                        <span class="rating-label">{{ getRatingLabel(form.rating) }}</span>
                    </div>
                    <p v-if="form.errors.rating" class="form-error">{{ form.errors.rating }}</p>
                </div>

                <!-- Submit -->
                <div class="review-form-actions">
                    <button
                        type="submit"
                        class="review-submit-btn"
                        :disabled="form.processing"
                    >
                        <Send :size="16" />
                        <span>{{ form.processing ? (isEdit ? 'A atualizar...' : 'A enviar...') : (isEdit ? 'Atualizar Opinião' : 'Enviar Opinião') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.review-form-wrapper {
    margin-bottom: 2rem;
}

.review-form-card {
    background: #fff;
    border-radius: 1rem;
    padding: 1.5rem;
    border: 1px solid var(--color-silk-300, #e5e7eb);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.review-form-title {
    font-family: 'Manrope', sans-serif;
    font-size: 1.25rem;
    font-weight: 800;
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
</style>
