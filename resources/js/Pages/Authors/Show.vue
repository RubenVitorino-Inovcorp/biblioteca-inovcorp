<script setup>
  import Layout from '@/Layouts/AppLayout.vue'
  import {Head, Link} from '@inertiajs/vue3'
  import {Pencil} from "@lucide/vue";

  defineProps({
      author: Object,
      books: Array,
  })
</script>

<template>
  <Layout>
      <Head :title="author.name" />

      <div class="show-card p-6 md:p-8 max-w-5xl mx-auto mt-6">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
              
              <div class="md:col-span-3">
                  <img :src="author.photo_path" :alt="author.name" class="show-image shadow-lg" />
              </div>

              <div class="md:col-span-9 space-y-6">
                  <div class="flex items-start justify-between">
                      <Link :href="route('autores.edit', author.id)" class="show-title-link">
                          <h1 class="show-title">{{ author.name }}</h1>
                          <Pencil />
                      </Link>
                  </div>
              </div>
          </div>
          
          <div v-if="books.length > 0" class="mt-8">
              <div class="show-divider"></div>

              <h2 class="show-section-title">Livros de {{ author.name }}</h2>

              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-4">
                  <div v-for="book in books" :key="book.id" class="book-card">
                      <Link :href="route('livros.show', book.id)">
                          <img :src="book.image_path" :alt="book.title" class="book-card-image" />
                      </Link>
                      <div class="book-card-body">
                          <Link :href="route('livros.show', book.id)" class="show-title-link">
                              <h3 class="book-card-title">{{ book.title }}</h3>
                          </Link>
                          <p class="book-card-isbn">ISBN: {{ book.isbn }}</p>

                          <div class="mt-2" v-if="book.publisher">
                              <div class="badge-publisher">
                                  {{ book.publisher.name }}
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </Layout>
</template>
