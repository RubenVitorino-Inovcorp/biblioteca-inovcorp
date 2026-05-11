<script setup>
import {Link, useForm} from '@inertiajs/vue3';
import { toast } from "vue-sonner";
import AuthorDeleteForm from "@/Components/AuthorDeleteForm.vue";
import {Pencil} from "@lucide/vue";

const props = defineProps({
    author: { type: Object, required: true },
    isModal: { type: Boolean, default: false }
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
    <div v-if="!isModal" class="show-card p-6 md:p-8 max-w-5xl mx-auto my-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

            <div class="md:col-span-3">
                <img :src="author.photo_path" :alt="author.name" class="show-image shadow-lg" />
            </div>

            <div class="md:col-span-9 space-y-6">
                <div class="flex items-start justify-between">
                    <h1 class="show-title">{{ author.name }}</h1>
                </div>
            </div>
        </div>
    </div>

    <form @submit.prevent="submit" :class="isModal ? 'w-full' : 'max-w-xl mx-auto p-8 bg-white rounded-2xl shadow-sm border border-gray-100'">
        <div v-if="!isModal" class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold font-['Manrope'] text-[#191c1e]">Atualizar Dados do Autor</h2>
        </div>

        <div class="form-control">
            <label class="label font-semibold text-[#3c4a42]">Nome</label>
            <input v-model="form.name" type="text" class="input input-bordered w-full" />
            <span v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</span>
        </div>

        <div class="form-control mt-4">
            <label class="label font-semibold text-[#3c4a42]">Foto do autor</label>
            <input
                type="file"
                @input="handleFileChange"
                class="file-input file-input-bordered w-full"
                accept="image/*"
            />
            <span v-if="form.errors.photo_path" class="text-red-500 text-xs mt-1">{{ form.errors.image_path }}</span>
        </div>

        <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-100">
            <AuthorDeleteForm :author="author" />
            <button type="submit" class="btn-add" :disabled="form.processing">
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                Guardar
            </button>
        </div>
    </form>
</template>
