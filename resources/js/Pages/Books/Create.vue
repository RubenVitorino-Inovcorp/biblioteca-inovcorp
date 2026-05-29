<script setup>
    import Layout from "@/Layouts/AppLayout.vue";
    import { Head } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import GoogleFetchDropdown from "@/Components/GoogleFetchDropdown.vue";
    import BookCreateForm from "@/Components/Books/BookCreateForm.vue";

    defineProps({
      publishers: Array,
      authors: Array,
      tags: Array,
      externalBooks: {type: Array, default: () => []},
      filters: Object,
    });

    const selectedBook = ref(null);

    const handleBookSelection = (book) => {
      selectedBook.value = book;
    };

</script>

<template>
  <Layout>
    <Head title="Novo livro" />
    <div class="p-6 md:p-8 max-w-5xl w-full mx-auto my-auto space-y-6">
        <GoogleFetchDropdown :filters="filters" :external-books="externalBooks" @select-book="handleBookSelection" />
        <BookCreateForm :publishers="publishers" :authors="authors" :tags="tags" :selected-book="selectedBook" />
      </div>
  </Layout>
</template>
