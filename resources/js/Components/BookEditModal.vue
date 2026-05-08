<script setup>
import { ref } from 'vue';
import BookEditForm from '@/Components/BookEditForm.vue';

const props = defineProps({
    book: Object,
    publishers: Array,
    authors: Array,
});

const modalRef = ref(null);

const selectedBook = ref(null);

const openModal = (book) => {
    selectedBook.value = { ...book };

    modalRef.value.showModal();
};

const closeModal = () => {
    modalRef.value.close();
};

</script>

<template>
    <button @click="openModal(book)" class="btn btn-sm btn-outline btn-primary">
        Editar
    </button>

    <dialog ref="modalRef" class="modal">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Editar Livro - {{ selectedBook?.title }}</h3>

            <BookEditForm
                v-if="selectedBook"
                :book="selectedBook"
                :publishers="publishers"
                :authors="authors"
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
