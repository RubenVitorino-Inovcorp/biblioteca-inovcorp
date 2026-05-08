<script setup>
    import Layout from "@/Layouts/AppLayout.vue";
    import { Head } from '@inertiajs/vue3'
    import BookCreateForm from "@/Components/BookCreateForm.vue";

    defineProps({
      publishers: Array,
      authors: Array,
    });
</script>

<template>
  <Layout>
    <Head title="Novo livro" />
      <BookCreateForm :publishers="publishers" :authors="authors" />
  </Layout>
</template>
