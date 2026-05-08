<script setup>
  import Layout from '@/Layouts/AppLayout.vue'
  import { Head } from '@inertiajs/vue3'
  import PublisherCreateForm from "@/Components/PublisherCreateForm.vue";

  defineProps({
      publisher: { type: Object },
  })
</script>

<template>
  <Layout>
    <Head title="Adicionar Editora" />
    <PublisherCreateForm publisher="publisher"/>
  </Layout>
</template>
