<script setup lang="ts">
    import Layout from '@/Layouts/AppLayout.vue'
    import { Link, router } from '@inertiajs/vue3'
    import { Plus } from '@lucide/vue';
    import { toast } from 'vue-sonner'
    import { Loan } from '@/types';

    const props = defineProps<{
        book: any;
        loans?: Loan[];
    }>();

    const quickAdd = () => {
        router.post(route('livros.store'), {
            title: props.book.title,
            isbn: props.book.isbn,
            bibliography: props.book.bibliography,
            price: 0,            
            total_stock: 1,      
            publisher_name: props.book.publisher_name || 'Desconhecida', 
            author_names: props.book.authors.map((a: any) => a.name),                          
            image_path: props.book.image_path
        }, {
            onSuccess: () => toast.success('Livro importado e guardado localmente!'),
            onError: () => toast.error('Erro ao importar o livro.')
        });
    };

</script>

<template>
    <Layout :title="book.title">


        <div class="show-card p-6 md:p-8 max-w-5xl w-full mx-auto my-auto space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

                <div class="md:col-span-4">
                    <img v-if="book.image_path !== '/storage/imagens/default.webp'" :src="book.image_path" :alt="book.title" class="show-image shadow-lg" />
                    <img v-else src="/storage/imagens/default.webp" alt="Capa" class="show-image shadow-lg" />
                </div>

                <div class="md:col-span-8 space-y-6">
                    <div class="flex items-start justify-between">
                        <h1 class="show-title">{{ book.title }}</h1>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="show-label">Autores</div>
                            <div class="flex flex-wrap gap-2">
                                <div class="badge-author" v-for="(author, index) in book.authors" :key="index">
                                    {{ author.name }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="show-label">Editora</div>
                            <div class="flex flex-wrap gap-2">
                                <div class="badge-publisher" v-if="book.publisher">
                                    {{ book.publisher?.name || 'Desconhecida' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="show-label">Bibliografia / Descrição</div>
                        <p class="show-text">{{ book.bibliography || 'Nenhuma descrição disponível.' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div>
                            <div class="show-label">Livro Externo (Google API)</div>
                            <p class="text-sm text-gray-500">Este livro ainda não se encontra no catálogo da biblioteca.</p>
                        </div>
                        <div class="flex justify-end mt-2">
                            <button @click="quickAdd" class="flex items-center gap-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-green-900 transition-colors font-['Manrope'] px-6 py-3">
                                <Plus :size="18" />
                                Adicionar ao Catálogo
                            </button>
                        </div>
                    </div>

                    <div class="show-divider"></div>

                    <div class="flex justify-between items-center">
                        <div class="show-isbn">
                            ISBN: {{ book.isbn || 'N/D' }}
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </Layout>
</template>
