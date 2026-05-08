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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">

            <div class="col-span-1">
                <img :src="book.image_path" :alt="book.title" class="rounded-xl shadow-lg w-full object-cover" />
            </div>

            <div class="col-span-2 space-y-4">
                <Link :href="route('livros.edit', book.id)"><h1 class="text-3xl font-bold text-base-content flex">{{ book.title }}<Pencil /></h1></Link>

                <div>
                    <label class="label font-bold">Autores</label>
                    <div class="flex flex-wrap gap-1">
                        <div class="badge badge-primary" v-for="author in book.authors" :key="author.id">
                            <Link :href="route('autores.show', author.id)">
                                {{ author.name }}
                            </Link>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="label font-bold">Editora</label>
                    <div class="flex flex-wrap gap-1">
                        <div class="badge badge-secondary gap-x-6 cursor-pointer" v-if="book.publisher">
                            <Link :href="route('editoras.show', book.publisher?.id)">
                                {{ book.publisher?.name }}
                            </Link>
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
    </Layout>
</template>
