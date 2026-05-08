<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

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
    <button class="btn btn-sm btn-outline btn-error" @click="showModal = true">
        Eliminar
    </button>

    <ConfirmationModal :show="showModal" @close="showModal = false">
        <template #title>Eliminar editora</template>
        <template #content>
            Tem a certeza que deseja remover <strong>{{ props.publisher.name }}</strong>?
        </template>

        <template #footer>
            <button class="btn btn-ghost mx-4" @click="showModal = false">
                Cancelar
            </button>
            <button
                class="btn btn-error"
                :disabled="form.processing"
                @click="deletePublisherAction"
            >
                Eliminar
            </button>
        </template>
    </ConfirmationModal>
</template>
