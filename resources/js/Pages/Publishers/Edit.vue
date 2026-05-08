<script setup>
  import Layout from '@/Layouts/AppLayout.vue'
  import { Head } from '@inertiajs/vue3'
  import PublisherEditForm from "@/Components/PublisherEditForm.vue";

  defineProps({
      publisher: Object,
  })
</script>

<template>
  <Layout>
      <Head :title="`Atualizar editora - ${publisher.name}`" />
      <PublisherEditForm :publisher="publisher" />
  </Layout>
</template>
