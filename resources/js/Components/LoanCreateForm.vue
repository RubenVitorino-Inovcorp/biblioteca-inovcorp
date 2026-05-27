<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { LibraryBig, CalendarClock, Info } from "@lucide/vue";

const props = defineProps({
    book: { type: Object, required: true },
});

const form = useForm({
    book_id: props.book.id,
});
const showModal = ref(false);

const formattedReturnDate = computed(() => {
    const date = new Date();
    date.setDate(date.getDate() + 5);
    return date.toLocaleDateString('pt-PT', { day: '2-digit', month: '2-digit', year: 'numeric' });
});

const createLoanAction = () => {
    form.post(route("catalog.requisicoes.store"), {
        onSuccess: () => {
            showModal.value = false;
            toast.success(`O livro "${props.book.title}" foi requisitado.`);
        },
        onError: (errors) => {
            showModal.value = false;
            const firstError = Object.values(errors)[0];
            toast.error(firstError || "Erro ao tentar requisitar o livro.");
        }
    });
};

</script>

<template>
    <span @click.prevent="showModal = true" class="inline-block cursor-pointer">
        <slot>
        </slot>
    </span>

    <ConfirmationModal :show="showModal" @close="showModal = false">
        <template #title>
            Requisitar Livro
        </template>
        <template #content>
            <p class="text-gray-600 mb-4 ">Verifique os detalhes da sua requisição antes de prosseguir.</p>
            
            <div class="bg-base-200/50 border border-base-300 rounded-xl p-5 flex flex-col  gap-4 text-sm">
                <!-- Detalhes do Livro -->
                <div class="flex items-center gap-4">
                    <div class="shrink-0 rounded-md overflow-hidden shadow-sm bg-base-100">
                        <img v-if="props.book.image_path" :src="props.book.image_path" :alt="props.book.title" class="w-12 h-16 object-cover" />
                        <div v-else class="w-12 h-16 bg-gray-100 flex items-center justify-center">
                            <LibraryBig class="text-gray-400" :size="20" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-base-content text-base">{{ props.book.title }}</h4>
                        <p class="text-gray-500 mt-1" v-if="props.book.isbn">ISBN: {{ props.book.isbn }}</p>
                    </div>
                </div>

                <div class="h-px bg-base-300 w-full my-1"></div>

                <!-- Detalhes da Requisição -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 flex items-center gap-2">
                            <CalendarClock :size="16" />
                            Data Prevista de Devolução
                        </span>
                        <span class="font-bold text-primary">{{ formattedReturnDate }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 flex items-center gap-2">
                            <LibraryBig :size="16" />
                            Duração Máxima
                        </span>
                        <span class="font-medium text-base-content">5 dias</span>
                    </div>
                </div>
            </div>
            
            <p class="mt-4  text-xs text-gray-500 flex gap-1.5 text-center">
                <Info :size="16" class="text-warning shrink-0"/>                
                <span>Pedimos que devolva o livro até à data prevista de devolução.</span>
            </p>
        </template>

        <template #footer>
            <button type="button" class="px-4 py-2 text-sm font-bold text-base-content/60 hover:text-base-content transition-colors font-['Manrope'] mr-3" @click="showModal = false">
                Cancelar
            </button>
            <button
                type="button"
                class="px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-green-900 transition-colors font-['Manrope']"
                :disabled="form.processing"
                @click="createLoanAction"
            >
                Confirmar Requisição
            </button>
        </template>
    </ConfirmationModal>
</template>
