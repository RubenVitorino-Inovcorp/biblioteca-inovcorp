<script setup lang="ts">
  import Layout from '@/Layouts/AppLayout.vue'
  import PublisherEditForm from "@/Components/PublisherEditForm.vue";
  import { Publisher } from '@/types';

  const props = defineProps<{
      publisher: Publisher;
  }>();
</script>

<template>
  <Layout :title="`Atualizar editora - ${publisher.name}`">
      <PublisherEditForm :publisher="publisher" />
  </Layout>

</template>
