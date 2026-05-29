<script setup>
  import Layout from '@/Layouts/AppLayout.vue'
  import { Head } from '@inertiajs/vue3'
  import BookEditForm from "@/Components/BookEditForm.vue";

  defineProps({
      book: Object,
      publishers: Array,
      authors: Array,
      tags: Array,
  });
</script>

<template>
    <Layout>
        <Head :title="`Atualizar livro - ${book.title}`" />
        <BookEditForm :book="book" :publishers="publishers" :authors="authors" :tags="tags" />
    </Layout>
</template>
