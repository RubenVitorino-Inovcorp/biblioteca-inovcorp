<script setup>
import { useForm } from '@inertiajs/vue3';
import { toast } from "vue-sonner";
import BookDeleteForm from "@/Components/BookDeleteForm.vue";

const props = defineProps({
    book: { type: Object, required: true },
    publishers: Array,
    authors: Array,
});

const emit = defineEmits(['success']);

const getAuthorIds = () => {
    if (!props.book?.authors) return [];
    return props.book.authors.map(author => author.id);
};

const form = useForm({
    _method: "put",
    title: props.book.title ?? '',
    bibliography: props.book.bibliography ?? '',
    isbn: props.book.isbn ?? '',
    price: props.book.price ?? 0,
    publisher_id: props.book.publisher_id ?? '',
    author_ids: getAuthorIds(),
    image_path: null,
});

const handleFileChange = (e) => {
    form.image_path = e.target.files[0];
};

const submit = () => {
    form.post(route('livros.update', props.book.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success')
            toast.success(`O livro "${props.book.title}" foi atualizado com sucesso!.`);
        },
        onError: () => {
            toast.error("Erro ao tentar atualizar livro.");
        }
    });
};
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">

        <div class="col-span-1">
            <img :src="book.image_path" :alt="book.title" class="rounded-xl shadow-lg w-full object-cover" />
        </div>

        <div class="col-span-2 space-y-4">
            <h1 class="text-3xl font-bold text-base-content">{{ book.title }}</h1>

            <div>
                <label class="label font-bold">Autores</label>
                <div class="flex flex-wrap gap-1">
                    <div class="badge badge-primary" v-for="author in book.authors" :key="author.id">
                        {{ author.name }}
                    </div>
                </div>
            </div>

            <div>
                <label class="label font-bold">Editora</label>
                <div class="flex flex-wrap gap-1">
                    <div class="badge badge-secondary gap-x-6" v-if="book.publisher">
                        {{ book.publisher.name }}
                    </div>
                </div>
            </div>

            <div>
                <label class="label font-bold pb-0">Bibliografia</label>
                <p class="text-base-content opacity-80">{{ book.bibliography }}</p>
            </div>

            <div class="divider"></div>

            <div class="flex justify-between items-center">
                <div class="text-sm opacity-60">
                    <p>ISBN: {{ book.isbn }}</p>
                </div>
                <div class="text-2xl font-bold text-primary">
                    {{ book.price }}€
                </div>

            </div>
        </div>
    </div>

    <form @submit.prevent="submit" class="max-w-md mx-auto p-6 bg-base-100">
        <div class="form-control">
            <label class="label font-semibold">Título</label>
            <input v-model="form.title" type="text" class="input input-bordered" />
            <span v-if="form.errors.title" class="text-error text-xs">{{ form.errors.title }}</span>
        </div>

        <div class="form-control mt-2">
            <label class="label font-semibold">Bibliografia</label>
            <textarea v-model="form.bibliography" class="textarea textarea-bordered h-24"></textarea>
            <span v-if="form.errors.bibliography" class="text-error text-xs">{{ form.errors.bibliography }}</span>
        </div>

        <div class="form-control mt-2">
            <label class="label font-semibold">ISBN</label>
            <input v-model="form.isbn" type="text" class="input input-bordered" />
            <span v-if="form.errors.isbn" class="text-error text-xs">{{ form.errors.isbn }}</span>
        </div>

        <div class="form-control mt-2">
            <label class="label font-semibold">Preço (€)</label>
            <input v-model="form.price" type="number" step="0.01" class="input input-bordered" />
            <span v-if="form.errors.price" class="text-error text-xs">{{ form.errors.price }}</span>
        </div>

        <div class="form-control mt-2">
            <label class="label font-semibold">Editora</label>
            <select v-model="form.publisher_id" class="select select-bordered">
                <option disabled value="">Selecione uma editora...</option>
                <option v-for="pub in publishers" :key="pub.id" :value="pub.id">
                    {{ pub.name }}
                </option>
            </select>
            <span v-if="form.errors.publisher_id" class="text-error text-xs">{{ form.errors.publisher_id }}</span>
        </div>

        <div class="form-control mt-2 mb-4">
            <label class="label font-semibold">Autor(es)</label>
            <select multiple v-model="form.author_ids" class="select select-bordered min-h-24">
                <option v-for="aut in authors" :key="aut.id" :value="aut.id">
                    {{ aut.name }}
                </option>
            </select>
            <span v-if="form.errors.author_ids" class="text-error text-xs">{{ form.errors.author_ids }}</span>
            <label class="label">
                <span class="label-text-alt text-gray-500">Ctrl/Cmd + Clique para selecionar vários</span>
            </label>
        </div>

        <div class="form-control">
            <label class="label font-semibold">Capa do Livro</label>
            <input
                type="file"
                @input="handleFileChange"
                class="file-input file-input-bordered file-input-primary w-full"
                accept="image/*"
            />
            <span v-if="form.errors.image_path" class="text-error text-xs">{{ form.errors.image_path }}</span>
        </div>

        <div class="flex justify-end gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                Guardar alterações
            </button>
            <BookDeleteForm :book="book" />
        </div>
    </form>
</template>
