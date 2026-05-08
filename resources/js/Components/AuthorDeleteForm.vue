<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

const props = defineProps({
    author: { type: Object, required: true },
});

const form = useForm({});
const showModal = ref(false);

const deleteAuthorAction = () => {
    form.delete(route("autores.destroy", props.author.id), {
        onSuccess: () => {
            showModal.value = false;
            toast.error(`O autor "${props.author.name}" foi eliminado.`);
        },
        onError: () => {
            toast.warning("Erro ao tentar eliminar o autor.");
        }
    });
};

</script>

<template>
    <button class="btn btn-sm btn-outline btn-error" @click="showModal = true">
        Eliminar
    </button>

    <ConfirmationModal :show="showModal" @close="showModal = false">
        <template #title>Eliminar autor</template>
        <template #content>
            Tem a certeza que deseja remover <strong>{{ props.author.name }}</strong>?
        </template>

        <template #footer>
            <button class="btn btn-ghost mx-4" @click="showModal = false">
                Cancelar
            </button>
            <button
                class="btn btn-error"
                :disabled="form.processing"
                @click="deleteAuthorAction"
            >
                Eliminar
            </button>
        </template>
    </ConfirmationModal>
</template>
