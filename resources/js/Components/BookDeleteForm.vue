<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

const props = defineProps({
    book: { type: Object, required: true },
});

const form = useForm({});
const showModal = ref(false);

const deleteBookAction = () => {
    form.delete(route("livros.destroy", props.book.id), {
        onSuccess: () => {
            showModal.value = false;
            toast.error(`O livro "${props.book.title}" foi eliminado.`);
        },
        onError: () => {
            toast.warning("Erro ao tentar eliminar o livro.");
        }
    });
};

</script>

<template>
    <button class="btn btn-sm btn-outline btn-error" @click="showModal = true">
        Eliminar
    </button>

    <ConfirmationModal :show="showModal" @close="showModal = false">
        <template #title>Eliminar Livro</template>
        <template #content>
            Tem a certeza que deseja remover <strong>{{ props.book.title }}</strong>?
        </template>

        <template #footer>
            <button class="btn btn-ghost mx-4" @click="showModal = false">
                Cancelar
            </button>
            <button
                class="btn btn-error"
                :disabled="form.processing"
                @click="deleteBookAction"
            >
                Eliminar
            </button>
        </template>
    </ConfirmationModal>
</template>
