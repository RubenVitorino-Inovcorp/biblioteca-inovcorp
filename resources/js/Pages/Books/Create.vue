<script setup lang="ts">
    import Layout from "@/Layouts/AppLayout.vue";
    import { ref } from 'vue';
    import GoogleFetchDropdown from "@/Components/GoogleFetchDropdown.vue";
    import BookCreateForm from "@/Components/Books/BookCreateForm.vue";
    import { Publisher, Author, Tag } from '@/types';

    interface Filters {
        search?: string;
    }

    const props = defineProps<{
      publishers: Publisher[];
      authors: Author[];
      tags: Tag[];
      externalBooks?: any[];
      filters?: Filters;
    }>();

    const selectedBook = ref<any | null>(null);

    const handleBookSelection = (book: any) => {
      selectedBook.value = book;
    };
</script>

<template>
  <Layout title="Novo livro">

    <div class="p-6 md:p-8 max-w-5xl w-full mx-auto my-auto space-y-6">
        <GoogleFetchDropdown :filters="filters" :external-books="externalBooks" @select-book="handleBookSelection" />
        <BookCreateForm :publishers="publishers" :authors="authors" :tags="tags" :selected-book="selectedBook" />
      </div>
  </Layout>
</template>
