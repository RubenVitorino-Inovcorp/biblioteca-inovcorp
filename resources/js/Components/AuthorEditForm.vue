<script setup>
import { useForm } from '@inertiajs/vue3';
import { toast } from "vue-sonner";
import AuthorDeleteForm from "@/Components/AuthorDeleteForm.vue";

const props = defineProps({
    author: { type: Object, required: true },
});

const emit = defineEmits(['success']);

const form = useForm({
    _method: "put",
    name: props.author.name ?? '',
    photo_path: null,
});

const handleFileChange = (e) => {
    form.photo_path = e.target.files[0];
};

const submit = () => {
    form.post(route('autores.update', props.author.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success')
            toast.success(`O autor "${props.author.name}" foi atualizado com sucesso!.`);
        },
        onError: () => {
            toast.error("Erro ao tentar atualizar autor.");
        }
    });
};
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">

        <div class="col-span-1">
            <img :src="author.photo_path" :alt="author.name" class="rounded-xl shadow-lg w-full object-cover" />
        </div>

        <div class="col-span-2 space-y-4">
            <h1 class="text-3xl font-bold text-base-content">{{ author.name }}</h1>
        </div>
    </div>

    <form @submit.prevent="submit" class="max-w-md mx-auto p-6 bg-base-100">
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
            <span v-if="form.errors.photo_path" class="text-error text-xs">{{ form.errors.image_path }}</span>
        </div>

        <div class="flex justify-end gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                Guardar alterações
            </button>
            <AuthorDeleteForm :author="author" />
        </div>
    </form>
</template>
