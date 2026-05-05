<script setup>
  import {Head, Link} from '@inertiajs/vue3'
  import AppLayout from "@/Layouts/AppLayout.vue";

  defineProps({
      authors: Array,
  })
</script>

<template>
    <AppLayout title="Biblioteca - Autores">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Autores
                </h2>
                <Link :href="route('autores.create')" class="btn btn-primary btn-sm">
                    + Adicionar Autores
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="author in authors" :key="author.id" class="card bg-base-100 shadow-xl border border-gray-200">
                            <figure class="px-4 pt-4">
                                <img :src="author.photo_path" :alt="author.name" class="rounded-xl h-64 w-full object-cover" />
                            </figure>
                            <div class="card-body">
                                <p class="text-sm text-gray-500"> {{author.name}} </p>
                                <div class="card-actions justify-end mt-4">
                                    <Link :href="route('autores.edit', author.id)" class="btn btn-ghost btn-sm">Editar</Link>
                                    <button class="btn btn-error btn-sm btn-outline">Apagar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="authors.length === 0" class="text-center py-10">
                        <p class="text-gray-500 italic">Ainda não há autores registados.</p>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
