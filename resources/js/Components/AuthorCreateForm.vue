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
    <form @submit.prevent="submit" class="max-w-md mx-auto p-6 bg-base-100 space-y-2">
        <div class="form-control">
            <label class="label font-semibold">Nome</label>
            <input v-model="form.name" type="text" class="input input-bordered" />
            <span v-if="form.errors.name" class="text-error text-xs">{{ form.errors.name }}</span>
        </div>

        <div class="form-control">
            <label class="label font-semibold">Foto do autor</label>
            <input
                type="file"
                @input="handleFileChange"
                class="file-input file-input-bordered file-input-primary w-full"
                accept="image/*"
            />
            <span v-if="form.errors.photo_path" class="text-error text-xs">{{ form.errors.photo_path }}</span>
        </div>

        <div class="flex justify-end gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                Adicionar autor
            </button>
        </div>
    </form>
</template>
