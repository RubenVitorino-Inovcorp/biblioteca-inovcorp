<script setup>
import {Link, useForm} from '@inertiajs/vue3';
import { toast } from "vue-sonner";
import BookDeleteForm from "@/Components/BookDeleteForm.vue";
import {Pencil} from "@lucide/vue";
import FormInputSearch from '@/Components/FormInputSearch.vue';

const props = defineProps({
    book: { type: Object, required: true },
    publishers: Array,
    authors: Array,
    tags: Array,
    isModal: { type: Boolean, default: false }
});

const emit = defineEmits(['success']);

const form = useForm({
    _method: "put",
    title: props.book.title ?? '',
    bibliography: props.book.bibliography ?? '',
    isbn: props.book.isbn ?? '',
    price: props.book.price ?? 0,
    total_stock: props.book.total_stock ?? 0,
    publisher: props.book.publisher ?? null,
    authors: props.book.authors ?? [],
    tags: props.book.tags ?? [],
    image_path: null,
});

const handleFileChange = (e) => {
    form.image_path = e.target.files[0];
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        publisher_id: data.publisher?.id || '',
        author_ids: data.authors.map(a => a.id),
        tag_ids: data.tags.map(t => t.id),
    })).post(route('livros.update', props.book.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success')
            toast.success(`O livro "${props.book.title}" foi atualizado com sucesso!.`);
        },
        onError: (errors) => {
            toast.error(errors.total_stock || "Erro ao atualizar o livro.");
        }
    });
};
</script>

<template>
    <div v-if="!isModal" class="show-card p-6 md:p-8 max-w-5xl mx-auto my-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

            <div class="md:col-span-4">
                <img :src="book.image_url" :alt="book.title" class="show-image shadow-lg" />
            </div>

            <div class="md:col-span-8 space-y-6">
                <div class="flex items-start justify-between">
                    <Link :href="route('livros.edit', book.id)" class="show-title-link">
                        <h1 class="show-title">{{ book.title }}</h1>
                        <Pencil />
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="show-label">Autores</div>
                        <div class="flex flex-wrap gap-2">
                            <div class="badge-author" v-for="author in book.authors" :key="author.id">
                                <Link :href="route('autores.show', author.id)">
                                    {{ author.name }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="show-label">Editora</div>
                        <div class="flex flex-wrap gap-2">
                            <div class="badge-publisher" v-if="book.publisher">
                                <Link :href="route('editoras.show', book.publisher?.id)">
                                    {{ book.publisher?.name }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="show-label">Bibliografia</div>
                    <p class="show-text">{{ book.bibliography }}</p>
                </div>

                <div class="show-divider"></div>

                <div class="flex justify-between items-center">
                    <div class="show-isbn">
                        <p>Disponibilidade:  {{ book.available_stock }}/{{ book.total_stock }}</p>
                        <p>ISBN: {{ book.isbn }}</p>
                    </div>
                    <div class="show-price">
                        {{ book.price }}€
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <form @submit.prevent="submit" :class="isModal ? 'w-full' : 'max-w-2xl mx-auto p-8 bg-base-100 rounded-2xl shadow-sm border border-gray-100'">
        <div v-if="!isModal" class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl text-base-content">Atualizar Dados do Livro</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label text-base-content/70">Título</label>
                <input v-model="form.title" type="text" class="input input-bordered w-full" />
                <span v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</span>
            </div>

            <div class="form-control">
                <label class="label text-base-content/70">ISBN</label>
                <input v-model="form.isbn" type="text" class="input input-bordered w-full" />
                <span v-if="form.errors.isbn" class="text-red-500 text-xs mt-1">{{ form.errors.isbn }}</span>
            </div>
        </div>

        <div class="form-control mt-2">
            <label class="label text-base-content/70">Bibliografia</label>
            <textarea v-model="form.bibliography" class="textarea textarea-bordered h-24 w-full"></textarea>
            <span v-if="form.errors.bibliography" class="text-red-500 text-xs mt-1">{{ form.errors.bibliography }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div class="form-control">
                <label class="label text-base-content/70">Preço (€)</label>
                <input v-model="form.price" type="number" step="0.01" class="input input-bordered w-full" />
                <span v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</span>
            </div>

            <div class="form-control">
                <label class="label text-base-content/70">Total Stock</label>
                <input v-model="form.total_stock" type="number" class="input input-bordered w-full" />
                <span v-if="form.errors.total_stock" class="text-red-500 text-xs mt-1">{{ form.errors.total_stock }}</span>
            </div>
        </div>

        <div class="form-control mt-2 mb-4 space-y-4">
            <FormInputSearch
                 v-model="form.publisher"
                 label="Editora"
                 placeholder="Procurar editora..."
                 :items="publishers"
                 :multiple="false"
            />
            <span v-if="form.errors.publisher_id" class="text-red-500 text-xs mt-1">{{ form.errors.publisher_id }}</span>

            <FormInputSearch
                 v-model="form.authors"
                 label="Autor(es)"
                 placeholder="Procurar autor..."
                 :items="authors"
                 :multiple="true"
             />
            <span v-if="form.errors.author_ids" class="text-red-500 text-xs mt-1">{{ form.errors.author_ids }}</span>
        </div>

        <div class="form-control mt-2 mb-4">
             <FormInputSearch
                 v-model="form.tags"
                 label="Tags"
                 placeholder="Procurar ou criar tag..."
                 :items="tags"
                 :multiple="true"
             />
            <span v-if="form.errors.tag_ids" class="text-red-500 text-xs mt-1">{{ form.errors.tag_ids }}</span>
        </div>

        <div class="form-control">
            <label class="label text-base-content/70">Capa do Livro</label>
            <input
                type="file"
                @input="handleFileChange"
                class="file-input file-input-bordered w-full"
                accept="image/*"
            />
            <span v-if="form.errors.image_path" class="text-red-500 text-xs mt-1">{{ form.errors.image_path }}</span>
        </div>

        <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-100">
            <BookDeleteForm :book="book" />
            <button type="submit" class="btn-add" :disabled="form.processing">
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                Guardar
            </button>
        </div>
    </form>
</template>
