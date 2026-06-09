<script setup lang="ts">
  import Layout from '@/Layouts/AppLayout.vue'
  import PublisherCreateForm from "@/Components/PublisherCreateForm.vue";
  import { Publisher } from '@/types';

  const props = defineProps<{
      publisher?: Publisher;
  }>();
</script>

<template>
  <Layout title="Adicionar Editora">
    <PublisherCreateForm :publisher="publisher"/>
  </Layout>

</template>
