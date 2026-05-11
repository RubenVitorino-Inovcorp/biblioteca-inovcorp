<script setup>
  import Layout from '@/Layouts/AppLayout.vue'
  import {Head, Link} from '@inertiajs/vue3'
  import { Pencil } from '@lucide/vue';

  defineProps({
      book: Object,
  })

</script>

<template>
    <Layout>
        <Head :title="book.title" />

        <div class="show-card p-6 md:p-8 max-w-5xl mx-auto mt-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">

                <div class="md:col-span-4">
                    <img :src="book.image_path" :alt="book.title" class="show-image shadow-lg" />
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
                            ISBN: {{ book.isbn }}
                        </div>
                        <div class="show-price">
                            {{ book.price }}€
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
