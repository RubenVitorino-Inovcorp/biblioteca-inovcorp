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
    <button @click="openModal(author)" class="btn-table-edit">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
        Editar
    </button>

    <dialog ref="modalRef" class="modal">
        <div class="modal-box max-w-md bg-white rounded-2xl shadow-2xl p-6 md:p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-[#191c1e] font-['Manrope']">Editar Autor</h3>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4">✕</button>
                </form>
            </div>

            <AuthorEditForm
                v-if="selectedAuthor"
                :author="selectedAuthor"
                :isModal="true"
                @success="closeModal"
            />

        </div>

        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
