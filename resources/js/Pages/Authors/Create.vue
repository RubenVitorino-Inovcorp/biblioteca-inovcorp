<script setup>
  import Layout from '@/Layouts/AppLayout.vue'
  import { Head } from '@inertiajs/vue3'
  import AuthorCreateForm from "@/Components/AuthorCreateForm.vue";

  defineProps({
      author: { type: Object },
  })
</script>

<template>
  <Layout>
    <Head title="Adicionar Autor" />
    <AuthorCreateForm author="author"/>
  </Layout>
</template>
