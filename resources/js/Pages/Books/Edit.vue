<script setup lang="ts">
  import Layout from '@/Layouts/AppLayout.vue'
  import BookEditForm from "@/Components/BookEditForm.vue";
  import { Book, Publisher, Author, Tag } from '@/types';

  const props = defineProps<{
      book: Book;
      publishers: Publisher[];
      authors: Author[];
      tags: Tag[];
  }>();
</script>

<template>
    <Layout :title="`Atualizar livro - ${book.title}`">
        <BookEditForm :book="book" :publishers="publishers" :authors="authors" :tags="tags" />
    </Layout>

</template>
