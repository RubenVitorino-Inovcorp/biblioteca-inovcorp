<script setup>
import { useForm } from '@inertiajs/vue3';
import { toast } from "vue-sonner";

const props = defineProps({
    author: Array,
});

const emit = defineEmits(['success']);

const form = useForm({
    name: '',
    photo_path: null,
});

const handleFileChange = (e) => {
    form.photo_path = e.target.files[0];
};

const submit = () => {
    form.post(route('autores.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success')
            toast.success(`O autor "${form.name}" foi adicionado com sucesso!`);
        },
        onError: () => {
            toast.error("Erro ao tentar adicionar autor.");
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="max-w-xl mx-auto p-8 bg-base-100 rounded-2xl shadow-sm border border-gray-100 mt-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold font-['Manrope'] text-base-content">Adicionar Novo Autor</h2>
        </div>

        <div class="form-control">
            <label class="label font-semibold text-base-content/70">Nome</label>
            <input v-model="form.name" type="text" class="input input-bordered w-full" placeholder="Ex: José Saramago" />
            <span v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</span>
        </div>

        <div class="form-control mt-4">
            <label class="label font-semibold text-base-content/70">Foto do autor</label>
            <input
                type="file"
                @input="handleFileChange"
                class="file-input file-input-bordered w-full"
                accept="image/*"
            />
            <span v-if="form.errors.photo_path" class="text-red-500 text-xs mt-1">{{ form.errors.photo_path }}</span>
        </div>

        <div class="flex justify-end mt-8 pt-6 border-t border-gray-100">
            <button type="submit" class="btn-add" :disabled="form.processing">
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                Adicionar Autor
            </button>
        </div>
    </form>
</template>
