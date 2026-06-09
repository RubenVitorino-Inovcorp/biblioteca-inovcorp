<script setup lang="ts">
  import Layout from '@/Layouts/AppLayout.vue'
  import AuthorEditForm from "@/Components/AuthorEditForm.vue";
  import { Author } from '@/types';

  const props = defineProps<{
      author: Author;
  }>();
</script>

<template>
  <Layout :title="`Atualizar autor - ${author.name}`">
      <AuthorEditForm :author="author" />
  </Layout>

</template>
