<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { TriangleAlert } from "@lucide/vue";

const props = defineProps({
    publisher: { type: Object, required: true },
});

const form = useForm({});
const showModal = ref(false);

const deletePublisherAction = () => {
    form.delete(route("editoras.destroy", props.publisher.id), {
        onSuccess: () => {
            showModal.value = false;
            toast.error(`O editora "${props.publisher.name}" foi eliminado.`);
        },
        onError: () => {
            toast.warning("Erro ao tentar eliminar o editora.");
        }
    });
};

</script>

<template>
    <button type="button" class="btn-table-delete" @click.prevent="showModal = true">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
        Eliminar
    </button>

    <ConfirmationModal :show="showModal" @close="showModal = false">
        <template #icon>
            <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center sm:h-10 sm:w-10">
                <TriangleAlert class="w-6 h-6 text-red-600" />
            </div>
        </template>
        <template #title>
            Eliminar editora
        </template>
        <template #content>
            Tem a certeza que deseja remover <strong>{{ props.publisher.name }}</strong>?
        </template>

        <template #footer>
            <button type="button" class="px-4 py-2 text-sm font-bold text-[#6c7a71] hover:text-[#191c1e] transition-colors font-['Manrope'] mr-3" @click="showModal = false">
                Cancelar
            </button>
            <button
                type="button"
                class="px-4 py-2 text-sm font-bold text-white bg-[#ba1a1a] rounded-lg hover:bg-[#901414] transition-colors font-['Manrope']"
                :disabled="form.processing"
                @click="deletePublisherAction"
            >
                Eliminar
            </button>
        </template>
    </ConfirmationModal>
</template>
