<script setup>
  import {Link} from '@inertiajs/vue3'
  import AppLayout from "@/Layouts/AppLayout.vue";
  import PublisherDeleteForm from "@/Components/PublisherDeleteForm.vue";
  import TableWrapper from "@/Components/TableWrapper.vue";
  import PublisherEditModal from "@/Components/PublisherEditModal.vue";
  import InputSearch from "@/Components/InputSearch.vue";

  const props = defineProps({
      publishers: Object,
      filters: Object,
  })
</script>

<template>
    <AppLayout title="Biblioteca - Editoras">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Editoras
                </h2>
                <InputSearch :filters="filters" route="editoras.index" placeholder="Pesquisar editora..." />
                <Link :href="route('editoras.create')" class="btn btn-primary btn-sm">
                    + Adicionar Editoras
                </Link>
            </div>
        </template>

        <TableWrapper>
            <template #header>
                <th>
                    <label>
                        <input type="checkbox" class="checkbox" />
                    </label>
                </th>
                <th>Nome</th>
                <th>Total Livros</th>
                <th></th>
                <th></th>
            </template>
            <template #body>
                <!-- row 3 -->
                <tr v-for="publisher in publishers.data" :key="publisher.id" class="hover:bg-base-300">
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox" />
                        </label>
                    </th>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="mask mask-squircle h-12 w-12">
                                    <img
                                        :src="publisher.logo_path"
                                        :alt="publisher.name" />
                                </div>
                            </div>
                            <div>
                                <Link class="hover:text-secondary" :href="route('editoras.show', publisher.id)">
                                    <div class="font-bold">{{ publisher.name }}</div>
                                </Link>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="badge badge-ghost">
                            {{ publisher.books_count }} {{ publisher.books_count === 1 ? 'livros' : 'livros' }}
                        </div>
                    </td>
                    <th>
                        <publisherEditModal :publisher="publisher" />
                    </th>
                    <th>
                        <publisherDeleteForm :publisher="publisher" />
                    </th>
                </tr>
            </template>
            <!-- foot -->
            <template #footer>
                <th>
                    <label>
                        <input type="checkbox" class="checkbox" />
                    </label>
                </th>
                <th>Nome</th>
                <th>Total Livros</th>
                <th></th>
                <th></th>
            </template>
        </TableWrapper>

        <div v-if="publishers.links && publishers.links.length > 3" class="flex justify-center mt-8 mb-4">
            <div class="join">
                <Link
                    v-for="(link, index) in publishers.links"
                    :key="index"
                    :href="link.url ?? ''"
                    class="join-item btn btn-sm"
                    :class="{
                    'btn-active btn-primary': link.active,
                    'btn-disabled opacity-50': !link.url
                }"
                    v-html="link.label"
                />
            </div>
        </div>

        <div v-if="publishers.data.length === 0" class="text-center py-10">
            <p class="text-gray-500 italic">Ainda não há autores registados.</p>
        </div>
    </AppLayout>
</template>
