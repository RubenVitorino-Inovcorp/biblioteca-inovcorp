<script setup>
  import {Head, Link} from '@inertiajs/vue3'
  import AppLayout from "@/Layouts/AppLayout.vue";

  defineProps({
      books: Array,
  })
</script>

<template>
    <AppLayout title="Biblioteca - Livros">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Livros
                </h2>
                <!-- Botão para criar novo livro (usando DaisyUI) -->
                <Link :href="route('livros.create')" class="btn btn-primary btn-sm">
                    + Adicionar Livro
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="book in books" :key="book.id" class="card bg-base-100 shadow-xl border border-gray-200">
                            <figure class="px-4 pt-4">
                                <img :src="book.image_path" :alt="book.title" class="rounded-xl h-64 w-full object-cover" />
                            </figure>
                            <div class="card-body">
                                <h2 class="card-title text-lg">{{ book.title }}</h2>
                                <p class="text-sm text-gray-600">ISBN: {{ book.isbn }}</p>

                                <div class="badge badge-outline" v-if="book.publisher">
                                    {{ book.publisher.name }}
                                </div>

                                <div class="card-actions justify-end mt-4">
                                    <span class="text-xl font-bold mr-auto">{{ book.price }}€</span>
                                    <Link :href="route('livros.edit', book.id)" class="btn btn-ghost btn-sm">Editar</Link>
                                    <button class="btn btn-error btn-sm btn-outline">Apagar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="books.length === 0" class="text-center py-10">
                        <p class="text-gray-500 italic">Ainda não há livros registados.</p>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
