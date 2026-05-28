<script setup>
import { Check, X, BookOpen, Star } from "@lucide/vue";
import { useForm } from '@inertiajs/vue3';
import { ref } from "vue";
import { toast } from "vue-sonner";
import ConfirmationModal from "./ConfirmationModal.vue";

const props = defineProps({
    review: { type: Object, required: true },
    action: {
        type: String, required: true,
        validator: (value) => ['approve', 'reject'].includes(value)
    },
});

const form = useForm({
    rejection_reason: '',
});

const showModal = ref(false);

const handleClick = () => {
    showModal.value = true;
};

const executeAction = () => {
    if (props.action === 'approve') {
        form.post(route("opinioes.aprovar", props.review.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                toast.success(`Opinião aprovada com sucesso.`);
            },
            onError: (errors) => {
                showModal.value = false;
                toast.error(Object.values(errors)[0] || "Erro ao aprovar a opinião.");
            },
        });
    } else {
        form.post(route("opinioes.rejeitar", props.review.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                toast.success(`Opinião rejeitada.`);
                form.reset();
            },
            onError: (errors) => {
                toast.error(Object.values(errors)[0] || "Erro ao rejeitar a opinião.");
            },
        });
    }
};
</script>

<template>
    <button
        type="button"
        class="pending-button" :class="{ 'pending-button--approve': action === 'approve', 'pending-button--reject': action === 'reject' }"
        :disabled="form.processing"
        @click.prevent="handleClick"
        :title="action === 'approve' ? 'Aprovar opinião' : 'Rejeitar opinião'"
    >
        <Check v-if="action === 'approve'" :size="18" />
        <X v-else :size="18" />
        <span>{{ action === 'approve' ? 'Aprovar' : 'Rejeitar' }}</span>
    </button>

    <ConfirmationModal :show="showModal" @close="showModal = false">
        <template #title>
            {{ action === 'approve' ? 'Aprovar Opinião' : 'Rejeitar Opinião' }}
        </template>
        <template #content>
            <p class="text-gray-600 mb-4">
                {{ action === 'approve' ? 'Tem a certeza que pretende aprovar esta opinião? Ela ficará visível no catálogo.' : 'Tem a certeza que pretende rejeitar esta opinião? Indique o motivo abaixo.' }}
            </p>

            <div v-if="action === 'reject'" class="mb-4">
                <textarea
                    v-model="form.rejection_reason"
                    class="textarea textarea-bordered w-full"
                    placeholder="Motivo da rejeição (obrigatório)..."
                    rows="3"
                ></textarea>
                <p v-if="form.errors.rejection_reason" class="text-red-500 text-xs mt-1">{{ form.errors.rejection_reason }}</p>
            </div>

            <div class="bg-base-200/50 border border-base-300 rounded-xl p-5 flex items-center gap-4 text-sm">
                <div class="shrink-0 rounded-md overflow-hidden shadow-sm bg-base-100">
                    <img v-if="review.book?.image_path" :src="review.book.image_path" :alt="review.book?.title" class="w-12 h-16 object-cover" />
                    <div v-else class="w-12 h-16 bg-gray-100 flex items-center justify-center">
                        <BookOpen class="text-gray-400" :size="20" />
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-base-content text-base">{{ review.book?.title }}</h4>
                    <p class="text-gray-500 mt-1">Opinião de <strong>{{ review.user?.name }}</strong></p>
                    <div class="flex items-center gap-1 mt-1 text-base-content">
                        <Star :size="14" fill="#EFBF04" color="#EFBF04"/> <span class="font-bold text-xs">{{ review.rating }}/10</span>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <button type="button" class="px-4 py-2 text-sm font-bold text-base-content/60 hover:text-base-content transition-colors font-['Manrope'] mr-3 hover:cursor-pointer" @click="showModal = false">
                Cancelar
            </button>
            <button
                type="button"
                class="px-4 py-2 text-sm font-bold text-white rounded-lg transition-colors font-['Manrope'] hover:cursor-pointer"
                :class="action === 'approve' ? 'bg-primary hover:bg-primary-focus' : 'bg-red-600 hover:bg-red-700'"
                :disabled="form.processing"
                @click="executeAction"
            >
                {{ action === 'approve' ? 'Confirmar Aprovação' : 'Confirmar Rejeição' }}
            </button>
        </template>
    </ConfirmationModal>
</template>

<style scoped>
.pending-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 18px;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    border-radius: 8px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.pending-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pending-button--approve {
    color: #ffffff;
    background: var(--color-primary);
    border-color: var(--color-primary);
}

.pending-button--approve:hover:not(:disabled) {
    background: var(--color-primary-dark);
    box-shadow: 0 2px 8px rgba(0, 108, 73, 0.3);
}

.pending-button--reject {
    color: #ba1a1a;
    background: transparent;
    border-color: #ba1a1a;
}

.pending-button--reject:hover:not(:disabled) {
    background: #ba1a1a;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(186, 26, 26, 0.2);
}
</style>
