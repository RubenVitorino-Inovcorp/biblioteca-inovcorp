<script setup>
import { ref } from 'vue';
import PublisherEditForm from "@/Components/PublisherEditForm.vue";

const props = defineProps({
    publisher: Object,
});

const modalRef = ref(null);

const selectedPublisher = ref(null);

const openModal = (publisher) => {
    selectedPublisher.value = { ...publisher };

    modalRef.value.showModal();
};

const closeModal = () => {
    modalRef.value.close();
};

</script>

<template>
    <button @click="openModal(publisher)" class="btn btn-sm btn-outline btn-primary">
        Editar
    </button>

    <dialog ref="modalRef" class="modal">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Editar Editora - {{ selectedPublisher?.name }}</h3>

            <PublisherEditForm
                v-if="selectedPublisher"
                :publisher="selectedPublisher"
                @success="closeModal"
            />

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost">Cancelar</button>
                </form>
            </div>
        </div>

        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
