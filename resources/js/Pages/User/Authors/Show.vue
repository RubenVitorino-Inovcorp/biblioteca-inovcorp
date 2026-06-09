<script setup lang="ts">
  import Layout from '@/Layouts/AppLayout.vue'
  import { Link } from '@inertiajs/vue3'
  import { Author, Book } from '@/types';

  const props = defineProps<{
      author: Author;
      books: Book[];
  }>();
</script>

<template>
  <Layout :title="author.name">


      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Author Header Profile -->
          <div class="bg-base-100 rounded-3xl border border-gray-100 shadow-sm p-8 flex flex-col items-center text-center mb-10 relative overflow-hidden">
              <div class="absolute top-0 left-0 w-full h-32 bg-primary/5"></div>
              <img :src="author.photo_url" :alt="author.name" class="relative w-40 h-40 object-cover rounded-full shadow-md ring-4 ring-white mb-6 z-10" />
              <h1 class="relative text-3xl font-bold text-base-content font-['Manrope'] z-10">{{ author.name }}</h1>
              <p class="relative mt-2 text-base-content/60 font-medium z-10">{{ books.length }} {{ books.length === 1 ? 'Obra disponível' : 'Obras disponíveis' }} no catálogo</p>
          </div>
          
          <div v-if="books.length > 0">
              <h2 class="text-2xl font-bold text-base-content font-['Manrope'] mb-6 flex items-center gap-2">
                  <span class="w-8 h-1 bg-primary rounded-full inline-block"></span>
                  Obras de {{ author.name }}
              </h2>

              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                  <div v-for="book in books" :key="book.id" class="group bg-base-100 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
                      <Link :href="route('catalog.livros.show', book.id)" class="block flex-grow p-4 bg-gray-50/50 flex justify-center items-center relative">
                          <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                          <img :src="book.image_url" :alt="book.title" class="relative w-auto h-48 object-cover rounded shadow-sm group-hover:scale-105 transition-transform duration-500" />
                      </Link>
                      <div class="p-4 flex flex-col flex-grow">
                          <Link :href="route('catalog.livros.show', book.id)" class="font-bold text-base-content hover:text-primary transition-colors line-clamp-2 mb-1" :title="book.title">
                              {{ book.title }}
                          </Link>
                          <p class="text-xs text-base-content/60 mb-3">ISBN: {{ book.isbn }}</p>

                          <div class="mt-auto">
                              <span v-if="book.publisher" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 truncate max-w-full">
                                  {{ book.publisher.name }}
                              </span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          
          <div v-else class="bg-base-100 rounded-2xl border border-gray-100 p-12 text-center mt-10">
              <p class="text-base-content/60 text-lg font-medium">Este autor ainda não tem obras associadas no catálogo.</p>
          </div>
      </div>
  </Layout>
</template>
