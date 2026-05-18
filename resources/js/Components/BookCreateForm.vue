<script setup>
import { useForm } from '@inertiajs/vue3';
import { toast } from "vue-sonner";

const props = defineProps({
    publishers: Array,
    authors: Array,
});

const emit = defineEmits(['success']);

const form = useForm({
    title: '',
    bibliography: '',
    isbn: '',
    price: 0,
    total_stock: 0,
    publisher_id: '',
    author_ids: [],
    image_path: null,
});

const handleFileChange = (e) => {
    form.image_path = e.target.files[0];
};

const submit = () => {
    form.post(route('livros.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success')
            toast.success(`O livro "${form.title}" foi adicionado com sucesso!`);
        },
        onError: () => {
            toast.error("Erro ao tentar adicionar livro.");
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="max-w-2xl mx-auto p-8 bg-white rounded-2xl shadow-sm border border-gray-100 mt-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold font-['Manrope'] text-[#191c1e]">Adicionar Novo Livro</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label font-semibold text-[#3c4a42]">Título</label>
                <input v-model="form.title" type="text" class="input input-bordered w-full" placeholder="Ex: O Principezinho" />
                <span v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</span>
            </div>

            <div class="form-control">
                <label class="label font-semibold text-[#3c4a42]">ISBN</label>
                <input v-model="form.isbn" type="text" class="input input-bordered w-full" placeholder="Ex: 978-989-X-XX-XXXXXX-X" />
                <span v-if="form.errors.isbn" class="text-red-500 text-xs mt-1">{{ form.errors.isbn }}</span>
            </div>
        </div>

        <div class="form-control mt-2">
            <label class="label font-semibold text-[#3c4a42]">Bibliografia</label>
            <textarea v-model="form.bibliography" class="textarea textarea-bordered h-24 w-full" placeholder="Sinopse ou descrição do livro..."></textarea>
            <span v-if="form.errors.bibliography" class="text-red-500 text-xs mt-1">{{ form.errors.bibliography }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div class="form-control">
                <label class="label font-semibold text-[#3c4a42]">Preço (€)</label>
                <input v-model="form.price" type="number" step="0.01" class="input input-bordered w-full" placeholder="0.00" />
                <span v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</span>
            </div>

            <div class="form-control">
                <label class="label font-semibold text-[#3c4a42]">Stock total</label>
                <input v-model="form.total_stock" type="number" step="1" class="input input-bordered w-full" placeholder="0" />
                <span v-if="form.errors.total_stock" class="text-red-500 text-xs mt-1">{{ form.errors.total_stock }}</span>
            </div>

            <div class="form-control">
                <label class="label font-semibold text-[#3c4a42]">Editora</label>
                <select v-model="form.publisher_id" class="select select-bordered w-full">
                    <option disabled value="">Selecione uma editora...</option>
                    <option v-for="pub in publishers" :key="pub.id" :value="pub.id">
                        {{ pub.name }}
                    </option>
                </select>
                <span v-if="form.errors.publisher_id" class="text-red-500 text-xs mt-1">{{ form.errors.publisher_id }}</span>
            </div>
        </div>

        <div class="form-control mt-2 mb-4">
            <label class="label font-semibold text-[#3c4a42]">Autor(es)</label>
            <select multiple v-model="form.author_ids" class="select select-bordered min-h-[120px] w-full">
                <option v-for="aut in authors" :key="aut.id" :value="aut.id">
                    {{ aut.name }}
                </option>
            </select>
            <span v-if="form.errors.author_ids" class="text-red-500 text-xs mt-1">{{ form.errors.author_ids }}</span>
            <label class="label">
                <span class="label-text-alt text-[#6c7a71]">Ctrl/Cmd + Clique para selecionar vários</span>
            </label>
        </div>

        <div class="form-control">
            <label class="label font-semibold text-[#3c4a42]">Capa do Livro</label>
            <input
                type="file"
                @input="handleFileChange"
                class="file-input file-input-bordered w-full"
                accept="image/*"
            />
            <span v-if="form.errors.image_path" class="text-red-500 text-xs mt-1">{{ form.errors.image_path }}</span>
        </div>

        <div class="flex justify-end mt-8 pt-6 border-t border-gray-100">
            <button type="submit" class="btn-add" :disabled="form.processing">
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                Adicionar Livro
            </button>
        </div>
    </form>
</template>
