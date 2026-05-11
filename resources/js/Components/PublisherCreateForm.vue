<script setup>
import { useForm } from '@inertiajs/vue3';
import { toast } from "vue-sonner";

const props = defineProps({
    publisher: Array,
});

const emit = defineEmits(['success']);

const form = useForm({
    name: '',
    logo_path: null,
});

const handleFileChange = (e) => {
    form.logo_path = e.target.files[0];
};

const submit = () => {
    form.post(route('editoras.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success')
            toast.success(`O editora "${form.name}" foi adicionado com sucesso!`);
        },
        onError: () => {
            toast.error("Erro ao tentar adicionar editora.");
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="max-w-xl mx-auto p-8 bg-white rounded-2xl shadow-sm border border-gray-100 mt-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold font-['Manrope'] text-[#191c1e]">Adicionar Nova Editora</h2>
        </div>

        <div class="form-control">
            <label class="label font-semibold text-[#3c4a42]">Nome</label>
            <input v-model="form.name" type="text" class="input input-bordered w-full" placeholder="Ex: Porto Editora" />
            <span v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</span>
        </div>

        <div class="form-control mt-4">
            <label class="label font-semibold text-[#3c4a42]">Lógotipo da editora</label>
            <input
                type="file"
                @input="handleFileChange"
                class="file-input file-input-bordered w-full"
                accept="image/*"
            />
            <span v-if="form.errors.logo_path" class="text-red-500 text-xs mt-1">{{ form.errors.logo_path }}</span>
        </div>

        <div class="flex justify-end mt-8 pt-6 border-t border-gray-100">
            <button type="submit" class="btn-add" :disabled="form.processing">
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                Adicionar Editora
            </button>
        </div>
    </form>
</template>
