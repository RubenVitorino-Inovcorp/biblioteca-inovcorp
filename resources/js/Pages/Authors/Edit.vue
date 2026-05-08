<script setup>
  import Layout from '@/Layouts/AppLayout.vue'
  import { Head } from '@inertiajs/vue3'
  import AuthorEditForm from "@/Components/AuthorEditForm.vue";

  defineProps({
      author: Object,
  })
</script>

<template>
  <Layout>
      <Head :title="`Atualizar autor - ${author.name}`" />
      <AuthorEditForm :author="author" />
  </Layout>
</template>
