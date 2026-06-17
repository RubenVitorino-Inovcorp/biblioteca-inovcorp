<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import InputSearch from "@/Components/InputSearch.vue";

const props = defineProps({
    filters: Object,
    externalBooks: { type: [Object, Array], default: null }
});

defineEmits(['select-book']);

const booksList = computed(() => {
    if (!props.externalBooks) return [];
    if (Array.isArray(props.externalBooks)) return props.externalBooks;
    if (props.externalBooks.data && Array.isArray(props.externalBooks.data)) {
        return props.externalBooks.data;
    }
    return [];
});
</script>

<template>
    <div class="bg-base-100 border-base-100 collapse collapse-plus border rounded-2xl">
        <input type="checkbox" class="peer" :checked="booksList.length > 0" />
        <div class="collapse-title bg-primary text-primary-content peer-checked:bg-base-100 peer-checked:text-base-content">
            <span class="flex items-center gap-4 font-bold">
                <svg role="img" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Google</title><path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/></svg>
                Adicionar livro através de Google Books
            </span>
        </div>
        <div class="collapse-content bg-base-100 text-base-content peer-checked:bg-base-100 peer-checked:text-base-content space-y-4">
            <div class="pt-4">
                <InputSearch :filters="filters" route="livros.create" placeholder="Pesquisar por ISBN, Título ou Autor..." />
            </div>

            <div v-if="booksList.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4 justify-items-center bg-base-200 rounded-box max-h-[22rem] overflow-y-auto">
                <div v-for="book in booksList" :key="book.google_id" class="card card-side w-full max-w-[22rem] bg-base-100 shadow-sm border border-base-300 p-3 gap-3 transition-colors hover:border-primary">
                    <figure class="w-20 h-28 flex-shrink-0 bg-base-300 rounded-lg overflow-hidden border border-base-300">
                        <img :src="book.capa || '/storage/imagens/default.webp'" alt="Capa" class="w-full h-full object-cover" />
                    </figure>
                    <div class="flex flex-col justify-between overflow-hidden w-full">
                        <div>
                            <h4 class="font-bold text-[14px] leading-tight truncate text-base-content" style="font-family: 'Manrope', sans-serif;">{{ book.titulo }}</h4>
                            <p class="text-xs font-semibold text-primary truncate mt-1">{{ book.autores }}</p>
                            <p v-if="book.isbn" class="text-[11px] text-base-content/60 mt-1">ISBN: {{ book.isbn }}</p>
                        </div>
                        <button 
                            type="button" 
                            @click="$emit('select-book', book)" 
                            class="btn-add w-full mt-2 justify-center py-1.5 text-[11px]"
                        >
                            Importar Dados
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paginação dos Livros Externos -->
            <div v-if="externalBooks?.links && externalBooks.links.length > 3" class="flex justify-center mt-4">
                <div class="flex items-center space-x-1">
                    <Link
                        v-for="(link, index) in externalBooks.links"
                        :key="index"
                        :href="link.url ?? ''"
                        class="px-3 py-1 text-xs border rounded-md transition-all duration-200"
                        :class="{
                            'bg-primary text-white border-primary font-semibold shadow-sm': link.active,
                            'bg-base-100 hover:bg-primary/5 hover:text-primary hover:border-primary border-base-300 text-base-content/70': !link.active && link.url,
                            'opacity-40 cursor-not-allowed text-base-content/30 border-base-300': !link.url
                        }"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </div>    
</template>