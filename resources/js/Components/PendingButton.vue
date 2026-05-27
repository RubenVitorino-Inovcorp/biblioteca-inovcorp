<script setup>
    import { Check, X, LibraryBig } from "@lucide/vue";
    import { useForm } from '@inertiajs/vue3';
    import { ref } from "vue";
    import { toast } from "vue-sonner";
    import ConfirmationModal from "./ConfirmationModal.vue";

    const approveForm = useForm({});
    const rejectForm = useForm({});
    const showRejectModal = ref(false);

    const props = defineProps({
        loan: { type: Object, required: true },
        action: {
            type: String, required: true, 
            validator: (value) => ['approve', 'reject'].includes(value)
        },
    });

    const handleClick = () => {
        if (props.action === 'approve') {
            approveAction();
        } else {
            showRejectModal.value = true;
        }
    };

    const approveAction = () => {
        approveForm.post(route("requisicoes.approve", props.loan.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`Requisição ${props.loan.loan_number} aprovada.`);
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            toast.error(firstError || "Erro ao aprovar a requisição.");
        },
    });
};

    const rejectAction = () => {
        rejectForm.post(route("requisicoes.reject", props.loan.id), {
            preserveScroll: true,
            onSuccess: () => {
                showRejectModal.value = false;
                toast.success(`Requisição ${props.loan.loan_number} rejeitada.`);
            },
            onError: (errors) => {
                showRejectModal.value = false;
                const firstError = Object.values(errors)[0];
                toast.error(firstError || "Erro ao rejeitar a requisição.");
            },
        });
    };

</script>

<template>
    <button
        type="button"
        class="pending-button" :class="{ 'pending-button--approve': action === 'approve', 'pending-button--reject': action === 'reject' }"
        :disabled="approveForm.processing || rejectForm.processing"
        @click.prevent="handleClick"
        :title="action === 'approve' ? 'Aprovar requisição' : 'Rejeitar requisição'"
    >
        <Check v-if="action === 'approve'" :size="18" />
        <X v-else :size="18" />
        <span>{{ action === 'approve' ? 'Aprovar' : 'Rejeitar' }}</span>
    </button>    <ConfirmationModal v-if="action === 'reject'" :show="showRejectModal" @close="showRejectModal = false">
        <template #title>
            Rejeitar Requisição
        </template>
        <template #content>
            <p class="text-gray-600 mb-4">Tem a certeza que pretende rejeitar esta requisição?</p>

            <div class="bg-base-200/50 border border-base-300 rounded-xl p-5 flex items-center gap-4 text-sm">
                <div class="shrink-0 rounded-md overflow-hidden shadow-sm bg-base-100">
                    <img v-if="loan.book?.image_path" :src="loan.book.image_path" :alt="loan.book?.title" class="w-12 h-16 object-cover" />
                    <div v-else class="w-12 h-16 bg-gray-100 flex items-center justify-center">
                        <LibraryBig class="text-gray-400" :size="20" />
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-base-content text-base">{{ loan.book?.title }}</h4>
                    <p class="text-gray-500 mt-1">Pedido por <strong>{{ loan.user?.name }}</strong></p>
                    <p class="text-gray-400 text-xs mt-0.5">{{ loan.loan_number }}</p>
                </div>
            </div>

            <p class="mt-4 text-xs text-gray-500 flex gap-1.5">
                <span>O stock do livro será reposto automaticamente.</span>
            </p>
        </template>
        <template #footer>
            <button type="button" class="px-4 py-2 text-sm font-bold text-base-content/60 hover:text-base-content transition-colors font-['Manrope'] mr-3" @click="showRejectModal = false">
                Cancelar
            </button>
            <button
                type="button"
                class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors font-['Manrope']"
                :disabled="rejectForm.processing"
                @click="rejectAction"
            >
                Confirmar Rejeição
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