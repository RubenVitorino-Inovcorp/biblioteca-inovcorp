<script setup>
import { useForm } from '@inertiajs/vue3';
import { toast } from "vue-sonner";
import { watch } from 'vue';
import { ref } from 'vue';
import FormInputSearch from '../FormInputSearch.vue';

const props = defineProps({
    publishers: Array,
    authors: Array,
    tags: Array,
    selectedBook: { type: Object, default: null },
});

const emit = defineEmits(['success']);

const form = useForm({
    title: '',
    bibliography: '',
    isbn: '',
    price: 0,
    total_stock: 0,
    publisher: null,
    authors: [],
    tags: [],
    image_path: null,
});

const imagePreview = ref(null);
const imageInput = ref(null);

const updateImagePreview = () => {
    const image = imageInput.value.files[0];

    if (! image) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };

    reader.readAsDataURL(image);
};

const handleFileChange = (e) => {
    form.image_path = e.target.files[0];
    updateImagePreview();
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        publisher_id: data.publisher ? data.publisher.id : null,
        author_ids: data.authors.map(a => a.id),
        tag_ids: data.tags.map(t => t.id),
    })).post(route('livros.store'), {
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

watch(() => props.selectedBook, (newBook) => {
    if (newBook) {
        form.title = newBook.title || newBook.titulo || '';
        form.isbn = newBook.isbn || '';
        form.bibliography = newBook.description || newBook.sinopse || '';
        
        if (!(form.image_path instanceof File)) {
            form.image_path = newBook.image_path || newBook.capa || null;
            if (form.image_path) {
                imagePreview.value = form.image_path;
            } else {
                imagePreview.value = null;
            }
        }

        if (newBook.publisher_id) {
             const pub = props.publishers.find(p => p.id === newBook.publisher_id);
             if (pub) form.publisher = pub;
        } else if (newBook.publisher_name) {
             form.publisher = { id: newBook.publisher_name, name: newBook.publisher_name };
        } else {
             form.publisher = null;
        }

        let authorsArr = Array.isArray(newBook.autores) ? newBook.autores : (newBook.autores ? newBook.autores.split(',').map(a => a.trim()).filter(Boolean) : []);
        form.authors = authorsArr.map(authorName => {
             const existing = props.authors.find(a => a.name.toLowerCase() === authorName.toLowerCase());
             return existing ? existing : { id: authorName, name: authorName };
        });

        toast.success(`Dados de "${newBook.titulo}" carregados no formulário.`);
    }
}, { deep: true });


</script>

<template>
    <form @submit.prevent="submit" class="max-w-2xl mx-auto space-y-10 p-8 bg-base-100 rounded-2xl shadow-sm border border-gray-100 mt-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold font-['Manrope'] text-base-content">Adicionar Novo Livro</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label font-semibold text-base-content/70">Título</label>
                <input v-model="form.title" type="text" class="input border focus:border-primary focus:border-2 p-4 focus:outline-none w-full" placeholder="Ex: O Principezinho" />
                <span v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</span>
            </div>

            <div class="form-control">
                <label class="label font-semibold text-base-content/70">ISBN</label>
                <input v-model="form.isbn" type="text" class="input border focus:border-primary focus:border-2 p-4 focus:outline-none w-full" placeholder="Ex: 978-989-X-XX-XXXXXX-X" />
                <span v-if="form.errors.isbn" class="text-red-500 text-xs mt-1">{{ form.errors.isbn }}</span>
            </div>
        </div>

        <div class="form-control mt-2">
            <label class="label font-semibold text-base-content/70">Bibliografia</label>
            <textarea v-model="form.bibliography" class="textarea border border focus:border-primary focus:border-2 p-4 focus:outline-none h-24 w-full" placeholder="Sinopse ou descrição do livro..."></textarea>
            <span v-if="form.errors.bibliography" class="text-red-500 text-xs mt-1">{{ form.errors.bibliography }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div class="form-control">
                <label class="label font-semibold text-base-content/70">Preço (€)</label>
                <input v-model="form.price" type="number" step="0.01" class="input border focus:border-primary focus:border-2 p-4 focus:outline-none w-full" placeholder="0.00" />
                <span v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</span>
            </div>

            <div class="form-control">
                <label class="label font-semibold text-base-content/70">Stock total</label>
                <input v-model="form.total_stock" type="number" step="1" class="input border focus:border-primary focus:border-2 p-4 focus:outline-none w-full" placeholder="0" />
                <span v-if="form.errors.total_stock" class="text-red-500 text-xs mt-1">{{ form.errors.total_stock }}</span>
            </div>
        </div>

        <div class="form-control">
            <FormInputSearch
                v-model="form.publisher"
                label="Editora"
                placeholder="Procurar editora..."
                :items="publishers"
                :multiple="false"
            />
            <span v-if="form.errors.publisher_id" class="text-red-500 text-xs mt-1">{{ form.errors.publisher_id }}</span>   
        </div>

        <div class="form-control mt-2 mb-4">
             <FormInputSearch
                 v-model="form.authors"
                 label="Autor(es)"
                 placeholder="Procurar autor..."
                 :items="authors"
                 :multiple="true"
             />
             <span v-if="form.errors.author_ids" class="text-red-500 text-xs mt-1">{{ form.errors.author_ids }}</span>
        </div>

        <div class="form-control mb-4">
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
            <div v-show="imagePreview" class="shrink-0">
                <span
                    class="block rounded-full size-20 bg-cover bg-no-repeat bg-center shadow-sm border border-gray-100"
                    :style="'background-image: url(\'' + imagePreview + '\');'"
                />
            </div>
            <label class="label font-semibold text-base-content/70">Capa do Livro</label>
            <input
                ref="imageInput"
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
