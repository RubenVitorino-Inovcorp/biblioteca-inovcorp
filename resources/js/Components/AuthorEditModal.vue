<script setup>
import { ref } from 'vue';
import AuthorEditForm from "@/Components/AuthorEditForm.vue";

const props = defineProps({
    author: Object,
});

const modalRef = ref(null);

const selectedAuthor = ref(null);

const openModal = (author) => {
    selectedAuthor.value = { ...author };

    modalRef.value.showModal();
};

const closeModal = () => {
    modalRef.value.close();
};

</script>

<template>
    <button @click="openModal(author)" class="btn btn-sm btn-outline btn-primary">
        Editar
    </button>

    <dialog ref="modalRef" class="modal">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Editar Autor - {{ selectedAuthor?.name }}</h3>

            <AuthorEditForm
                v-if="selectedAuthor"
                :author="selectedAuthor"
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
