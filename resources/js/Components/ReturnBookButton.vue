<script setup>
import { Undo2, LibraryBig } from "@lucide/vue";
import { useForm } from '@inertiajs/vue3';
import { ref } from "vue";
import { toast } from "vue-sonner";
import ConfirmationModal from "./ConfirmationModal.vue";

const showModal = ref(false);

const props = defineProps({
    loan: { type: Object, required: true },
    routeName: { type: String, default: 'catalog.requisicoes.devolver' },
    compact: { type: Boolean, default: false },
});

const form = useForm({});

const returnBook = () => {
    form.post(route(props.routeName, props.loan.id), {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            toast.success(`Livro "${props.loan.book?.title}" devolvido com sucesso.`);
        },
        onError: (errors) => {
            showModal.value = false;
            const firstError = Object.values(errors)[0];
            toast.error(firstError || "Erro ao devolver o livro.");
        },
    });
};
</script>

<template>
    <button
        type="button"
        class="return-button"
        :class="{ 'return-button--compact': compact }"
        @click="showModal = true"
        title="Devolver livro"
    >
        <Undo2 :size="compact ? 16 : 18" />
        <span>Devolver</span>
    </button>

    <ConfirmationModal :show="showModal" @close="showModal = false">
        <template #icon>
            <div class="return-modal-icon">
                <Undo2 :size="24" />
            </div>
        </template>
        <template #title>
            Devolver Livro
        </template>
        <template #content>
            <p class="text-gray-600 mb-4">Confirma a devolução deste livro?</p>

            <div class="bg-base-200/50 border border-base-300 rounded-xl p-5 flex items-center gap-4 text-sm">
                <div class="shrink-0 rounded-md overflow-hidden shadow-sm bg-base-100">
                    <img v-if="loan.book?.image_url" :src="loan.book.image_url" :alt="loan.book?.title" class="w-12 h-16 object-cover" />
                    <div v-else class="w-12 h-16 bg-gray-100 flex items-center justify-center">
                        <LibraryBig class="text-gray-400" :size="20" />
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-base-content text-base">{{ loan.book?.title }}</h4>
                    <p class="text-gray-500 mt-1">Requisitado por <strong>{{ loan.user?.name }}</strong></p>
                    <p class="text-gray-400 text-xs mt-0.5">{{ loan.loan_number }}</p>
                </div>
            </div>
        </template>
        <template #footer>
            <button type="button" class="return-modal-cancel" @click="showModal = false">
                Cancelar
            </button>
            <button
                type="button"
                class="return-modal-confirm"
                :disabled="form.processing"
                @click="returnBook"
            >
                <Undo2 :size="16" />
                Confirmar Devolução
            </button>
        </template>
    </ConfirmationModal>
</template>

<style scoped>

.return-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 18px;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    font-weight: 700;
    border-radius: 8px;
    border: 1px solid var(--color-primary);
    color: var(--color-primary);
    background: transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.return-button:hover {
    background: var(--color-primary);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 108, 73, 0.3);
}

.return-button--compact {
    padding: 6px 14px;
    font-size: 12px;
    border-radius: 6px;
    gap: 4px;
}

/* ─── Modal ─── */
.return-modal-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    color: var(--color-primary);
}

.return-modal-cancel {
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 700;
    color: var(--color-silk-muted);
    font-family: 'Manrope', sans-serif;
    margin-right: 12px;
    transition: color 0.15s ease;
    cursor: pointer;
    background: none;
    border: none;
}

.return-modal-cancel:hover {
    color: var(--color-silk-content);
}

.return-modal-confirm {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    background: var(--color-primary);
    border-radius: 8px;
    font-family: 'Manrope', sans-serif;
    transition: background 0.15s ease;
    cursor: pointer;
    border: none;
}

.return-modal-confirm:hover:not(:disabled) {
    background: var(--color-primary-dark);
}

.return-modal-confirm:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
