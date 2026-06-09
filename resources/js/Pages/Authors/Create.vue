<script setup lang="ts">
  import Layout from '@/Layouts/AppLayout.vue'
  import AuthorCreateForm from "@/Components/AuthorCreateForm.vue";
  import { Author } from '@/types';

  const props = defineProps<{
      author?: Author;
  }>();
</script>

<template>
  <Layout title="Adicionar Autor">
    <AuthorCreateForm :author="author"/>
  </Layout>

</template>
