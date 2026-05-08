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

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">

          <div class="col-span-1">
              <img :src="author.photo_path" :alt="author.name" class="rounded-xl shadow-lg w-full object-cover" />
          </div>

          <div class="col-span-2 space-y-4">
              <Link :href="route('autores.edit', author.id)"><h1 class="text-3xl font-bold text-base-content flex">{{ author.name }}<Pencil /></h1></Link>
          </div>
      </div>

      <div v-if="books.length > 0">
          <div class="divider"></div>

          <h1 class="text-3xl font-bold text-base-content">Livros de {{ author.name }}</h1>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="book in books" :key="book.id" class="card bg-base-100 shadow-xl border border-gray-200">
                  <Link :href="route('livros.show', book.id)">
                      <figure class="px-4 pt-4">
                          <img :src="book.image_path" :alt="book.title" class="rounded-xl h-80 w-80 object-cover" />
                      </figure>
                  </Link>
                  <div class="card-body">
                      <h2 class="card-title text-lg">{{ book.title }}</h2>
                      <p class="text-sm text-base-content opacity-70">ISBN: {{ book.isbn }}</p>

                      <div class="badge badge-outline" v-if="book.publisher">
                          {{ book.publisher.name }}
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </Layout>
</template>
